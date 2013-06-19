<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");

if(isset($_SESSION['admin'])) {
	$array = array(); //the result of the search, if any
	
	if(isset($_REQUEST['filter_name']) && isset($_REQUEST['filter_description']) && isset($_REQUEST['filter_type']) && isset($_REQUEST['filter_sk']) && isset($_REQUEST['filter_sv'])) {
		$array = adminSearchServices($_REQUEST['filter_name'], $_REQUEST['filter_description'], $_REQUEST['filter_type'], $_REQUEST['filter_sk'], $_REQUEST['filter_sv']);
	}
	
	get_page("search", "admin", array('result' => $array));
} else {
	header("Location: ./");
}

?>
