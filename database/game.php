<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/database.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_SESSION['is_' . $_REQUEST['id'] . '_database']) && isset($_REQUEST['gid']) && is_numeric($_REQUEST['gid'])) {
	$gid = $_REQUEST['gid'];
	$game = databaseGetGame($_REQUEST['id'], $gid);
	get_page("game", "database", array('service_id' => $_REQUEST['id'], 'game' => $game, 'gid' => $gid));
} else {
	header("Location: ../panel/");
}

?>
