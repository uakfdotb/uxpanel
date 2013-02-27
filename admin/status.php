<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/status.php");

if(isset($_SESSION['admin'])) {
	$status = statusOverview();
	$overdue = statusDue(true);
	$duesoon = statusDue();
	
	//display
	get_page("status", "admin", array('status' => $status, 'overdue' => $overdue, 'duesoon' => $duesoon));
} else {
	header("Location: ./");
}

?>
