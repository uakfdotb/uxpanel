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
	'bot_reconnect' => array(2, 0, 0, 'Enable GProxy++ reconnects'),
	'bot_reconnectwaittime' => array(1, 3, 0, 'The number of minutes allowable to wait for GProxy++ reconnect.'),
	'bot_maxgames' => array(1, 40, 0, 'Maximum number of games bot should host.'),
	'bot_commandtrigger' => array(0, '!', 0, "The in-game command trigger."),
	'bot_savereplays' => array(2, 0, 0, "Save replays"),
	'bot_virtualhostname' => array(0, '|cFF4080C0GHost', 0, "The virtual host name in lobby"),
	'bot_checkmultipleipusage' => array(2, 1, 0, 'Whether to report multiple IP usage in lobby'),
	'bot_spoofchecks' => array(3, 1, array(0 => 'Disable automatic spoofchecking', 1 => 'Enable auto-spoofcheck on all players', 2 => 'Enable auto-spoofcheck on admins only'), 'Spoofchecking method'),
	'bot_requirespoofchecks' => array(2, 1, 0, 'Whether to require players to spoofcheck before starting the game'),
	'bot_reserveadmins' => array(2, 1, 0, 'Whether to reserve admins (reserved players can always join full games)'),
	'bot_autolock' => array(2, 0, 0, 'Whether to lock the game when the owner joins'),
	'bot_allowdownloads' => array(3, 1, array(0 => 'Disable map downloads', 1 => 'Enable map downloads', 2 => 'Conditional map downloads'), 'Whether to allow map downloads'),
	'bot_autokickping' => array(1, 400, 0, 'Maximum ping before a player will be kicked'),
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
	'publiccommands' => array(2, 0, 0, 'Whether to allow non-admins to use commands over Battle.net'),
	'custom_exeversion' => array(0, '', 0, 'PVPGN setting: exe version; generally leave blank'),
	'custom_exeversionhash' => array(0, '', 0, 'PVPGN setting: exe version hash; generally leave blank'),
	'custom_passwordhashtype' => array(3, '', array('' => 'default', 'pvpgn' => 'pvpgn'), 'Set to pvpgn if this is a PVPGN realm, or default for Battle.net'),
	'custom_pvpgnrealmname' => array(0, 'PvPGN Realm', 0, 'PVPGN setting: realm name; generally leave default')
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
	'bot_mappath' => "maps",
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
	'db_mysql_database' => '',
	'db_mysql_user' => '',
	'db_mysql_password' => $config['ghost_password']
	);

$updatableFiles = array('motd.txt', 'gameover.txt', 'gameloaded.txt', 'language.cfg');

//get additional parameters from configuration

if(isset($config['ghostParameters'])) {
	$ghostParameters = array_merge($ghostParameters, $config['ghostParameters']);
}

if(isset($config['defaultParameters'])) {
	$defaultParameters = array_merge($defaultParameters, $config['defaultParameters']);
}

if(isset($config['bnetParameters'])) {
	$bnetParameters = array_merge($bnetParameters, $config['bnetParameters']);
}

if(isset($config['updatableFiles'])) {
	$updatableFiles = array_merge($updatableFiles, $config['updatableFiles']);
}

require_once(includePath() . "/jail.php");

