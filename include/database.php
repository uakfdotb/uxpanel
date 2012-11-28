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

//returns array of realm_server
// this is an internal function, so the database link must be provided
function databaseGetRealms($link) {
	$result = mysql_query("SELECT DISTINCT spoofedrealm FROM gameplayers", $link);
	$array = array();
	
	while($row = mysql_fetch_array($result)) {
		$array[] = $row[0];
	}
	
	return $array;
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
		
	$realms = databaseGetRealms($link);
	
	//find a realm to use by default that is not blank
	$default_realm = "";
	
	for($realms as $realm) {
		if($realm != "") {
			$default_realm = $realm;
			break;
		}
	}
	
	if($realm != "") $realms = array($realm);
		
	foreach($realms as $realm_it) {
		$where = "WHERE name = '$username' AND spoofedrealm = '$realm_it'";
		
		//unban the user if we're supposed to
		if($unban) {
			mysql_query("DELETE FROM bans WHERE name = '$username' AND server = '$realm_it'", $link);
			continue;
		}
		
		//make sure user isn't already banned
		$result = mysql_query("SELECT COUNT(*) FROM bans WHERE name = '$username' AND server = '$realm_it'", $link);
		$row = mysql_fetch_row($result);
		if($row[0] > 0) {
			continue;
		}
		
		//last few IP addresses logged; limited to 15 addresses within the last 30 days
		$result = mysql_query("SELECT DISTINCT ip FROM gameplayers LEFT JOIN games ON gameplayers.gameid = games.id $where AND datetime > DATE_SUB( NOW( ), INTERVAL 30 DAY) ORDER BY gameplayers.id DESC LIMIT 15", $link);
	
		if(mysql_num_rows($result) > 0) {
			while($row = mysql_fetch_row($result)) {
				$ip = escape($row[0]);
				
				//if this is for non-spoofchecked users, ban on default realm
				$ban_realm = $realm_it;
				if($ban_realm == "") $ban_realm = $default_realm;
				
				mysql_query("INSERT INTO bans (botid, server, name, ip, date, gamename, admin, reason, expiredate) VALUES ('0', '$ban_realm', '$username', '$ip', CURDATE(), '', '$username_clean', '$reason', DATE_ADD( NOW( ), INTERVAL $duration second ))", $link);
			}
		} else {
			//no previous games found; ban by username only if this is an actual realm
			if($realm_it != "") {
				mysql_query("INSERT INTO bans (botid, server, name, ip, date, gamename, admin, reason, expiredate) VALUES ('0', '$realm_it', '$username', '', CURDATE(), '', '$username_clean', '$reason', DATE_ADD( NOW( ), INTERVAL $duration second ))", $link);
			}
		}
	}
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
		$array[$row[0]] = array('name' => $row[1], 'server' => $row[2], 'ip' => $row[3], 'admin' => $row[4], 'gamename' => $row[5], 'reason' => $row[6], 'date' => $row[7], 'expiredate'] => $row[8]);
	}
	
	return $array;
}

function searchUser($service_id, $username, $realm) {
	$link = databaseConnect($service_id);
	
	if(!$link) {
		return array();
	}
	
	$username = escape($username);
	$realm = escape($realm);
	
	$realms = databaseGetRealms($link);
	if($realm != "") $realms = array($realm);
	
	foreach($realms as $realm_it) {
		$where = "WHERE name = '$username'";
		if($realm_it != "*") $where .= " AND realm = '$realm_it'";
		
		//grab general statistics
		$result = mysql_query("SELECT time_created, time_active, num_games, (total_leftpercent / num_games)*100, lastgames FROM gametrack $where", $link);
		$row = mysql_fetch_row($result);
		
		$firstgame = uxtDate(convertTime($row[0]));
		$lastgame = uxtDate(convertTime($row[1]));
		$totalgames = $row[2];
		$leftpercent = $row[3];
		$lastgames = $row[4];
		
		echo "<h2>$username@$realm_it</h2>";
		
		if($totalgames != 0) {
			echo "<h3>General statistics</h3>";
			echo "<table>";
			echo "<tr><th>Field</th><th>Value</th></tr>";
			echo "<tr><td>First game</td><td>" . $firstgame . "</td></tr>";
			echo "<tr><td>Last game</td><td>" . $lastgame . "</td></tr>";
			echo "<tr><td>Total games</td><td>" . $totalgames . "</td></tr>";
			echo "<tr><td>Left percent</td><td>" . $leftpercent . "</td></tr>";
			echo "</table>";
		
			//last few games
			echo "<h3>Last few games</h3>";
			
			$lastgames = explode(",", $lastgames);
			
			echo "<ul>";
			//process in reverse
			for($i = count($lastgames) - 1; $i >= 0; $i--) {
				$gameid = $lastgames[$i];
				$result = mysql_query("SELECT gamename FROM games WHERE id = '$gameid'");
				
				if($row = mysql_fetch_array($result)) {
					echo "<li><a href=\"game.php?id=$gameid\">" . $row[0] . "</a></li>";
				}
			}
			echo "<li><b><a href=\"games.php?username=$username&realm=$realm_it\">More</a></b></li>";
			echo "</ul>";
			
			//ban history
			$result = mysql_query("SELECT admin, reason, gamename, date, expiredate FROM ban_history WHERE name = '$username' AND server = '$realm_it' ORDER BY id DESC LIMIT 6");
			
			if(mysql_num_rows($result) > 0) {
				echo "<h3>Ban history</h3>";
			
				echo "<table cellpadding=\"2\">";
				echo "<tr><th>Admin</th><th>Reason</th><th>Gamename</th><th>Date</th><th>Expire</th></tr>";
				
				while($row = mysql_fetch_row($result)) {
					echo "<tr>";
					echo "<td>" . $row[0] . "</td>";
					echo "<td>" . $row[1] . "</td>";
					echo "<td>" . $row[2] . "</td>";
					echo "<td>" . $row[3] . "</td>";
					echo "<td>" . uxtDate(convertTime($row[4])) . "</td>";
					echo "</tr>";
				}
				echo "</table>";
			}
		} else {
			echo "<p><b><i>No games found for this user.</i></b></p>";
		}
	}
}

?>
