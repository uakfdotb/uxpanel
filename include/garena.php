<?php

if(!isset($GLOBALS['IN_UXPANEL'])) {
	die("Access forbidden.");
}

// array of editable gcb.cfg parameters and their type
// key => (type, default, default extra, description)
// types:
//  0: string
//  1: integer
//  2: boolean
//  3: select from choices
// if 3, default extra is array of value => value description; otherwise 0
$garenaParameters = array();

// connection parameters
$garenaConnectionParameters = array(
	'username' => array(0, '', 0, 'Garena username (note: you can use the same username for all rooms)'),
	'password' => array(0, '', 0, 'Garena password'),
	'mainhost' => array(0, 'con3.garenaconnect.com', 0, 'Hostname of the login server'),
	'roomname' => array(0, '', 0, 'Room name; this must be exact (see first column of http://gcb.googlecode.com/svn/trunk/rooms.txt)')
	);

// default parameters
// note that static parameters can be put in the garena_path/gcb.cfg file
$garenaDefaultParameters = array(
	'rcon_port' => '8{ID3}'
	);

//get additional parameters from configuration

if(isset($config['garenaParameters'])) {
	$garenaParameters = array_merge($garenaParameters, $config['garenaParameters']);
}

if(isset($config['garenaConnectionParameters'])) {
	$garenaConnectionParameters = array_merge($garenaConnectionParameters, $config['garenaConnectionParameters']);
}

if(isset($config['garenaDefaultParameters'])) {
	$garenaDefaultParameters = array_merge($garenaDefaultParameters, $config['garenaDefaultParameters']);
}

require_once(includePath() . "/jail.php");

//escapes function in configuration file
function garenaEscape($type, $default, $type_extra, $value) {
	if($type == 0) {
		//string, just strip newlines
		return str_replace(array("\n", "\r"), array("", ""), $value);
	} else if($type == 1) {
		//integer, convert
		return intval($value);
	} else if($type == 2) {
		if($value == 1 || $value === "true") {
			return "true";
		} else {
			return "false";
		}
	} else if($type == 3) {
		if(isset($type_extra[$value])) {
			return $value;
		} else {
			return $default;
		}
	}
}

//integer: success, service id
//string: error message
function garenaAddService($account_id, $service_name, $service_description, $identifier, $id3 = -1) {
	global $config, $garenaDefaultParameters;
	
	$identifier = stripAlphaNumeric($identifier);
	
	//set target directory
	$directory = $config['garena_path'] . $identifier . '/';
	
	if(file_exists($directory)) {
		return "the target directory $directory already exists!";
	}
	
	//register database
	$service_id = createService($account_id, $service_name, $service_description, "garena", array('id' => $identifier));
	
	if($service_id > 999 && $id3 == -1) {
		return "Error: exceeded the maximum three-digit ID!";
	} else if($id3 == -1) {
		$id3 = $service_id;
	}
	
	//create parameters needed later
	$id3 = $id3 . "";
	while(strlen($id3) < 3) {
		$id3 = "0" . $id3;
	}
	
	setServiceParam($service_id, 'id3', $id3);
	
	//create target directory
	mkdir($directory, 0700);
	
	//create configuration file
	copy($config['garena_path'] . "gcb.cfg", $directory . "gcb.cfg");
	$rewrite = array();
	
	foreach($garenaDefaultParameters as $key => $value) {
		$value = str_replace("{ID3}", $id3, $value);
		$rewrite[$key] = $value;
	}
	
	garenaReconfigure($service_id, $rewrite);
	
	//link files
	symlink($config['garena_path'] . "gkey.pem", $directory . "gkey.pem");
	symlink($config['garena_path'] . "gcb.jar", $directory . "gcb.jar");
	symlink($config['garena_path'] . "gcbrooms.txt", $directory . "gcbrooms.txt");
	symlink($config['garena_path'] . "lib", $directory . "lib");
	
	//make the subdirectories
	mkdir($directory . "log", 0700);
	mkdir($directory . "plugins", 0700);
	
	return $service_id;
}

