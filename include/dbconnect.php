<?php

if(!isset($GLOBALS['IN_UXPANEL'])) {
	die("Access forbidden.");
}

//if slave, use persistent connections in case we are remote and making connection takes a while
if($config['slave_enabled']) {
	$prefix = "p:"; 
} else {
	$prefix = "";
}

$db = new mysqli($prefix . $config['db_hostname'], $config['db_username'], $config['db_password'], $config['db_name']);

if($db->connect_error) {
	die("Could not connect to MySQL database. Check config.php.<br />" . $db->connect_error);
}

?>
