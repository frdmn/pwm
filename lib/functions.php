<?php

$scriptdir=realpath(dirname(__FILE__));
include($scriptdir.'/../lib/config.php');

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
