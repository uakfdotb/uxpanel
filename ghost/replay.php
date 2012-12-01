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
		if($_POST['action'] == "remove" && isset($_POST['replay'])) {
			ghostReplayDelete($_REQUEST['id'], $_POST['replay']);
		
			if(!isset($_SESSION['noredirect'])) {
				header("Location: replay.php?id=" . $_REQUEST['id'] . "&message=" . urlencode("Replay deleted."));
				return;
			}
		}
	} else if(isset($_GET['action']) && $_GET['action'] == "download" && isset($_GET['replay'])) {
		ghostReplayDownload($_REQUEST['id'], $_GET['replay']);
		return;
	}
	
	$replays = ghostReplayList($_REQUEST['id']);
	get_page("replay", "ghost", array('service_id' => $_REQUEST['id'], 'replays' => $replays, 'message' => $message));
} else {
	header("Location: ../panel/");
}

?>
