<?php

// array of editable ghost.cfg parameters and their type
// this does not include bnet parameters
// key => (type, default, default extra, description)
// types:
//  0: string
//  1: integer
//  2: boolean
//  3: select from choices
// if 3, default extra is array of value => value description; otherwise 0
$minecraftParameters = array(
	'allow_flight' => array(2, 0, 0, 'Allow flight in survival mode if mod supplying flight is enabled'),
	'allow_nether' => array(2, 1, 0, 'Allow players to travel to the Nether'),
	'difficulty' => array(3, 1, array(0 => 'Peaceful', 1 => 'Easy', 2 => 'Normal', 3 => 'Hard'), 'Difficulty of the server'),
	'enable-query' => array(2, 0, 0, 'Enable GameSpy4 protocol server listener'),
	'enable-query' => array(2, 0, 0, 'Enable remote access to the server console'),
	'enable-command-block' => array(2, 0, 0, 'Enable command blocks'),
	'gamemode' => array(3, 0, array(0 => 'Survival', 1 => 'Creative', 2 => 'Adventure'), 'Mode of gameplay'),
	'generate-structures' => array(2, 1, 0, 'Defines whether structures will be generated'),
	'hardcore' => array(2, 0, 0, 'Permanently ban players on death'),
	'level-name' => array(0, 'world', 0, 'The world name and its folder name'),
	'level-seed' => array(0, '', 0, 'Seed for your world, as in singleplayer'),
	'level-type' => array(0, 'DEFAULT', array('DEFAULT' => 'standard world: hills, valleys, water, etc.', 'FLAT' => 'flat, featureless world :(', 'LARGEBIOMES' => 'like default but with larger biomes'), 'Determines type of map that is generated'),
	'max-build-height' => array(1, 256, 0, 'Maximum allowed building height'),
	'motd' => array(0, 'Minecraft server with uxpanel', 0, 'Message displayed in client server list below name; max 59 characters'),
	'online-mode' => array(2, 1, 0, 'Enforces account database'),
	'pvp' => array(2, 1, 0, 'Enable PvP on server'),
	'snooper-enabled' => array(2, 1, 0, 'Whether the server sends snoop data regularly'),
	'spawn-animals' => array(2, 1, 0, 'Enable animals to spawn'),
	'spawn-monsters' => array(2, 1, 0, 'Enable monsters to spawn'),
	'spawn-npcs' => array(2, 1, 0, 'Enable non-player characters to spawn'),
	'spawn-protection' => array(1, 16, 0, 'Radius of the spawn protection'),
	'texture-pack' => array(0, '', 0, 'Prompt client to download this texture pack on join'),
	'view-distance' => array(1, 10, 0, 'Amount of world data the server sends the client, measured in chunks in each direction of the player'),
	'white-list' => array(2, 0, 0, 'Enable server whitelist')
	);

// array of non-editable Minecraft parameters
$defaultMinecraftParameters = array(
	'max-players' => '20',
	'query.port' => '25565',
	'rcon.password' => '',
	'rcon.port' => '25575',
	'server-ip' => '',
	'server-port' => ''
	);

$minecraftUpdatableFiles = array('banned-ips.txt', 'banned-players.txt', 'ops.txt', 'white-list.txt');

//get additional parameters from configuration

if(isset($config['minecraftParameters'])) {
	$minecraftParameters = array_merge($minecraftParameters, $config['minecraftParameters']);
}

if(isset($config['defaultMinecraftParameters'])) {
	$defaultMinecraftParameters = array_merge($defaultMinecraftParameters, $config['defaultMinecraftParameters']);
}

if(isset($config['minecraftUpdatableFiles'])) {
	$minecraftUpdatableFiles = array_merge($minecraftUpdatableFiles, $config['minecraftUpdatableFiles']);
}

require_once(includePath() . "/jail.php");

