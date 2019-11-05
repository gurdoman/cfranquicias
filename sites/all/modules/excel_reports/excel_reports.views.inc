<?php

/**
 * @file
 * Views include file with views hooks.
 */

/**
 * Implementation of hook_views_plugins().
 */
function excel_reports_views_plugins() {
  $path = drupal_get_path('module', 'excel_reports');

  $style_defaults = array(
    'parent' => 'excel_reports',
    'theme' => 'excel_reports',
    'theme path' => $path,
    'theme file' => 'excel_reports.theme.inc',
    'uses row plugin' => FALSE,
    'uses fields' => TRUE,
    'uses options' => TRUE,
    'type' => 'excel_reports',
  );

  return array(
    'display' => array(
      'excel_reports' => array(
        'title' => t('Excel reports'),
        'help' => t('Export the view results as an XLSX file. Can handle very large result sets.'),
        'handler' => '\Drupal\excel_reports\ViewsPlugins\DisplayExport',
        'parent' => 'feed',
        'uses hook menu' => TRUE,
        'use ajax' => FALSE,
        'use pager' => FALSE,
        'accept attachments' => FALSE,
        'admin' => t('Excel reports'),
        'help topic' => 'display-excel-reports',
      ),
    ),
    'style' => array(
      'excel_reports' => array(
    'title' => t('Excel file'),
    'help' => t('Display the view as a comma separated list.'),
    'handler' => '\Drupal\excel_reports\ViewsPlugins\StyleExport',
    // Views Data Export element that will be used to set additional headers when serving the feed.
    'export headers' => array('Content-type' => 'application/vnd.ms-excel; charset=utf-8'),
    // Views Data Export element mostly used for creating some additional classes and template names.
    'export feed type' => 'xlsx',
    'export feed text' => 'XLSX',
    'export feed file' => '%view.xlsx',
    'export feed icon' => $path . '/images/xls.png',
    'additional themes' => array(
      'excel_reports_header' => 'style',
      'excel_reports_body' => 'style',
    ),
    'additional themes base' => 'excel_reports',
      ) + $style_defaults,
    ),
  );
}
