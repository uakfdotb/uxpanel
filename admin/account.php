<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");

if(isset($_SESSION['admin']) && isset($_REQUEST['id'])) {
	$account_id = $_REQUEST['id'];
	$message = "";
	
	if(isset($_REQUEST['message'])) {
		$message = htmlentities($_REQUEST['message']);
	}
	
	if(isset($_POST['action'])) {
		if($_POST['action'] == "add" && isset($_POST['name']) && isset($_POST['description']) && isset($_POST['type']) && isset($_POST['identifier']) && isset($_POST['due']) && isset($_POST['price'])) {
			include("../include/ghost.php");
			$type = $_POST['type'];
			
			if($type == "ghost") {
				$id3 = -1;
				
				if(isset($_POST['id3']) && $_POST['id3'] != "") {
					$id3 = $_POST['id3'];
				}
				
				$result = ghostAddService($account_id, $_POST['name'], $_POST['description'], $_POST['identifier'], $id3);
				
				if(is_integer($result)) {
					setServiceParam($result, "due", $_POST['due']);
					setServiceParam($result, "price", $_POST['price']);
					$message = "GHost service has been setup successfully. Note that the default.cfg database settings are not automatically configured and must be set via the service page.";
				} else {
					header("Location: account.php?id=$account_id&message=" . urlencode($result));
				}
			} else if($type == "channel") {
				include("../include/channel.php");
				$result = channelAddService($account_id, $_POST['name'], $_POST['description'], $_POST['identifier']);
				
				if(is_integer($result)) {
					setServiceParam($result, "due", $_POST['due']);
					setServiceParam($result, "price", $_POST['price']);
					$message = "Channel bot service has been setup successfully. Note that the default.cfg database settings are not automatically configured and must be set via the service page.";
				} else {
					header("Location: account.php?id=$account_id&message=" . urlencode($result));
				}
			} else if($type == "minecraft") {
				include("../include/minecraft.php");
				$result = minecraftAddService($account_id, $_POST['name'], $_POST['description'], $_POST['identifier']);
				
				if(is_integer($result)) {
					setServiceParam($result, "due", $_POST['due']);
					setServiceParam($result, "price", $_POST['price']);
					$message = "Minecraft service has been setup successfully.";
				} else {
					header("Location: account.php?id=$account_id&message=" . urlencode($result));
				}
			} else if($type == "garena") {
				include("../include/garena.php");
				$result = garenaAddService($account_id, $_POST['name'], $_POST['description'], $_POST['identifier']);
				
				if(is_integer($result)) {
					setServiceParam($result, "due", $_POST['due']);
					setServiceParam($result, "price", $_POST['price']);
					$message = "Garena service has been setup successfully.";
				} else {
					header("Location: account.php?id=$account_id&message=" . urlencode($result));
				}
			} else if($type == "database") {
				include("../include/database.php");
				$result = databaseAddService($account_id, $_POST['name'], $_POST['description'], $_POST['identifier']);
				
				if(is_integer($result)) {
					setServiceParam($result, "due", $_POST['due']);
					setServiceParam($result, "price", $_POST['price']);
					$message = "Database service has been setup successfully. Note that the database settings and tables are not automatically configured and created. Create the database manually, set the database settings by clicking on the service and setting parameters, and then setup the tables there as well.";
				} else {
					header("Location: account.php?id=$account_id&message=" . urlencode("Error occurred while setting up database."));
				}
			}
		} else if($_POST['action'] == "delete" && isset($_POST['delete_id'])) {
			removeService($_POST['delete_id']);
			$message = "The service has been removed. Note that no service-related files or databases have been erased.";
		}
		
		if(!isset($_SESSION['noredirect'])) {
			header("Location: account.php?id=$account_id&message=" . urlencode($message));
		}
	}
	
	//account info
	$info = adminGetAccount($account_id);
	
	//services
	$services = getServices($account_id);
	$serviceExtra = getServiceExtra($services);
	
	//display
	get_page("account", "admin", array('id' => $account_id, 'info' => $info, 'services' => $services, 'serviceExtra' => $serviceExtra, 'message' => $message));
} else {
	header("Location: ./");
}

?>
