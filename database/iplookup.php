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
	
	$ips = databaseIPLookup($_REQUEST['id'], $player[0], $player[1]);
	get_page("iplookup", "database", array('service_id' => $_REQUEST['id'], 'ips' => $ips));
} else {
	header("Location: ../panel/");
}

?>
