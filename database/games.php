<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/database.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_SESSION['is_' . $_REQUEST['id'] . '_database'])) {
	$start = 0;
	
	if(isset($_REQUEST['start']) && is_numeric($_REQUEST['start'])) {
		$start = $_REQUEST['start'];
	}
	
	$games = databaseGetGames($_REQUEST['id'], $start);
	get_page("games", "database", array('service_id' => $_REQUEST['id'], 'games' => $games, 'start' => $start));
} else {
	header("Location: ../panel/");
}

?>
