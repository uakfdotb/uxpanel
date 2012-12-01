<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/auth.php");

if(isset($_SESSION['admin'])) {
	if(isset($_POST['action'])) {
		if($_POST['action'] == "delete" && isset($_POST['delete_id'])) {
			adminDeleteAccount($_POST['delete_id']);
		} else if($_POST['action'] == "register" && isset($_POST['email']) && isset($_POST['password']) && isset($_POST['name'])) {
			adminRegisterAccount($_POST['email'], $_POST['password'], $_REQUEST['name']);
		} else if($_POST['action'] == "morph" && isset($_POST['morph_email'])) {
			authAccount($_POST['morph_email'], "", true);
			header("Location: ../panel/");
			return;
		}
		
		//don't want that post data remaining, instead redirect back
		if(!isset($_SESSION['noredirect'])) {
			header("Location: accounts.php");
		}
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
