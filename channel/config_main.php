<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/channel.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_SESSION['is_' . $_REQUEST['id'] . '_channel'])) {
	if(isset($_POST['action']) && $_POST['action'] == "update") {
		//create array of configurations we are updating
		$array = channelGetConfigFromRequest($channelParameters, $_REQUEST);
		channelReconfigure($_REQUEST['id'], $array);
		
		if(!isset($_SESSION['noredirect'])) {
			header("Location: config_main.php?id=" . $_REQUEST['id']);
			return;
		}
	}
	
	$mconfig = channelGetConfiguration($_REQUEST['id']);
	get_page("config_main", "channel", array('service_id' => $_REQUEST['id'], 'mconfig' => $mconfig, 'parameters' => $channelParameters));
} else {
	header("Location: ../panel/");
}

?>
