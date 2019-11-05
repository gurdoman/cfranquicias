<?php

namespace Drupal\excel_reports;

/**
 * Classe to handle the PHP Excel library to create XLSX files inside Drupal
 */
class ExcelReport {

  protected $library, $objWriter, $filename, $filepath, $sheets;

  /**
   *
   * @var PHPExcel
   */
  protected $objPHPExcel;

  public function __construct($filepath) {

    module_load_include('inc', 'phpexcel', 'phpexcel');
    $this->library = libraries_load('PHPExcel');

    if (!$this->library['loaded']) {
      throw new Exception('PHPExcel library was not loaded correctly');
    }

    if (file_exists($filepath)) {
      $this->objPHPExcel = \PHPExcel_IOFactory::load($filepath);
      $this->filepath = $filepath;
    }
    else {
      $this->objPHPExcel = new \PHPExcel();
      $this->objPHPExcel->getProperties()
          ->setCreator(variable_get('site_name'));
      $this->filepath = $filepath;
    }
  }

  public function setFilepath($filepath) {
    $this->filepath = $filepath;
  }

  public static function pageCallback() {
    $class_name = get_class($this);
    $report = new $class_name();
    $report->download();
  }

  /**
   * Here is the place to populate the $this->objPHPExcel object, inserting data
   * into cells and columns
   */
  public function process() {
    throw new Exception(t('ExcelReport class needs to be extended so that you can implement and use the process() method'));
  }

  /**
   *
   * @param array $data_array
   * @param int $start_cell_column Begins on '0'
   * @param int $start_cell_row Begins on '1'
   */
  public function createTableFromArray(array $data_array, $start_cell_column = 0, $start_cell_row = 0) {
    for ($i = 0, $rows_count = count($data_array); $i < $rows_count; $i++) {
      $values = array_values($data_array[$i]);
      $columns_count = count($values);
      for ($j = 0; $j < $columns_count; $j++) {
        $value = isset($values[$j]) ? $values[$j] : '';
        $this->objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(($start_cell_column + $j), ($start_cell_row + $i), $value);
      }
    }
  }

  /**
   * @deprecated
   */
  public function download() {
    $this->process();
    header('Content-Type: application/vnd.ms-excel');
    header('Content-Disposition: attachment;filename="' . $this->filename . '.xlsx"');
    header('Cache-Control: max-age=0');
    $this->objWriter = \PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
    $this->objWriter->save('php://output');
    drupal_exit();
  }

  public function savePermanentFile() {
    $this->objWriter = \PHPExcel_IOFactory::createWriter($this->objPHPExcel, 'Excel2007');
    $this->objWriter->save($this->filepath);
  }

  public function resetSpreadsheets() {
    $this->objPHPExcel->setActiveSheetIndex(0);
  }

  public function getHighestRow() {
    return $this->objPHPExcel->getActiveSheet()->getHighestRow();
  }

}
