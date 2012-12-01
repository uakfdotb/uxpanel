<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/channel.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_SESSION['is_' . $_REQUEST['id'] . '_channel'])) {
	$service_id = $_REQUEST['id'];
	$message = "";
	
	if(isset($_POST['action'])) {
		if($_POST['action'] == "start") {
			$result = channelBotStart($service_id);
			
			if($result === true) {
				$message = "pychop instance started successfully.";
			} else {
				$message = $result;
			}
		} else if($_POST['action'] == "restart") {
			$result = channelBotRestart($service_id);
			
			if($result === true) {
				$message = "pychop instance restarted successfully.";
			} else {
				$message = $result;
			}
		} else if($_POST['action'] == "stop") {
			$result = channelBotStop($service_id);
			
			if($result === true) {
				$message = "pychop instance stopped successfully.";
			} else {
				$message = $result;
			}
		}
	}
	
	$status = channelGetStatus($service_id);
	get_page("status", "channel", array('service_id' => $service_id, 'status' => $status, 'message' => $message));
} else {
	header("Location: ../panel/");
}

?>
