<?php

### this file contains functions for the database service
### it is not for uxpanel database utility functions

// array of cron parameters and their type
// key => (type, default, default extra, description)
// types:
//  0: string
//  1: integer
//  2: boolean
//  3: select from choices
// if 3, default extra is array of value => value description; otherwise 0
$cronParameters = array();

if(isset($config['cronParameters'])) {
	$cronParameters = $config['cronParameters'];
}

//identifier will be used as the database name
function databaseAddService($account_id, $service_name, $service_description, $identifier) {
	$identifier = stripAlphaNumeric($identifier);
	
	//register database
	$service_id = createService($account_id, $service_name, $service_description, "database", array('db_name' => $identifier, 'db_host' => "localhost", 'db_username' => 'root', 'db_password' => ''));
	
	return $service_id;
}

//returns array settings on success
// ('name' => db_name, 'server' => db_server, 'username' => db_username, 'password' => db_password)
//or false on failure
function databaseSettings($service_id) {
	$db_name = getServiceParam($service_id, 'db_name');
	$db_host = getServiceParam($service_id, 'db_host');
	$db_username = getServiceParam($service_id, 'db_username');
	$db_password = getServiceParam($service_id, 'db_password');
	
	if($db_name !== false && $db_host !== false && $db_username !== false && $db_password !== false) {
		return array('name' => $db_name, 'server' => $db_host, 'username' => $db_username, 'password' => $db_password);
	} else {
		return false;
	}
}

//returns false on failure, or link identifier on success
function databaseConnect($service_id) {
	# if this connection has already been made, just use the existing one
	if(isset($GLOBALS['link_service_' . $service_id])) {
		return $GLOBALS['link_service_' . $service_id];
	}
	
	$db_name = getServiceParam($service_id, 'db_name');
	$db_host = getServiceParam($service_id, 'db_host');
	$db_username = getServiceParam($service_id, 'db_username');
	$db_password = getServiceParam($service_id, 'db_password');
	
	if($db_name !== false && $db_host !== false && $db_username !== false && $db_password !== false) {
		$link = mysql_connect($db_host, $db_username, $db_password);
		mysql_select_db($db_name, $link);
		
		# set timezone for compatibility	
		mysql_query("SET time_zone = '+0:00'", $link);
		
		# set global field
		$GLOBALS['link_service_' . $service_id] = $link;
		
		return $link;
	} else {
		return false;
	}
}

//this will setup or reset the database
//returns true on success, string error message on failure
function databaseSetup($service_id, $reset = false) {
	global $config;
	
	if($link = databaseConnect($service_id)) {
		$filename = 'install.sql';
		
		if($reset) {
			$filename = 'reset.sql';
		}
		
		$fin = @fopen($config['ghost_path'] . $filename, 'r');
		
		if($fin === false) {
			return "Failed to read from " . $config['ghost_path'] . $filename;
		}
		
		$query_buffer = "";
		while(($buffer = fgets($fin, 4096)) !== false) {
			$buffer = trim($buffer);
			
			if(strlen($buffer) > 0 && $buffer[0] != "#") {
				if(strpos($buffer, ";") !== false) {
					mysql_query($query_buffer . $buffer, $link);
					$query_buffer = "";
				} else {
					$query_buffer .= $buffer . " ";
				}
			}
		}
		
		fclose($fin);
		return true;
	} else {
		return "Make sure that db_name, db_host, db_username, and db_password parameters are set for this service. And that they are set correctly.";
	}
}

//returns array of realm_server
function databaseGetRealms($service_id) {
	$link = databaseConnect($service_id);
	
	if(!$link) {
		return array();
	}
	
	$result = mysql_query("SELECT DISTINCT spoofedrealm FROM gameplayers", $link);
	$array = array("uswest.battle.net", "useast.battle.net", "europe.battle.net", "asia.battle.net");
	
	while($row = mysql_fetch_array($result)) {
		if($row[0] != "" && !in_array($row[0], $array)) {
			$array[] = $row[0];
		}
	}
	
	return $array;
}