//escapes function in configuration file
function ghostEscape($type, $default, $type_extra, $value) {
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
function ghostAddService($account_id, $service_name, $service_description, $identifier, $id3 = -1) {
	global $config;

	$identifier = stripAlphaNumeric($identifier);

	//set target directory
	$directory = $config['ghost_path'] . $identifier . '/';

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
		$id3 = "0" . $id3;
	}

	setServiceParam($service_id, 'id3', $id3);

	//create target directory
	mkdir($directory, 0700);

	//create default.cfg
	$fh = fopen($directory . 'default.cfg', 'w');

	foreach($GLOBALS['defaultParameters'] as $key => $value) {
		$value = str_replace("{ID3}", $id3, $value);
		fwrite($fh, "$key = $value\n");
	}

	fclose($fh);

	//create ghost.cfg
	$fh = fopen($directory . 'ghost.cfg', 'w');

	foreach($GLOBALS['ghostParameters'] as $key => $array) {
		$value = str_replace("{ID3}", $id3, $array[1]);
		fwrite($fh, "$key = $value\n");
	}

	fclose($fh);

	//create motd.txt, gameloaded.txt, gameover.txt
	$fh = fopen($directory . "motd.txt", 'w');
	fwrite($fh, "This is the default welcome message. If you are the bot administrator, you can change this by logging into uxpanel and going to the message manager.\nThis game is hosted using GHost and uxpanel.\nuxpanel is developed by uakf.b and Luna Ghost.\nFor more information, see codelain.com and uxpanel.clanent.net.");
	fclose($fh);

	$fh = fopen($directory . "gameloaded.txt", 'w');
	fwrite($fh, "This game is hosted using GHost and uxpanel.\nuxpanel is developed by uakf.b and Luna Ghost.");
	fclose($fh);

	$fh = fopen($directory . "gameover.txt", 'w');
	fwrite($fh, "This game was hosted using GHost and uxpanel.\nuxpanel is developed by uakf.b and Luna Ghost.");
	fclose($fh);

	//copy files
	copy($config['ghost_path'] . "language.cfg", $directory . "language.cfg");
	symlink($config['ghost_path'] . "ghost++", $directory . "ghost++");
	chmod($directory . "ghost++", 0700);

	//make the subdirectories
	mkdir($directory . "replays", 0700);
	mkdir($directory . "maps", 0700);
	mkdir($directory . "mapcfgs", 0700);

	return $service_id;
}

//returns array('status' => status string, 'err' => array(error strings), 'color' => suggested color)
function ghostGetStatus($service_id) {
	global $config;

	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));

	if($id === false) {
		return array('status' => "ERROR: failed to find bot identifier", 'err' => array(), 'color' => 'red');
	}

	//read last lines of the log file and scan for interesting things
	$lines = ghostGetLog($service_id, 1000);

	if($lines === false) {
		return array('status' => "Failed to read log file", 'err' => array(), 'color' => 'red');
	}

	$lastline = $lines[count($lines) - 1];
	$errors = array();
	$status = "Up, no game";
	$color = "orange";

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

		# check if user has joined game recently
		if(strpos($line, 'joined the game') !== false) {
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

			if(time() - $time > 1200) {
				$status = "Down";
				$errors[] = "Does not appear to be running!";
				$color = "red";
			}
		}
	}

	return array('status' => $status, 'err' => $errors, 'color' => $color);
}

function ghostGetParameters($service_id) {
	global $ghostParameters;
	$parameters = $ghostParameters;

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
//if skip is set, it will skip parameters not in ghostParameters
function ghostGetConfiguration($service_id, $skip = true) {
	global $config;
	$parameters = ghostGetParameters($service_id);

	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));

	if($id === false) {
		return false;
	}

	//read the configuration file
	$jail = jailEnabled($service_id);
	if($jail) {
		jailFileOpen($service_id, "ghost", "ghost.cfg");
	}

	$fh = fopen($config['ghost_path'] . $id . "/ghost.cfg", 'r');
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
		jailFileClose($service_id, "ghost", "ghost.cfg", false);
	}

	return $array;
}

//returns false if key is not bnet, or array('id' => bnet id, 'key' => subkey) if it is
function ghostConfigurationBnetKey($key) {
	if(substr($key, 0, 4) == "bnet" && ($index = strpos($key, "_")) !== false) {
		$bnet_id = intval(substr($key, 4, $index - 4));

		if($bnet_id == "" || $bnet_id == 0) {
			$bnet_id = 1;
		}

		$subkey = substr($key, $index + 1);

		return array('id' => $bnet_id, 'key' => $subkey);
	} else {
		return false;
	}
}

