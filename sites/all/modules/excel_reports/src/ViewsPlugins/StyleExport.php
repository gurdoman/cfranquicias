<?php

namespace Drupal\excel_reports\ViewsPlugins;

/**
 * @file
 * Plugin include file for export style plugin.
 */

/**
 * Generalized style plugin for export plugins.
 *
 * @ingroup views_style_plugins
 */
class StyleExport extends \views_plugin_style {

  /**
   * Set options fields and default values.
   *
   * @return
   * An array of options information.
   */
  function option_definition() {
    $options = parent::option_definition();

    $options['attach_text'] = array(
      'default' => $this->definition['export feed text'],
      'translatable' => TRUE,
    );
    $options['provide_file'] = array(
      'default' => FALSE,
      'translatable' => FALSE,
    );
    $options['filename'] = array(
      'default' => $this->definition['export feed file'],
      'translatable' => FALSE,
    );
    $options['parent_sort'] = array(
      'default' => FALSE,
      'translatable' => FALSE,
    );
    return $options;
  }

  /**
   * Options form mini callback.
   *
   * @param $form
   * Form array to add additional fields to.
   * @param $form_state
   * State of the form.
   * @return
   * None.
   */
  function options_form(&$form, &$form_state) {
    $form['attach_text'] = array(
      '#type' => 'textfield',
      '#title' => t('Attach text'),
      '#default_value' => $this->options['attach_text'],
      '#description' => t('This text is used in building the feed link. By default it is the "alt" text for the feed image.'),
    );
    $form['provide_file'] = array(
      '#type' => 'checkbox',
      '#title' => t('Provide as file'),
      '#default_value' => $this->options['provide_file'],
      '#description' => t('By deselecting this, the xml file will be provided as a feed instead of a file for download.'),
    );
    $form['filename'] = array(
      '#type' => 'textfield',
      '#title' => t('Filename'),
      '#default_value' => $this->options['filename'],
      '#description' => t('The filename that will be suggested to the browser for downloading purposes. You may include replacement patterns from the list below.'),
      '#process' => array('ctools_dependent_process'),
      '#dependency' => array(
        'edit-style-options-provide-file' => array(TRUE),
      ),
    );

    // General token replacement.
    $output = t('<p>The following substitution patterns are available for this display. Use the pattern shown on the left to display the value indicated on the right.</p>');
    $items = array(
      '%view == ' . t('View name'),
      '%display == ' . t('Display name'),
    );

    $output .= theme('item_list', array('items' => $items));

    // Get a list of the available arguments for token replacement.
    $options = array();

    $count = 0; // This lets us prepare the key as we want it printed.
    foreach ($this->view->display_handler->get_handlers('argument') as $arg => $handler) {
      $options[t('Arguments')]['%' . ++$count . '-title'] = t('@argument title', array('@argument' => $handler->ui_name()));
      $options[t('Arguments')]['%' . $count . '-value'] = t('@argument value', array('@argument' => $handler->ui_name()));
    }

    // Append the list with exposed filters stuff.
    $options[t('Exposed filters')]['%exposed'] = t('effective exposed filters, like <em>filter1_foo-filter2_bar</em>');

    // ...and datestamp.
    $time = REQUEST_TIME;
    $parts = array(
      'full' => 'Y-m-d\TH-i-s',
      'yy' => 'y',
      'yyyy' => 'Y',
      'mm' => 'm',
      'mmm' => 'M',
      'dd' => 'd',
      'ddd' => 'D',
      'hh' => 'H',
      'ii' => 'i',
      'ss' => 's',
    );
    foreach ($parts as $part => $format) {
      $options[t('Timestamp')]['%timestamp-' . $part] = format_date($time, 'custom', $format);
    }

    // We have some options, so make a list.
    if (!empty($options)) {
      foreach (array_keys($options) as $type) {
        if (!empty($options[$type])) {
          $items = array();
          foreach ($options[$type] as $key => $value) {
            $items[] = $key . ' == ' . $value;
          }
          $output .= theme('item_list', array('items' => $items, 'title' => $type));
        }
      }
    }
    $form['help'] = array(
      '#type' => 'fieldset',
      '#title' => t('Replacement patterns'),
      '#collapsible' => TRUE,
      '#collapsed' => TRUE,
      '#value' => $output,
      '#dependency' => array(
        'edit-style-options-provide-file' => array(1),
      ),
    );
    $form['parent_sort'] = array(
      '#type' => 'checkbox',
      '#title' => t('Parent sort'),
      '#default_value' => $this->options['parent_sort'],
      '#description' => t('Try to apply any additional sorting from the attached display like table sorting to the exported feed.'),
    );
  }