//escapes function in configuration file
function minecraftEscape($type, $default, $type_extra, $value) {
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
function minecraftAddService($account_id, $service_name, $service_description, $identifier) {
	global $config;
	
	$identifier = stripAlphaNumeric($identifier);
	
	//set target directory
	$directory = $config['minecraft_path'] . $identifier . '/';
	
	if(file_exists($directory)) {
		return "the target directory $directory already exists!";
	}
	
	//register database
	$service_id = createService($account_id, $service_name, $service_description, "minecraft", array('id' => $identifier, 'memory' => '1024'));
	
	if($service_id > 999 && $id3 == -1) {
		return "Error: exceeded the maximum three-digit ID!";
	} else if($id3 == -1) {
		$id3 = $service_id;
	}
	
	//create target directory
	mkdir($directory, 0700);
	
	//create properties file
	$fh = fopen($directory . 'server.properties', 'w');
	
	foreach($GLOBALS['defaultMinecraftParameters'] as $key => $value) {
		fwrite($fh, "$key=$value\n");
	}
	
	foreach($GLOBALS['minecraftParameters'] as $key => $array) {
		fwrite($fh, "$key={$array[1]}\n");
	}
	
	fclose($fh);
	
	return $service_id;
}

//returns array('status' => status string, 'err' => array(error strings), 'color' => suggested color)
function minecraftGetStatus($service_id) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return array('status' => "ERROR: failed to find bot identifier", 'err' => array(), 'color' => 'red');
	}
	
	//read last lines of the log file and scan for interesting things
	$lines = minecraftGetLog($service_id, 1000);
	
	if($lines === false) {
		return array('status' => "Failed to read log file", 'err' => array(), 'color' => 'red');
	}
	
	$lastline = $lines[count($lines) - 1];
	$errors = array();
	$status = "Up, no activity";
	$color = "orange";
	
	//scan lines for interesting things
	foreach($lines as $line) {
		# check if user has joined game recently
		if(strpos($line, 'joined') !== false) {
			$status = "Good";
			$color = "green";
		}
	}
	
	# check last line to see if the bot is still running
	$firstSpace = strpos($lastline, ' ');
	
	if($firstSpace !== false) {
		$secondSpace = strpos($firstSpace, ' ', $firstSpace);
		
		if($secondSpace !== false) {
			$strTime = substr($lastline, 0, $secondSpace);
			$time = strtotime($strTime);
			
			if(time() - $time > 1200) {
				$status = "Down";
				$errors[] = "Does not appear to be running!";
				$color = "red";
			}
		}
	}
	
	return array('status' => $status, 'err' => $errors, 'color' => $color);
}

//returns config array (k => v) on success, or false on failure
//if skip is set, it will skip parameters not in channelParameters
function minecraftGetConfiguration($service_id, $skip = true) {
	global $config, $minecraftParameters;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return false;
	}
	
	//we ignore some settings to allow panel administrator to restrict configurations
	// on a per-user basis; this is a space-separated list of configuration keys
	$ignoreKeys = getServiceParam($service_id, "ignorekeys");
	
	if($ignoreKeys === false) {
		$ignoreKeys = array();
	} else {
		$ignoreKeys = explode(" ", $ignoreKeys);
	}
	
	//similarly, we add some extra keys
	$extraKeys = getServiceParam($service_id, "extrakeys");
	
	if($extraKeys === false) {
		$extraKeys = array();
	} else {
		$extraKeys = explode(" ", $extraKeys);
	}
	
	$jail = jailEnabled($service_id);
	if($jail) {
		jailFileOpen($service_id, "minecraft", "server.properties");
	}
	
	//read the configuration file
	$fh = fopen($config['minecraft_path'] . $id . "/server.properties", 'r');
	$array = array();
	
	while(($buffer = fgets($fh, 4096)) !== false) {
		$buffer = trim($buffer);
		
		if(strlen($buffer) > 3 && $buffer[0] != '#') {
			$index = strpos($buffer, "=");
			
			if($index !== false) {
				$key = trim(substr($buffer, 0, $index));
				$val = "";
				
				if(strlen($buffer) > $index + 1) {
					$val = trim(substr($buffer, $index + 1));
				}
				
				if(!$skip || ((isset($minecraftParameters[$key]) || in_array($key, $extraKeys)) && !in_array($key, $ignoreKeys))) {
					$array[$key] = $val;
				}
			}
		}
	}
	
	fclose($fh);
	
	if($jail) {
		jailFileClose($service_id, "minecraft", "server.properties", false);
	}
	
	return $array;
}