//returns array('status' => status string, 'err' => array(error strings), 'color' => suggested color)
function garenaGetStatus($service_id) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return array('status' => "ERROR: failed to find bot identifier", 'err' => array(), 'color' => 'red');
	}
	
	//read last lines of the log file and scan for interesting things
	$lines = garenaGetLog($service_id, 1000);
	
	if(empty($lines)) {
		return array('status' => "Failed to read log file", 'err' => array(), 'color' => 'red');
	}
	
	$lastline = $lines[count($lines) - 1];
	$errors = array();
	$status = "Up, no activity";
	$color = "orange";
	
	//scan lines for interesting things
	foreach($lines as $line) {
		# check if user has joined game recently
		if(strpos($line, 'Starting TCP') !== false) {
			$status = "Good";
			$color = "green";
		}
	}
	
	# check last line to see if the bot is still running
	$posBegin = strpos($lastline, '[');
	
	if($posBegin !== false) {
		$posEnd = strpos($lastline, ']', $posBegin);
		
		if($posEnd !== false) {
			$strTime = substr($lastline, $posBegin + 1, $posEnd - $posBegin - 1);
			$time = strtotime($strTime);
			$time2 = strtotime($strTime . " UTC"); //sometimes gcb output might not be system timezone
			
			if(abs(time() - $time) > 1200 && abs(time() - $time2) > 1200) {
				$status = "Down";
				$errors[] = "Does not appear to be running!";
				$color = "red";
			}
		}
	}
	
	return array('status' => $status, 'err' => $errors, 'color' => $color);
}

function garenaGetParameters($service_id) {
	global $garenaParameters;
	$parameters = $garenaParameters;
	
	//we ignore some settings to allow panel administrator to restrict configurations
	// on a per-user basis; this is a space-separated list of configuration keys
	$ignoreKeys = getServiceParam($service_id, "ignorekeys");
	
	if($ignoreKeys === false) {
		$ignoreKeys = array();
	} else {
		$ignoreKeys = explode(" ", $ignoreKeys);
		
		foreach($ignoreKeys as $key) {
			if(isset($parameters[$key])) {
				unset($parameters[$key]);
			}
		}
	}
	
	//similarly, we add some extra keys
	$extraKeys = getServiceParam($service_id, "extrakeys");
	
	if($extraKeys === false) {
		$extraKeys = array();
	} else {
		$extraKeys = explode(" ", $extraKeys);
		
		foreach($extraKeys as $key) {
			if(!isset($parameters[$key])) {
				$parameters[$key] = array(0, '', 0, '');
			}
		}
	}
	
	return $parameters;
}

//returns config array (k => v) on success, or false on failure
//if skip is set, it will skip parameters not in garenaParameters
function garenaGetConfiguration($service_id, $skip = true) {
	global $config;
	$parameters = garenaGetParameters($service_id);
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return false;
	}
	
	//read the configuration file
	$jail = jailEnabled($service_id);
	if($jail) {
		jailFileOpen($service_id, "garena", "gcb.cfg");
	}
	
	$fh = fopen($config['garena_path'] . $id . "/gcb.cfg", 'r');
	$array = array();
	
	while(($buffer = fgets($fh, 4096)) !== false) {
		$buffer = trim($buffer);
		
		if(strlen($buffer) > 3 && $buffer[0] != '#') {
			$index = strpos($buffer, " =");
			
			if($index !== false) {
				$key = trim(substr($buffer, 0, $index));
				$val = "";
				
				if(strlen($buffer) > $index + 3) {
					$val = trim(substr($buffer, $index + 3));
				}
				
				if(!$skip || isset($parameters[$key])) {
					$array[$key] = $val;
				}
			}
		}
	}
	
	fclose($fh);
	
	if($jail) {
		jailFileClose($service_id, "garena", "gcb.cfg", false);
	}
	
	return $array;
}

//returns false if key is not connection, or array('id' => connection id, 'key' => subkey) if it is
function garenaConfigurationConnectionKey($key) {
	if(substr($key, 0, 6) == "garena" && ($index = strpos($key, "_")) !== false) {
		$connection_id = intval(substr($key, 6, $index - 6));
		
		if($connection_id == "") {
			$connection_id = 0;
		}
		
		$subkey = substr($key, $index + 1);
		
		return array('id' => $connection_id, 'key' => $subkey);
	} else {
		return false;
	}
}

//returns array (connection id => room name) on success, or false on failure
function garenaGetConnection($service_id) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return false;
	}
	
	//process the configuration
	$configuration = garenaGetConfiguration($service_id, false);
	$array = array();
	
	foreach($configuration as $k => $v) {
		if(($ckey_info = garenaConfigurationConnectionKey($k)) !== false) {
			if($ckey_info['key'] == "roomname") {
				$array[$ckey_info['id']] = $v;
			}
		}
	}
	
	//sort by id
	ksort($array);
	
	return $array;
}

