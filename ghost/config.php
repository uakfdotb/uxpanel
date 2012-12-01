<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/ghost.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id']) && is_numeric($_REQUEST['id']) && isset($_SESSION['is_' . $_REQUEST['id'] . '_ghost'])) {
	get_page("config", "ghost", array('service_id' => $_REQUEST['id']));
} else {
	header("Location: ../panel/");
}

?>
