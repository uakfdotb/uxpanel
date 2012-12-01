<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/database.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_SESSION['is_' . $_REQUEST['id'] . '_database'])) {
	$player = array("", "");
	
	if(isset($_REQUEST['player'])) {
		$player = databaseGetPlayer($_REQUEST['player']);
	}
	
	$array = array();
	databaseAliases($_REQUEST['id'], $player[0], $player[1], 2, $array);
	$players = array_keys($array);
	$playersNice = array();

	foreach($players as $p_str) {
		$p_info = databaseGetPlayer($p_str);
		$playersNice[] = array($p_info[0], $p_info[1], databaseLastPlayed($_REQUEST['id'], $p_info[0]));
	}

	get_page("alias", "database", array('service_id' => $_REQUEST['id'], 'players' => $playersNice));
} else {
	header("Location: ../panel/");
}

?>
