<?php

// array of editable chop.cfg parameters and their type
// key => (type, default, default extra, description)
// types:
//  0: string
//  1: integer
//  2: boolean
//  3: select from choices
// if 3, default extra is array of value => value description; otherwise 0
$channelParameters = array(
	'bot_whisperallowed' => array(2, 1, 0, 'Interpret whispered commands'),
	'bot_follow' => array(3, 1, array(0 => 'Disable following', 1 => 'Enable for clan members only', 2 => 'Follow all users'), 'Follow feature can display the game users join after they leave the channel'),
	'bot_disablepublic' => array(2, 1, 0, 'Disable commands for users with no access'),
	'bot_disablebanned' => array(2, 1, 0, 'Disable commands for banned users'),
	'bot_displaynoaccess' => array(2, 0, 0, 'Tell users with access > 0 when they cannot use a command'),
	'bot_banlistchannel' => array(2, 0, 0, 'Ban !banned users from the channel when they join'),
	'op_antispam' => array(2, 0, 0, 'Enable core plugin: kick users who spam'),
	'op_antiyell' => array(2, 0, 0, 'Enable core plugin: kick users who yell (all caps)'),
	'bnet_tft' => array(3, 1, array(0 => 'RoC', 1 => 'TFT'), 'Whether to authenticate as RoC or TFT'),
	'bnet_cdkey_roc' => array(0, '', 0, 'The Reign of Chaos CD key'),
	'bnet_cdkey_tft' => array(0, '', 0, 'The Frozen Throne CD key'),
	'bnet_username' => array(0, '', 0, 'Realm username'),
	'bnet_password' => array(0, '', 0, 'Realm password'),
	'bnet_firstchannel' => array(0, 'The Void', 0, 'Realm default channel'),
	'bnet_commandtrigger' => array(0, '!', 0, 'Command trigger'),
	'bnet_rootadmin' => array(0, '', 0, 'Root administrator (only one root admin allowed)'),
	'bnet_custom_war3version' => array(0, '26', 0, 'Warcraft III version to authenticate with'),
	'bnet_custom_exeversion' => array(0, '', 0, 'PvPGN setting; leave blank if using Battle.net'),
	'bnet_custom_exeversionhash' => array(0, '', 0, 'PvPGN setting; leave blank if using Battle.net'),
	'bnet_custom_passwordhashtype' => array(0, '', 0, 'PvPGN setting; leave blank if using Battle.net')
	);

// array of non-editable chop.cfg parameters
$defaultChannelParameters = array(
	'bot_log' => 'chop.log',
	'bot_language' => 'language.cfg',
	'bot_war3path' => '/usr/lib/',
	'bot_bindaddress' => '',
	'bot_cfgpath' => 'cfg/',
	'bot_pluginpath' => '.',
	'bot_seenallusers' => '0',
	'op_phrasekick' => '0',
	'op_spam_cachesize' => '4',
	'db_server' => 'localhost',
	'db_database' => '',
	'db_user' => '',
	'db_password' => '',
	'db_port' => '3306',
	'bnet_server' => ''
	);

$channelPlugins = array('accesskick', 'afk', 'alarm', 'announce', 'calc', 'chanstats', 'clanactivity', 'clanmembers', 'copycat', 'dbstats', 'gamequeue', 'getgames', 'getgames-whois', 'gettime', 'greet', 'inactive', 'inviteme', 'lottery', 'matchmake', 'plugindb', 'pluginman', 'pounce', 'rand', 'randkick', 'randspeed', 'rroulette', 'security', 'snipe', 'trivia', 'votekick', '__init__');

$channelUpdatableFiles = array("language.cfg", "cfg/ask8ball.txt", "cfg/command.txt", "cfg/quote.txt", "cfg/slap_neg.txt", "cfg/slap_pos.txt");

//get additional parameters from configuration

if(isset($config['channelParameters'])) {
	$channelParamaters = array_merge($channelParameters, $config['channelParameters']);
}

if(isset($config['defaultChannelParameters'])) {
	$defaultChannelParameters = array_merge($defaultChannelParameters, $config['defaultChannelParameters']);
}

