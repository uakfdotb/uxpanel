<?php

function adminRegisterAccount($email, $password, $name) {
	global $db;
	
	$email = escape($email);
	
	if(substr($password, 0, 6) == ":hash:") {
		$password = escape(substr($password, 6));
	} else {
		require_once(includePath() . "/pbkdf2.php");
		$password = escape("*pbkdf2*" . create_hash($password));
	}
	
	$name = escape($name);
	mysql_query("INSERT INTO accounts (email, password, name) VALUES ('$email', '$password', '$name')", $db);
}

function adminDeleteAccount($id) {
	global $db;
	
	$id = escape($id);
	mysql_query("DELETE FROM accounts WHERE id = '$id'", $db);
}

//returns array of account_id => array(email, name)
function adminGetAccounts() {
	global $db;
	
	$result = mysql_query("SELECT id, email, name FROM accounts", $db);
	$accounts = array();
	
	while($row = mysql_fetch_row($result)) {
		$accounts[$row[0]] = array('email' => $row[1], 'name' => $row[2]);
	}
	
	return $accounts;
}

//returns array(email, name)
function adminGetAccount($id) {
	global $db;
	
	$id = escape($id);
	$result = mysql_query("SELECT email, name FROM accounts WHERE id = '$id'", $db);
	
	if($row = mysql_fetch_array($result)) {
		return array('email' => $row[0], 'name' => $row[1]);
	} else {
		return array("User not found", "User not found");
	}
}

function createService($account_id, $service_name, $service_description, $service_type, $service_param) {
	global $db;
	
	$account_id = escape($account_id);
	$service_name = escape($service_name);
	$service_description = escape($service_description);
	$service_type = escape($service_type);
	
	mysql_query("INSERT INTO services (account_id, name, description, type) VALUES ('$account_id', '$service_name', '$service_description', '$service_type')", $db);
	$service_id = intval(mysql_insert_id());
	
	foreach($service_param as $key => $val) {
		$key = escape($key);
		$val = escape($val);
		
		mysql_query("INSERT INTO service_params (service_id, k, v) VALUES ('$service_id', '$key', '$val')", $db);
	}
	
	return $service_id;
}

function getServiceType($service_id) {
	global $db;
	$service_id = escape($service_id);
	$result = mysql_query("SELECT type FROM services WHERE id = '$service_id'", $db);
	
	if($row = mysql_fetch_array($result)) {
		return $row[0];
	} else {
		return false;
	}
}

//returns array of array(id, name, description, type)
function getServices($account_id) {
	global $db;
	
	$account_id = escape($account_id);
	$result = mysql_query("SELECT id, name, description, type FROM services WHERE account_id = '$account_id'", $db);
	$array = array();
	
	while($row = mysql_fetch_row($result)) {
		$array[] = array('id' => $row[0], 'name' => $row[1], 'description' => $row[2], 'type' => $row[3]);
	}
	
	return $array;
}

//returns false on failure or array(name, description, type)
function getService($service_id) {
	global $db;
	
	$service_id = escape($service_id);
	$result = mysql_query("SELECT name, description, type FROM services WHERE id = '$service_id'", $db);
	
	if($row = mysql_fetch_array($result)) {
		return array('name' => $row[0], 'description' => $row[1], 'type' => $row[2]);
	} else {
		return false;
	}
}

//deletes a service
function removeService($service_id) {
	global $db;
	
	$service_id = escape($service_id);
	mysql_query("DELETE FROM services WHERE id = '$service_id'", $db);
	mysql_query("DELETE FROM service_params WHERE service_id = '$service_id'", $db);
}

//returns the account id associated with a service, or false on failure
function getServiceOwner($service_id) {
	global $db;
	$service_id = escape($service_id);
	$result = mysql_query("SELECT account_id FROM services WHERE id = '$service_id'", $db);
	
	if($row = mysql_fetch_array($result)) {
		return $row[0];
	} else {
		return false;
	}
}

//returns specific extra service parameters for an array of services
//format is array of service id => array of k => v
//parameters returned: price, due, link
function getServiceExtra($services) {
	$serviceExtra = array();
	
	foreach($services as $service) {
		$price = getServiceParam($service['id'], 'price');
		
		if($price === false) {
			$price = "Unknown";
		}
		
		$due = getServiceParam($service['id'], 'due');
		
		if($due === false) {
			$due = "Unknown";
		}
		
		$preLink = "service_redirect.php?";
		
		if(($linkParam = getServiceParam($service['id'], 'link')) !== false) {
			$preLink = $linkParam;
		}
		
		$link = $preLink . "id=" . $service['id'];
		
		$serviceExtra[$service['id']] = array('price' => $price, 'due' => $due, 'link' => $link);
	}
	
	return $serviceExtra;
}

//sets a key, val pair parameter for a certain service
// or deletes if val = false
function setServiceParam($service_id, $key, $val) {
	global $db;
	
	$service_id = escape($service_id);
	$key = escape($key);
	
	if($val !== false) {
		$val = escape($val);
	}
	
	//check if key exists already (in that case just update the existing one)
	$result = mysql_query("SELECT id FROM service_params WHERE service_id = '$service_id' AND k = '$key'", $db);
	
	if($row = mysql_fetch_array($result)) {
		if($val !== false) {
			mysql_query("UPDATE service_params SET v = '$val' WHERE id = '{$row[0]}'", $db);
		} else {
			mysql_query("DELETE FROM service_params WHERE id = '{$row[0]}'", $db);
		}
	} else if($val !== false) {	
		mysql_query("INSERT INTO service_params (service_id, k, v) VALUES ('$service_id', '$key', '$val')", $db);
	}
	
	//also update parameter cache
	if(isset($GLOBALS['paramcache'][$service_id])) {
		if($val !== false) {
			$GLOBALS['paramcache'][$service_id][$key] = $val;
		} else if(isset($GLOBALS['paramcache'][$service_id][$key])) {
			unset($GLOBALS['paramcache'][$service_id][$key]);
		}
	}
}

function getServiceParam($service_id, $key) {
	global $db, $config;
	
	$service_id = escape($service_id);
	$key = escape($key);
	
	//check parameter cache
	if(isset($GLOBALS['paramcache'][$service_id])) {
		if(isset($GLOBALS['paramcache'][$service_id][$key])) {
			return $GLOBALS['paramcache'][$service_id][$key];
		} else {
			return false;
		}
	}
	
	//if slave, then cache all service parameters in global variable to make it faster
	if($config['slave_enabled']) {
		getServiceParams($service_id);
		
		if(isset($GLOBALS['paramcache'][$service_id][$key])) {
			return $GLOBALS['paramcache'][$service_id][$key];
		} else {
			return false;
		}
	} else {
		$result = mysql_query("SELECT v FROM service_params WHERE service_id = '$service_id' AND k = '$key'", $db);
	
		if($row = mysql_fetch_array($result)) {
			return $row[0];
		} else {
			return false;
		}
	}
}

//gets all service params as an array of k => v
function getServiceParams($service_id) {
	global $db;
	
	$service_id = escape($service_id);
	$result = mysql_query("SELECT k, v FROM service_params WHERE service_id = '$service_id'", $db);
	$array = array();
	
	while($row = mysql_fetch_array($result)) {
		$array[$row[0]] = $row[1];
	}
	
	//cache the parameters
	$GLOBALS['paramcache'][$service_id] = $array;
	
	return $array;
}

?>
