<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/database.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_SESSION['is_' . $_REQUEST['id'] . '_database'])) {
	$message = "";
	
	if(isset($_REQUEST['message'])) {
		$message = str_replace("[br]", "<br>", htmlspecialchars($_REQUEST['message']));
	}
	
	if(isset($_POST['username']) && isset($_POST['reason']) && isset($_POST['realm']) && isset($_POST['duration'])) {
		$unban = isset($_REQUEST['unban']);
		$nameonly = isset($_REQUEST['nameonly']);
		$aliases = isset($_REQUEST['aliases']);
		
		$message = databaseBanUser($_REQUEST['id'], $_POST['username'], $_POST['realm'], $_POST['duration'], $_POST['reason'], $unban, $nameonly, $aliases);
		
		if(!isset($_SESSION['noredirect'])) {
			$messageEscape = urlencode(str_replace(array("<br>", "<br/>", "<br />"), array("[br]", "[br]", "[br]"), $message));
			header("Location: ban.php?id=" . $_REQUEST['id'] . "&message=" . $messageEscape);
		}
	}

	$realms = databaseGetRealms($_REQUEST['id']);
	get_page("ban", "database", array('service_id' => $_REQUEST['id'], 'message' => $message, 'realms' => $realms));
} else {
	header("Location: ../panel/");
}

?>