//returns array (bnet id => server) on success, or false on failure
function ghostGetBnet($service_id) {
	global $config;

	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));

	if($id === false) {
		return false;
	}

	//process the configuration
	$configuration = ghostGetConfiguration($service_id, false);
	$array = array();

	foreach($configuration as $k => $v) {
		if(($bkey_info = ghostConfigurationBnetKey($k)) !== false) {
			if($bkey_info['key'] == "server") {
				$array[$bkey_info['id']] = $v;
			}
		}
	}

	//sort by id
	ksort($array);

	return $array;
}

//returns true on success or string error on failure
function ghostAddBnet($service_id, $server) {
	global $bnetParameters;

	//get next bnet id
	$bnets = ghostGetBnet($service_id);

	if($bnets === false) {
		return "Error: failed to find identifier. Perhaps this isn't a GHost service?";
	}

	$next_bnet_id = 1;

	foreach($bnets as $k => $v) {
		if($k >= $next_bnet_id) {
			$next_bnet_id = $k + 1;
		}
	}

	if($next_bnet_id > 13) {
		return "Error: too many Battle.net connections. Contact support.";
	}

	//decide what the prestring is
	$prestring = "bnet{$next_bnet_id}_";

	if($next_bnet_id == 1) {
		$prestring = "bnet_";
	}

	//add the bnet id
	$array = array();

	foreach($bnetParameters as $k => $p_info) {
		if($k == "server") {
			$array[$prestring . $k] = $server;
		} else {
			$array[$prestring . $k] = $p_info[1];
		}
	}

	ghostReconfigure($service_id, $array);

	return true;
}

//returns true on success or false on failure
function ghostRemoveBnet($service_id, $bnet_id) {
	global $bnetParameters;

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
	global $config, $bnetParameters;

	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));

	if($id === false) {
		return false;
	}

	//process the configuration
	$configuration = ghostGetConfiguration($service_id, false);
	$array = array();

	foreach($configuration as $k => $v) {
		if(($bkey_info = ghostConfigurationBnetKey($k)) !== false && $bkey_info['id'] == $bnet_id) {
			$array[$bkey_info['key']] = $v;
		}
	}

	return $array;
}

//returns ghostReconfigure() result
function ghostReconfigureBnet($service_id, $bnet_id, $array) {
	//transform the array's keys to global keys that include bnet prefix
	$prefix = "bnet{$bnet_id}_";

	if($bnet_id == 1) {
		$prefix = "bnet_";
	}

	$globalArray = array();

	foreach($array as $k => $v) {
		$globalArray[$prefix . $k] = $v;
	}

	return ghostReconfigure($service_id, $globalArray);
}

function ghostConfigurationComparatorHelper($x) {
	global $ghostParameters, $bnetParameters;

	if(isset($ghostParameters[$x])) {
		return indexInArray($x, $ghostParameters);
	} else if(($bkey_info = ghostConfigurationBnetKey($x)) !== false) {
		$bnet_id = $bkey_info['id'];
		$subkey = $bkey_info['key'];

		if(isset($bnetParameters[$subkey])) {
			return $bnet_id * 1000 + indexInArray($subkey, $bnetParameters);
		} else {
			return $bnet_id * 1000 + 999;
		}
	} else {
		//put it at bottom
		return 99999;
	}
}

//determines order in configuration things should go
function ghostConfigurationComparator($a, $b) {
	return ghostConfigurationComparatorHelper($a) - ghostConfigurationComparatorHelper($b);
}

