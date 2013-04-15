<html>
<body>
<table>
<tr>
	<th>Status</th>
	<th>Name</th>
	<th>Description</th>
</tr>

<?

if(file_exists('config.php') && is_readable('config.php')) {
	include("include/common.php");
	include("config.php");
	result('Configuration file', 'Configuration file is readable', true);
	
	if(isset($config)) {
		//make sure required values are set
		$config_keys = array('site_name', 'format_time', 'format_date', 'root_path', 'mail_from', 'mail_fromname', 'admin_username', 'admin_password', 'admin_passwordformat', 'db_hostname', 'db_name', 'db_username', 'db_password', 'slave_enabled');
		
		foreach($config_keys as $config_key) {
			if(!isset($config[$config_key])) {
				result("Config: " . $config_key, "Config key " . $config_key . " is not set (check config.php)", false);
			}
		}
	} else {
		result('Configuration variable', 'Configuration variable is not set (check config.php)', false);
	}
} else {
	result('Configuration file', 'config.php is not readable!', false);
}

if((bool) ini_get('register_globals') && strtolower(ini_get('register_globals')) != 'off') {
	result('PHP register_globals', 'PHP register_globals is enabled; this is obsolete and is a security risk', false);
} else {
	result('PHP register_globals', 'PHP register_globals is disabled', true);
}

if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc() == 1) {
	result('PHP magic quotes', 'PHP magic quotes are enabled, this could result in overquoted entries in database', false);
} else {
	result('PHP magic quotes', 'PHP magic quotes are disabled', true);
}

$connection = mysql_connect($config['db_hostname'], $config['db_username'], $config['db_password']);

if($connection === FALSE) {
	result('MySQL connection', 'Cannot connect to MySQL database', false);
} else {
	result('MySQL connection', 'Connection to MySQL database successful', true);
	$selectResult = mysql_select_db($config['db_name'], $connection);
	
	if($selectResult === FALSE) {
		result('MySQL database', 'Error while selecting MySQL database ' . $config['db_name'], false);
	} else {
		result('MySQL database', 'MySQL database exists', true);
		$mysqlResult = mysql_query("SELECT COUNT(*) FROM accounts"); //test if tables are created
		
		if($mysqlResult === FALSE) {
			result('MySQL tables', 'MySQL tables do not appear to be created', false);
		} else {
			result('MySQL tables', 'MySQL tables appear to be created', true);
			mysql_free_result($mysqlResult);
		}
	}
	
	mysql_close($connection);
}

if(!extension_loaded('zip')) {
	result('PHP zip extension', 'PHP zip extension is not loaded, file backups will not work', false);
} else {
	result('PHP zip extension', 'PHP zip extension is loaded', true);
}

if(!extension_loaded('mcrypt')) {
	result('PHP mcrypt extension', 'PHP mcrypt extension is not loaded, account creation will not work', false);
} else {
	result('PHP mcrypt extension', 'PHP mcrypt extension is loaded', true);
}

if(!file_exists($config['root_path']) || !is_writable($config['root_path'])) {
	result('Submission directory', "Root service path (\$config['root_path']) does not exist or is not writable", false);
} else {
	result('Submission directory', 'Root service path exists and is writable', true);
}

if(!in_array('sha512', hash_algos())) {
	result('Hash algorithm', 'The default hash algorithm, sha512, does not exist!', false);
} else {
	result('Hash algorithm', 'The default hash algorithm, sha512, exists', true);
}

if(!function_exists('openssl_random_pseudo_bytes')) {
	result('Secure random source', 'Preferred source, openssl_random_pseudo_bytes, does not exist', false);
} else {
	result('Secure random source', 'Preferred source, openssl_random_pseudo_bytes, exists', true);
}

if((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) {
	result('Encryption', 'HTTPS is enabled', true);
} else {
	result('Encryption', 'HTTPS is disabled; passwords will be sent in plaintext', false);
}

function result($name, $desc, $status) {
	if($status) {
		echo '<tr bgcolor="#90EE90">';
		echo '<td>Good</td>';
	} else {
		echo '<tr bgcolor="#FF6347">';
		echo '<td>Error</td>';
	}
	
	echo "<td>$name</td>";
	echo "<td>$desc</td>";
	echo '</tr>';
}

?>

</table>
</body>
</html>

