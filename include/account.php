<?php

function adminRegisterAccount($email, $password, $name) {
	global $db;
	
	$email = escape($email);
	
	if(substr($password, 0, 6) == ":hash:") {
		$password = escape(substr($password, 6));
	} else {
		$password = escape(chash($password));
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
		$accounts[$row[0]] = array($row[1], $row[2]);
	}
	
	return $accounts;
}

//returns array(email, name)
function adminGetAccount($id) {
	global $db;
	
	$id = escape($id);
	$result = mysql_query("SELECT email, name FROM accounts WHERE id = '$id'", $db);
	
	if($row = mysql_fetch_array($result)) {
		return array($row[0], $row[1]);
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
	$service_id = escape(mysql_insert_id());
	
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

//returns array of array(service id, name, description, type)
function getServices($account_id) {
	global $db;
	
	$account_id = escape($account_id);
	$result = mysql_query("SELECT id, name, description, type FROM services WHERE account_id = '$account_id'", $db);
	$array = array();
	
	while($row = mysql_fetch_row($result)) {
		$array[] = array($row[0], $row[1], $row[2], $row[3]);
	}
	
	return $array;
}

//returns extra service parameters for an array of services
//format is array of service id => array of k => v
function getServiceExtra($services) {
	$serviceExtra = array();
	
	foreach($services as $service) {
		$price = getServiceParam($service[0], 'price');
		
		if($price === false) {
			$price = "Unknown";
		}
		
		$due = getServiceParam($service[0], 'due');
		
		if($due === false) {
			$due = "Unknown";
		}
		
		$link = "#";
		
		if($services[3] == "ghost") {
			$link = "../ghost/index.php?id={$services[0]}";
		} else if($services[3] == "channel") {
			$link = "../channel/index.php?id={$services[0]}";
		} else if($services[3] == "database") {
			$link = "../database/index.php?id={$services[0]}";
		}
		
		$serviceExtra[$service[0]] = array('price' => $price, 'due' => $due, 'link' => $link);
	}
	
	return $serviceExtra;
}

function setServiceParam($service_id, $key, $val) {
	global $db;
	
	$service_id = escape($service_id);
	$key = escape($key);
	$val = escape($val);
	
	//check if key exists already (in that case just update the existing one)
	$result = mysql_query("SELECT id FROM service_params WHERE service_id = '$service_id' AND k = '$key'", $db);
	
	if($row = mysql_fetch_array($result)) {
		mysql_query("UPDATE service_params SET v = '$val' WHERE id = '{$row[0]}'", $db);
	} else {	
		mysql_query("INSERT INTO service_params (service_id, k, v) VALUES ('$service_id', '$key', '$val')", $db);
	}
}

function getServiceParam($service_id, $key) {
	global $db;
	
	$service_id = escape($service_id);
	$key = escape($key);
	$result = mysql_query("SELECT v FROM service_params WHERE service_id = '$service_id' AND k = '$key'", $db);
	
	if($row = mysql_fetch_array($result)) {
		return $row[0];
	} else {
		return false;
	}
}

?>
