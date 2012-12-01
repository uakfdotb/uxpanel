<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/ghost.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_SESSION['is_' . $_REQUEST['id'] . '_ghost'])) {
	$log = ghostGetLog($_REQUEST['id']);
	get_page("log", "ghost", array('service_id' => $_REQUEST['id'], 'log' => $log));
} else {
	header("Location: ../panel/");
}

?>