  /**
   * Attach this view to another display as a feed.
   *
   * Provide basic functionality for all export style views like attaching a
   * feed image link.
   */
  function attach_to($display_id, $path, $title) {

    $type = $this->definition['export feed type'];
    $theme_pattern = array(
      'excel_reports_feed_icon__' . $this->view->name . '__' . $display_id . '__' . $type,
      'excel_reports_feed_icon__' . $this->view->name . '__' . $display_id,
      'excel_reports_feed_icon__' . $this->view->name . '__' . $type,
      'excel_reports_feed_icon__' . $display_id . '__' . $type,
      'excel_reports_feed_icon__' . $display_id,
      'excel_reports_feed_icon__' . $type,
      'excel_reports_feed_icon',
    );
    $query = $this->view->get_exposed_input();
    // Stash the display id we're coming form in the url so we can hijack it later.
    if ($this->options['parent_sort']) {
      $query['attach'] = $display_id;
    }
    if (!isset($this->view->feed_icon)) {
      $this->view->feed_icon = '';
    }
    $this->view->feed_icon .= theme($theme_pattern, array(
      'image_path' => $this->definition['export feed icon'],
      'url' => $this->view->get_url(NULL, $path),
      'query' => $query,
      'text' => $this->options['attach_text'],
        )
    );
  }

  function build_sort() {

    // Bypass doing any sort of testing if parent sorting is disabled.
    if (!$this->options['parent_sort']) {
      return parent::build_sort();
    }

    $displays = $this->display->handler->get_option('displays');

    // Here is later. We can get the passed argument and use it to know which
    // display we can from and then do some addition processing.
    // If the display exists and is attached these two tests will succeed.
    if (isset($_GET['attach']) && isset($displays[$_GET['attach']]) && $displays[$_GET['attach']]) {
      // Setup the second style we're going to be using to sort on.
      $plugin_id = $displays[$_GET['attach']];
      $parent_display = $this->view->display[$plugin_id];
      $style_name = $parent_display->handler->get_option('style_plugin');
      $style_options = $parent_display->handler->get_option('style_options');
      $this->extra_style = views_get_plugin('style', $style_name);
      $this->extra_style->init($this->view, $parent_display, $style_options);

      // Call the second styles sort funciton and return the value.
      return $this->extra_style->build_sort();
    }
  }

  function build_sort_post() {
    // If we found an extra style plugin earlier, pass off the build_sort_post call to it.
    if (isset($this->extra_style)) {
      return $this->extra_style->build_sort_post();
    }
    else {
      return parent::build_sort_post();
    }
  }

  function render_header() {
    $rows = array(0 => array());
    $fields = &$this->view->field;
    foreach ($fields as $key => $field) {
      if (empty($field->options['exclude'])) {
        $rows[0][$key] = check_plain($field->label());
      }
    }
    return $rows;
  }

  function render_body() {

    if ($this->uses_row_plugin() && empty($this->row_plugin)) {
      vpr('views_plugin_style_default: Missing row plugin');
      return;
    }

    // Group the rows according to the grouping field, if specified.
    $sets = $this->render_grouping($this->view->result, $this->options['grouping']);

    // Render each group separately and concatenate.  Plugins may override this
    // method if they wish some other way of handling grouping.
    $results = array();
    foreach ($sets as $title => $records) {
      if ($this->uses_row_plugin()) {
        $rows = array();
        foreach ($records as $row_index => $row) {
          $this->view->row_index = $row_index;
          $rows[] = $this->row_plugin->render($row);
        }
      }
      else {
        $rows = $records;
      }
      $results = array_merge($results, $this->renderRowsFields($rows));
    }
    unset($this->view->row_index);
    return $results;
  }

