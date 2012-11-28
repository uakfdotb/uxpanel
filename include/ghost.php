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
$ghostParameters = array(
	'bot_tft' => array(3, 1, array(0 => 'RoC', 1 => 'TFT'), 'Whether to authenticate as RoC or TFT'),
	'bot_reconnect' => array(2, 0, 0, 'Whether to enable GProxy++ reconnects'),
	'bot_reconnectwaittime' => array(1, 3, 0, 'The number of minutes allowable to wait for GProxy++ reconnect.'),
	'bot_maxgames' => array(1, 40, 0, 'Maximum number of games bot should host.'),
	'bot_commandtrigger' => array(0, '!', 0, "The in-game command trigger."),
	'bot_virtualhostname' => array(0, '|cFF4080C0GHost', 0, "The virtual host name in lobby"),
	'bot_checkmultipleipusage' => array(2, 1, 0, 'Whether to report multiple IP usage in lobby'),
	'bot_spoofchecks' => array(3, 1, array(0 => 'Disable automatic spoofchecking', 1 => 'Enable auto-spoofcheck on all players', 2 => 'Enable auto-spoofcheck on admins only'),
	'bot_requirespoofchecks' => array(2, 1, 0, 'Whether to require players to spoofcheck before starting the game'),
	'bot_reserveadmins' => array(2, 1, 0, 'Whether to reserve admins (reserved players can alwyas join full games)'),
	'bot_autolock' => array(2, 0, 0, 'Whether to lock the game when the owner joins'),
	'bot_allowdownloads' => array(3, 1, array(0 => 'Disable map downloads', 1 => 'Enable map downloads', 2 => 'Conditional map downloads'), 'Whether to allow map downloads'),
	'bot_autokickping' => array(1, 1, 0, 'Maximum ping before a player will be kicked'),
	'bot_lobbytimelimit' => array(1, 10, 0, 'Minutes to allow a game to stay in lobby without an owner'),
	'bot_latency' => array(1, 100, 0, 'Latency to use in-game'),
	'bot_synclimit' => array(1, 50, 0, 'Maximum packets behind before lag screen comes up'),
	'bot_votekickallowed' => array(2, 1, 0, 'Whether to allow votekicks'),
	'bot_votekickpercentage' => array(1, 80, 0, 'Percentage votes needed to pass a votekick'),
	'bot_defaultmap' => array(0, 'wormwar', 0, 'Default map configuration file'),
	'autohost_maxgames' => array(1, 0, 0, 'Maximum number of games for autohosting, or 0 to disable'),
	'autohost_startplayers' => array(1, 0, 0, 'Number of players to wait for autohosting before starting'),
	'autohost_gamename' => array(0, "", 0, 'Gamename to use when autohosting; must be 27 characters or less'),
	'db_mysql_botid' => array(1, 1, 0, 'If you have multiple bots, set this to a unique integer for each bot')
	);

// bnet parameters
$bnetParameters = array(
	'server' => array(0, 'useast.battle.net', 0, 'The Battle.net server to connect to'),
	'serveralias' => array(0, '', 0, 'An alias for this connection, or blank to automatically detect'),
	'cdkeyroc' => array(0, 'FFFFFFFFFFFFFFFFFFFFFFFFFF', 0, 'The RoC CD key'),
	'cdkeytft' => array(0, 'FFFFFFFFFFFFFFFFFFFFFFFFFF', 0, 'The TFT CD key'),
	'username' => array(0, '', 0, 'Battle.net username; account must already exist'),
	'password' => array(0, '', 0, 'Battle.net password (warning: will be stored in plaintext)'),
	'firstchannel' => array(0, 'The Void', 0, 'Battle.net channel to join'),
	'rootadmin' => array(0, '', 0, 'Root admin for this realm'),
	'commandtrigger' => array(0, '!', 0, 'The command trigger for this realm'),
	'holdfriends' => array(2, 1, 0, 'Whether to reserve friends'),
	'holdclan' => array(2, 1, 0, 'Whether to reserve clan members'),
	'publiccommands' => array(2, 0, 0, 'Whether to allow non-admins to use commands over Battle.net')
	'bnet_custom_exeversion' => array(0, '', 0, 'PVPGN setting: exe version; generally leave blank'),
	'bnet_custom_exeversionhash' => array(0, '', 0, 'PVPGN setting: exe version hash; generally leave blank'),
	'bnet_custom_passwordhashtype' => array(3, '', array('' => 'default', 'pvpgn' => 'pvpgn'), 'Set to pvpgn if this is a PVPGN realm, or default for Battle.net'),
	'bnet_custom_pvpgnrealmname' => array(0, 'PvPGN Realm', 0, 'PVPGN setting: realm name; generally leave default')
	);

// default.cfg parameters
$defaultParameters = array(
	'bot_log' => 'ghost.log',
	'bot_logmethod' => 1,
	'bot_language' => 'language.cfg',
	'bot_war3path' => '/usr/lib',
	'bot_bindaddress' => '',
	'bot_hostport' => '6{ID3}',
	'bot_reconnectport' => '7{ID3}',
	'bot_mapcfgpath' => "mapcfgs",
	'bot_savegamepath' => "savegames",
	'bot_maps' => "maps",
	'bot_replaypath' => 'replays',
	'replay_war3version' => '26',
	'replay_buildnumber' => '6059',
	'bot_hideipaddresses' => '1',
	'bot_refreshmessages' => '0',
	'bot_autosave' => '0',
	'bot_pingduringdownloads' => '0',
	'bot_maxdownloaders' => '4',
	'bot_maxdownloadspeed' => '300',
	'bot_lcpings' => '1',
	'bot_banmethod' => '3',
	'bot_ipblacklistfile' => $config['ghost_path'] . "ipblacklist.txt",
	'bot_motdfile' => 'motd.txt',
	'bot_gameloadedfile' => 'gameloaded.txt',
	'bot_gameoverfile' => 'gameover.txt',
	'bot_localadminmessages' => '1',
	'tcp_nodelay' => '0',
	'bot_matchmakingmethod' => '0',
	'admingame_create' => '0',
	'admingame_port' => '8000',
	'admingame_password' => '',
	'admingame_map' => '',
	'lan_war3version' => '26',
	'udp_broadcasttarget' => '',
	'udp_dontroute' => '0',
	'db_type' => 'mysql',
	'db_sqlite3_file' => 'ghost.dbs',
	'db_mysql_server' => 'localhost',
	'db_mysql_database' => 'ghost{ID3}',
	'db_mysql_user' => 'ghost{ID3}',
	'db_mysql_password' => $config['ghost_password']
	);

//escapes function in configuration file
function ghostEscape($type, $default, $type_extra, $value) {
	if($type == 0) {
		//string, just strip newlines
		return str_replace(array("\n", "\r"), array("", ""), $value);
	} else if($type == 1) {
		//integer, convert
		return intval($value);
	} else if($type == 2) {
		if($value == "true" || $value == "1") {
			return 1;
		} else {
			return 2;
		}
	} else if($type == 3) {
		$value = intval($value);
		
		if($value >= 0 && $value < count($type_extra)) {
			return $type_extra[$value];
		} else {
			return $default;
		}
	}
}

//integer: success, service id
//string: error message
function ghostAddService($account_id, $service_name, $service_description, $identifier, $id3 = -1) {
	$identifier = stripAlphaNumeric($identifier);
	
	//set target directory
	$directory = $config['ghost'] . $identifier . '/';
	
	if(file_exists($directory)) {
		return "the target directory $directory already exists!";
	}
	
	//register database
	$service_id = createService($account_id, $service_name, $service_description, "ghost", array('id' => $identifier));
	
	if($service_id > 999 && $id3 == -1) {
		return "Error: exceeded the maximum three-digit ID!";
	} else if($id3 == -1) {
		$id3 = $service_id;
	}
	
	//create parameters needed later
	$id3 = $id3 . "";
	while(strlen($id3) < 3) {
		$id3 .= "0";
	}
	
	setServiceParameter($service_id, 'id3', $id3);
	
	//create target directory
	mkdir($directory);
	
	//create default.cfg
	$fh = fopen($directory . 'default.cfg', 'w');
	
	foreach($GLOBALS['defaultParameters'] as $key => $value) {
		fwrite($fh, "$key = $value\n");
	}
	
	fclose($fh);
	
	//create ghost.cfg
	$fh = fopen($directory . 'ghost.cfg', 'w');
	
	foreach($GLOBALS['ghostParameters'] as $key => $array) {
		fwrite($fh, "$key = {$array[1]}");
	}
	
	fclose($fh);
	
	//create motd.txt, gameloaded.txt, gameover.txt
	$fh = fopen("motd.txt", 'w');
	fwrite($fh, "This is the default welcome message. If you are the bot administrator, you can change this by logging into uxpanel and going to the message manager.\nThis game is hosted using GHost and uxpanel.\nFor more information, see codelain.com and uxpanel.clanent.net.");
	fclose($fh);
	
	$fh = fopen("gameloaded.txt", 'w');
	fwrite($fh, "This game is hosted using GHost and uxpanel.");
	fclose($fh);
	
	$fh = fopen("motd.txt", 'w');
	fwrite($fh, "This game was hosted using GHost and uxpanel.");
	fclose($fh);
	
	//copy files
	copy($config['ghost_path'] . "language.cfg", $directory . "language.cfg");
	copy($config['ghost_path'] . "ghost++", $directory . "ghost++");
	
	//make the replays directory
	mkdir($directory . "replays");
	
	return $service_id;
}

//returns config array (k => v) on success, or false on failure
//if skip is set, it will skip parameters not in ghostParameters
function ghostGetConfiguration($service_id, $skip = true) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return false;
	}
	
	//read the configuration file
	$fh = fopen($config['ghost_path'] . $id . "/ghost.cfg", 'r');
	$array = array();
	
	while(($buffer = fgets($fh, 4096)) !== false) {
		$buffer = trim($buffer);
		
		if(strlen($buffer) > 3 && $buffer[0] != '#') {
			$index = strpos($buffer, " = ");
			
			if($index !== false) {
				$key = trim(substr($buffer, 0, $index));
				$val = trim(substr($buffer, $index + 3));
				
				if(!$skip || isset($ghostParameters[$key])) {
					$array[$key] = $val;
				}
			}
		}
	}
	
	fclose($fh);
	return $array;
}

