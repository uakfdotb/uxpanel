<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/database.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_SESSION['is_' . $_REQUEST['id'] . '_database'])) {
	$games = databaseGetRunning($_REQUEST['id']);
	get_page("current", "database", array('service_id' => $_REQUEST['id'], 'games' => $games));
} else {
	header("Location: ../panel/");
}

?>