//returns true on success or string error on failure
function garenaAddConnection($service_id, $roomname) {
	global $garenaConnectionParameters;
	
	//get next connection id
	$connections = garenaGetConnection($service_id);
	
	if($connections === false) {
		return "Error: failed to find identifier. Perhaps this isn't a Garena service?";
	}
	
	$next_connection_id = 1;
	
	foreach($connections as $k => $v) {
		if($k >= $next_connection_id) {
			$next_connection_id = $k + 1;
		}
	}
	
	//check if user has exceeded maximum permitted connections
	$connection_limit = getServiceParam($service_id, "climit");
	
	if($connection_limit !== false && $next_connection_id > $connection_limit) {
		return "Error: too many Garena room connections. Contact support.";
	}
	
	//decide what the prestring is
	$prestring = "garena{$next_connection_id}_";
	
	//add the connection id
	$array = array();
	
	foreach($garenaConnectionParameters as $k => $p_info) {
		if($k == "roomname") {
			$array[$prestring . $k] = $roomname;
		} else {
			$array[$prestring . $k] = $p_info[1];
		}
	}
	
	garenaReconfigure($service_id, $array);
	
	return true;
}

//returns true on success or false on failure
function garenaRemoveConnection($service_id, $connection_id) {
	global $garenaConnectionParameters;
	
	//decide what the prestring is
	$prestring = "garena{$connection_id}_";
	
	//get config options
	$array = array();
	
	foreach($garenaConnectionParameters as $k => $v) {
		$array[$prestring . $k] = $v;
	}
	
	//delete from the current config
	garenaReconfigure($service_id, $array, true);
	
	return true;
}

//returns config array (k => v) on success, or false on failure
function garenaGetConnectionConfiguration($service_id, $connection_id) {
	global $config, $garenaConnectionParameters;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return false;
	}
	
	//process the configuration
	$configuration = garenaGetConfiguration($service_id, false);
	$array = array();
	
	foreach($configuration as $k => $v) {
		if(($ckey_info = garenaConfigurationConnectionKey($k)) !== false && $ckey_info['id'] == $connection_id && isset($garenaConnectionParameters[$ckey_info['key']])) {
			$array[$ckey_info['key']] = $v;
		}
	}
	
	return $array;
}

//returns garenaReconfigure() result
function garenaReconfigureConnection($service_id, $connection_id, $array) {
	//transform the array's keys to global keys that include connection prefix
	$prefix = "garena{$connection_id}_";
	
	$globalArray = array();
	
	foreach($array as $k => $v) {
		$globalArray[$prefix . $k] = $v;
	}
	
	return garenaReconfigure($service_id, $globalArray);
}

function garenaConfigurationComparatorHelper($x) {
	global $garenaParameters, $garenaConnectionParameters;
	
	if(isset($garenaParameters[$x])) {
		return indexInArray($x, $garenaParameters);
	} else if(($ckey_info = garenaConfigurationConnectionKey($x)) !== false) {
		$connection_id = $ckey_info['id'];
		$subkey = $ckey_info['key'];
		
		if(isset($garenaConnectionParameters[$subkey])) {
			return $connection_id * 1000 + indexInArray($subkey, $garenaConnectionParameters);
		} else {
			return $connection_id * 1000 + 999;
		}
	} else {
		//put it at bottom
		return 99999;
	}
}

//determines order in configuration things should go
function garenaConfigurationComparator($a, $b) {
	return garenaConfigurationComparatorHelper($a) - garenaConfigurationComparatorHelper($b);
}

