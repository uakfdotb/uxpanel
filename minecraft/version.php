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
			$result = minecraftServerLink($_REQUEST['id'], $_POST['filename'], "version", "");
			
			if($result === true) {
				$message = "Server version linked successfully with repository.";
			} else {
				$message = $result;
			}
		} else if($_POST['action'] == "upload" && isset($_POST['upload'])) {
			$result = minecraftServerUpload($_REQUEST['id'], $_FILES, "version");
			
			if($result === true) {
				$message = "Server version uploaded successfully.";
			} else {
				$message = $result;
			}
		}
		
		if(!isset($_SESSION['noredirect'])) {
			header("Location: version.php?id=" . $_REQUEST['id'] . "&message=" . urlencode($message));
			return;
		}
	}
	
	# get the versions in repository
	$versions = minecraftServerList($_REQUEST['id'], "versions");
	get_page("version", "minecraft", array('service_id' => $_REQUEST['id'], 'versions' => $versions, 'message' => $message));
} else {
	header("Location: ../panel/");
}

?>
