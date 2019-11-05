Excel Reports
=================

Introduction
------------

This module is designed to provide a way to export large amounts of data from
views into XLSX files and make it easier to create complex spreadsheets 
programmatically by extending ExcelReport class. It provides a display plugin 
that can rendered progressively in a batch.

Using the "Excel Reports" module
------------------------------------

1. Add a new "Data export" display to your view.
2. Configure the options (such as name, etc.). You can go back and do this at 
   any time by clicking the gear icon next to the style plugin you just selected.
3. Give it a path in the settings such as "path/to/view/xls".
4. Optionally, you can choose to attach this to another of your displays by
   updating the "Attach to:" option in feed settings.

Advanced usage
--------------

This module also exposes a drush command that can execute the view and save its
results to a file.

  drush excel-reports [view-name] [display-id] [output-file]

and another one to execute programmatically created reports:

  drush xlsr ClassExtendingExcelReport

History
-------

This module was a merge of the "Views Data Export" module and the Excel Reports,
an internal module focused on create advanced Excel spreadsheets with the 
PHP Excel (https://github.com/PHPOffice/PHPExcel) library.