//returns true on success, false on failure
//if remove is set, the keys of array will be removed instead of added
function garenaReconfigure($service_id, $array, $remove = false) {
	global $config, $garenaConnectionParameters;
	$parameters = garenaGetParameters($service_id);
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return false;
	}
	
	//get the existing configuration
	$garenaConfiguration = garenaGetConfiguration($service_id, false);
	
	//modify the configuration based on input $array settings
	foreach($array as $k => $v) {
		if(!$remove) {
			if(isset($parameters[$k])) {
				$garenaConfiguration[$k] = garenaEscape($parameters[$k][0], $parameters[$k][1], $parameters[$k][2], $v);
			} else if(($ckey_info = garenaConfigurationConnectionKey($k)) !== false && isset($garenaConnectionParameters[$ckey_info['key']])) {
				$subkey = $ckey_info['key'];
				$garenaConfiguration[$k] = garenaEscape($garenaConnectionParameters[$subkey][0], $garenaConnectionParameters[$subkey][1], $garenaConnectionParameters[$subkey][2], $v);
			}
		} else {
			if(isset($garenaConfiguration[$k])) {
				unset($garenaConfiguration[$k]);
			}
		}
	}
	
	//sort the configuration intelligently
	uksort($garenaConfiguration, 'garenaConfigurationComparator');
	
	//re-order the configuration so that the connection id's start from 1 and go up incrementally
	$curr_connection_id = 0; //the connection id counter
	$seen_connection_id = -1; //the last seen connection id from the input
	$reorderedConfiguration = array();
	
	foreach($garenaConfiguration as $k => $v) {
		if(($ckey_info = garenaConfigurationConnectionKey($k)) !== false) {
			$connection_id = $ckey_info['id'];
			$subkey = $ckey_info['key'];
			
			if($connection_id != $seen_connection_id) {
				$curr_connection_id++;
				$seen_connection_id = $connection_id;
			}
			
			if($connection_id != $curr_connection_id) {
				$k = "garena{$curr_connection_id}_$subkey";
			}
		}
		
		$reorderedConfiguration[$k] = $v;
	}
	
	//sort the configuration intelligently again, just in case?
	uksort($reorderedConfiguration, 'garenaConfigurationComparator');
	
	//write the configuration out
	$fout = fopen($config['garena_path'] . $id . "/gcb.cfg", 'w');
	
	foreach($reorderedConfiguration as $k => $v) {
		fwrite($fout, "$k = $v\n");
	}
	
	fclose($fout);
	
	$jail = jailEnabled($service_id);
	if($jail) {
		jailFileClose($service_id, "garena", "gcb.cfg", true);
	}
	
	return true;
}

//returns array of key => value
function garenaGetConfigFromRequest(&$parameters, &$request) {
	$array = array();
	
	foreach($parameters as $k => $param) { //param is array(type, default, default extra, description)
		$form_k = "gcform_$k";
		
		//strings, integers, and select: add if it's set
		if(($param[0] == 0 || $param[0] == 1 || $param[0] == 3) && isset($request[$form_k])) {
			$array[$k] = $request[$form_k];
		}
		
		//boolean variables: always set, and base on checkbox
		else if($param[0] == 2) {
			$array[$k] = isset($request[$form_k]) ? 1 : 0;
		}
	}
	
	return $array;
}

//returns false if failed to read log, or array of lines on success
function garenaGetLog($service_id, $numlines = 400) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return "Error: the identifier for this service is not set.";
	}
	
	$log_file = $config['garena_path'] . $id . '/gcb.log';
	
	$jail = jailEnabled($service_id);
	
	if(($jail && !jailFileExists($service_id, "gcb.log")) || (!$jail && !file_exists($log_file))) {
		return false;
	}
	
	//read last lines of the log file
	$output_array = array();
	
	if($jail) {
		jailExecute($service_id, "tail -n 1000 " . escapeshellarg(jailPath($service_id) . "gcb.log"), $output_array);
	} else {
		exec("tail -n 1000 " . escapeshellarg($log_file), $output_array);
	}
	
	return $output_array;
}

//gets the log quickly for AJAX updates
//database calls are eliminated by caching the "id" service parameter
// (requires reload if id changes)
//file is loaded quickly using tail + grep combination, based on the last line received
function garenaGetLogFast($service_id, $last_line) {
	global $config;
	
	//sanity check on input
	if(strlen($last_line) > 2048) {
		return false;
	}
	
	//get the identifier
	if(!isset($_SESSION[$service_id . '_getlogfast_id'])) {
		$_SESSION[$service_id . '_getlogfast_id'] = stripAlphaNumeric(getServiceParam($service_id, "id"));
	}
	
	$id = $_SESSION[$service_id . '_getlogfast_id'];
	$log_file = $config['garena_path'] . $id . '/gcb.log';
	$jail = jailEnabled($service_id);
	
	if(($jail && !jailFileExists($service_id, "gcb.log")) || (!$jail && !file_exists($log_file))) {
		return false;
	}
	
	//read last lines of the log file that client hasn't received yet
	$output_array = array();
	
	if($jail) {
		jailExecute($service_id, "tail -n 1000 " . escapeshellarg(jailPath($service_id) . "gcb.log") . " | tac | fgrep -B 1000 -m 1 " . escapeshellarg($last_line) . " | tac", $output_array);
	} else {
		exec("tail -n 1000 " . escapeshellarg($log_file) . " | tac | fgrep -B 1000 -m 1 " . escapeshellarg($last_line) . " | tac", $output_array);
	}
	
	if(count($output_array) <= 1) {
		return false;
	} else {
		array_shift($output_array);
		return $output_array;
	}
}

