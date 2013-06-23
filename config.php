<?php

if(!isset($GLOBALS['IN_UXPANEL'])) {
	die("Access forbidden.");
}

$config = array();
include(basePath() . "/config_default.php");

if(file_exists(basePath() . "/config_local.php")) {
	include(basePath() . "/config_local.php");
}

?>
