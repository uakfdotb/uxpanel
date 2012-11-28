<?php

### this file contains functions for the database service
### it is not for uxpanel database utility functions

//identifier will be used as the database name
function databaseAddService($account_id, $service_name, $service_description, $identifier) {
	$identifier = stripAlphaNumeric($identifier);
	
	//register database
	$service_id = createService($account_id, $service_name, $service_description, "ghost", array('db_name' => $identifier, 'db_host' => "localhost", 'db_username' => 'root', 'db_password' => ''));
	
	return $service_id;
}

//returns false on failure, or link identifier on success
function databaseConnect($service_id) {
	$db_name = getServiceParam($service_id, 'db_name');
	$db_host = getServiceParam($service_id, 'db_host');
	$db_username = getServiceParam($service_id, 'db_username');
	$db_password = getServiceParam($service_id, 'db_password');
	
	if($db_name !== false && $db_host !== false && $db_username !== false && $db_password !== false) {
		$link = mysql_connect($db_host, $db_username, $db_password);
		mysql_select_db($db_name, $link);
		
		# set timezone for compatibility	
		mysql_query("SET time_zone = '+0:00'", $link);
		
		return $link;
	} else {
		return false;
	}
}

//this will setup or reset the database
//returns true on success, string error message on failure
function databaseSetup($service_id) {
	global $config;
	
	if($link = databaseConnect($service_id)) {
		$fin = @fopen($config['ghost_path'] . '/install.sql', 'r');
		
		if($fin === false) {
			return "Failed to read from " . $config['ghost_path'] . '/install.sql';
		}
		
		while(($buffer = fgets($fin, 4096)) !== false) {
			$buffer = trim($buffer);
			mysql_query($buffer);
		}
		
		fclose($fin);
		return true;
	} else {
		return "Make sure that db_name, db_host, db_username, and db_password parameters are set for this service. And that they are set correctly.";
	}
}

//gets current games based on gamelist patch
// gamelist patch is available at http://www.codelain.com/forum/index.php?topic=18076.0
//returns array of ('botid' =. botid, 'gamename' => gamename, 'ownername' => ownername, 'creatorname' => creatorname, 'map' => map, 'slotstaken' => slotstaken, 'slotstotal' => slotstotal, 'usernames' => usernames, 'totalgames' => totalgames, 'totalplayers' => totalplayers)
function databaseGetRunning($service_id) {
	$link = databaseConnect($service_id);
	
	if($link) {
		$result = mysql_query("SELECT botid, gamename, ownername, creatorname, map, slotstaken, slotstotal, usernames, totalgames, totalplayers FROM gamelist WHERE gamename != '' ORDER BY botid, id", $link);
		$array = array();
		
		while($row = mysql_fetch_row($result)) {
			$array[] = array('botid' => $row[0], 'gamename' => $row[1], 'ownername' => $row[2], 'creatorname' => $row[3], 'map' => $row[4], 'slotstaken' => $row[5], 'slotstotal' => $row[6], 'usernames' => $row[7], 'totalgames' => $row[8], 'totalplayers' => $row[9]);
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
function databaseGetGame($service_id, $game_id) {
	$game_id = escape($game_id);
	$link = databaseConnect($service_id);
	
	if($link) {
		$result = mysql_query("SELECT botid, gamename, ownername, creatorname, map, datetime, duration FROM games WHERE id = '$game_id'", $link);
		
		if($row = mysql_fetch_row($result)) {
			$array[] = array('botid' => $row[0], 'gamename' => $row[1], 'ownername' => $row[2], 'creatorname' => $row[3], 'map' => $row[4], 'datetime' => $row[5], 'duration' => $row[6], 'players' => array());
			
			$result = mysql_query("SELECT name, ip, spoofedrealm, left, leftreason FROM gameplayers WHERE id = '$game_id'", $link);
			
			while($row = mysql_fetch_row($result)) {
				$array['players'][] = array('name' => $row[0], 'ip' => $row[1], 'spoofedrealm' => $row[2], 'left' => $row[3], 'leftreason' => $row[4]);
			}
			
			return $array;
		} else {
			return false;
		}
	} else {
		return false;
	}
}

//bans a user
//$unban: if true it will unban instead of ban
//$name_only: if true it will only ban the username, not the IP addresses used
//$ban_aliases: if true it will also ban aliases
function databaseBanUser($service_id, $username, $realm, $duration, $reason, $unban = false, $name_only = false, $ban_aliases = false) {
	$link = databaseConnect($service_id);
	
	if(!$link) {
		return;
	}
	
	$username = escape(strtolower($username));
	$realm = escape($realm);
	$duration = escape(intval($duration) * 3600);
	$reason = escape($reason);
		
	$realms = array("uswest.battle.net", "useast.battle.net", "europe.battle.net", "asia.battle.net", "");
	if($realm != "") $realms = array($realm);
		
	foreach($realms as $realm_it) {
		$where = "WHERE name = '$username' AND spoofedrealm = '$realm_it'";
		
		//unban the user if we're supposed to
		if($unban) {
			mysql_query("DELETE FROM bans WHERE name = '$username' AND server = '$realm_it'", $link);
			continue;
		}
		
		//make sure user isn't already banned
		$result = mysql_query("SELECT COUNT(*) FROM bans WHERE name = '$username' AND server = '$realm_it' AND context = 'ttr.cloud'", $link);
		$row = mysql_fetch_row($result);
		if($row[0] > 0) {
			continue;
		}
		
		//last few IP addresses logged; limited to 15 addresses within the last 30 days
		$result = mysql_query("SELECT DISTINCT ip FROM gameplayers LEFT JOIN games ON gameplayers.gameid = games.id $where AND datetime > DATE_SUB( NOW( ), INTERVAL 30 DAY) ORDER BY gameplayers.id DESC LIMIT 15", $link);
	
		if(mysql_num_rows($result) > 0) {
			while($row = mysql_fetch_row($result)) {
				$ip = escape($row[0]);
				
				//if this is for non-spoofchecked users, ban on USWest by default
				$ban_realm = $realm_it;
				if($ban_realm == "") $ban_realm = "uswest.battle.net";
				
				mysql_query("INSERT INTO bans (botid, server, name, ip, date, gamename, admin, reason, expiredate, context) VALUES ('0', '$ban_realm', '$username', '$ip', CURDATE(), '', '$username_clean', '$reason', DATE_ADD( NOW( ), INTERVAL $duration second ), 'ttr.cloud')", $link);
			}
		} else {
			//no previous games found; ban by username only if this is an actual realm
			if($realm_it != "") {
				mysql_query("INSERT INTO bans (botid, server, name, ip, date, gamename, admin, reason, expiredate, context) VALUES ('0', '$realm_it', '$username', '', CURDATE(), '', '$username_clean', '$reason', DATE_ADD( NOW( ), INTERVAL $duration second ), 'ttr.cloud')", $link);
			}
		}
}

?>