  function renderRowsFields($rows) {
    $view = $this->view;
    $fields = &$view->field;
    $hide_empty_support = !empty($vars['hide_empty_support']);

    $return_rows = array();
    $keys = array_keys($fields);
    foreach ($rows as $num => $row) {
      $return_rows[$num] = array();

      foreach ($keys as $id) {
        if (empty($fields[$id]->options['exclude'])) {
          $content = $view->style_plugin->rendered_fields[$num][$id];
          if ($hide_empty_support && !empty($fields[$id]->options['hide_empty'])) {
            if ($fields[$id]->is_value_empty($content, $fields[$id]->options['empty_zero'])) {
              continue;
            }
          }
          $return_rows[$num][$id] = $content;
        }
      }
    }

    // Format row values.
    foreach ($return_rows as $i => $values) {
      foreach ($values as $j => $value) {
        $output = decode_entities($value);
        $return_rows[$i][$j] = $output;
      }
    }
    return $return_rows;
  }

  /**
   * Provide a full list of possible theme templates used by this style.
   */
  function theme_functions($hook = NULL) {
    if (is_null($hook)) {
      $hook = $this->definition['theme'];
    }
    return views_theme_functions($hook, $this->view, $this->display);
  }

  /**
   * Add any HTTP headers that this style plugin wants to.
   */
  function add_http_headers() {

    drupal_add_http_header('Cache-Control', 'max-age=60, must-revalidate');

    if (!empty($this->definition['export headers'])) {
      foreach ($this->definition['export headers'] as $name => $value) {
        drupal_add_http_header($name, $value);
      }
    }

    if (isset($this->options['filename']) && !empty($this->options['provide_file'])) {
      $filename = $this->generate_filename();

      if ($filename) {
        drupal_add_http_header('Content-Disposition', 'attachment; filename="' . $filename . '"');
      }
    }
  }

  /**
   * Generate the filename for the export.
   */
  function generate_filename() {
    $view = $this->view;
    $filename = '';

    if (isset($this->options['filename']) && !empty($this->options['provide_file'])) {
      // General tokens.
      $tokens = array(
        '%view' => check_plain($view->name),
        '%display' => check_plain($view->current_display),
      );
      // Argument tokens.
      $count = 0;
      foreach ($view->display_handler->get_handlers('argument') as $arg => $handler) {
        $token = '%' . ++$count;
        $tokens[$token . '-title'] = check_plain($handler->title());
        $tokens[$token . '-value'] = isset($view->args[$count - 1]) ? check_plain($view->args[$count - 1]) : '';
      }

      // Effective exposed filters token.
      $exposed = array();
      foreach ($view->display_handler->get_handlers('filter') as $arg => $handler) {
        if (!$handler->options['exposed']) {
          continue;
        }
        if (!empty($view->exposed_input[$handler->options['expose']['identifier']])) {
          $identifier = $handler->options['expose']['identifier'];
          $option = $view->exposed_input[$identifier];
          // The option may be a string or an array, depending on whether the
          // widget is a text box/area or a select box.
          if (is_array($option)) {
            $option = implode('--', $option);
          }
          $exposed[] = check_plain($identifier) . '_' . check_plain($option);
        }
      }
      if (!empty($exposed)) {
        $tokens['%exposed'] = implode('-', $exposed);
      }
      else {
        $tokens['%exposed'] = 'default';
      }

      // Timestamp token.
      $time = REQUEST_TIME;
      $parts = array(
        'full' => 'Y-m-d\TH-i-s',
        'yy' => 'y',
        'yyyy' => 'Y',
        'mm' => 'm',
        'mmm' => 'M',
        'dd' => 'd',
        'ddd' => 'D',
        'hh' => 'H',
        'ii' => 'i',
        'ss' => 's',
      );
      foreach ($parts as $part => $format) {
        $tokens['%timestamp-' . $part] = format_date($time, 'custom', $format);
      }

      $filename = strtr($this->options['filename'], $tokens);
    }
    return $filename;
  }

}
