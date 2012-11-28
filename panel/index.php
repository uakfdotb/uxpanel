<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

if(isset($_SESSION['account_id'])) {
	if(isset($_REQUEST['action']) && $_REQUEST['action'] == "logout") {
		session_unset();
		header("Location: ../");
	} else {
		get_page("index", "panel", array());
	}
} else {
	header("Location: ../");
}

?>