function minecraftConfigurationComparatorHelper($x) {
	global $minecraftParameters;
	
	if(isset($minecraftParameters[$x])) {
		return indexInArray($x, $minecraftParameters);
	} else {
		//put it at bottom
		return 99999;
	}
}

//determines order in configuration things should go
function minecraftConfigurationComparator($a, $b) {
	return minecraftConfigurationComparatorHelper($a) - minecraftConfigurationComparatorHelper($b);
}

//returns true on success, false on failure
//if force is set, all keys will be written whether or not they are in minecraftParameters
function minecraftReconfigure($service_id, $array, $force = false) {
	global $config, $minecraftParameters;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return false;
	}
	
	//get the existing configuration
	$minecraftConfiguration = minecraftGetConfiguration($service_id, false);
	
	//modify the configuration based on input $array settings
	foreach($array as $k => $v) {
		if(isset($minecraftParameters[$k])) {
			$minecraftConfiguration[$k] = minecraftEscape($minecraftParameters[$k][0], $minecraftParameters[$k][1], $minecraftParameters[$k][2], $v);
		} else if(in_array($k, $extraKeys) || $force) {
			$minecraftConfiguration[$k] = minecraftEscape(0, 0, 0, $v);
		}
	}
	
	//sort the configuration intelligently
	uksort($minecraftConfiguration, 'minecraftConfigurationComparator');
	
	//write the configuration out
	$fout = fopen($config['minecraft_path'] . $id . "/server.properties", 'w');
	
	foreach($minecraftConfiguration as $k => $v) {
		fwrite($fout, "$k = $v\n");
	}
	
	fclose($fout);
	
	$jail = jailEnabled($service_id);
	if($jail) {
		jailFileClose($service_id, "minecraft", "server.properties", true);
	}
	
	return true;
}

//returns array of key => value
function minecraftGetConfigFromRequest(&$parameters, &$request) {
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

//returns string on failure or true on success
function minecraftUpdateFile($service_id, $filename, $content) {
	global $config, $minecraftUpdatableFiles;
	
	//limit writable length
	if(strlen($content) > 100000) {
		return;
	}
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return;
	}
	
	$pass = in_array($filename, $minecraftUpdatableFiles);
	$target = $config['minecraft_path'] . $id . "/" . $filename;
	
	if($pass) {
		$content = str_replace("\r", "", $content);
		$fout = fopen($target, 'w');
		fwrite($fout, $content);
		fclose($fout);
		
		$jail = jailEnabled($service_id);
		if($jail) {
			jailFileClose($service_id, "minecraft", $filename, true);
		}
	}
}

//returns string file content
function minecraftDisplayFile($service_id, $filename) {
	global $config, $minecraftUpdatableFiles;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return "Error: failed to load file!";
	}
	
	if(in_array($filename, $minecraftUpdatableFiles)) {
		$jail = jailEnabled($service_id);
		if($jail) {
			jailFileOpen($service_id, "minecraft", $filename);
		}
		
		$str = file_get_contents($config['minecraft_path'] . $id . "/" . $filename);
		
		if($jail) {
			jailFileClose($service_id, "minecraft", $filename, false);
		}
		
		return $str;
	} else {
		return "Error: failed to load file!";
	}
}

