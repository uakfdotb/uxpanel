<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/ghost.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_SESSION['is_' . $_REQUEST['id'] . '_ghost'])) {
	$service_id = $_REQUEST['id'];
	$status = ghostGetStatus($service_id);
	get_page("status", "ghost", array('service_id' => $service_id, 'status' => $status));
} else {
	header("Location: ../panel/");
}

?>