//gets current games based on gamelist patch
// gamelist patch is available at http://www.codelain.com/forum/index.php?topic=18076.0
//returns array of ('botid' =. botid, 'gamename' => gamename, 'ownername' => ownername, 'creatorname' => creatorname, 'map' => map, 'slotstaken' => slotstaken, 'slotstotal' => slotstotal, 'usernames' => usernames, 'totalgames' => totalgames, 'totalplayers' => totalplayers, 'id' => table id)
function databaseGetRunning($service_id) {
	$link = databaseConnect($service_id);
	
	if($link) {
		$result = mysql_query("SELECT botid, gamename, ownername, creatorname, map, slotstaken, slotstotal, usernames, totalgames, totalplayers, id FROM gamelist WHERE gamename != '' ORDER BY botid, id DESC", $link);
		$array = array();
		
		while($row = mysql_fetch_row($result)) {
			$array[] = array('botid' => $row[0], 'gamename' => $row[1], 'ownername' => $row[2], 'creatorname' => $row[3], 'map' => $row[4], 'slotstaken' => $row[5], 'slotstotal' => $row[6], 'usernames' => $row[7], 'totalgames' => $row[8], 'totalplayers' => $row[9], 'id' => $row[10]);
		}
		
		return $array;
	} else {
		return array();
	}
}

//gets last 30 games, starting from $start
//returns array of ('id' => gameid, 'botid' => botid, 'gamename' => gamename, 'ownername' => ownername, 'creatorname' => creatorname, 'map' => map, 'datetime' => datetime, 'duration' => duration)
function databaseGetGames($service_id, $start = 0) {
	$start = intval($start);
	$link = databaseConnect($service_id);
	
	if($link) {
		$result = mysql_query("SELECT id, botid, gamename, ownername, creatorname, map, datetime, duration FROM games WHERE gamename != '' ORDER BY id DESC LIMIT $start, 30", $link);
		$array = array();
		
		while($row = mysql_fetch_row($result)) {
			$array[] = array('id' => $row[0], 'botid' => $row[1], 'gamename' => $row[2], 'ownername' => $row[3], 'creatorname' => $row[4], 'map' => $row[5], 'datetime' => $row[6], 'duration' => $row[7]);
		}
		
		return $array;
	} else {
		return array();
	}
}

//information about a game
//returns array(botid, gamename, ownername, creatorname, map, datetime, duration, players) or false on failure
//players is array of (name, ip, spoofedrealm, left, leftreason)
//fast: if fast is set, it will only return the gamename
function databaseGetGame($service_id, $game_id, $fast = false) {
	$game_id = escape($game_id);
	$link = databaseConnect($service_id);
	
	if($link) {
		if(!$fast) {
			$result = mysql_query("SELECT botid, gamename, ownername, creatorname, map, datetime, duration FROM games WHERE id = '$game_id'", $link);
		
			if($row = mysql_fetch_row($result)) {
				$array = array('botid' => $row[0], 'gamename' => $row[1], 'ownername' => $row[2], 'creatorname' => $row[3], 'map' => $row[4], 'datetime' => $row[5], 'duration' => $row[6], 'players' => array());
			
				$result = mysql_query("SELECT name, ip, spoofedrealm, `left`, leftreason FROM gameplayers WHERE gameid = '$game_id'", $link);
			
				while($row = mysql_fetch_row($result)) {
					$array['players'][] = array('name' => $row[0], 'ip' => $row[1], 'spoofedrealm' => $row[2], 'left' => $row[3], 'leftreason' => $row[4]);
				}
			
				return $array;
			} else {
				return false;
			}
		} else {
			$result = mysql_query("SELECT gamename FROM games WHERE id = '$game_id'", $link);
			
			if($row = mysql_fetch_row($result)) {
				return $row[0];
			} else {
				return "Unknown gamename";
			}
		}
	} else {
		return false;
	}
}