//returns false if failed to read log, or array of lines on success
function minecraftGetLog($service_id, $numlines = 400) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return "Error: the identifier for this service is not set.";
	}
	
	$log_file = $config['minecraft_path'] . $id . '/server.log';
	
	$jail = jailEnabled($service_id);
	
	if(($jail && !jailFileExists($service_id, "server.log")) || (!$jail && !file_exists($log_file))) {
		return false;
	}
	
	//read last lines of the log file
	$output_array = array();
	
	if($jail) {
		jailExecute($service_id, "tail -n 1000 " . escapeshellarg(jailPath($service_id) . "server.log"), $output_array);
	} else {
		exec("tail -n 1000 " . escapeshellarg($log_file), $output_array);
	}
	
	return $output_array;
}

//true on success, string error on failure
function minecraftStart($service_id) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return "Error: failed to find identifier. Perhaps this isn't a minecraft service?";
	}
	
	//check if the bot is already started
	$pid = getServiceParam($service_id, "pid");
	
	if($pid !== false && $pid != 0) {
		return "Error: the server is already online.";
	}
	
	//make sure we didn't start too recently to prevent Battle.net flooding
	//** eh... maybe remove this because... it's minecraft server? :P
	$last_time = getServiceParam($service_id, "start_time2");
	
	if($last_time !== false && $last_time != 0 && time() - $last_time < 900) {
		return "Error: please wait ten minutes between starting or restarting the server.";
	}
	
	//start the bot
	$jail = jailEnabled($service_id);
	$memoryLimit = getServiceParam($service_id, "memory");
	
	if($memoryLimit === false) {
		$memoryLimit = 1024;
	} else {
		$memoryLimit = intval($memoryLimit);
	}
	
	if($jail) {
		$pid = jailExecuteBackground($service_id, "cd " . escapeshellarg(jailPath($service_id)) . " && nohup java -jar -Xmx{$memoryLimit}M minecraft.jar > /dev/null 2>&1 & echo $!");
	} else {
		$pid = execBackground("cd " . escapeshellarg($config['minecraft_path'] . $id) . " && nohup java -jar -Xmx{$memoryLimit}M minecraft.jar > /dev/null 2>&1 & echo $!");
	}
	
	//save the pid and last start time
	setServiceParam($service_id, "pid", $pid);
	setServiceParam($service_id, "start_time2", getServiceParam($service_id, "start_time"));
	setServiceParam($service_id, "start_time", time());
	
	return true;
}

//if restart is set, it will:
// a) allow stopping the bot even if nostop flag is set
// b) ignore warning if bot is already offline according to PID
function minecraftStop($service_id, $restart = false) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return "Error: failed to find identifier. Perhaps this isn't a minecraft service?";
	}
	
	//make sure we are allowed to stop the bot
	if(!$restart) {
		$nostop = getServiceParam($service_id, "nostop");
	
		if($nostop) {
			return "Error: you are not allowed to stop this server. Use restart instead.";
		}
	}
	
	//get the pid
	$pid = stripAlphaNumeric(getServiceParam($service_id, "pid"));
	
	if($pid === false || $pid == 0) {
		if($restart) {
			return true;
		} else {
			return "Error: the server is already offline.";
		}
	}
	
	//stop the bot
	$jail = jailEnabled($service_id);
	if($jail) {
		jailExecute($service_id, "kill -s INT $pid");
	} else {
		//make sure PID is still of pychop
		$result = exec("cat /proc/$pid/cmdline");
		
		if(stripos($result, 'minecraft') !== false) {
			exec("kill -s INT $pid");
		}
	}
	
	//reset the pid
	setServiceParam($service_id, "pid", 0);
	
	return true;
}

function minecraftRestart($service_id) {
	$result = minecraftStop($service_id, true);
	
	if($result === true) {
		sleep(1);
		return minecraftStart($service_id);
	} else {
		return $result;
	}
}

//STYLE FUNCTIONS
function minecraftDisplayConfiguration($k, $v, $parameters) {
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
