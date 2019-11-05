<?php

/**
 * @file
 * Theme related functions for processing our output style plugins.
 *
 * Views bug: http://drupal.org/node/593336
 */

/**
 * Theme a status message
 */
function theme_excel_reports_message($var) {
  return '<div class="messages status ' . $var['type'] . '">' . $var['message'] . '</div>';
}

/**
 * Theme a feed link.
 *
 * This theme function uses the theme pattern system to allow it to be
 * overidden in a more specific manner. The options for overiding this include
 * providing per display id; per type; per display id and per type.
 *
 * e.g.
 * For the view "export_test" with the display "page_1" and the type "csv" you
 * would have the following options.
 *   excel_reports_feed_icon__export_test__page_1
 *   excel_reports_feed_icon__page_1
 *   excel_reports_feed_icon
 *
 * @ingroup themeable
 */
function theme_excel_reports_feed_icon($variables) {
  extract($variables, EXTR_SKIP);
  $url_options = array('html' => TRUE);
  if ($query) {
    $url_options['query'] = $query;
  }
  $image = theme('image', array('path' => $image_path, 'alt' => $text, 'title' => $text));
  return l($image, $url, $url_options);
}

/**
 * Theme callback for the export complete page.
 *
 * @param $file
 *  Link to output file
 */
function theme_excel_reports_complete_page($variables) {
  extract($variables, EXTR_SKIP);
  drupal_set_title(t('Excel report created successfully'));
  drupal_add_html_head(array('#tag' => 'meta', '#attributes' => array('http-equiv' => "Refresh", 'content' => '3;url=' . $file)), 'excel_reports_download');
  $output = '<p>' . t('Your export has been created. View/download the file <a href="@link">here</a> (will automatically download in 3 seconds.)', array('@link' => $file)) . '</p>';

  if (!empty($return_url)) {
    $output .= '<p>' . l(t('Return to previous page'), $return_url) . '</p>';
  }
  return $output;
}
