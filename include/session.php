<?php
session_start();

if (!isset($_SESSION['initiated'])) {
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

?>
