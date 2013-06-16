<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/garena.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_SESSION['is_' . $_REQUEST['id'] . '_garena'])) {
	$connection_id = 0;
	$connections = false;
	$gconfig = false;
	$message = "";

	if(isset($_REQUEST['message'])) {
		$message = $_REQUEST['message'];
	}
	
	if(isset($_POST['action'])) {
		if($_POST['action'] == "add" && isset($_POST['room'])) {
			$result = garenaAddConnection($_REQUEST['id'], $_POST['room']);
			
			if($result === true) {
				$connections = garenaGetConnection($_REQUEST['id']);
				//last key of connections is highest id and should be our newly inserted one
				$connection_keys = array_keys($connections);
				
				if(count($connection_keys) > 0) {
					$connection_id = $connection_keys[count($connection_keys) - 1];
				}
			} else {
				$message = $result;
			}
		} else if($_POST['action'] == "remove" && isset($_POST['connection'])) {
			$connection_id = $_POST['connection'];
			garenaRemoveConnection($_REQUEST['id'], $connection_id);
		} else if($_POST['action'] == "update" && isset($_POST['connection'])) {
			//get parameters for this case
			$connection_id = $_POST['connection'];
			$array = garenaGetConfigFromRequest($garenaConnectionParameters, $_REQUEST);
			garenaReconfigureConnection($_REQUEST['id'], $connection_id, $array);
		}
		
		if(!isset($_SESSION['noredirect'])) {
			header("Location: config_connection.php?id=" . $_REQUEST['id'] . "&connection=$connection_id&message=" . urlencode($message));
			return;
		}
	}
	
	if($connections === false) {
		$connections = garenaGetConnection($_REQUEST['id']);
	}
	
	if(isset($_REQUEST['connection']) && isset($connections[$_REQUEST['connection']])) {
		$connection_id = $_REQUEST['connection'];
	}
	
	if($connection_id != 0) {
		$gconfig = garenaGetConnectionConfiguration($_REQUEST['id'], $connection_id);
	}
	
	get_page("config_connection", "garena", array('service_id' => $_REQUEST['id'], 'gconfig' => $gconfig, 'parameters' => $garenaConnectionParameters, 'connections' => $connections, 'connection_id' => $connection_id, 'message' => $message));
} else {
	header("Location: ../panel/");
}

?>
