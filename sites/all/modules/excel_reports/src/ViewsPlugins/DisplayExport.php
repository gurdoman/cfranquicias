<?php

namespace Drupal\excel_reports\ViewsPlugins;

/**
 * @file
 * Contains the bulk export display plugin.
 *
 * This allows views to be rendered in parts by batch API.
 */

/**
 * The plugin that batches its rendering.
 *
 * We are based on a feed display for compatibility.
 *
 * @ingroup views_display_plugins
 */
class DisplayExport extends \views_plugin_display_feed {

  /**
   * The batched execution state of the view.
   */
  public $batched_execution_state;

  /**
   * The alias of the weight field in the index table.
   */
  var $weight_field_alias = '';

  /**
   * A map of the index column names to the expected views aliases.
   */
  var $field_aliases = array();

  /**
   * Private variable that stores the filename to save the results to.
   */
  var $_output_file = '';
  var $excel_reports_cached_view_loaded;
  var $errors = array();

  /**
   * Return the type of styles we require.
   */
  function get_style_type() {
    return 'excel_reports';
  }

  /**
   * Return the sections that can be defaultable.
   */
  function defaultable_sections($section = NULL) {
    if (in_array($section, array('items_per_page', 'offset', 'use_pager', 'pager_element',))) {
      return FALSE;
    }

    return parent::defaultable_sections($section);
  }

  /**
   * Define the option for this view.
   */
  function option_definition() {
    $options = parent::option_definition();
    $options['use_batch'] = array('default' => 'no_batch');
    $options['items_per_page'] = array('default' => '0');
    $options['return_path'] = array('default' => '');
    $options['style_plugin']['default'] = 'excel_reports';

    // This is the default size of a segment when doing a batched export.
    $options['segment_size']['default'] = 100;

    if (isset($options['defaults']['default']['items_per_page'])) {
      $options['defaults']['default']['items_per_page'] = FALSE;
    }

    return $options;
  }

  /**
   * Provide the summary for page options in the views UI.
   *
   * This output is returned as an array.
   */
  function options_summary(&$categories, &$options) {
    // It is very important to call the parent function here:
    parent::options_summary($categories, $options);

    $categories['page']['title'] = t('Excel export settings');

    $options['use_batch'] = array(
      'category' => 'page',
      'title' => t('Batched export'),
      'value' => ($this->get_option('use_batch') == 'batch' ? t('Yes') : t('No')),
    );

    if (!$this->is_compatible() && $this->get_option('use_batch')) {
      $options['use_batch']['value'] .= ' <strong>' . t('(Warning: incompatible)') . '</strong>';
    }
  }

  /**
   * Provide the default form for setting options.
   */
  function options_form(&$form, &$form_state) {
    // It is very important to call the parent function here:
    parent::options_form($form, $form_state);

    switch ($form_state['section']) {
      case 'use_batch':
        $form['#title'] .= t('Batched export');
        $form['use_batch'] = array(
          '#type' => 'radios',
          '#description' => t(''),
          '#default_value' => $this->get_option('use_batch'),
          '#options' => array(
            'no_batch' => t('Export data all in one segment. Possible time and memory limit issues.'),
            'batch' => t('Export data in small segments to build a complete export. Recommended for large exports sets (1000+ rows)'),
          ),
        );
        // Allow the administrator to configure the number of items exported per batch.
        $form['segment_size'] = array(
          '#type' => 'select',
          '#title' => t('Segment size'),
          '#description' => t('If each row of your export consumes a lot of memory to render, then reduce this value. Higher values will generally mean that the export completes in less time but will have a higher peak memory usage.'),
          '#options' => drupal_map_assoc(range(1, 500)),
          '#default_value' => $this->get_option('segment_size'),
          '#process' => array('ctools_dependent_process'),
          '#dependency' => array(
            'radio:use_batch' => array('batch')
          ),
        );
        $form['return_path'] = array(
          '#title' => t('Return path'),
          '#type' => 'textfield',
          '#description' => t('Return path after the batched operation, leave empty for default. This path will only be used if the export URL is visited directly, and not by following a link when attached to another view display.'),
          '#default_value' => $this->get_option('return_path'),
          '#dependency' => array(
            'radio:use_batch' => array('batch')
          ),
        );
        if (!$this->is_compatible()) {
          $form['use_batch']['#disabled'] = TRUE;
          $form['use_batch']['#default_value'] = 'no_batch';
          $form['use_batch']['message'] = array(
            '#type' => 'markup',
            '#markup' => theme('excel_reports_message', array('message' => t('The underlying database (!db_driver) is incompatible with the batched export option and it has been disabled.', array('!db_driver' => $this->_get_database_driver())), 'type' => 'warning')),
            '#weight' => -10,
          );
        }
        break;

      case 'cache':
        // We're basically going to disable using cache plugins, by disabling
        // the UI.
        if (isset($form['cache']['type']['#options'])) {
          foreach ($form['cache']['type']['#options'] as $id => $v) {
            if ($id != 'none') {
              unset($form['cache']['type']['#options'][$id]);
            }
            $form['cache']['type']['#description'] = t("Views data export isn't currently compatible with caching plugins.");
          }
        }
        break;
    }
  }