//returns true on success, false on failure
//if remove is set, the keys of array will be removed instead of added
function ghostReconfigure($service_id, $array, $remove = false) {
	global $config, $bnetParameters;
	$parameters = ghostGetParameters($service_id);

	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));

	if($id === false) {
		return false;
	}

	//get the existing configuration
	$ghostConfiguration = ghostGetConfiguration($service_id, false);

	//modify the configuration based on input $array settings
	foreach($array as $k => $v) {
		if(!$remove) {
			if(isset($parameters[$k])) {
				$ghostConfiguration[$k] = ghostEscape($parameters[$k][0], $parameters[$k][1], $parameters[$k][2], $v);
			} else if(($bkey_info = ghostConfigurationBnetKey($k)) !== false && isset($bnetParameters[$bkey_info['key']])) {
				$subkey = $bkey_info['key'];
				$ghostConfiguration[$k] = ghostEscape($bnetParameters[$subkey][0], $bnetParameters[$subkey][1], $bnetParameters[$subkey][2], $v);
			}
		} else {
			if(isset($ghostConfiguration[$k])) {
				unset($ghostConfiguration[$k]);
			}
		}
	}

	//sort the configuration intelligently
	uksort($ghostConfiguration, 'ghostConfigurationComparator');

	//re-order the configuration so that the bnet id's start from 1 and go up incrementally
	$curr_bnet_id = 0; //the bnet id counter
	$seen_bnet_id = -1; //the last seen bnet id from the input
	$reorderedConfiguration = array();

	foreach($ghostConfiguration as $k => $v) {
		if(($bkey_info = ghostConfigurationBnetKey($k)) !== false) {
			$bnet_id = $bkey_info['id'];
			$subkey = $bkey_info['key'];

			if($bnet_id != $seen_bnet_id) {
				$curr_bnet_id++;
				$seen_bnet_id = $bnet_id;
			}

			if($bnet_id != $curr_bnet_id) {
				if($curr_bnet_id == 1) {
					$k = "bnet_$subkey";
				} else {
					$k = "bnet{$curr_bnet_id}_$subkey";
				}
			}
		}

		$reorderedConfiguration[$k] = $v;
	}

	//sort the configuration intelligently again, just in case?
	uksort($reorderedConfiguration, 'ghostConfigurationComparator');

	//write the configuration out
	$fout = fopen($config['ghost_path'] . $id . "/ghost.cfg", 'w');

	foreach($reorderedConfiguration as $k => $v) {
		fwrite($fout, "$k = $v\n");
	}

	fclose($fout);

	$jail = jailEnabled($service_id);
	if($jail) {
		jailFileClose($service_id, "ghost", "ghost.cfg", true);
	}

	return true;
}

//returns array of key => value
function ghostGetConfigFromRequest(&$parameters, &$request) {
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
//if mapcfg is true, this will be written to the mapcfgs directory and will accept new files
function ghostUpdateFile($service_id, $filename, $content, $mapcfg = false) {
	global $config, $updatableFiles;

	//limit writable length
	if(strlen($content) > 100000) {
		return;
	}

	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));

	if($id === false) {
		return;
	}

	$pass = in_array($filename, $updatableFiles);
	$relTarget = $filename;

	if($mapcfg) {
		$filename = escapeFile($filename);
		$relTarget = "mapcfgs/" . $filename;
		$pass = true;

		if(!file_exists($config['ghost_path'] . $id . "/" . $relTarget)) {
			//make sure didn't exceed limit on mapcfgs
			$cfgLimit = getServiceParam($service_id, "mclimit");

			if($cfgLimit === false) {
				$cfgLimit = 200;
			}

			$pass = dirCount($config['ghost_path'] . $id . "/mapcfgs/") <= $cfgLimit;
		}
	}

	if($pass) {
		$target = $config['ghost_path'] . $id . "/" . $relTarget;
		$content = str_replace("\r", "", $content);
		$fout = fopen($target, 'w');
		fwrite($fout, $content);
		fclose($fout);

		$jail = jailEnabled($service_id);
		if($jail) {
			jailFileClose($service_id, "ghost", $relTarget, true);
		}
	}
}

