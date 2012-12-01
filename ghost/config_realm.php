<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/ghost.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_SESSION['is_' . $_REQUEST['id'] . '_ghost'])) {
	$bnet_id = 0;
	$bnets = false;
	$bconfig = false;
	$message = "";

	if(isset($_REQUEST['message'])) {
		$message = $_REQUEST['message'];
	}
	
	if(isset($_POST['action'])) {
		if($_POST['action'] == "add" && isset($_POST['server'])) {
			$result = ghostAddBnet($_REQUEST['id'], $_POST['server']);
			
			if($result === true) {
				$bnets = ghostGetBnet($_REQUEST['id']);
				//last key of bnets is highest id and should be our newly inserted one
				$bnet_keys = array_keys($bnets);
				
				if(count($bnet_keys) > 0) {
					$bnet_id = $bnet_keys[count($bnet_keys) - 1];
				}
			} else {
				$message = $result;
			}
		} else if($_POST['action'] == "remove" && isset($_POST['bnet'])) {
			$bnet_id = $_POST['bnet'];
			ghostRemoveBnet($_REQUEST['id'], $bnet_id);
		} else if($_POST['action'] == "update" && isset($_POST['bnet'])) {
			//get parameters for this case
			$bnet_id = $_POST['bnet'];
			$array = ghostGetConfigFromRequest($bnetParameters, $_REQUEST);
			ghostReconfigureBnet($_REQUEST['id'], $bnet_id, $array);
		}
		
		if(!isset($_SESSION['noredirect'])) {
			header("Location: config_realm.php?id=" . $_REQUEST['id'] . "&bnet=$bnet_id&message=" . urlencode($message));
			return;
		}
	}
	
	if($bnets === false) {
		$bnets = ghostGetBnet($_REQUEST['id']);
	}
	
	if(isset($_REQUEST['bnet']) && isset($bnets[$_REQUEST['bnet']]) && isset($bnets[$_REQUEST['bnet']])) {
		$bnet_id = $_REQUEST['bnet'];
	}
	
	if($bnet_id != 0) {
		$bconfig = ghostGetBnetConfiguration($_REQUEST['id'], $bnet_id);
	}
	
	get_page("config_realm", "ghost", array('service_id' => $_REQUEST['id'], 'bconfig' => $bconfig, 'parameters' => $bnetParameters, 'bnets' => $bnets, 'bnet_id' => $bnet_id, 'message' => $message));
} else {
	header("Location: ../panel/");
}

?>
