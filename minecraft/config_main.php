<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/minecraft.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_SESSION['is_' . $_REQUEST['id'] . '_minecraft'])) {
	if(isset($_POST['action']) && $_POST['action'] == "update") {
		//create array of configurations we are updating
		$array = minecraftGetConfigFromRequest($minecraftParameters, $_REQUEST);
		minecraftReconfigure($_REQUEST['id'], $array);
		
		if(!isset($_SESSION['noredirect'])) {
			header("Location: config_main.php?id=" . $_REQUEST['id']);
			return;
		}
	}
	
	$mconfig = minecraftGetConfiguration($_REQUEST['id']);
	get_page("config_main", "minecraft", array('service_id' => $_REQUEST['id'], 'mconfig' => $mconfig, 'parameters' => $minecraftParameters));
} else {
	header("Location: ../panel/");
}

?>
