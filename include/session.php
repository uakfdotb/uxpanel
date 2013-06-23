<?php

if(!isset($GLOBALS['IN_UXPANEL'])) {
	die("Access forbidden.");
}

session_start();

if (!isset($_SESSION['initiated']) || !isset($_SESSION['active']) || time() - $_SESSION['active'] > 10800) {
	session_unset();
	session_regenerate_id();
	$_SESSION['initiated'] = true;
}

//validate user agent
if(isset($_SERVER['HTTP_USER_AGENT'])) {
	if(isset($_SESSION['HTTP_USER_AGENT'])) {
		if ($_SESSION['HTTP_USER_AGENT'] != md5($_SERVER['HTTP_USER_AGENT'])) {
			session_unset();
		}
	} else {
		$_SESSION['HTTP_USER_AGENT'] = md5($_SERVER['HTTP_USER_AGENT']);
	}
}

//validate they are accessing this site, in case multiple are hosted
if(isset($_SESSION['site_name'])) {
	if($_SESSION['site_name'] != $config['site_name']) {
		session_unset();
	}
} else {
	$_SESSION['site_name'] = $config['site_name'];
}

$_SESSION['active'] = time();

//CSRF guard library
include(includePath() . "/csrfguard.php");

//handle noredirect option
if(isset($_REQUEST['noredirect'])) {
	if($_REQUEST['noredirect'] === "false") {
		unset($_SESSION['noredirect']);
	} else if($_REQUEST['noredirect'] === "true") {
		$_SESSION['noredirect'] = true;
	}
}

//redirect slave if needed
$script_name = basename($_SERVER["SCRIPT_FILENAME"]);
$script_directory = basename(substr($_SERVER['SCRIPT_FILENAME'], 0, strrpos($_SERVER['SCRIPT_FILENAME'], '/')));

if($config['slave_enabled'] && ($script_directory != "database" && $script_directory != "ghost" && $script_directory != "channel" && $script_directory != "minecraft" && $script_directory != "garena" && $script_directory != "admin") && ($script_name != "remote_login.php" && $script_name != "service_redirect.php")) {
	header("Location: " . $config['slave_master']);
}

?>