//true on success, string error on failure
function garenaStart($service_id) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return "Error: failed to find identifier. Perhaps this isn't a Garena service?";
	}
	
	//check if gcb is already started
	$pid = getServiceParam($service_id, "pid");
	
	if($pid !== false && $pid != 0) {
		return "Error: the Garena connection software is already online.";
	}
	
	//start gcb
	$jail = jailEnabled($service_id);
	
	if($jail) {
		$pid = jailExecuteBackground($service_id, "cd " . escapeshellarg(jailPath($service_id)) . " && nohup java -jar -Xmx90m gcb.jar > /dev/null 2>&1 & echo $!");
	} else {
		$pid = execBackground("cd " . escapeshellarg($config['garena_path'] . $id) . " && nohup java -jar -Xmx90m gcb.jar 2>&1 >> " . escapeshellarg($config['garena_path'] . $id . "/gcb.log") . " & echo $!");
	}
	
	//save the pid and last start time
	setServiceParam($service_id, "pid", $pid);
	
	return true;
}

//if restart is set, ignore warning if bot is already offline according to PID
function garenaStop($service_id, $force = false, $restart = false) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return "Error: failed to find identifier. Perhaps this isn't a Garena service?";
	}
	
	if($force) {
		//get the pid
		$pid = stripAlphaNumeric(getServiceParam($service_id, "pid"));
	
		if($pid === false || $pid == 0) {
			if($restart) {
				return true;
			} else {
				return "Error: the bot is already offline.";
			}
		}
	
		//stop the bot
		$jail = jailEnabled($service_id);
		if($jail) {
			jailExecute($service_id, "kill $pid");
		} else {
			//make sure PID is still of garena
			$result = exec("cat /proc/$pid/cmdline");
		
			if(stripos($result, 'java') !== false) {
				exec("kill $pid");
			}
		}
	} else {
		//try to send an rcon stop command
		$config = garenaGetConfiguration($service_id, false);
		$fail = true;
		
		if(isset($config['gcb_rcon']) && isset($config['rcon_password']) && isset($config['rcon_port']) && ($config['gcb_rcon'] == "true" || $config['gcb_rcon'] == "1")) {
			$socket = @fsockopen("localhost", $config['rcon_port'], $errno, $errstr, 5);
			
			if($socket) {
				$status = @fwrite($socket, $config['rcon_password'] . "\n");
				
				if($status !== false) {
					fwrite($socket, "exit nicely\n");
					sleep(1);
					socket_close($socket);
					$fail = false;
				}
			}
		}
		
		//if we failed to send, then force stop it
		if($fail) {
			garenaStop($service_id, true, $restart);
		}
	}
	
	//reset the pid
	setServiceParam($service_id, "pid", 0);
	
	return true;
}

function garenaRestart($service_id) {
	$result = garenaStop($service_id, false, true);
	
	if($result === true) {
		sleep(1);
		return garenaStart($service_id);
	} else {
		return $result;
	}
}

//STYLE FUNCTIONS
function garenaDisplayConfiguration($k, $v, $parameters) {
	$form_k = htmlspecialchars("gcform_$k");
	
	if(isset($parameters[$k])) {
		$type = $parameters[$k][0];
		$options = $parameters[$k][2];
		$description = $parameters[$k][3];
	} else {
		$type = 0;
		$options = 0;
		$description = "";
	}
	?>
	<tr>
		<td><?= htmlspecialchars($k) ?></td>
		<td>
		<? if($type == 0 || $type == 1) { ?>
			<input type="text" name="<?= $form_k ?>" value="<?= htmlspecialchars($v) ?>" style="align:left;" />
		<? } else if($type == 2) {
			$checked = $v == 1 ? " checked" : ""; ?>
			<input type="checkbox" name="<?= $form_k ?>" value="<?= 1 ?>"<?= $checked ?> />
		<? } else if($type == 3) { ?>
			<select name="<?= $form_k ?>">
			<?
			foreach($options as $option => $option_desc) {
				$selected = $v == $option ? " selected" : ""; //determine if this option is selected
				?>
				<option value="<?= htmlspecialchars($option) ?>"<?= $selected ?>><?= htmlspecialchars($option_desc) ?></option>
			<? } ?>
			</select>
		<? } ?>
		</td>
		<td><?= htmlspecialchars($description) ?></td>
	</tr>
	<?
}

?>
