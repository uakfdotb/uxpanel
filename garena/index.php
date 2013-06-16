<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/garena.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_SESSION['is_' . $_REQUEST['id'] . '_garena'])) {
	$service_id = $_REQUEST['id'];
	$message = "";
	
	if(isset($_REQUEST['message'])) {
		$message = $_REQUEST['message'];
	}
	
	if(isset($_POST['action'])) {
		if($_POST['action'] == "start") {
			$result = garenaStart($service_id);
			
			if($result === true) {
				$message = "Garena connection started successfully.";
			} else {
				$message = $result;
			}
		} else if($_POST['action'] == "restart") {
			$result = garenaRestart($service_id);
			
			if($result === true) {
				$message = "Garena connection restarted successfully.";
			} else {
				$message = $result;
			}
		} else if($_POST['action'] == "stop") {
			$result = garenaStop($service_id);
			
			if($result === true) {
				$message = "Garena connection stopped successfully.";
			} else {
				$message = $result;
			}
		}
		
		if(!isset($_SESSION['noredirect'])) {
			header("Location: index.php?id=" . $service_id . "&message=" . urlencode($message));
		}
	}
	
	$status = garenaGetStatus($service_id);
	$botStatus = getServiceParam($service_id, "pid") != 0 ? "Online" : "Offline";
	get_page("status", "garena", array('service_id' => $service_id, 'status' => $status, 'message' => $message, 'botStatus' => $botStatus));
} else {
	header("Location: ../panel/");
}

?>
