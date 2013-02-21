<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/minecraft.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_SESSION['is_' . $_REQUEST['id'] . '_minecraft'])) {
	$message = "";

	if(isset($_REQUEST['message'])) {
		$message = $_REQUEST['message'];
	}
	
	if(isset($_POST['action'])) {
		if($_POST['action'] == "link" && isset($_POST['filename'])) {
			$result = minecraftServerLink($_REQUEST['id'], $_POST['filename']);
			
			if($result === true) {
				$message = "Plugin linked successfully with repository.";
			} else {
				$message = $result;
			}
		} else if($_POST['action'] == "upload" && isset($_POST['upload'])) {
			$result = minecraftServerUpload($_REQUEST['id'], $_FILES);
			
			if($result === true) {
				$message = "Plugin uploaded successfully.";
			} else {
				$message = $result;
			}
		} else if($_POST['action'] == "remove" && isset($_POST['filename'])) {
			minecraftServerDelete($_REQUEST['id'], $_POST['filename']);
			$message = "Plugin deleted.";
		}
		
		if(!isset($_SESSION['noredirect'])) {
			header("Location: plugin.php?id=" . $_REQUEST['id'] . "&message=" . urlencode($message));
			return;
		}
	}
	
	# get the current repository and user files
	$repositoryPlugins = minecraftServerList($_REQUEST['id'], "repository");
	$userPlugins = minecraftServerList($_REQUEST['id'], "plugins");
	
	get_page("plugin", "minecraft", array('service_id' => $_REQUEST['id'], 'repositoryPlugins' => $repositoryPlugins, 'userPlugins' => $userPlugins, 'message' => $message));
} else {
	header("Location: ../panel/");
}

?>