//returns string file content
function ghostDisplayFile($service_id, $filename, $mapcfg = false) {
	global $config, $updatableFiles;

	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));

	if($id === false) {
		return "Error: failed to load file!";
	}

	if(!$mapcfg && in_array($filename, $updatableFiles)) {
		$jail = jailEnabled($service_id);
		if($jail) {
			jailFileOpen($service_id, "ghost", $filename);
		}

		$str = file_get_contents($config['ghost_path'] . $id . "/" . $filename);

		if($jail) {
			jailFileClose($service_id, "ghost", $filename, false);
		}

		return $str;
	} else if($mapcfg) {
		$filename = escapeFile($filename);

		if(getExtension($filename) != "cfg") {
			return "Error: bad filename. Incident has been reported to administration.";
		}

		$jail = jailEnabled($service_id);

		if(($jail && jailFileExists($service_id, 'mapcfgs/' . $filename)) || (!$jail && file_exists($config['ghost_path'] . $id . "/mapcfgs/" . $filename))) {
			if($jail) {
				jailFileOpen($service_id, "ghost", "mapcfgs/" . $filename);
			}

			$str = file_get_contents($config['ghost_path'] . $id . "/mapcfgs/" . $filename);

			if($jail) {
				jailFileClose($service_id, "ghost", "mapcfgs/" . $filename, false);
			}

			return $str;
		} else {
			return "This map configuration file does not exist.";
		}
	} else {
		return "Error: failed to load file!";
	}
}

//adds a map from repository
//true on success, string on failure
function ghostMapLink($service_id, $filename) {
	global $config;

	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));

	if($id === false) {
		return "Error: the identifier for this service is not set.";
	}

	//escape the map
	$filename = escapeFile(baseName($filename));

	//determine the source and target files
	$source = $config['ghost_path'] . "maps/" . $filename;
	$target = $config['ghost_path'] . $id . "/maps/" . $filename;

	//if source doesn't exist or target exists
	if(!file_exists($source)) {
		return "Error: the requested file does not exist.";
	} else if(file_exists($target)) {
		return "Error: you already have a map with the same filename.";
	}

	//create the symlink
	$result = symlink($source, $target);

	if($result === false) {
		return "Error: could not link the files. Please contact support.";
	} else {
		return true;
	}
}

//true on success, string error on failure
//$files is the $_FILES array
function ghostMapUpload($service_id, $files) {
	global $config;

	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));

	if($id === false) {
		return "Error: the identifier for this service is not set.";
	}

	//make sure didn't exceed limit on maps
	$mapLimit = getServiceParam($service_id, "mlimit");

	if($mapLimit === false) {
		$mapLimit = 1000;
	}

	$num_maps = dirCount($config['ghost_path'] . $id . "/maps");

	if($num_maps > $mapLimit) {
		return "You have exceeded the limit on the number of maps. Please contact support.";
	}

	if((!empty($files["uploaded_file"])) && ($files['uploaded_file']['error'] == 0)) {
	    //Check if the file is a map and it's size is less than 10MB
	    $filename = basename($files['uploaded_file']['name']);
	    $ext = getExtension($filename);

	    if (($ext == "w3x" || $ext == "w3m") && ($files["uploaded_file"]["size"] < 10000000)) {
	        //Determine the path to which we want to save this file
	        $newname = $config['ghost_path'] . $id . "/maps/" . escapeFile($filename);
	        //Check if the file with the same name is already exists on the server
	        if (!file_exists($newname)) {
	            //Attempt to move the uploaded file to it's new place
	            if ((move_uploaded_file($files['uploaded_file']['tmp_name'], $newname))) {
	                return true;
	            } else {
	                return "Error: could not move the uploaded file.";
	            }
	        } else {
	            return "Error: a map with the same filename already exists!";
	        }
	    } else {
	        return "Error: Only .w3x or .w3m map files under 10MB can be uploaded. Please contact support.";
	    }
    } else {
        return "No file was uploaded, or there was an error during the upload.";
    }
}

