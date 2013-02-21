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
		if($_POST['action'] == "backup" && isset($_POST['label'])) {
			$result = minecraftBackupWorld($_REQUEST['id'], $_POST['label']);
			
			if($result === true) {
				$message = "World backed up successfully.";
			} else {
				$message = $result;
			}
		} else if($_POST['action'] == "remove" && isset($_POST['filename'])) {
			minecraftServerDelete($_REQUEST['id'], $_POST['filename'], ".");
			$message = "Backup deleted.";
		} else if($_POST['action'] == "restore" && isset($_POST['filename'])) {
			$result = minecraftRestoreWorld($_REQUEST['id'], removeExtension($_POST['filename']));
			
			if($result === true) {
				$message = "The world has been restored based on the backup successfully.";
			} else {
				$message = $result;
			}
		}
		
		if(!isset($_SESSION['noredirect'])) {
			header("Location: backup.php?id=" . $_REQUEST['id'] . "&message=" . urlencode($message));
			return;
		}
	}
	
	//list backups to show which ones can be deleted or restored
	$backups = minecraftServerList($_REQUEST['id'], "backups");
	get_page("backup", "minecraft", array('service_id' => $_REQUEST['id'], 'backups' => $backups, 'message' => $message));
} else {
	header("Location: ../panel/");
}

?>
