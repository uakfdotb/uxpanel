<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/ghost.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_SESSION['is_' . $_REQUEST['id'] . '_ghost'])) {
	$message = "";
	$edit = "";
	$content = "";

	if(isset($_REQUEST['message'])) {
		$message = $_REQUEST['message'];
	}
	
	if(isset($_REQUEST['edit_filename'])) {
		$edit = $_REQUEST['edit_filename'];
		$content = ghostDisplayFile($_REQUEST['id'], $edit, true);
	}
	
	if(isset($_POST['action'])) {
		if($_POST['action'] == "remove" && isset($_POST['filename'])) {
			ghostMapDelete($_REQUEST['id'], $_POST['filename'], true);
			$message = "Map configuration file has been removed.";
		} else if($_POST['action'] == "add" && isset($_POST['filename'])) {
			$filename = $_POST['filename'];
			
			if(strpos($filename, ".") === false) {
				$filename .= ".cfg";
			}
			
			ghostUpdateFile($_REQUEST['id'], $filename, "# New map configuration file", true);
			$message = "Map configuration file added.";
		} else if($_POST['action'] == "edit" && isset($_POST['filename']) && isset($_POST['content'])) {
			$filename = $_POST['filename'];
			
			if(strpos($filename, ".") === false) {
				$filename .= ".cfg";
			}
			
			ghostUpdateFile($_REQUEST['id'], $filename, $_POST['content'], true);
			$message = "Map configuration file edited.";
		}
		
		if(!isset($_SESSION['noredirect'])) {
			header("Location: mapcfg.php?id=" . $_REQUEST['id'] . "&message=" . urlencode($message));
			return;
		}
	}
	
	$list = ghostMapList($_REQUEST['id'], "mapcfgs");
	get_page("mapcfg", "ghost", array('service_id' => $_REQUEST['id'], 'list' => $list, 'message' => $message, 'edit' => $edit, 'content' => $content));
} else {
	header("Location: ../panel/");
}

?>
