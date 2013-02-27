<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/ghost.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_SESSION['is_' . $_REQUEST['id'] . '_ghost'])) {
	if(isset($_POST['action']) && $_POST['action'] == "update") {
		//create array of configurations we are updating
		$array = ghostGetConfigFromRequest(ghostGetParameters($_REQUEST['id']), $_REQUEST);
		ghostReconfigure($_REQUEST['id'], $array);
		
		if(!isset($_SESSION['noredirect'])) {
			header("Location: config_ghost.php?id=" . $_REQUEST['id']);
			return;
		}
	}
	
	$gconfig = ghostGetConfiguration($_REQUEST['id']);
	get_page("config_ghost", "ghost", array('service_id' => $_REQUEST['id'], 'gconfig' => $gconfig, 'parameters' => $ghostParameters));
} else {
	header("Location: ../panel/");
}

?>
