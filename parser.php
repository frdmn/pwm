<?php

/*
 * Configuration options
 */

$items_per_page = 10;

$log_debug = false;
$log_info = true;
$log_error = true;

/*
 * Functions, includes and initializations
 */

// Function to print stuff to stdout/stderr
function std($type, $msg) {
  global $log_debug, $log_info, $log_error;

  if (!$log_debug && $type == "debug") {
    return false;
  }

  if (!$log_info && $type == "info") {
    return false;
  }

  if (!$log_error && $type == "error") {
    return false;
  }

  if ($type == "error") {
    error_log("[".strtoupper($type)."] ".$msg);
  } else {
    echo "[".strtoupper($type)."] ".$msg."\n";
  }
}

// Include SimpleHTMLDOM class
require('simplehtmldom.php');

/*
 * Parse available pages
 */

// Initial request to parse pagination
$pagination_html = file_get_html('http://www.packal.org/workflow-list?sort_by=changed&sort_order=DESC&items_per_page='.$items_per_page);

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
  $page_html = file_get_html('http://www.packal.org/workflow-list?sort_by=changed&sort_order=DESC&items_per_page='.$items_per_page.'&page='.$page_current);
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
