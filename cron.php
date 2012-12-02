<?php

include("include/common.php");
include("config.php");
include("include/session.php");
include("include/dbconnect.php");

include("include/account.php");
include("include/database.php");

include($config['cron_path']);

$result = mysql_query("SELECT id FROM services WHERE type = 'database'", $db);

while($row = mysql_fetch_array($result)) {
	$service_id = $row[0];
	
	$params = array();
	$result2 = mysql_query("SELECT k, v FROM service_params WHERE service_id = '$service_id'");
	
	while($row2 = mysql_fetch_array($result2)) {
		$params[$row2[0]] = $row2[1];
	}
	
	$link = databaseConnect($service_id);
	executeCron($service_id, $link, $params);
}

?>
