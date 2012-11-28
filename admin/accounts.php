<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");

if(isset($_SESSION['admin'])) {
	if(isset($_REQUEST['action'])) {
		if($_REQUEST['action'] == "delete" && isset($_REQUEST['delete_id'])) {
			adminDeleteAccount($_REQUEST['delete_id']);
		} else if($_REQUEST['action'] == "register" && isset($_REQUEST['email']) && isset($_REQUEST['password']) && isset($_REQUEST['name'])) {
			adminRegisterAccount($_REQUEST['email'], $_REQUEST['password'], $_REQUEST['name']);
		}
		
		//don't want that post data remaining, instead redirect back
		header("Location: accounts.php");
		return;
	}
	
	//get accounts
	$accounts = adminGetAccounts();
	
	//display
	get_page("accounts", "admin", array('accounts' => $accounts));
} else {
	header("Location: ./");
}

?>