//returns array (bnet id => server) on success, or false on failure
function ghostGetBnet($service_id) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return false;
	}
	
	//read the configuration file
	$fh = fopen($config['ghost_path'] . $id . "/ghost.cfg", 'r');
	$array = array();
	
	while(($buffer = fgets($fh, 4096)) !== false) {
		$buffer = trim($buffer);
		
		if(strlen($buffer) > 3 && $buffer[0] != '#') {
			$index = strpos($buffer, " = ");
			
			if($index !== false) {
				$key = trim(substr($buffer, 0, $index));
				$val = trim(substr($buffer, $index + 3));
				
				$index = strpos($key, "_");
				
				if(substr($key, 0, 4) == "bnet" && $index !== false) {
					$this_bnet_id = $key[4];
					
					if($this_bnet_id == "_") {
						$this_bnet_id = 1;
					}
					
					$subkey = substr($key, $index + 1);
					
					if($subkey == "server") {
						$array[$this_bnet_id] = $val;
					}
				}
			}
		}
	}
	
	fclose($fh);
	return $array;
}

//returns true on success or string error on failure
function ghostAddBnet($service_id, $server) {
	//get next bnet id
	$bnets = ghostGetBnet($service_id);
	
	if($bnets === false) {
		return "Error: failed to find identifier. Perhaps this isn't a GHost service?";
	}
	
	$next_bnet_id = 1;
	
	for($bnets as $k => $v) {
		if($k >= $next_bnet_id) {
			$next_bnet_id = $k + 1;
		}
	}
	
	if($next_bnet_id > 9) {
		return "Error: too many Battle.net connections. Contact support.";
	}
	
	//decide what the prestring is
	$prestring = "bnet{$next_bnet_id}_";
	
	if($next_bnet_id == 1) {
		$prestring = "bnet_";
	}
	
	//add the bnet id
	$array = array();
	
	foreach($bnetParameters as $k => $v) {
		if($k == "server") {
			$array[$prestring . $k] = $server;
		} else {
			$array[$prestring . $k] = $v;
		}
	}
	
	ghostReconfigure($service_id, $array);
	
	return true;
}

