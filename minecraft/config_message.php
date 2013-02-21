<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/minecraft.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_SESSION['is_' . $_REQUEST['id'] . '_minecraft'])) {
	if(isset($_POST['action']) && $_POST['action'] == "update" && isset($_POST['filename']) && isset($_POST['content'])) {
		minecraftUpdateFile($_REQUEST['id'], $_POST['filename'], $_POST['content']);
		
		if(!isset($_SESSION['noredirect'])) {
			header("Location: config_message.php?id=" . $_REQUEST['id'] . "&filename=" . urlencode($_REQUEST['filename']));
			return;
		}
	}
	
	$filename = "";
	$content = "";
	
	if(isset($_REQUEST['filename'])) {
		$filename = $_REQUEST['filename'];
		$content = minecraftDisplayFile($_REQUEST['id'], $_REQUEST['filename']);
	}
	
	get_page("config_message", "minecraft", array('service_id' => $_REQUEST['id'], 'filename' => $filename, 'content' => $content, 'files' => $minecraftUpdatableFiles));
} else {
	header("Location: ../panel/");
}

?>
