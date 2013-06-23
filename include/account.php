<?php

if(!isset($GLOBALS['IN_UXPANEL'])) {
	die("Access forbidden.");
}

function adminRegisterAccount($email, $password, $name) {
	global $db;
	
	$email = escape($email);
	
	if(substr($password, 0, 6) == ":hash:") {
		$password = escape(substr($password, 6));
	} else {
		require_once(includePath() . "/pbkdf2.php");
		$password = escape("*pbkdf2*" . pbkdf2_create_hash($password));
	}
	
	$name = escape($name);
	$db->query("INSERT INTO accounts (email, password, name) VALUES ('$email', '$password', '$name')");
}

function adminDeleteAccount($id) {
	global $db;
	
	$id = escape($id);
	$db->query("DELETE FROM accounts WHERE id = '$id'");
}

//returns array of account_id => array(email, name)
function adminGetAccounts() {
	global $db;
	
	$result = $db->query("SELECT id, email, name FROM accounts");
	$accounts = array();
	
	while($row = $result->fetch_array()) {
		$accounts[$row[0]] = array('email' => $row[1], 'name' => $row[2]);
	}
	
	$result->close();
	return $accounts;
}

//returns array(email, name)
function adminGetAccount($id) {
	global $db;
	
	$id = escape($id);
	$result = $db->query("SELECT email, name FROM accounts WHERE id = '$id'");
	
	if($row = $result->fetch_array()) {
		$result->close();
		return array('email' => $row[0], 'name' => $row[1]);
	} else {
		$result->close();
		return array("User not found", "User not found");
	}
}

//search for all services matching some filter(s)
//returns array(account id, account name, account email, service id, service name, service type)
function adminSearchServices($name, $description, $type, $param_key, $param_val) {
	global $db;
	
	$name = escape($name);
	$description = escape($description);
	$type = escape($type);
	$param_key = escape($param_key);
	$param_val = escape($param_val);

	$query = "SELECT DISTINCT accounts.id, accounts.name, accounts.email, services.id, services.name, services.type";
	
	$query .= " FROM services, accounts";
	if(!empty($param_key) || !empty($param_val)) {
		$query .= ", service_params";
	}
	
	$query .= " WHERE accounts.id = services.account_id";
	
	if(!empty($name)) {
		$query .= " AND services.name LIKE '%$name%'";
	}
	
	if(!empty($description)) {
		$query .= " AND services.description LIKE '%$description%'";
	}
	
	if(!empty($type)) {
		$query .= " AND services.type LIKE '%$type%'";
	}
	
	if(!empty($param_key) || !empty($param_val)) {
		$query .= " AND services.id = service_params.service_id";
		
		if(!empty($param_key)) {
			$query .= " AND service_params.k LIKE '%$param_key%'";
		}
		
		if(!empty($param_val)) {
			$query .= " AND service_params.v LIKE '%$param_val%'";
		}
	}
	
	$query .= " ORDER BY services.id";
	
	$result = $db->query($query);
	$array = array();
	
	while($row = $result->fetch_array()) {
		$array[] = array('account_id' => $row[0], 'account_name' => $row[1], 'account_email' => $row[2], 'service_id' => $row[3], 'service_name' => $row[4], 'service_type' => $row[5]);
	}
	
	return $array;
}

function createService($account_id, $service_name, $service_description, $service_type, $service_param) {
	global $db;
	
	$account_id = escape($account_id);
	$service_name = escape($service_name);
	$service_description = escape($service_description);
	$service_type = escape($service_type);
	
	$db->query("INSERT INTO services (account_id, name, description, type) VALUES ('$account_id', '$service_name', '$service_description', '$service_type')");
	$service_id = intval($db->insert_id);
	
	foreach($service_param as $key => $val) {
		$key = escape($key);
		$val = escape($val);
		
		$db->query("INSERT INTO service_params (service_id, k, v) VALUES ('$service_id', '$key', '$val')");
	}
	
	return $service_id;
}

function getServiceType($service_id) {
	global $db;
	$service_id = escape($service_id);
	$result = $db->query("SELECT type FROM services WHERE id = '$service_id'");
	
	if($row = $result->fetch_array()) {
		$result->close();
		return $row[0];
	} else {
		$result->close();
		return false;
	}
}

//returns array of array(id, name, description, type)
function getServices($account_id) {
	global $db;
	
	$account_id = escape($account_id);
	$result = $db->query("SELECT id, name, description, type FROM services WHERE account_id = '$account_id'");
	$array = array();
	
	while($row = $result->fetch_array()) {
		$array[] = array('id' => $row[0], 'name' => $row[1], 'description' => $row[2], 'type' => $row[3]);
	}
	
	$result->close();
	return $array;
}

//returns false on failure or array(name, description, type)
function getService($service_id) {
	global $db;
	
	$service_id = escape($service_id);
	$result = $db->query("SELECT name, description, type FROM services WHERE id = '$service_id'");
	
	if($row = $result->fetch_array()) {
		$result->close();
		return array('name' => $row[0], 'description' => $row[1], 'type' => $row[2]);
	} else {
		$result->close();
		return false;
	}
}

//deletes a service
function removeService($service_id) {
	global $db;
	
	$service_id = escape($service_id);
	$db->query("DELETE FROM services WHERE id = '$service_id'");
	$db->query("DELETE FROM service_params WHERE service_id = '$service_id'");
}

//returns the account id associated with a service, or false on failure
function getServiceOwner($service_id) {
	global $db;
	$service_id = escape($service_id);
	$result = $db->query("SELECT account_id FROM services WHERE id = '$service_id'");
	
	if($row = $result->fetch_array()) {
		$result->close();
		return $row[0];
	} else {
		$result->close();
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
	$result = $db->query("SELECT id FROM service_params WHERE service_id = '$service_id' AND k = '$key'");
	
	if($row = $result->fetch_array()) {
		if($val !== false) {
			$db->query("UPDATE service_params SET v = '$val' WHERE id = '{$row[0]}'");
		} else {
			$db->query("DELETE FROM service_params WHERE id = '{$row[0]}'");
		}
	} else if($val !== false) {	
		$db->query("INSERT INTO service_params (service_id, k, v) VALUES ('$service_id', '$key', '$val')");
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
		$result = $db->query("SELECT v FROM service_params WHERE service_id = '$service_id' AND k = '$key'");
		$row = $result->fetch_array();
		$result->close();
		
		if($row) {
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
	$result = $db->query("SELECT k, v FROM service_params WHERE service_id = '$service_id'");
	$array = array();
	
	while($row = $result->fetch_array()) {
		$array[$row[0]] = $row[1];
	}
	
	//cache the parameters
	$GLOBALS['paramcache'][$service_id] = $array;
	
	$result->close();
	return $array;
}

?>
