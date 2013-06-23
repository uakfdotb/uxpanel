<?php

if(!isset($GLOBALS['IN_UXPANEL'])) {
	die("Access forbidden.");
}

function authCheckPassword($parameter, $password, $parameter_type = "email") {
	global $db;
	$parameter = escape($parameter);
	
	if($parameter_type == "id") {
		$result = $db->query("SELECT password FROM accounts WHERE id = '$parameter'");
	} else {
		$result = $db->query("SELECT password FROM accounts WHERE email = '$parameter'");
	}
	
	$row = $result->fetch_array();
	$result->close();
	
	if($row) {
		$good_password = $row[0];
		$format = "hash";
		
		if($good_password[0] == "*" && strlen($good_password) > 2) {
			$parts = explode("*", substr($good_password, 1), 2);
			
			if(count($parts) == 2) {
				$format = $parts[0];
				$good_password = $parts[1];
			}
		}
		
		if(!validatePassword($password, $good_password, $format)) {
			return false;
		}
	} else {
		return false;
	}
	
	return true;
}

//true: success login
//-1: try again later
//-2: invalid information
//if force is set, any password will be accepted
function authAccount($email, $password, $force = false) {
	global $_SESSION, $db;
	
	if(!checkLock("checkuser")) {
		return -1;
	}
	
	if(!$force) {
		if(!authCheckPassword($email, $password)) {
			lockAction("checkuser");
			return -2;
		}
	}
	
	$email = escape($email);
	$result = $db->query("SELECT id, email, name FROM accounts WHERE email = '$email'");
	$row = $result->fetch_array();
	$result->close();
	
	if($row) {
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
	
	if($config['admin_username'] == $username && validatePassword($password, $config['admin_password'], $config['admin_passwordformat'])) {
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
	
	if(!authCheckPassword($user_id, $old_password, "id")) {
		lockAction("checkuser");
		return "The password you entered is not correct.";
	}
	
	$user_id = escape($user_id);
	
	require_once(includePath() . "/pbkdf2.php");
	$new_password = escape("*pbkdf2*" . pbkdf2_create_hash($new_password));
	$db->query("UPDATE accounts SET password = '$new_password' WHERE id = '$user_id'");
	return true;
}

//going to go to remote
//returns token
function authRemoteRegister($user_id, $service_id, $ip) {
	global $db;
	
	$user_id = escape($user_id);
	$service_id = escape($service_id);
	$ip = escape($ip);
	$time = time();
	
	//generate token
	$token = uid(128);
	
	//insert user
	$db->query("INSERT INTO remote_tokens (user_id, service_id, ip, token, time) VALUES ('$user_id', '$service_id', '$ip', '$token', '$time')");
	
	//housekeeping: delete entries older than two minutes
	$db->query("DELETE FROM remote_tokens WHERE time < '$time' - 120");
	
	return $token;
}

//authenticate as remote, returns true on success or false on failure
function authRemote($user_id, $service_id, $ip, $token) {
	global $db;
	
	$user_id = escape($user_id);
	$service_id = escape($service_id);
	$ip = escape($ip);
	$token = escape($token);
	
	//get user
	$result = $db->query("SELECT id FROM remote_tokens WHERE user_id = '$user_id' AND service_id = '$service_id' AND ip = '$ip' AND token = '$token'");
	$row = $result->fetch_array();
	$result->close();
	
	if($row) {
		//delete the token
		$db->query("DELETE FROM remote_tokens WHERE id = '{$row[0]}'");
		
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
