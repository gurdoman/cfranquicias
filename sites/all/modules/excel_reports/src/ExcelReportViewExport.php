<?php

namespace Drupal\excel_reports;

/**
 * Classe abstrata para trabalhar com a geração de arquivos em Excel
 */
class ExcelReportViewExport {

  /**
   * Save a new export into the database.
   */
  public static function save($view_name, $view_display_id, $file) {
    // Insert new row into exports table
    $record = (object) array(
          'view_name' => $view_name,
          'view_display_id' => $view_display_id,
          'time_stamp' => REQUEST_TIME,
          'fid' => $file,
          'batch_state' => excel_reports_HEADER,
          'sandbox' => array(),
    );
    drupal_write_record('excel_reports', $record);
    return $record;
  }

  /**
   * Update an export row in the database
   */
  public static function update($state) {
    // Note, drupal_write_record handles serializing
    // the sandbox field as per our schema definition
    drupal_write_record('excel_reports', $state, 'eid');
  }

  /**
   * Get the information about a previous export.
   */
  public static function get($export_id) {
    $object = db_query("SELECT * FROM {excel_reports} WHERE eid = :eid", array(':eid' => (int) $export_id))->fetch();
    if ($object) {
      $object->sandbox = unserialize($object->sandbox);
    }
    return $object;
  }

  /**
   * Remove the information about an export.
   */
  public static function clear($export_id) {
    db_delete('excel_reports')
        ->condition('eid', $export_id)
        ->execute();
    \Drupal\excel_reports\ExcelReportViewObjectCache::clear($export_id);
  }

}
