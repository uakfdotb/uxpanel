<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");

if(isset($_SESSION['admin']) && isset($_REQUEST['id'])) {
	$service_id = $_REQUEST['id'];
	$message = "";
	
	if(isset($_REQUEST['message'])) {
		$message = htmlentities($_REQUEST['message']);
	}
	
	if(isset($_POST['action'])) {
		if($_POST['action'] == "set" && isset($_POST['k']) && isset($_POST['v'])) {
			$delete = isset($_POST['delete']) && $_POST['delete'] == "delete";
			
			if($delete) {
				setServiceParam($service_id, $_POST['k'], false);
			} else {
				setServiceParam($service_id, $_POST['k'], $_POST['v']);
			}
		} else if($_POST['action'] == "setup") {
			include("../include/database.php");
			$result = databaseSetup($_REQUEST['id']);
			
			if($result !== true) {
				$message = $result;
			} else {
				$message = "Database setup successfully.";
			}
		} else if($_POST['action'] == "ghostdb" && isset($_POST['db_id'])) {
			include("../include/database.php");
			include("../include/ghost.php");
			$db_settings = databaseSettings($_POST['db_id']);
			
			if($db_settings !== false) {
				ghostSetDatabase($_REQUEST['id'], $db_settings);
				$message = "Database settings should be set properly.";
			} else {
				$message = "Failed to find database settings for the specified database service.";
			}
		} else if($_POST['action'] == "channeldb" && isset($_POST['db_id'])) {
			include("../include/database.php");
			include("../include/channel.php");
			$db_settings = databaseSettings($_POST['db_id']);
			
			if($db_settings !== false) {
				channelSetDatabase($_REQUEST['id'], $db_settings);
				$message = "Database settings should be set properly.";
			} else {
				$message = "Failed to find database settings for the specified database service.";
			}
		}
		
		header("Location: service.php?id=$service_id&message=" . urlencode($message));
	}
	
	$service = getService($service_id);
	$parameters = getServiceParams($service_id);
	
	get_page("service", "admin", array('id' => $service_id, 'service' => $service, 'parameters' => $parameters, 'message' => $message));
} else {
	header("Location: ./");
}

?>
