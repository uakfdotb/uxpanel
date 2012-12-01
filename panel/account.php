<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/auth.php");

if(isset($_SESSION['account_id'])) {
	$message = "";
	
	if(isset($_REQUEST['message'])) {
		$message = $_REQUEST['message'];
	}
	
	if(isset($_POST['action'])) {
		if($_POST['action'] == "changepass" && isset($_POST['old_password']) && isset($_POST['new_password']) && isset($_POST['new_password_conf'])) {
			if($_POST['new_password'] == $_POST['new_password_conf']) {
				$result = authChangePassword($_SESSION['account_id'], $_POST['old_password'], $_POST['new_password']);
			
				if($result !== true) {
					$message = $result;
				} else {
					$message = "Your password has been changed successfully.";
				}
			} else {
				$message = "The passwords you entered do not match!";
			}
		}
		
		if(!isset($_SESSION['noredirect'])) {
			header("Location: account.php?message=" . urlencode($message));
		}
	}
	
	get_page("account", "panel", array('email' => $_SESSION['account_email'], 'name' => $_SESSION['account_name'], 'message' => $message));
} else {
	header("Location: ../");
}

?>
