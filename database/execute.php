<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/database.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_SESSION['is_' . $_REQUEST['id'] . '_database'])) {
	$message = "";
	
	if(isset($_REQUEST['message'])) {
		$message = $_REQUEST['message'];
	}
	
	if(isset($_POST['botid']) && isset($_POST['command'])) {
		databaseExecuteCommand($_REQUEST['id'], $_POST['botid'], $_POST['command']);
		
		if(!isset($_SESSION['noredirect'])) {
			header("Location: execute.php?id=" . $_REQUEST['id'] . "&message=" . urlencode("Command submitted for execution."));
		}
	}

	get_page("execute", "database", array('service_id' => $_REQUEST['id'], 'message' => $message));
} else {
	header("Location: ../panel/");
}

?>