//deletes a map
// if mapcfg is set, it will delete the map configuration file
function ghostMapDelete($service_id, $filename, $mapcfg = false) {
	global $config;

	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));

	if($id === false) {
		return "Error: the identifier for this service is not set.";
	}

	//escape the map
	$filename = escapeFile(baseName($filename));
	$relTarget = ($mapcfg ? "mapcfgs/" : "maps/") . $filename;
	$target = $config['ghost_path'] . $id . "/" . $relTarget;

	$jail = jailEnabled($service_id);
	//unlink if this is a map, or if we're not jailed (maps are not jailed)
	if(!$jail || !$mapcfg) {
		if(file_exists($target)) {
			unlink($target);
		}
	} else {
		jailFileDelete($service_id, $relTarget);
	}
}

//lists maps on the server
//source is one of "maps", "repository", and "mapcfgs"
// repository: load maps from common repository in ghost_path/maps/
function ghostMapList($service_id, $source = "maps") {
	global $config;

	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));

	if($id === false) {
		return "Error: the identifier for this service is not set.";
	}

	//ok iterate through maps
	$extensions = array("w3x", "w3m");

	if($source == "mapcfgs") {
		$extensions = array("cfg");
	}

	if($source == "repository") {
		$dir = new DirectoryIterator($config['ghost_path'] . "maps");
	} else if($source == "maps") {
		$dir = new DirectoryIterator($config['ghost_path'] . $id . "/maps");
	} else if($source == "mapcfgs") {
		$jail = jailEnabled($service_id);
		if($jail) {
			return jailDirList($service_id, "mapcfgs", $extensions);
		}

		$dir = new DirectoryIterator($config['ghost_path'] . $id . "/mapcfgs");
	} else {
		return "Error: bad source to ghostMapList.";
	}

	$array = array();
	foreach($dir as $file) {
		if($file->isFile()) {
			$ext = getExtension($file->getFilename());

			if(in_array($ext, $extensions)) {
		    	array_push($array, $file->getFilename());
		    }
		}
	}

	sort($array);
	return $array;
}

//download a map
function ghostMapDownload($service_id, $filename, $repository = false) {
	global $config;

	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));

	if($id === false) {
		return;
	}

	//escape the map
	$filename = escapeFile(baseName($filename));

	if($repository) {
		$target = $config['ghost_path'] . "maps/" . $filename;
	} else {
		$target = $config['ghost_path'] . $id . "/maps/" . $filename;
	}

	fileDownload($target);
}

function ghostReplayList($service_id) {
	global $config;

	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));

	if($id === false) {
		return "Error: the identifier for this service is not set.";
	}

	//ok iterate through replays
	$dir = new DirectoryIterator($config['ghost_path'] . $id . "/replays");

	$array = array();
	foreach($dir as $file) {
		if($file->isFile()) {
			$ext = getExtension($file->getFilename());

			if($ext == "w3g") {
		    	array_push($array, $file->getFilename());
		    }
		}
	}

	sort($array);
	return $array;
}

//deletes a replay
function ghostReplayDelete($service_id, $replay) {
	global $config;

	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));

	if($id === false) {
		return "Error: the identifier for this service is not set.";
	}

	//escape the replay
	$replay = escapeFile(baseName($replay));
	$target = $config['ghost_path'] . $id . "/replays/" . $replay;

	if(file_exists($target)) {
		unlink($target);
	}
}

//download a replay
function ghostReplayDownload($service_id, $replay) {
	global $config;

	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));

	if($id === false) {
		return;
	}

	//escape the replay
	$replay = escapeFile(baseName($replay));
	$target = $config['ghost_path'] . $id . "/replays/" . $replay;
	fileDownload($target);
}

