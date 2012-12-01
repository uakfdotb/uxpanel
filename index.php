<?php

include("include/common.php");
include("config.php");
include("include/session.php");
include("include/dbconnect.php");

include("include/auth.php");

if(isset($_SESSION['account_id'])) {
	header("Location: panel/");
} else if(isset($_POST['email']) && isset($_POST['password'])) {
	$result = authAccount($_POST['email'], $_POST['password']);
	
	if($result === true) {
		header("Location: panel/");
	} else if($result === -1) {
		header("Location: index.php?message=" . urlencode("Login failed: too many failed login attempts. Please wait a few seconds before trying again."));
	} else if($result === -2) {
		header("Location: index.php?message=" . urlencode("Login failed: invalid email address or password."));
	} else {
		header("Location: index.php?message=" . urlencode("Unknown error occurred."));
	}
} else {
	$message = "";
	
	if(isset($_REQUEST['message'])) {
		$message = $_REQUEST['message'];
	}
	
	get_page("index", "main", array('message' => $message));
}

?>
