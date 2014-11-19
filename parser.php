<?php

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

$pagination_regex_pattern='/(\d+) of (\d+)/';
$pagination_regex_result = preg_match($pagination_regex_pattern, $pagination_dom[0]->plaintext, $pagination_regex_match);

if ($pagination_regex_match[1] && $pagination_regex_match[2]) {
  $page_current = $pagination_regex_match[1]-1;
  $page_last = $pagination_regex_match[2]-1;
}

/*
 * Parse workflows per page
 */

while ($page_current <= $page_last) {
  $page_html = file_get_html('http://www.packal.org/workflow-list?sort_by=changed&sort_order=DESC&items_per_page=100&page='.$page_current);
  $workflow_dom = $page_html->find('tbody tr td h4 a');

  foreach ($workflow_dom as $workflow) {
    var_dump($workflow->plaintext);
  }

  $page_current++;
}

// Encode as json
// echo json_encode($dongers);

?>
