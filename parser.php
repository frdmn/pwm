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
  std("info","Available pages in pagination: ".intval($page_last+1));
  std("info","Workflows per page: ".$items_per_page);
}

/*
 * Parse workflows per page
 */

// For each page
while ($page_current <= $page_last) {
  // Report to stdout
  std("info","Starting to scrape page #".intval($page_current+1));
  // Download page source
  $page_html = file_get_html('http://www.packal.org/workflow-list?sort_by=changed&sort_order=DESC&items_per_page='.$items_per_page.'&page='.$page_current);
  // Store DOM
  $page_dom = $page_html->find('tbody tr td h4 a');

  // Search for workflows
  foreach ($page_dom as $workflow) {
    // Download workflow source
    $workflow_detail_html = file_get_html('http://www.packal.org'.$workflow->href);

    // Parse detail informations
    $workflow_detail_url = 'http://www.packal.org'.$workflow->href;
    $workflow_detail_title = $workflow_detail_html->find('#page-title', 0);
    $workflow_detail_version = $workflow_detail_html->find('.pane-node-field-version .pane-content', 0);
    $workflow_detail_bundleid = $workflow_detail_html->find('.pane-node-field-bundle-id .pane-content', 0);
    $workflow_detail_author = $workflow_detail_html->find('.pane-user-picture .pane-content td', 0);
    $workflow_detail_author_avatar = $workflow_detail_html->find('.user-picture a img', 0);
    $workflow_detail_logo = $workflow_detail_html->find('.field-icon a img', 0);
    $workflow_detail_description_short = $workflow_detail_html->find('.pane-node-field-short-description .field-short-description', 0);
    $workflow_detail_description_long = $workflow_detail_html->find('.field-body p', 0);
    $workflow_detail_categories = $workflow_detail_html->find('.field-categories a');

    // Store in array
    $workflow_object['name'] = trim($workflow_detail_title->plaintext);
    $workflow_object['url'] = trim($workflow_detail_url);
    $workflow_object['bundle-id'] = trim($workflow_detail_bundleid->plaintext);
    $workflow_object['version'] = trim($workflow_detail_version->plaintext);
    $workflow_object['author'] = trim($workflow_detail_author->plaintext);
    $workflow_object['author-avatar'] = trim($workflow_detail_author_avatar->src);
    $workflow_object['workflow-logo'] = trim($workflow_detail_logo->src);
    $workflow_object['description-short'] = trim($workflow_detail_description_short->plaintext);
    $workflow_object['description-long'] = trim($workflow_detail_description_long->plaintext);

    // Log to stdout
    std("info", "-> Parsing '".trim($workflow_detail_title->plaintext));
    std("debug", "=> URL: '".trim($workflow_detail_url)."'");
    std("debug", "=> Bundle ID: '".trim($workflow_detail_bundleid->plaintext)."'");
    std("debug", "=> Version: '".trim($workflow_detail_version->plaintext)."'");
    std("debug", "=> Author: '".trim($workflow_detail_author->plaintext)."'");
    std("debug", "=> Avatar: '".trim($workflow_detail_author_avatar->src)."'");
    std("debug", "=> Logo: '".trim($workflow_detail_logo->src)."'");
    std("debug", "=> Short description: '".trim($workflow_detail_description_short->plaintext)."'");
    std("debug", "=> Long description: '".trim($workflow_detail_description_long->plaintext)."'");

    // Add categories
    unset($workflow_object['categories']);
    foreach ($workflow_detail_categories as $workflow_detail_category) {
      std("debug", "==> Category: '".trim($workflow_detail_category->plaintext)."'");
      $workflow_object['categories'][] = trim($workflow_detail_category->plaintext);
    }

    // Add workflow item, to workflows
    $workflows[] = $workflow_object;
  }

  // Increment active page
  $page_current++;
}

// Encode as json
echo json_encode($workflows);

?>