//returns true on success or false on failure
function ghostRemoveBnet($service_id, $bnet_id) {
	//decide what the prestring is
	$prestring = "bnet{$bnet_id}_";
	
	if($bnet_id == 1) {
		$prestring = "bnet_";
	}
	
	//get config options
	$array = array();
	
	foreach($bnetParameters as $k => $v) {
		$array[$prestring . $k] = $v;
	}
	
	//delete from the current config
	ghostReconfigure($service_id, $array, true);
	
	return true;
}

//returns config array (k => v) on success, or false on failure
function ghostGetBnetConfiguration($service_id, $bnet_id) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return false;
	}
	
	//read the configuration file
	$fh = fopen($config['ghost_path'] . $id . "/ghost.cfg", 'r');
	$array = array();
	
	while(($buffer = fgets($fh, 4096)) !== false) {
		$buffer = trim($buffer);
		
		if(strlen($buffer) > 3 && $buffer[0] != '#') {
			$index = strpos($buffer, " = ");
			
			if($index !== false) {
				$key = trim(substr($buffer, 0, $index));
				$val = trim(substr($buffer, $index + 3));
				
				$index = strpos($key, "_");
				
				if(substr($key, 0, 4) == "bnet" && $index !== false) {
					$this_bnet_id = $key[4];
					
					if($this_bnet_id == "_") {
						$this_bnet_id = 1;
					}
					
					$subkey = substr($key, $index + 1);
					
					if(isset($bnetParameters[$subkey]) && $this_bnet_id == $bnet_id) {
						$array[$subkey] = $val;
					}
				}
			}
		}
	}
	
	fclose($fh);
	return $array;
}

