<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/database.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_SESSION['is_' . $_REQUEST['id'] . '_database'])) {
	if(isset($_POST['action']) && $_POST['action'] == "update") {
		$array = databaseGetConfigFromRequest($cronParameters, $_REQUEST);
		databaseSetCronConfig($_REQUEST['id'], $array);
		
		if(!isset($_SESSION['noredirect'])) {
			header("Location: cron.php?id=" . $_REQUEST['id']);
			return;
		}
	}

	$cconfig = databaseGetCronConfig($_REQUEST['id']);
	get_page("cron", "database", array('service_id' => $_REQUEST['id'], 'parameters' => $cronParameters, 'cconfig' => $cconfig));
} else {
	header("Location: ../panel/");
}

?>
