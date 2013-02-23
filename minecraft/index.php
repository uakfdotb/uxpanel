<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/minecraft.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_SESSION['is_' . $_REQUEST['id'] . '_minecraft'])) {
	$service_id = $_REQUEST['id'];
	$message = "";
	
	if(isset($_REQUEST['message'])) {
		$message = $_REQUEST['message'];
	}
	
	if(isset($_POST['action'])) {
		if($_POST['action'] == "start") {
			$result = minecraftStart($service_id);
			
			if($result === true) {
				$message = "Minecraft instance started successfully.";
			} else {
				$message = $result;
			}
		} else if($_POST['action'] == "restart") {
			$result = minecraftRestart($service_id);
			
			if($result === true) {
				$message = "Minecraft instance restarted successfully.";
			} else {
				$message = $result;
			}
		} else if($_POST['action'] == "stop") {
			$result = minecraftStop($service_id);
			
			if($result === true) {
				$message = "Minecraft instance stopped successfully.";
			} else {
				$message = $result;
			}
		}
		
		if(!isset($_SESSION['noredirect'])) {
			header("Location: index.php?id=" . $service_id . "&message=" . urlencode($message));
		}
	}
	
	$status = minecraftGetStatus($service_id);
	$botStatus = getServiceParam($service_id, "pid") != 0 ? "Online" : "Offline";
	$resources = minecraftResources($service_id);
	get_page("status", "minecraft", array('service_id' => $service_id, 'status' => $status, 'message' => $message, 'botStatus' => $botStatus, 'resources' => $resources));
} else {
	header("Location: ../panel/");
}

?>
