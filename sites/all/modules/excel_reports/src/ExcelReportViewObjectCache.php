<?php

namespace Drupal\excel_reports;

class ExcelReportViewObjectCache {

  /**
   * Store a view in the object cache.
   */
  public static function store($export_id, $view) {
    // Store a clean copy of the view.
    $_view = $view->clone_view();

    ExcelReportViewObjectCache::clear($export_id);
    $record = array(
      'eid' => $export_id,
      'data' => $_view,
      'updated' => REQUEST_TIME,
    );
    drupal_write_record('excel_reports_object_cache', $record);
  }

  /**
   * Retrieve a view from the object cache.
   */
  public static function retrieve($export_id) {
    views_include('view');
    $data = db_query("SELECT * FROM {excel_reports_object_cache} WHERE eid = :eid", array(':eid' => $export_id))->fetch();
    if ($data) {
      $view = unserialize($data->data);
    }
    return $view;
  }

  /**
   * Clear a view from the object cache.
   *
   * @param $export_id
   *   An export ID or an array of export IDs to clear from the object cache.
   */
  public static function clear($export_id) {
    if (is_array($export_id)) {
      db_delete('excel_reports_object_cache')
          ->condition('eid', $export_id, 'IN')
          ->execute();
    }
    else {
      db_delete('excel_reports_object_cache')
          ->condition('eid', $export_id)
          ->execute();
    }
  }

}