  function get_option($option) {
    // Force people to never use caching with Views data export. Sorry folks,
    // but it causes too many issues for our workflow. If you really want to add
    // caching back, then you can subclass this display handler and override
    // this method to add it back.
    if ($option == 'cache') {
      return array('type' => 'none');
    }

    return parent::get_option($option);
  }

  /**
   * Save the options from the options form.
   */
  function options_submit(&$form, &$form_state) {
    // It is very important to call the parent function here:
    parent::options_submit($form, $form_state);
    switch ($form_state['section']) {
      case 'use_batch':
        $this->set_option('use_batch', $form_state['values']['use_batch']);
        $this->set_option('segment_size', $form_state['values']['segment_size']);
        $this->set_option('return_path', $form_state['values']['return_path']);
        break;
    }
  }

  /**
   * Determine if this view should run as a batch or not.
   */
  function is_batched() {
    // The source of this option may change in the future.
    return ($this->get_option('use_batch') == 'batch') && empty($this->view->live_preview);
  }

  /**
   * Add HTTP headers for the file export.
   */
  function add_http_headers() {
    // Ask the style plugin to add any HTTP headers if it wants.
    if (method_exists($this->view->style_plugin, 'add_http_headers')) {
      $this->view->style_plugin->add_http_headers();
    }
  }

  /**
   * Execute this display handler.
   *
   * This is the main entry point for this display. We do different things based
   * on the stage in the rendering process.
   *
   * If we are being called for the very first time, the user has usually just
   * followed a link to our view. For this phase we:
   * - Register a new batched export with our parent module.
   * - Build and execute the view, redirecting the output into a temporary table.
   * - Set up the batch.
   *
   * If we are being called during batch processing we:
   * - Set up our variables from the context into the display.
   * - Call the rendering layer.
   * - Return with the appropriate progress value for the batch.
   *
   * If we are being called after the batch has completed we:
   * - Remove the index table.
   * - Show the complete page with a download link.
   * - Transfer the file if the download link was clicked.
   */
  function execute() {
    if (!$this->is_batched()) {
      return parent::execute();
    }

    // Try and get a batch context if possible.
    $eid = !empty($_GET['eid']) ? $_GET['eid'] :
        (!empty($this->batched_execution_state->eid) ? $this->batched_execution_state->eid : FALSE);
    if ($eid) {
      $this->batched_execution_state = \Drupal\excel_reports\ExcelReportViewExport::get($eid);
    }

    // First time through
    if (empty($this->batched_execution_state)) {
      $output = $this->execute_initial();
    }

    // Last time through
    if ($this->batched_execution_state->batch_state == excel_reports_FINISHED) {
      $output = $this->execute_final();
    }
    // In the middle of processing
    else {
      $output = $this->execute_normal();
    }

    //Ensure any changes we made to the database sandbox are saved
    \Drupal\excel_reports\ExcelReportViewExport::update($this->batched_execution_state);

    return $output;
  }