if(isset($config['channelUpdatableFiles'])) {
	$channelUpdatableFiles = array_merge($channelUpdatableFiles, $config['channelUpdatableFiles']);
}

if(isset($config['channelPlugins'])) {
	$channelPlugins = array_merge($channelPlugins, $config['channelPlugins']);
}

require_once(includePath() . "/jail.php");

//escapes function in configuration file
function channelEscape($type, $default, $type_extra, $value) {
	if($type == 0) {
		//string, just strip newlines
		return str_replace(array("\n", "\r"), array("", ""), $value);
	} else if($type == 1) {
		//integer, convert
		return intval($value);
	} else if($type == 2) {
		if($value == 1 || $value === "true") {
			return 1;
		} else {
			return 0;
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
function channelAddService($account_id, $service_name, $service_description, $identifier) {
	global $config;
	
	$identifier = stripAlphaNumeric($identifier);
	
	//set target directory
	$directory = $config['channel_path'] . $identifier . '/';
	
	if(file_exists($directory)) {
		return "the target directory $directory already exists!";
	}
	
	//register database
	$service_id = createService($account_id, $service_name, $service_description, "channel", array('id' => $identifier));
	
	//create target directory
	mkdir($directory, 0700);
	
	//write settings
	$fh = fopen($directory . 'chop.cfg', 'w');
	
	foreach($GLOBALS['defaultChannelParameters'] as $key => $value) {
		fwrite($fh, "$key = $value\n");
	}
	
	foreach($GLOBALS['channelParameters'] as $key => $array) {
		fwrite($fh, "$key = {$array[1]}\n");
	}
	
	fclose($fh);
	
	//make the subdirectories
	mkdir($directory . "cfg", 0700);
	mkdir($directory . "plugins", 0700);
	mkdir($directory . "plugins/pychop", 0700);
	
	//copy files
	copy($config['channel_path'] . "language.cfg", $directory . "language.cfg");
	copy($config['channel_path'] . "chop++", $directory . "chop++");
	chmod($directory . "chop++", 0700);
	copy($config['channel_path'] . "cfg/ask8ball.txt", $directory . "cfg/ask8ball.txt");
	copy($config['channel_path'] . "cfg/command.txt", $directory . "cfg/command.txt");
	copy($config['channel_path'] . "cfg/quote.txt", $directory . "cfg/quote.txt");
	copy($config['channel_path'] . "cfg/slap_neg.txt", $directory . "cfg/slap_neg.txt");
	copy($config['channel_path'] . "cfg/slap_pos.txt", $directory . "cfg/slap_pos.txt");
	copy($config['channel_path'] . "plugins/__init__.py", $directory . "plugins/__init__.py");
	
	foreach($GLOBALS['channelPlugins'] as $plugin) {
		copy($config['channel_path'] . "plugins/pychop/$plugin.py", $directory . "plugins/pychop/$plugin.py");
	}
	
	return $service_id;
}

//returns array('status' => status string, 'err' => array(error strings), 'color' => suggested color)
function channelGetStatus($service_id) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return array('status' => "ERROR: failed to find bot identifier", 'err' => array(), 'color' => 'red');
	}
	
	//read last lines of the log file and scan for interesting things
	$lines = channelGetLog($service_id, 1000);
	
	if($lines === false) {
		return array('status' => "Failed to read log file", 'err' => array(), 'color' => 'red');
	}
	
	$lastline = $lines[count($lines) - 1];
	$errors = array();
	$status = "Up";
	$color = "green";
	
	//scan lines for interesting things
	foreach($lines as $line) {
		//check if we disconnected from battle.net
		if(strpos($line, 'disconnected from battle.net') !== false) {
			$posBegin = strpos($line, 'BNET: ');
			
			if($posBegin !== false) {
				$realmBegin = $posBegin + 6;
				$posEnd = strpos($line, ']', $posBegin);
				
				if($posEnd !== false) {
					$realm = substr($line, $realmBegin, $posEnd - $realmBegin);
					$error = "Disconnected from bnet/$realm";
				
					//need to ensure this error wasn't added
					// because this could occur multiple times
					if(!in_array($error, $errors)) {
						$errors[] = $error;
					}
				}
			}
		}
		
		# cd keys in use?
		if(strpos($line, 'CD key in use') !== false) {
			$posBegin = strpos($line, 'BNET: ');
			
			if($posBegin !== false) {
				$realmBegin = $posBegin + 6;
				$posEnd = strpos($line, ']', $posBegin);
				
				if($posEnd !== false) {
					$realm = substr($line, $realmBegin, $posEnd - $realmBegin);
					$error = "CD keys in use on bnet/$realm";
				
					//need to ensure this error wasn't added
					// because this could occur multiple times
					if(!in_array($error, $errors)) {
						$errors[] = $error;
					}
				}
			}
		}
	}
	
	# check last line to see if the bot is still running
	$posBegin = strpos($lastline, '[');
	
	if($posBegin !== false) {
		$posEnd = strpos($lastline, ']', $posBegin);
		
		if($posEnd !== false) {
			$strTime = substr($lastline, $posBegin + 1, $posEnd - $posBegin - 1);
			$time = strtotime($strTime);
			
			if(time() - $time > 2400) {
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
function channelGetConfiguration($service_id, $skip = true) {
	global $config, $channelParameters;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return false;
	}
	
	$jail = jailEnabled($service_id);
	if($jail) {
		jailFileOpen($service_id, "channel", "chop.cfg");
	}
	
	//read the configuration file
	$fh = fopen($config['channel_path'] . $id . "/chop.cfg", 'r');
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
				
				if(!$skip || isset($channelParameters[$key])) {
					$array[$key] = $val;
				}
			}
		}
	}
	
	fclose($fh);
	
	if($jail) {
		jailFileClose($service_id, "channel", "chop.cfg", false);
	}
	
	return $array;
}

function channelConfigurationComparatorHelper($x) {
	global $channelParameters;
	
	if(isset($channelParameters[$x])) {
		return indexInArray($x, $channelParameters);
	} else {
		//put it at bottom
		return 99999;
	}
}

//determines order in configuration things should go
function channelConfigurationComparator($a, $b) {
	return channelConfigurationComparatorHelper($a) - channelConfigurationComparatorHelper($b);
}

//returns true on success, false on failure
//if force is set, all keys will be written whether or not they are in channelParameters
function channelReconfigure($service_id, $array, $force = false) {
	global $config, $channelParameters;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return false;
	}
	
	//get the existing configuration
	$channelConfiguration = channelGetConfiguration($service_id, false);
	
	//modify the configuration based on input $array settings
	foreach($array as $k => $v) {
		if(isset($channelParameters[$k])) {
			$channelConfiguration[$k] = channelEscape($channelParameters[$k][0], $channelParameters[$k][1], $channelParameters[$k][2], $v);
		} else if($force) {
			$channelConfiguration[$k] = channelEscape(0, 0, 0, $v);
		}
	}
	
	//sort the configuration intelligently
	uksort($channelConfiguration, 'channelConfigurationComparator');
	
	//write the configuration out
	$fout = fopen($config['channel_path'] . $id . "/chop.cfg", 'w');
	
	foreach($channelConfiguration as $k => $v) {
		fwrite($fout, "$k = $v\n");
	}
	
	fclose($fout);
	
	$jail = jailEnabled($service_id);
	if($jail) {
		jailFileClose($service_id, "channel", "chop.cfg", true);
	}
	
	return true;
}

