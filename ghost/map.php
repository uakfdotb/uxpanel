<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/ghost.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_SESSION['is_' . $_REQUEST['id'] . '_ghost'])) {
	$message = "";

	if(isset($_REQUEST['message'])) {
		$message = $_REQUEST['message'];
	}
	
	if(isset($_POST['action'])) {
		if($_POST['action'] == "link" && isset($_POST['filename'])) {
			$result = ghostMapLink($_REQUEST['id'], $_POST['filename']);
			
			if($result === true) {
				$message = "Map linked successfully with repository.";
			} else {
				$message = $result;
			}
		} else if($_POST['action'] == "upload" && isset($_POST['upload'])) {
			$result = ghostMapUpload($_REQUEST['id'], $_FILES);
			
			if($result === true) {
				$message = "Map uploaded successfully.";
			} else {
				$message = $result;
			}
		} else if($_POST['action'] == "remove" && isset($_POST['filename'])) {
			ghostMapDelete($_REQUEST['id'], $_POST['filename']);
			$message = "Map deleted.";
		}
		
		if(!isset($_SESSION['noredirect'])) {
			header("Location: map.php?id=" . $_REQUEST['id'] . "&message=" . urlencode($message));
			return;
		}
	} else if(isset($_GET['action'])) {
		if($_GET['action'] == "download" && isset($_GET['filename'])) {
			ghostMapDownload($_REQUEST['id'], $_GET['filename']);
			return;
		} else if($_GET['action'] == "repodownload" && isset($_GET['filename'])) {
			ghostMapDownload($_REQUEST['id'], $_GET['filename'], true);
			return;
		}
	}
	
	# get the current repository and user files
	$repositoryMaps = ghostMapList($_REQUEST['id'], "repository");
	$userMaps = ghostMapList($_REQUEST['id'], "maps");
	
	get_page("map", "ghost", array('service_id' => $_REQUEST['id'], 'repositoryMaps' => $repositoryMaps, 'userMaps' => $userMaps, 'message' => $message));
} else {
	header("Location: ../panel/");
}

?>