//returns true on success, false on failure
//if remove is set, the keys of array will be removed instead of added
function ghostReconfigure($service_id, $array, $remove = false) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return false;
	}
	
	//backup the existing configuration
	$existingConfig = ghostGetConfiguration($service_id, false);
	
	//write the configuration file
	$fout = fopen($config['ghost_path'] . $id . "/ghost.cfg", 'w');
	
	if(!$remove) {
		foreach($array as $k => $v) {
			if(isset($ghostParameters[$k])) {
				$v = ghostEscape($ghostParameters[$k][0], $ghostParameters[$k][1], $ghostParameters[$k][2], $v);
				fwrite("$k = $v\n");
			} else if(substr($k, 0, 4) == "bnet")) {
				$index = strpos($k, "_");
			
				if($index !== false) {
					$sub_key = substr($k, $index + 1);
				
					if(isset($bnetParameters[$sub_key])) {
						$v = ghostEscape($bnetParameters[$sub_key][0], $ghostParameters[$sub_key][1], $ghostParameters[$sub_key][2], $v);
						fwrite($fout, "$k = $v\n");
					}
				}
			}
		}
	}
	
	foreach($existingConfig as $k => $v) {
		if(!isset($array[$k])) {
			fwrite("$k = $v\n");
		}
	}
	
	fclose($fout);
	return true;
}

//true on success, string error on failure
function ghostBotStart($service_id) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return "Error: failed to find identifier. Perhaps this isn't a GHost service?";
	}
	
	//check if the bot is already started
	$pid = getServiceParam($service_id, "pid");
	
	if($pid !== false && $pid != 0) {
		return "Error: the bot is already online.";
	}
	
	//make sure we didn't start too recently
	$last_time = getServiceParam($service_id, "start_time2");
	
	if($last_time !== false && $last_time != 0 && time() - $last_time < 900) {
		return "Error: please wait ten minutes between starting or restarting the bot.";
	}
	
	//start the bot
	$pid = shell_exec("nohup cd {$config['ghost_path']}$id && ./ghost++ ghost.cfg > /dev/null 2>&1 & echo $!");
	
	//save the pid and last start time
	setServiceParam($service_id, "pid", $pid);
	setServiceParam($service_id, "start_time2", getServiceParam($service_id, "start_time"));
	setServiceParam($service_id, "start_time", time());
	
	return true;
}

function ghostBotStop($service_id, $ignore_warning = false) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return "Error: failed to find identifier. Perhaps this isn't a GHost service?";
	}
	
	//get the pid
	$pid = stripAlphaNumeric(getServiceParam($service_id, "pid"));
	
	if($pid === false || $pid == 0) {
		if($ignore_warning) {
			return true;
		} else {
			return "Error: the bot is already offline.";
		}
	}
	
	//stop the bot
	exec("kill -s INT $pid");
	
	//reset the pid
	setServiceParam($service_id, "pid", 0);
	
	return true;
}

function ghostBotRestart($service_id) {
	$result = ghostBotStop($service_id, true);
	
	if($result === true) {
		return ghostBotStart($service_id);
	} else {
		return $result;
	}
}

?>
