<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/database.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_SESSION['is_' . $_REQUEST['id'] . '_database'])) {
	$ip = $_SERVER['REMOTE_ADDR'];
	
	if(isset($_REQUEST['ip'])) {
		$ip = htmlspecialchars($_REQUEST['ip']);
	}
	
	$players = databaseNameLookup($_REQUEST['id'], $ip);
	$nicePlayers = array();
	
	foreach($players as $player) {
		$nicePlayers[] = array($player[0], $player[1], databaseLastPlayed($_REQUEST['id'], $player[0]));
	}
	
	get_page("namelookup", "database", array('service_id' => $_REQUEST['id'], 'players' => $nicePlayers));
} else {
	header("Location: ../panel/");
}

?>