  /**
   * Initializes the whole export process and starts off the batch process.
   *
   * Page execution will be ended at the end of this function.
   */
  function execute_initial() {

    // Register this export with our central table - get a unique eid
    // Also store our view in a cache to be retrieved with each batch call
    $this->batched_execution_state = \Drupal\excel_reports\ExcelReportViewExport::save($this->view->name, $this->view->current_display, $this->outputfile_create());
    \Drupal\excel_reports\ExcelReportViewObjectCache::store($this->batched_execution_state->eid, $this->view);

    // We need to build the index right now, before we lose $_GET etc.
    $this->initialize_index();
    // Initialize the progress counter
    $this->batched_execution_state->sandbox['max'] = db_query('SELECT COUNT(*) FROM {' . $this->index_tablename() . '}')->fetchField();
    // Record the time we started.
    list($usec, $sec) = explode(' ', microtime());
    $this->batched_execution_state->sandbox['started'] = (float) $usec + (float) $sec;

    // Build up our querystring for the final page callback.
    $querystring = array(
      'eid' => $this->batched_execution_state->eid,
    );
    // If we were attached to another view, grab that for the final URL.
    if (!empty($_GET['attach']) && isset($this->view->display[$_GET['attach']])) {
      // Get the path of the attached display:
      $querystring['return-url'] = $this->view->get_url(NULL, $this->view->display[$_GET['attach']]->handler->get_path());
    }
    else {
      $return_path = $this->get_option('return_path');
      $querystring['return-url'] = isset($return_path) ? $return_path : NULL;
    }

    //Set the batch off
    $batch = array(
      'operations' => array(
        array('_excel_reports_batch_process', array($this->batched_execution_state->eid, $this->view->current_display, $this->view->get_exposed_input())),
      ),
      'title' => t('Building export'),
      'init_message' => t('Export is starting up.'),
      'progress_message' => t('Exporting @percentage% complete,'),
      'error_message' => t('Export has encountered an error.'),
    );

    // We do not return, so update database sandbox now
    \Drupal\excel_reports\ExcelReportViewExport::update($this->batched_execution_state);

    $final_destination = $this->view->get_url();

    // Provide a way in for others at this point
    // e.g. Drush to grab this batch and yet execute it in
    // it's own special way
    $batch['view_name'] = $this->view->name;
    $batch['exposed_filters'] = $this->view->get_exposed_input();
    $batch['display_id'] = $this->view->current_display;
    $batch['eid'] = $this->batched_execution_state->eid;
    $batch_redirect = array($final_destination, array('query' => $querystring));
    drupal_alter('excel_reports_batch', $batch, $batch_redirect);

    // Modules may have cleared out $batch, indicating that we shouldn't process further.
    if (!empty($batch)) {
      batch_set($batch);
      // batch_process exits
      batch_process($batch_redirect);
    }
  }

  /**
   * Compiles the next chunk of the output file
   */
  function execute_normal() {

    // Pass through to our render method,
    $output = $this->view->render();

    if (!empty($output)) {
      $this->outputfile_write($output);
    }

    // Store for convenience.
    $state = &$this->batched_execution_state;
    $sandbox = &$state->sandbox;

    // Update progress measurements & move our state forward
    switch ($state->batch_state) {

      case excel_reports_BODY:
        // Remove rendered results from our index
        if (count($this->view->result) && ($sandbox['weight_field_alias'])) {
          $last = end($this->view->result);
          db_delete($this->index_tablename())
              ->condition($sandbox['weight_field_alias'], $last->{$sandbox['weight_field_alias']}, '<=')
              ->execute();

          // Update progress.
          $progress = db_query('SELECT COUNT(*) FROM {' . $this->index_tablename() . '}')->fetchField();
          // TODO: These next few lines are messy, clean them up.
          $progress = 0.99 - ($progress / $sandbox['max'] * 0.99);
          $progress = ((int) floor($progress * 100000));
          $progress = $progress / 100000;
          $sandbox['finished'] = $progress;
        }
        else {
          // No more results.
          $sandbox['finished'] = 1;
          $state->batch_state = excel_reports_FINISHED;
        }
        break;

      case excel_reports_HEADER:
        $sandbox['finished'] = 0;
        $state->batch_state = excel_reports_BODY;
        break;
    }

    // Create a more helpful exporting message.
    $sandbox['message'] = $this->compute_time_remaining($sandbox['started'], $sandbox['finished']);
  }

  /**
   * Renders the final page
   *  We should be free of the batch at this point
   */
  function execute_final() {
    // Should we download the file.
    if (!empty($_GET['download'])) {
      // This next method will exit.
      $this->transfer_file();
    }
    else {
      // Remove the index table.
      $this->remove_index();
      return $this->render_complete();
    }
  }

  /**
   * Render the display.
   *
   * We basically just work out if we should be rendering the header, body or
   * footer and call the appropriate functions on the style plugins.
   */
  function render() {

    if (!$this->is_batched()) {
      $result = parent::render();
      if (empty($this->view->live_preview)) {
        $this->add_http_headers();
      }
      return $result;
    }

    $this->view->build();

    switch ($this->batched_execution_state->batch_state) {
      case excel_reports_HEADER:
        $output = $this->view->style_plugin->render_header();
        break;
      case excel_reports_BODY:
        $output = $this->view->style_plugin->render_body();
        break;
    }

    return $output;
  }

