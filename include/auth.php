<?php

//true: success login
//-1: try again later
//-2: invalid information
//if force is set, any password will be accepted
function authAccount($email, $password, $force = false) {
	global $_SESSION, $db;
	
	if(!checkLock("checkuser")) {
		return -1;
	}

	$email = escape($email);
	$password = escape(chash($password));
	
	if($force) {
		$result = mysql_query("SELECT id, email, name FROM accounts WHERE email = '$email'", $db);
	} else {
		$result = mysql_query("SELECT id, email, name FROM accounts WHERE email = '$email' AND password = '$password'", $db);
	}
	
	if($row = mysql_fetch_row($result)) {
		$_SESSION['account_id'] = $row[0];
		$_SESSION['account_email'] = $row[1];
		$_SESSION['account_name'] = $row[2];
		return true;
	} else {
		lockAction("checkuser");
		return -2;
	}
}

function authAdmin($username, $password) {
	global $config;
	
	if(!checkLock("checkadmin")) {
		return false;
	}
	
	if($config['admin_username'] == $username && $config['admin_password'] == $password) {
		return true;
	} else {
		lockAction("checkadmin");
		return false;
	}
}

//string: error message
//true: success
function authChangePassword($user_id, $old_password, $new_password) {
	global $config, $db;
	
	if(!checkLock("checkuser")) {
		return "Too many failed attempts. Please try again later.";
	}
	
	if(strlen($new_password) < 6) {
		return "The new password is too short. Please use at least six characters.";
	}
	
	if($old_password == $new_password) {
		return "The old and new passwords are identical.";
	}
	
	$user_id = escape($user_id);
	$old_password = escape(chash($old_password));
	$new_password = escape(chash($new_password));
	
	$result = mysql_query("UPDATE accounts SET password = '$new_password' WHERE id = '$user_id' AND password = '$old_password'", $db);
	
	if(mysql_affected_rows() == 0) {
		lockAction("checkuser");
		return "The password you entered is not correct.";
	} else {
		return true;
	}
}

//going to go to remote
//returns token
function authRemoteRegister($user_id, $service_id, $ip) {
	$user_id = escape($user_id);
	$service_id = escape($service_id);
	$ip = escape($ip);
	$time = time();
	
	//generate token
	$token = uid(128);
	
	//insert user
	mysql_query("INSERT INTO remote_tokens (user_id, service_id, ip, token, time) VALUES ('$user_id', '$service_id', '$ip', '$token', '$time')");
	
	//housekeeping: delete entries older than two minutes
	mysql_query("DELETE FROM remote_tokens WHERE time < '$time' - 120");
	
	return $token;
}

//authenticate as remote, returns true on success or false on failure
function authRemote($user_id, $service_id, $ip, $token) {
	$user_id = escape($user_id);
	$service_id = escape($service_id);
	$ip = escape($ip);
	$token = escape($token);
	
	//housekeeping: delete entries older than two minutes
	mysql_query("DELETE FROM remote_tokens WHERE time < '$time' - 120");
	
	//get user
	$result = mysql_query("SELECT id FROM remote_tokens WHERE user_id = '$user_id' AND service_id = '$service_id' AND ip = '$ip' AND token = '$token'");
	
	if($row = mysql_fetch_row($result)) {
		//delete the token
		mysql_query("DELETE FROM remote_tokens WHERE id = '{$row[0]}'");
		
		//update session
		$_SESSION['account_id'] = $user_id;
		$_SESSION['account_email'] = 'unknown';
		$_SESSION['account_name'] = 'unknown';
		$_SESSION['slave'] = 1;
		
		//all good
		return true;
	} else {
		return false;
	}
}

?>