//returns array of key => value
function channelGetConfigFromRequest(&$parameters, &$request) {
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
function channelUpdateFile($service_id, $filename, $content) {
	global $config, $channelUpdatableFiles;
	
	//limit writable length
	if(strlen($content) > 100000) {
		return;
	}
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return;
	}
	
	$pass = in_array($filename, $channelUpdatableFiles);
	$target = $config['channel_path'] . $id . "/" . $filename;
	
	if($pass) {
		$content = str_replace("\r", "", $content);
		$fout = fopen($target, 'w');
		fwrite($fout, $content);
		fclose($fout);
		
		$jail = jailEnabled($service_id);
		if($jail) {
			jailFileClose($service_id, "channel", $filename, true);
		}
	}
}

//returns string file content
function channelDisplayFile($service_id, $filename) {
	global $config, $channelUpdatableFiles;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return "Error: failed to load file!";
	}
	
	if(in_array($filename, $channelUpdatableFiles)) {
		$jail = jailEnabled($service_id);
		if($jail) {
			jailFileOpen($service_id, "channel", $filename);
		}
		
		$str = file_get_contents($config['channel_path'] . $id . "/" . $filename);
		
		if($jail) {
			jailFileClose($service_id, "channel", $filename, false);
		}
		
		return $str;
	} else {
		return "Error: failed to load file!";
	}
}

