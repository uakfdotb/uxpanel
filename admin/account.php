<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/ghost.php");

if(isset($_SESSION['admin']) && isset($_REQUEST['id'])) {
	$account_id = $_REQUEST['id'];
	$message = "";
	
	if(isset($_REQUEST['message'])) {
		$message = htmlentities($_REQUEST['message']);
	}
	
	if(isset($_REQUEST['action'])) {
		if($_REQUEST['action'] == "add" && isset($_REQUEST['name']) && isset($_REQUEST['description']) && isset($_REQUEST['type']) && isset($_REQUEST['identifier']) && isset($_REQUEST['due']) && isset($_REQUEST['price'])) {
			$type = $_REQUEST['type'];
			
			if($type == "ghost") {
				$id3 = -1;
				
				if(isset($_REQUEST['id3']) && $_REQUEST['id3'] != "") {
					$id3 = $_REQUEST['id3'];
				}
				
				$result = ghostAddService($account_id, $_REQUEST['name'], $_REQUEST['description'], $_REQUEST['identifier'], $id3);
				
				if(is_integer($result)) {
					setServiceParam($result, "due", $_REQUEST['due']);
					setServiceParam($result, "price", $_REQUEST['price']);
				} else {
					header("Location: account.php?message=" . urlencode($result));
				}
			}
		}
		
		header("Location: account.php");
	}
	
	//account info
	$info = adminGetAccount($account_id);
	
	//services
	$services = getServices($account_id);
	$serviceExtra = getServiceExtra($account_id);
	
	//display
	get_page("account", "admin", array('id' => $account_id, 'info' => $info, 'services' => $services, 'serviceExtra' => $serviceExtra));
} else {
	header("Location: ./");
}

?>
