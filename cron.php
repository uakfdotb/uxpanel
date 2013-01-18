<?php

include("include/common.php");
include("config.php");
include("include/dbconnect.php");

include("include/account.php");
include("include/database.php");

include($config['cron_path']);

//run database cron, but only if we are the master uxpanel instance
if(!$config['slave_enabled']) {
	$result = mysql_query("SELECT id FROM services WHERE type = 'database'", $db);

	while($row = mysql_fetch_array($result)) {
		$service_id = $row[0];
	
		$params = array();
		$result2 = mysql_query("SELECT k, v FROM service_params WHERE service_id = '$service_id'", $db);
	
		while($row2 = mysql_fetch_array($result2)) {
			$params[$row2[0]] = $row2[1];
		}
	
		$link = databaseConnect($service_id);
		executeCron($service_id, $link, $params);
	}
}

if(function_exists('executeCronOther')) {
	//also cron for the non-database services
	//for uxpanel slave instances, there is currently no good way to check
	// if the slave instance is the correct instance
	//as a result, we simply run on all slave instances
	//this means that the cron script being called shouldn't do anything bad
	$result = mysql_query("SELECT id, type FROM services WHERE type != 'database'", $db);

	while($row = mysql_fetch_array($result)) {
		$service_id = $row[0];
		$service_type = $row[1];
	
		$params = array();
		$result2 = mysql_query("SELECT k, v FROM service_params WHERE service_id = '$service_id'", $db);
	
		while($row2 = mysql_fetch_array($result2)) {
			$params[$row2[0]] = $row2[1];
		}
		
		//if slave parameter is set but we are not a slave, don't run (and vice versa)
		//similarly, if slave_id parameter is set but it doesn't match our ID, don't run
		$isslave = isset($params['slave']) && $params['slave'];
		
		if((!$isslave || $config['slave_enabled']) &&
			($isslave || !$config['slave_enabled']) &&
			(!isset($params['slave_id']) || ($config['slave_enabled'] && $params['slave_id'] == $config['slave_id'])))
		{
			executeCronOther($service_id, $service_type, $params);
		}
	}
}

if(function_exists('executeCronShutdown')) {
	executeCronShutdown();
}

?>
