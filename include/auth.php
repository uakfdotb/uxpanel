<?php

//true: success login
//-1: try again later
//-2: invalid information
function authAccount($email, $password) {
	global $_SESSION, $db;

	$email = escape($email);
	$password = escape(chash($password));
	
	$result = mysql_query("SELECT id, email, name FROM accounts WHERE email = '$email' AND password = '$password'", $db);
	
	if($row = mysql_fetch_row($result)) {
		$_SESSION['account_id'] = $row[0];
		$_SESSION['account_email'] = $row[1];
		$_SESSION['account_name'] = $row[2];
		return true;
	} else {
		return -2;
	}
}

function authAdmin($username, $password) {
	global $config;
	
	if($config['admin_username'] == $username && $config['admin_password'] == $password) {
		return true;
	} else {
		return false;
	}
}

?>