//bans a user
//$unban: if true it will unban instead of ban
//$name_only: if true it will only ban the username, not the IP addresses used
//$ban_aliases: if true it will also ban aliases
//false on failure, or string success message on success
function databaseBanUser($service_id, $username, $realm, $duration, $reason, $unban = false, $name_only = false, $ban_aliases = false) {
	$link = databaseConnect($service_id);
	
	if(!$link) {
		return false;
	}
	
	$message = "";
	$username = escape(strtolower($username));
	$realm = escape($realm);
	$duration = escape(intval($duration) * 3600);
	$reason = escape($reason);
		
	$realms = databaseGetRealms($service_id);
	
	//find a realm to use by default that is not blank
	$default_realm = "";
	
	foreach($realms as $i_realm) {
		if($i_realm != "") {
			$default_realm = $i_realm;
			break;
		}
	}
	
	if($realm != "") $realms = array($realm);
		
	foreach($realms as $realm_it) {
		$where = "WHERE name = '$username' AND spoofedrealm = '$realm_it'";
		
		//unban the user if we're supposed to
		if($unban) {
			mysql_query("DELETE FROM bans WHERE name = '$username' AND server = '$realm_it'", $link);
			$message .= "Unbanned $username on $realm_it<br />";
			continue;
		}
		
		//make sure user isn't already banned
		$result = mysql_query("SELECT COUNT(*) FROM bans WHERE name = '$username' AND server = '$realm_it' AND context = 'ttr.cloud'", $link);
		$row = mysql_fetch_row($result);
		if($row[0] > 0) {
			$message .= "Skipping $realm_it: already banned!<br />";
			continue;
		}
		
		//last few IP addresses logged; limited to 15 addresses within the last 30 days
		$result = mysql_query("SELECT DISTINCT ip FROM gameplayers LEFT JOIN games ON gameplayers.gameid = games.id $where AND datetime > DATE_SUB( NOW( ), INTERVAL 30 DAY) ORDER BY gameplayers.id DESC LIMIT 15", $link);
		
		//only continue if both we have found some addresses and we don't want to just ban by name
		if(!$name_only && mysql_num_rows($result) > 0) {
			while($row = mysql_fetch_row($result)) {
				$ip = escape($row[0]);
				
				//if this is for non-spoofchecked users, ban on default realm
				$ban_realm = $realm_it;
				if($ban_realm == "") $ban_realm = $default_realm;
				
				mysql_query("INSERT INTO bans (botid, server, name, ip, date, gamename, admin, reason, expiredate, context) VALUES ('0', '$ban_realm', '$username', '$ip', CURDATE(), '', 'uxpanel', '$reason', DATE_ADD( NOW( ), INTERVAL $duration second ), 'ttr.cloud')", $link);
				$message .= "Banned used IP address [$ip] on $realm_it<br />";
			}
		} else {
			//no previous games found; ban by username only if this is an actual realm
			if($realm_it != "") {
				mysql_query("INSERT INTO bans (botid, server, name, ip, date, gamename, admin, reason, expiredate, context) VALUES ('0', '$realm_it', '$username', '', CURDATE(), '', 'uxpanel', '$reason', DATE_ADD( NOW( ), INTERVAL $duration second ), 'ttr.cloud')", $link);
				$message .= "Banned by name on $realm_it<br />";
			}
		}
	}
	
	if($ban_aliases) {
		$message .= "Banning aliases...<br />";
		
		$searchRealm = $realm;
		
		if($searchRealm == "") {
			$searchRealm = $default_realm;
		}
		
		//get list of aliases and ban them on the default realm
		$array = array();
		databaseAliases($service_id, $username, $searchRealm, 1, $array);
		$players = array_keys($array);
		
		foreach($players as $p_str) {
			$p_info = databaseGetPlayer($p_str);
			
			$aliasName = escape($p_info[0]);
			$aliasRealm = escape($p_info[1]);
			
			if($aliasName == $username && $aliasRealm == $searchRealm) {
				continue;
			}
			
			$message .= databaseBanUser($service_id, $aliasName, $aliasRealm, $duration, $reason, false, true);
			$message .= "Banned alias $aliasName@$aliasRealm<br />";
		}
	}
	
	return $message;
}

//deletes a ban
function databaseDeleteBan($service_id, $ban_id) {
	$link = databaseConnect($service_id);
	
	if(!$link) {
		return;
	}
	
	$ban_id = escape($ban_id);
	mysql_query("DELETE FROM bans WHERE id = '$ban_id'", $link);
}

//returns array of id => (name, server, ip, admin, gamename, reason, date, expiredate)
function databaseGetBans($service_id) {
	$link = databaseConnect($service_id);
	
	if(!$link) {
		return array();
	}
	
	$result = mysql_query("SELECT id, name, server, ip, admin, gamename, reason, date, expiredate FROM bans ORDER BY id", $link);
	$array = array();
	
	while($row = mysql_fetch_array($result)) {
		$array[$row[0]] = array('name' => $row[1], 'server' => $row[2], 'ip' => $row[3], 'admin' => $row[4], 'gamename' => $row[5], 'reason' => $row[6], 'date' => $row[7], 'expiredate' => $row[8]);
	}
	
	return $array;
}

