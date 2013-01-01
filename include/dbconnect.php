<?php

//if slave, use persistent connections in case we are remote and making connection takes a while
if($config['slave_enabled']) {
	$db = mysql_pconnect($config['db_hostname'], $config['db_username'], $config['db_password']) or die("Could not connect to MySQL database. Check config.php.<br />" . mysql_error());
} else {
	$db = mysql_connect($config['db_hostname'], $config['db_username'], $config['db_password']) or die("Could not connect to MySQL database. Check config.php.<br />" . mysql_error());
}

mysql_select_db($config['db_name'], $db) or die("Could not select the uxpanel MySQL database. Check config.php.<br />" . mysql_error());

?>
