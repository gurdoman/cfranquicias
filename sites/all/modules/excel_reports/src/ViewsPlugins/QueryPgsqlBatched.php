<?php

namespace Drupal\excel_reports\ViewsPlugins;

/**
 * @file
 * Contains the bulk export display plugin.
 *
 * This allows views to be rendered in parts by batch API.
 */
class QueryPgsqlBatched extends QueryDefaultBatched {

  /**
   * Executes the query and fills the associated view object with according
   * values.
   *
   * Values to set: $view->result, $view->total_rows, $view->execute_time,
   * $view->current_page.
   */
  function execute(&$view) {
    $display_handler = &$view->display_handler;
    $external = FALSE; // Whether this query will run against an external database.
    $query = $view->build_info['query'];
    $count_query = $view->build_info['count_query'];

    $query->addMetaData('view', $view);
    $count_query->addMetaData('view', $view);

    if (empty($this->options['disable_sql_rewrite'])) {
      $base_table_data = views_fetch_data($this->base_table);
      if (isset($base_table_data['table']['base']['access query tag'])) {
        $access_tag = $base_table_data['table']['base']['access query tag'];
        $query->addTag($access_tag);
        $count_query->addTag($access_tag);
      }
    }

    $items = array();
    if ($query) {
      $additional_arguments = module_invoke_all('views_query_substitutions', $view);

      // Count queries must be run through the preExecute() method.
      // If not, then hook_query_node_access_alter() may munge the count by
      // adding a distinct against an empty query string
      // (e.g. COUNT DISTINCT(1) ...) and no pager will return.
      // See pager.inc > PagerDefault::execute()
      // http://api.drupal.org/api/drupal/includes--pager.inc/function/PagerDefault::execute/7
      // See http://drupal.org/node/1046170.
      $count_query->preExecute();

      // Build the count query.
      $count_query = $count_query->countQuery();

      // Add additional arguments as a fake condition.
      // XXX: this doesn't work... because PDO mandates that all bound arguments
      // are used on the query. TODO: Find a better way to do this.
      if (!empty($additional_arguments)) {
        // $query->where('1 = 1', $additional_arguments);
        // $count_query->where('1 = 1', $additional_arguments);
      }

      $start = microtime(TRUE);

      if ($this->pager->use_count_query() || !empty($view->get_total_rows)) {
        $this->pager->execute_count_query($count_query);
      }

      // Let the pager modify the query to add limits.
      $this->pager->pre_execute($query);

      if (!empty($this->limit) || !empty($this->offset)) {
        // We can't have an offset without a limit, so provide a very large limit instead.
        $limit = intval(!empty($this->limit) ? $this->limit : 999999);
        $offset = intval(!empty($this->offset) ? $this->offset : 0);
        $query->range($offset, $limit);
      }

      try {
        // The $query is final and ready to go, we are going to redirect it to
        // become an insert into our table, sneaky!
        // Our query will look like:
        // CREATE TABLE {idx} SELECT @row := @row + 1 AS weight_alias, cl.* FROM
        // (-query-) AS cl, (SELECT @row := 0) AS r
        // We do some magic to get the row count.

        $display_handler->batched_execution_state->sandbox['weight_field_alias'] = $display_handler->_weight_alias_create($view);

        $display_handler->batched_execution_state->sandbox['field_aliases'] = $display_handler->field_aliases_create($view);
        $select_aliases = array();
        foreach ($display_handler->batched_execution_state->sandbox['field_aliases'] as $hash => $alias) {
          $select_aliases[] = "cl.$alias AS $hash";
        }

        // TODO: this could probably be replaced with a query extender and new query type.
        $query->preExecute();
        $args = $query->getArguments();
        // Create temporary sequence
        $seq_name = $display_handler->index_tablename() . '_seq';
        db_query('CREATE TEMP sequence ' . $seq_name);
        // Query uses sequence to create row number
        $insert_query = 'CREATE TABLE {' . $display_handler->index_tablename() . "} AS SELECT nextval('" . $seq_name . "') AS " . $display_handler->batched_execution_state->sandbox['weight_field_alias'] . ', ' . implode(', ', $select_aliases) . ' FROM (' . (string) $query . ') AS cl';
        db_query($insert_query, $args);


        $view->result = array();

        $this->pager->post_execute($view->result);

        if ($this->pager->use_pager()) {
          $view->total_rows = $this->pager->get_total_items();
        }

        // Now create an index for the weight field, otherwise the queries on the
        // index will take a long time to execute.
        db_add_unique_key($display_handler->index_tablename(), $display_handler->batched_execution_state->sandbox['weight_field_alias'], array($display_handler->batched_execution_state->sandbox['weight_field_alias']));
      }
      catch (Exception $e) {
        $view->result = array();
        debug('Exception: ' . $e->getMessage());
      }
    }
    $view->execute_time = microtime(TRUE) - $start;
  }
}
