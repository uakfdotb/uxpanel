<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/minecraft.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_SESSION['is_' . $_REQUEST['id'] . '_minecraft'])) {
	$message = "";

	if(isset($_POST['command'])) {
		$result = minecraftCommand($_REQUEST['id'], $_POST['command']);
		
		if($result !== true) {
			$message = $result;
		} else {
			$message = "Command sent successfully!";
		}
	}

	$log = minecraftGetLog($_REQUEST['id']);
	get_page("log", "minecraft", array('service_id' => $_REQUEST['id'], 'log' => $log, 'message' => $message));
} else {
	header("Location: ../panel/");
}

?>
