<?php

/*
 * Functions, includes and initializations
 */

// Function to print stuff to stdout/stderr
function std($type, $msg) {
  if ($type == "error") {
    error_log("[".strtoupper($type)."] ".$msg);
  } else {
    echo "[".strtoupper($type)."] ".$msg."\n";
  }
}

// Include SimpleHTMLDOM class
require('simplehtmldom.php');

// Init empty array
$workflows = array();

/*
 * Parse available pages
 */

// Initial request to parse pagination
$pagination_html = file_get_html('http://www.packal.org/workflow-list?sort_by=changed&sort_order=DESC&items_per_page=100');

// Store DOM in variable
$pagination_dom = $pagination_html->find('li.pager-current');

// Regex to parse pagination (current and last page)
$pagination_regex_pattern='/(\d+) of (\d+)/';
$pagination_regex_result = preg_match($pagination_regex_pattern, $pagination_dom[0]->plaintext, $pagination_regex_match);

// Store in variables if we have a match
if ($pagination_regex_match[1] && $pagination_regex_match[2]) {
  $page_current = $pagination_regex_match[1]-1;
  $page_last = $pagination_regex_match[2]-1;
  std("info","Found pagination: '".$page_current." of ".$page_last);
}

/*
 * Parse workflows per page
 */

// For each page
while ($page_current <= $page_last) {
  // Download page source
  $page_html = file_get_html('http://www.packal.org/workflow-list?sort_by=changed&sort_order=DESC&items_per_page=100&page='.$page_current);
  // Store DOM
  $page_dom = $page_html->find('tbody tr td h4 a');

  // Search for workflows
  foreach ($page_dom as $workflow) {
    var_dump($workflow->href);
  }

  // Increment active page
  $page_current++;
}

// Encode as json
// echo json_encode($dongers);

?>
