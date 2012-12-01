<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/database.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_SESSION['is_' . $_REQUEST['id'] . '_database'])) {
	if(isset($_POST['action'])) {
		if($_POST['action'] == "delete" && isset($_POST['delete_id'])) {
			databaseDeleteAdmin($_POST['id'], $_POST['delete_id']);
		} else if($_REQUEST['action'] == "add" && isset($_POST['name']) && isset($_POST['server'])) {
			databaseAddAdmin($_POST['id'], $_POST['name'], $_POST['server']);
		}
		
		if(!isset($_SESSION['noredirect'])) {
			header("Location: admins.php?id=" . $_REQUEST['id']);
		}
	}

	$admins = databaseGetAdmins($_REQUEST['id']);
	get_page("admins", "database", array('service_id' => $_REQUEST['id'], 'admins' => $admins));
} else {
	header("Location: ../panel/");
}

?>
