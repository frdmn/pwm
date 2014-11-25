<?php

/*
 * Initalization
 */

$scriptdir=realpath(dirname(__FILE__));
include($scriptdir.'/../lib/config.php');

/*
 * Helper functions
 */

// Function to print stuff to stdout/stderr
function std($type, $msg) {
  global $log_debug, $log_info, $log_error;

  // If logging "debug" is unwanted, leave function
  if (!$log_debug && $type == "debug") {
    return false;
  }

  // If logging "info" is unwanted, leave function
  if (!$log_info && $type == "info") {
    return false;
  }

  // If logging "error" is unwanted, leave function
  if (!$log_error && $type == "error") {
    return false;
  }

  // Append log type as prefix
  if ($type == "error") {
    error_log("[".strtoupper($type)."] ".$msg);
    return true;
  } else {
    echo "[".strtoupper($type)."] ".$msg."\n";
    return true;
  }
}

// Function to check if a string contains a specific substring
function contains($pattern, $string){
  return strpos(strtolower($string), strtolower($pattern)) !== false;
}

/*
 * Alfred related
 */

// Function to parse location of Alfred.preferences
function getAlfredPreferencesLocation(){
  // Get possible "syncfolder"
  exec('/usr/libexec/PlistBuddy -c "Print syncfolder" ${HOME}/Library/Preferences/com.runningwithcrayons.Alfred-Preferences.plist 2> /dev/null', $retval, $exitcode);

  // If error code => no syncfolder set => use "Application Support"
  // Otherwise use the custom folder, returned by PlistBuddy
  if ($exitcode == 0) {
    $workflow_folder=$retval[0]."/Alfred.alfredpreferences/workflows";
  } else {
    $workflow_folder=$_SERVER['HOME']."/Library/Application Support/Alfred 2/Alfred.alfredpreferences/workflows";
  }

  // Replace tilde with actual home folder
  $workflow_folder=str_replace("~", $_SERVER['HOME'], $workflow_folder);

  return $workflow_folder;
}

/*
 * Workflow related
 */

// Function to list all locally installed workflows
function listInstalledWorkflows($location){
  // Scan for workflows and exclude hidden files
  $installed_workflows = array();
  $workflows = preg_grep('/^([^.])/', scandir($location));

  foreach($workflows as $workflow) {
    // Parse and store name and description
    $workflow_name=exec('/usr/libexec/PlistBuddy -c "Print name" "'.$location.'/'.$workflow.'/Info.plist" 2> /dev/null');
    $installed_workflows[] = $workflow_name;
  }

  return $installed_workflows;
}

// Function to display the description of a specific workflow
function getWorkflowDescription($workflow){
  global $cache_file;

  // Load and store cache
  $raw_cache=file_get_contents($cache_file);
  $json_cache=json_decode($raw_cache);

  // Iterate through available workflows
  foreach ($json_cache->workflows as $i => $cached_workflow) {
    // If workflow name contains search term, push to $result_matches
    if ($workflow == $cached_workflow->name) {
      return $cached_workflow->description_short;
    }
  }
}

// Function to check if a specific workflow is locally installed via Packal
function checkIfSpecificWorkflowIsInstalledViaPackal($workflow) {
  global $scriptdir, $cache_file;

  // Load and store cache
  $raw_cache=file_get_contents($cache_file);
  $json_cache=json_decode($raw_cache);

  // Create empty array to store cached Packal workflows in
  $cached_packal_workflows = array();

  // Iterate through cache
  foreach ($json_cache->workflows as $i => $cached_packal_workflow) {
    // ... and add to array
    $cached_packal_workflows[] = $cached_packal_workflow->name;
  }

  if (in_array($workflow, $cached_packal_workflows)) {
    return "✔";
  } else {
    return "✘";
  }
}

?>
