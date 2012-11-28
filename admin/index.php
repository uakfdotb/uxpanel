<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/auth.php");

if(isset($_SESSION['admin'])) {
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == "logout") {
		session_unset();
		header("Location: ./");
	} else {
		get_page("index", "admin", array());
	}
} else if(isset($_REQUEST['username']) && isset($_REQUEST['password'])) {
	if(authAdmin($_REQUEST['username'], $_REQUEST['password'])) {
		$_SESSION['admin'] = $_REQUEST['username'];
		header("Location: index.php");
	} else {
		header("Location: index.php?message=" . urlencode("Login failed."));
	}
} else {
	$message = "";
	
	if(isset($_REQUEST['message'])) {
		$message = $_REQUEST['message'];
	}
	
	get_page("index_login", "admin", array());
}

?>