  /**
   * Trick views into thinking that we have executed the query and got results.
   *
   * We are called in the build phase of the view, but short circuit straight to
   * getting the results and making the view think it has already executed the
   * query.
   */
  function query() {

    if (!$this->is_batched()) {
      return parent::query();
    }

    // Make the query distinct if the option was set.
    if ($this->get_option('distinct')) {
      $this->view->query->set_distinct();
    }

    if (!empty($this->batched_execution_state->batch_state) && !empty($this->batched_execution_state->sandbox['weight_field_alias'])) {

      switch ($this->batched_execution_state->batch_state) {
        case excel_reports_BODY:
        case excel_reports_HEADER:
          // Tell views its been executed.
          $this->view->executed = TRUE;

          // Grab our results from the index, and push them into the view result.
          // TODO: Handle external databases.
          $result = db_query_range('SELECT * FROM {' . $this->index_tablename() . '} ORDER BY ' . $this->batched_execution_state->sandbox['weight_field_alias'] . ' ASC', 0, $this->get_option('segment_size'));
          $this->view->result = array();
          foreach ($result as $item_hashed) {
            $item = new \stdClass();
            // We had to shorten some of the column names in the index, restore
            // those now.
            foreach ($item_hashed as $hash => $value) {
              if (isset($this->batched_execution_state->sandbox['field_aliases'][$hash])) {
                $item->{$this->batched_execution_state->sandbox['field_aliases'][$hash]} = $value;
              }
              else {
                $item->{$hash} = $value;
              }
            }
            // Push the restored $item in the views result array.
            $this->view->result[] = $item;
          }
          $this->view->_post_execute();
          break;
      }
    }
  }

  /**
   * Render the 'Export Finished' page with the link to the file on it.
   */
  function render_complete() {
    $return_path = empty($_GET['return-url']) ? '' : $_GET['return-url'];

    $query = array(
      'download' => 1,
      'eid' => $this->batched_execution_state->eid,
    );

    return theme('excel_reports_complete_page', array(
      'file' => url($this->view->get_url(), array('query' => $query)),
      'errors' => $this->errors,
      'return_url' => $return_path));
  }

  /**
   * TBD - What does 'preview' mean for bulk exports?
   */
  function preview() {
    return parent::preview();
  }

  /**
   * Transfer the output file to the client.
   */
  function transfer_file() {
    // Build the view so we can set the headers.
    $this->view->build();
    // Arguments can cause the style to not get built.
    if (!$this->view->init_style()) {
      $this->view->build_info['fail'] = TRUE;
    }
    // Set the headers.
    $this->add_http_headers();
    file_transfer($this->outputfile_path(), array());
  }

  /**
   * Called on export initialization.
   *
   * Modifies the view query to insert the results into a table, which we call
   * the 'index', this means we essentially have a snapshot of the results,
   * which we can then take time over rendering.
   *
   * This method is essentially all the best bits of the view::execute() method.
   */
  protected function initialize_index() {
    $view = &$this->view;
    // Get views to build the query.
    $view->build();

    // Change the query object to use our custom one.
    switch ($this->_get_database_driver()) {
      case 'pgsql':
        $query_class = '\Drupal\excel_reports\ViewsPlugins\QueryPgsqlBatched';
        break;

      default:
        $query_class = '\Drupal\excel_reports\ViewsPlugins\QueryDefaultBatched';
        break;
    }
    $query = new $query_class();
    // Copy the query over:
    foreach ($view->query as $property => $value) {
      $query->$property = $value;
    }
    // Replace the query object.
    $view->query = $query;

    $view->execute();
  }

  /**
   * Given a view, construct an map of hashed aliases to aliases.
   *
   * The keys of the returned array will have a maximum length of 33 characters.
   */
  function field_aliases_create(&$view) {
    $all_aliases = array();
    foreach ($view->query->fields as $field) {
      if (strlen($field['alias']) > 32) {
        $all_aliases['a' . md5($field['alias'])] = $field['alias'];
      }
      else {
        $all_aliases[$field['alias']] = $field['alias'];
      }
    }
    return $all_aliases;
  }

  /**
   * Create an alias for the weight field in the index.
   *
   * This method ensures that it isn't the same as any other alias in the
   * supplied view's fields.
   */
  function _weight_alias_create(&$view) {
    $alias = 'vde_weight';
    $all_aliases = array();
    foreach ($view->query->fields as $field) {
      $all_aliases[] = $field['alias'];
    }
    // Keep appending '_' until we are unique.
    while (in_array($alias, $all_aliases)) {
      $alias .= '_';
    }
    return $alias;
  }

  /**
   * Remove the index.
   */
  function remove_index() {
    $ret = array();
    if (db_table_exists($this->index_tablename())) {
      db_drop_table($this->index_tablename());
    }
  }

  /**
   * Return the name of the unique table to store the index in.
   */
  function index_tablename() {
    return excel_reports_INDEX_TABLE_PREFIX . $this->batched_execution_state->eid;
  }

