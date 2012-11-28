<?php

$config = array();
include(basePath() . "/config_default.php");

if(file_exists(basePath() . "/config_local.php")) {
	include(basePath() . "/config_local.php");
}

?>
