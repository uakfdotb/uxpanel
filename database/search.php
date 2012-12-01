<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/database.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_SESSION['is_' . $_REQUEST['id'] . '_database'])) {
	$username = false;
	$result = false;
	$realms = databaseGetRealms($_REQUEST['id']);
	
	if(isset($_REQUEST['username']) && isset($_REQUEST['realm'])) {
		$username = $_REQUEST['username'];
		$result = databaseSearchUser($_REQUEST['id'], $username, $_REQUEST['realm']);
	}

	get_page("search", "database", array('service_id' => $_REQUEST['id'], 'result' => $result, 'username' => $username, 'realms' => $realms));
} else {
	header("Location: ../panel/");
}

?>