//returns array of realm => array('firstgame' => first game, 'lastgame' => last game, 'totalgames' => total games, 'leftpercent' => leftpercentage, 'lastgames' => last few games, 'bans' => ban history)
//last few games is array of game id => gamename
//ban history is array of id => ('admin' => admin, 'reason' => reason, 'gamename' => gamename, 'date' => date, 'expiredate' => expiredate)
function databaseSearchUser($service_id, $username, $realm) {
	$link = databaseConnect($service_id);
	
	if(!$link) {
		return array();
	}
	
	$username = escape($username);
	$realm = escape($realm);
	
	$realms = databaseGetRealms($service_id);
	if($realm != "") $realms = array($realm);
	
	$array = array();
	
	foreach($realms as $realm_it) {
		$where = "WHERE name = '$username'";
		if($realm_it != "*") $where .= " AND realm = '$realm_it'";
		
		//grab general statistics
		$result = mysql_query("SELECT time_created, time_active, num_games, (total_leftpercent / num_games)*100, lastgames FROM gametrack $where", $link);
		$row = mysql_fetch_row($result);
		
		$firstgame = $row[0];
		$lastgame = $row[1];
		$totalgames = $row[2];
		$leftpercent = $row[3];
		$lastgames = explode(",", $row[4]);
		
		$array[$realm_it] = array('firstgame' => $firstgame, 'lastgame' => $lastgame, 'totalgames' => $totalgames, 'leftpercent' => $leftpercent, 'lastgames' => array(), 'bans' => array());
		
		foreach($lastgames as $gid) {
			if($gid != 0) {
				$array[$realm_it]['lastgames'][$gid] = databaseGetGame($service_id, $gid, true);
			}
		}
		
		if($totalgames != 0) {
			//ban history
			$result = mysql_query("SELECT admin, reason, gamename, date, expiredate FROM ban_history WHERE name = '$username' AND server = '$realm_it' ORDER BY id DESC LIMIT 6", $link);
			
			while($row = mysql_fetch_row($result)) {
				$array[$realm_it]['bans'][] = array('admin' => $row[0], 'reason' => $row[1], 'gamename' => $row[2], 'date' => $row[3], 'expiredate' => $row[4]);
			}
		}
	}
	
	return $array;
}

//returns array of IP addresses
function databaseIPLookup($service_id, $name, $realm, $hours = 1440) {
	$link = databaseConnect($service_id);
	
	if(!$link) {
		return array();
	}
	
	$name = escape($name);
	$realm = escape($realm);
	
	$result = mysql_query("SELECT DISTINCT ip FROM gameplayers LEFT JOIN games ON games.id = gameplayers.gameid WHERE name = '$name' AND spoofedrealm = '$realm' AND games.datetime > DATE_SUB( NOW( ), INTERVAL $hours HOUR) AND ip != '0.0.0.0' AND ip != '127.0.0.1'", $link);
	$array = array();
	
	while($row = mysql_fetch_array($result)) {
		$array[] = $row[0];
	}
	
	return $array;
}

//returns array of (username, realm)
function databaseNameLookup($service_id, $ip) {
	$link = databaseConnect($service_id);
	
	if(!$link) {
		return array();
	}
	
	$ip = escape($ip);
	$result = false;
	
	if(substr($ip, -1) == ".") {
		$parts = explode(".", $ip);
		$safe_ip = "";
		$counter = 0;
	
		foreach($parts as $part) {
			if(trim($part) != '') {
				$safe_ip .= intval($part) . ".";
				$counter++;
			}
		}
		
		if($counter >= 2) {
			$safe_ip = escape($safe_ip);
			$result = mysql_query("SELECT DISTINCT name, spoofedrealm FROM gameplayers WHERE ip LIKE '$safe_ip%' LIMIT 20", $link);
		}
	}
	
	if($result === false) {
		$result = mysql_query("SELECT DISTINCT name, spoofedrealm FROM gameplayers WHERE ip = '$ip'", $link);
	}
	
	$array = array();
	
	while($row = mysql_fetch_row($result)) {
		$array[] = array($row[0], $row[1]);
	}
	
	return $array;
}