//returns false if failed to read log, or array of lines on success
function channelGetLog($service_id, $numlines = 400) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return "Error: the identifier for this service is not set.";
	}
	
	$log_file = $config['channel_path'] . $id . '/chop.log';
	
	$jail = jailEnabled($service_id);
	
	if(($jail && !jailFileExists($service_id, "chop.log")) || (!$jail && !file_exists($log_file))) {
		return false;
	}
	
	//read last lines of the log file
	$output_array = array();
	
	if($jail) {
		jailExecute($service_id, "tail -n 1000 " . escapeshellarg(jailPath($service_id) . "chop.log"), $output_array);
	} else {
		exec("tail -n 1000 " . escapeshellarg($log_file), $output_array);
	}
	
	return $output_array;
}

//true on success, string error on failure
function channelBotStart($service_id) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return "Error: failed to find identifier. Perhaps this isn't a channel service?";
	}
	
	//check if the bot is already started
	$pid = getServiceParam($service_id, "pid");
	
	if($pid !== false && $pid != 0) {
		return "Error: the bot is already online.";
	}
	
	//make sure we didn't start too recently to prevent Battle.net flooding
	$last_time = getServiceParam($service_id, "start_time2");
	
	if($last_time !== false && $last_time != 0 && time() - $last_time < 900) {
		return "Error: please wait ten minutes between starting or restarting the bot.";
	}
	
	//start the bot
	$jail = jailEnabled($service_id);
	
	if($jail) {
		$pid = jailExecute($service_id, "cd " . escapeshellarg(jailPath($service_id)) . " && nohup ./chop++ chop.cfg > /dev/null 2>&1 & echo $!");
	} else {
		$pid = shell_exec("cd " . escapeshellarg($config['ghost_path'] . $id) . " && nohup ./chop++ chop.cfg > /dev/null 2>&1 & echo $!");
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
function channelBotStop($service_id, $restart = false) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return "Error: failed to find identifier. Perhaps this isn't a channel service?";
	}
	
	//make sure we are allowed to stop the bot
	if(!$restart) {
		$nostop = getServiceParam($service_id, "nostop");
	
		if($nostop) {
			return "Error: you are not allowed to stop this bot. Use restart instead.";
		}
	}
	
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
		jailExecute("kill -s INT $pid");
	} else {
		//make sure PID is still of pychop
		$result = exec("cat /proc/$pid/cmdline");
		
		if(stripos($result, 'chop') !== false) {
			exec("kill -s INT $pid");
		}
	}
	
	//reset the pid
	setServiceParam($service_id, "pid", 0);
	
	return true;
}

function channelBotRestart($service_id) {
	$result = channelBotStop($service_id, true);
	
	if($result === true) {
		sleep(1);
		return channelBotStart($service_id);
	} else {
		return $result;
	}
}

function channelSetDatabase($service_id, $db_settings) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return false;
	}
	
	//reconfigure
	channelReconfigure($service_id, array('db_server' => $db_settings['server'], 'db_database' => $db_settings['name'], 'db_user' => $db_settings['username'], 'db_password' => $db_settings['password']));
}

//STYLE FUNCTIONS
function channelDisplayConfiguration($k, $v, $parameters) {
	$form_k = htmlspecialchars("gcform_$k");
	$type = $parameters[$k][0];
	$options = $parameters[$k][2];
	$description = $parameters[$k][3];
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
