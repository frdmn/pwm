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

// Function to check if a specific workflow is locally installed
function checkIfWorkflowIsInstalled($workflow) {
  global $scriptdir;

  $listcmd="php ".$scriptdir."/../commands/list";
  $grepcmd=$listcmd." | grep --ignore-case \"".$workflow."\"";

  exec($grepcmd, $retval, $exitcode);
  if ($exitcode == 0) {
    return "✔";
  } else {
    return "✘";
  }
}

?>
