<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/garena.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_SESSION['is_' . $_REQUEST['id'] . '_garena'])) {
	if(isset($_POST['action']) && $_POST['action'] == "update") {
		//create array of configurations we are updating
		$array = garenaGetConfigFromRequest(garenaGetParameters($_REQUEST['id']), $_REQUEST);
		garenaReconfigure($_REQUEST['id'], $array);
		
		if(!isset($_SESSION['noredirect'])) {
			header("Location: config_garena.php?id=" . $_REQUEST['id']);
			return;
		}
	}
	
	$gconfig = garenaGetConfiguration($_REQUEST['id']);
	get_page("config_garena", "garena", array('service_id' => $_REQUEST['id'], 'gconfig' => $gconfig, 'parameters' => $garenaParameters));
} else {
	header("Location: ../panel/");
}

?>
