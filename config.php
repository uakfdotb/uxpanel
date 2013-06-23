<?php

if(!isset($GLOBALS['IN_UXPANEL'])) {
	die("Access forbidden.");
}

$config = array();

if(file_exists(basePath() . "/config_local.php")) {
	include(basePath() . "/config_local.php");
}

//fill in any missing configuration options with the defaults
include(basePath() . "/config_default.php");

?>
