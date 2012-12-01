<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/ghost.php");
include("../include/database.php");

if(isset($_SESSION['admin']) && isset($_REQUEST['id'])) {
	$account_id = $_REQUEST['id'];
	$message = "";
	
	if(isset($_REQUEST['message'])) {
		$message = htmlentities($_REQUEST['message']);
	}
	
	if(isset($_POST['action'])) {
		if($_POST['action'] == "add" && isset($_POST['name']) && isset($_POST['description']) && isset($_POST['type']) && isset($_POST['identifier']) && isset($_POST['due']) && isset($_POST['price'])) {
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
				} else {
					header("Location: account.php?id=$account_id&message=" . urlencode($result));
				}
			} else if($type == "database") {
				$result = databaseAddService($account_id, $_POST['name'], $_POST['description'], $_POST['identifier']);
				
				if(is_integer($result)) {
					setServiceParam($result, "due", $_POST['due']);
					setServiceParam($result, "price", $_POST['price']);
				} else {
					header("Location: account.php?id=$account_id&message=" . urlencode("Error occurred while setting up database."));
				}
			}
		} else if($_POST['action'] == "delete" && isset($_POST['delete_id'])) {
			removeService($_POST['delete_id']);
		}
		
		header("Location: account.php?id=$account_id");
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