  /**
   * Get the output file path.
   */
  function outputfile_path() {
    if (empty($this->_output_file)) {
      if (!empty($this->batched_execution_state->fid)) {
        // Return the filename associated with this file.
        $this->_output_file = $this->file_load($this->batched_execution_state->fid);
      }
      else {
        return NULL;
      }
    }
    return $this->_output_file->uri;
  }

  /**
   * Called on export initialization
   * Creates the output file, registers it as a temporary file with Drupal
   * and returns the fid
   */
  protected function outputfile_create() {

    $dir = variable_get('excel_reports_directory', 'temporary://views_plugin_display');

    // Make sure the directory exists first.
    if (!file_prepare_directory($dir, FILE_CREATE_DIRECTORY | FILE_MODIFY_PERMISSIONS)) {
      $this->abort_export(t('Could not create temporary directory for result export (@dir). Check permissions.', array('@dir' => $dir)));
    }

    $this->filepath = drupal_tempnam($dir, 'excel_reports');

    // Save the file into the DB.
    $file = $this->file_save_file($this->filepath);

    /**
     * Create the XLSX file structure with the temp file just created
     */
    $ExcelReport = new \Drupal\excel_reports\ExcelReport(drupal_realpath($this->filepath));
    $ExcelReport->savePermanentFile();

    return $file->fid;
  }

  /**
   * Write to the output file.
   */
  protected function outputfile_write($rows) {
    try {
      // Some arrays has objects inside, so we need to convert them to arrays too
      if (is_object($rows[0])) {
        foreach ($rows as $key => $row) {
          $sanitized_rows[$key] = get_object_vars($row);
        }
        $rows = $sanitized_rows;
      }

      $output_file = $this->outputfile_path();

      $ExcelReport = new \Drupal\excel_reports\ExcelReport(drupal_realpath($output_file));
      $ExcelReport->resetSpreadsheets();
      $ExcelReport->createTableFromArray($rows, 0, $ExcelReport->getHighestRow() + 1);
      $ExcelReport->savePermanentFile();
    }
    catch (Exception $ex) {
      $this->abort_export(t('Could not write to temporary output file for result export (@file). Check permissions.', array('@file' => $output_file)));
    }
  }

  function abort_export($errors) {
    // Just cause the next batch to do the clean-up
    if (!is_array($errors)) {
      $errors = array($errors);
    }
    foreach ($errors as $error) {
      drupal_set_message($error . ' [' . t('Export Aborted') . ']', 'error');
    }
    $this->batched_execution_state->batch_state = excel_reports_FINISHED;
    $this->batched_execution_state->sandbox['finished'] = 1;
  }

  /**
   * Load a file from the database.
   *
   * @param $fid
   *   A numeric file id or string containing the file path.
   * @return
   *   A file object.
   */
  function file_load($fid) {
    return file_load($fid);
  }

  /**
   * Save a file into a file node after running all the associated validators.
   *
   * This function is usually used to move a file from the temporary file
   * directory to a permanent location. It may be used by import scripts or other
   * modules that want to save an existing file into the database.
   *
   * @param $filepath
   *   The local file path of the file to be saved.
   * @return
   *   An array containing the file information, or 0 in the event of an error.
   */
  function file_save_file($filepath) {
    return file_save_data('', $filepath, FILE_EXISTS_REPLACE);
  }

  /**
   * Helper function that computes the time remaining
   */
  function compute_time_remaining($started, $finished) {
    list($usec, $sec) = explode(' ', microtime());
    $now = (float) $usec + (float) $sec;
    $diff = round(($now - $started), 0);
    // So we've taken $diff seconds to get this far.
    if ($finished > 0) {
      $estimate_total = $diff / $finished;
      $stamp = max(1, $estimate_total - $diff);
      // Round up to nearest 30 seconds.
      $stamp = ceil($stamp / 30) * 30;
      // Set the message in the batch context.
      return t('Time remaining: about @interval.', array('@interval' => format_interval($stamp)));
    }
  }

  /**
   * Checks the driver of the database underlying
   * this query and returns FALSE if it is imcompatible
   * with the approach taken in this display.
   * Basically mysql & mysqli will be fine, pg will not
   */
  function is_compatible() {
    $incompatible_drivers = array(
        //'pgsql',
    );
    $db_driver = $this->_get_database_driver();
    return !in_array($db_driver, $incompatible_drivers);
  }

  function _get_database_driver() {
    $name = !empty($this->view->base_database) ? $this->view->base_database : 'default';
    $conn_info = \Database::getConnectionInfo($name);
    return $conn_info['default']['driver'];
  }

}