//populates $array with keys of "username@realm" strings that are aliases of the input
function databaseAliases($service_id, $name, $realm, $depth = 1, &$array, $hours = 720, &$iparray = array()) {
	if($depth > 3) return;
	
	//set the parameter player as seen
	$array[$name . '@' . $realm] = true;
	
	//decrement depth
	$depth--;
	
	//find used IP addresses
	$used_ips = databaseIPlookup($service_id, $name, $realm, $hours);
	
	foreach($used_ips as $ip) {
		if(!isset($iparray[$ip])) {
			$iparray[$ip] = true;
			
			$names = databaseNameLookup($service_id, $ip);
			
			foreach($names as $p_array) {
				$player = $p_array[0] . "@" . $p_array[1];
				
				if(!isset($array[$player])) {
					$array[$player] = true;
					
					if($depth > 0) {
						databaseAliases($service_id, $p_array[0], $p_array[1], $depth, $array, $hours, $iparray);
					}
				}
			}
		}
	}
}

//parses a username@realm into array(username, realm)
function databaseGetPlayer($player) {
	$playerParts = explode('@', $player);
	
	if(count($playerParts) >= 2) {
		$name = strtolower($playerParts[0]);
		$realm = strtolower($playerParts[1]);
		
		if($realm == "uswest" || $realm == "west") {
			$realm = "uswest.battle.net";
		} else if($realm == "useast" || $realm == "east") {
			$realm = "useast.battle.net";
		} else if($realm == "europe") {
			$realm = "europe.battle.net";
		} else if($realm == "asia") {
			$realm = "asia.battle.net";
		}
		
		return array($name, $realm);
	} else {
		return array($player, "uswest.battle.net");
	}
}

//returns the last time (string) that a username was seen, on any realm
function databaseLastPlayed($service_id, $name) {
	$link = databaseConnect($service_id);
	
	if(!$link) {
		return "Never";
	}
	
	$name = escape($name);
	$result = mysql_query("SELECT MAX(games.datetime) FROM gameplayers LEFT JOIN games ON gameplayers.gameid = games.id WHERE gameplayers.name = '$name'", $link);
	$row = mysql_fetch_row($result);
	
	if(is_null($row[0])) return "Never";
	else return $row[0];
}

//returns array of id => ('name' => admin name, 'realm' => admin realm)
function databaseGetAdmins($service_id) {
	$link = databaseConnect($service_id);
	
	if(!$link) {
		return array();
	}
	
	$result = mysql_query("SELECT id, name, server FROM admins ORDER BY id", $link);
	$array = array();
	
	while($row = mysql_fetch_row($result)) {
		$array[$row[0]] = array('name' => $row[1], 'realm' => $row[2]);
	}
	
	return $array;
}

function databaseAddAdmin($service_id, $name, $server) {
	$link = databaseConnect($service_id);
	
	if(!$link) {
		return array();
	}
	
	$name = escape($name);
	$server = escape($server);
	mysql_query("INSERT INTO admins (botid, name, server) VALUES ('0', '$name', '$server')", $link);
}

function databaseDeleteAdmin($service_id, $admin_id) {
	$link = databaseConnect($service_id);
	
	if(!$link) {
		return;
	}
	
	$admin_id = escape($admin_id);
	mysql_query("DELETE FROM admins WHERE id = '$admin_id'", $link);
}

function databaseExecuteCommand($service_id, $botid, $command) {
	$link = databaseConnect($service_id);
	
	if(!$link) {
		return array();
	}
	
	$botid = escape($botid);
	$command = escape($command);
	mysql_query("INSERT INTO commands (botid, command) VALUES ('$botid', '$command')", $link);
}

//returns array of key => value
function databaseGetConfigFromRequest(&$parameters, &$request) {
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

//escapes function in configuration file
function databaseConfEscape($type, $default, $type_extra, $value) {
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

//returns an array of k => v
function databaseGetCronConfig($service_id) {
	$array = array();
	
	foreach($GLOBALS['cronParameters'] as $k => $v) {
		$setting = getServiceParam($service_id, "cron_" . $k);
		
		if($setting === false) {
			$setting = $v[1];
		}
		
		$array[$k] = $setting;
	}
	
	return $array;
}

function databaseSetCronConfig($service_id, $array) {
	foreach($array as $k => $v) {
		if(isset($GLOBALS['cronParameters'][$k])) {
			$v = databaseConfEscape($GLOBALS['cronParameters'][$k][0], $GLOBALS['cronParameters'][$k][1], $GLOBALS['cronParameters'][$k][2], $v);
			setServiceParam($service_id, "cron_" . $k, $v);
		}
	}
}

//STYLE FUNCTIONS
function databaseDisplayConfiguration($k, $v, $parameters) {
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