//returns false if failed to read log, or array of lines on success
function ghostGetLog($service_id, $numlines = 400) {
	global $config;

	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));

	if($id === false) {
		return "Error: the identifier for this service is not set.";
	}

	$log_file = $config['ghost_path'] . $id . '/ghost.log';

	$jail = jailEnabled($service_id);

	if(($jail && !jailFileExists($service_id, "ghost.log")) || (!$jail && !file_exists($log_file))) {
		return false;
	}

	//read last lines of the log file
	$output_array = array();

	if($jail) {
		jailExecute($service_id, "tail -n 1000 " . escapeshellarg(jailPath($service_id) . "ghost.log"), $output_array);
	} else {
		exec("tail -n 1000 " . escapeshellarg($log_file), $output_array);
	}

	return $output_array;
}

//true if we can start, false otherwise
//this is due to time restriction
function ghostBotCanStart($service_id) {
	$last_time = getServiceParam($service_id, "start_time2");

	if($last_time !== false && $last_time != 0 && time() - $last_time < 900) {
		return false;
	}

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

	//make sure we didn't start too recently to prevent Battle.net flooding
	if(!ghostBotCanStart($service_id)) {
		return "Error: please wait ten minutes between starting or restarting the bot.";
	}

	//start the bot
	$jail = jailEnabled($service_id);

	if($jail) {
		$pid = jailExecuteBackground($service_id, "cd " . escapeshellarg(jailPath($service_id)) . " && nohup ./ghost++ ghost.cfg > /dev/null 2>&1 & echo $!");
	} else {
		$pid = execBackground("cd " . escapeshellarg($config['ghost_path'] . $id) . " && nohup ./ghost++ ghost.cfg > /dev/null 2>&1 & echo $!");
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
function ghostBotStop($service_id, $restart = false) {
	global $config;

	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));

	if($id === false) {
		return "Error: failed to find identifier. Perhaps this isn't a GHost service?";
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
		jailExecute($service_id, "kill -s INT $pid");
	} else {
		//make sure PID is still of pychop
		$result = exec("cat /proc/$pid/cmdline");

		if(stripos($result, 'ghost') !== false) {
			exec("kill -s INT $pid");
		}
	}

	//reset the pid
	setServiceParam($service_id, "pid", 0);

	return true;
}

function ghostBotRestart($service_id) {
	if(!ghostBotCanStart($service_id)) {
		return "Error: please wait ten minutes between starting or restarting the bot.";
	}

	$result = ghostBotStop($service_id, true);

	if($result === true) {
		sleep(1);
		return ghostBotStart($service_id);
	} else {
		return $result;
	}
}

function ghostSetDatabase($service_id, $db_settings) {
	global $config;

	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));

	if($id === false) {
		return false;
	}

	//read/write the configuration file
	$jail = jailEnabled($service_id);

	if($jail) {
		jailFileOpen($service_id, "ghost", "default.cfg");
	}

	$fin = fopen($config['ghost_path'] . $id . "/default.cfg", 'r');
	$fout = fopen($config['ghost_path'] . $id . "/default.cfg_", 'w');

	while(($buffer = fgets($fin, 4096)) !== false) {
		$buffer = trim($buffer);

		if(strpos($buffer, "db_mysql_database") !== false) {
			fwrite($fout, "db_mysql_database = {$db_settings['name']}\n");
		} else if(strpos($buffer, "db_mysql_server") !== false) {
			fwrite($fout, "db_mysql_server = {$db_settings['server']}\n");
		} else if(strpos($buffer, "db_mysql_user") !== false) {
			fwrite($fout, "db_mysql_user = {$db_settings['username']}\n");
		} else if(strpos($buffer, "db_mysql_password") !== false) {
			fwrite($fout, "db_mysql_password = {$db_settings['password']}\n");
		} else {
			fwrite($fout, $buffer . "\n");
		}
	}

	fclose($fin);
	fclose($fout);
	rename($config['ghost_path'] . $id . "/default.cfg_", $config['ghost_path'] . $id . "/default.cfg");

	if($jail) {
		jailFileOpen($service_id, "ghost", "default.cfg", true);
	}
}

//STYLE FUNCTIONS
function ghostDisplayConfiguration($k, $v, $parameters) {
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
