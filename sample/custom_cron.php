<?php

function dirInfo($path) {
	$fileInfo = array();
	$files = scandir($path);

	foreach($files as $file) {
		$filePath = rtrim($path, '/') . '/' . $file;
		
	    if (is_file($filePath)) {
	        $fileInfo[$filePath] = array(filesize($filePath), filectime($filePath));
	    }   
	}

	return $fileInfo;
}

function executeCron($service_id, $link, $params) {
	echo "Executing for service $service_id\n";
	
	if(isset($params['skipcron'])) {
		echo "skipcron skipping\n";
		return;
	}
	
	echo "bancache\n";
	//ban cache
	if(!isset($params['cron_updateban']) || $params['cron_updateban'] == 1) {
		updateBan($link);
	}
	
	echo "uscore\n";
	//update scores
	$db_settings = databaseSettings($service_id);
	
	if(isset($params['cron_dotascores']) && $params['cron_dotascores'] == 1) {
		updateDotaScores($db_settings);
	}
	
	if(isset($params['cron_w3mmdscores']) && $params['cron_w3mmdscores'] == 1) {
		updateW3MMDScores($db_settings);
	}
	
	echo "ads\n";
	//announce ads
	if(isset($params['ads']) && $params['ads'] == 1) {
		displayAds($link);
	}
	
	echo "gametrack\n";
	//gametrack script
	if(!isset($params['cron_gametrack']) || $params['cron_gametrack'] == 1) {
		$nextPlayer = 0;
		
		if(isset($params['gametrack_next'])) {
			$nextPlayer = $params['gametrack_next'];
		}
		
		$nextPlayer = gameTrack($link, $nextPlayer);
		setServiceParam($service_id, "gametrack_next", $nextPlayer);
	}
}

function executeCronOther($service_id, $type, $params) {
	global $config, $db;
	
	if(!isset($params['id'])) {
		return;
	}
	
	echo "Executing for service $service_id ($type)\n";
	
	echo "logcron\n";
	//log cron
	if(!isset($params['last_logcron']) || time() - $params['last_logcron'] > 3600 * 24) {
		$logname = "ghost";
		
		if($type == "channel") {
			$logname = "chop";
		} else if($type == "garena") {
			$logname = "gcb";
		}
		
		if(isset($params['jail']) && $params['jail'] == 1) {
			require_once("include/jail.php");
			jailFileMove($service_id, "$logname.log", "$logname.log_");
		} else {
			rename($config['ghost_path'] . $params['id'] . "/$logname.log", $config['ghost_path'] . $params['id'] . "/$logname.log_");
		}
		
		setServiceParam($service_id, "last_logcron", time());
	} else {
		if($type == "ghost") {
			require_once("include/ghost.php");
			$statusArray = ghostGetStatus($service_id);
		} else {
			require_once("include/channel.php");
			$statusArray = channelGetStatus($service_id);
		}
		
		if(!isset($GLOBALS['status_updates'])) {
			$GLOBALS['status_updates'] = array();
		}
		
		$problems = $statusArray['err'];
		sort($problems);
		
		if(count($problems) > 0) {
			$problemString = implode("\n", $problems);
			$result = $db->query("SELECT id, message FROM cron_problems WHERE service_id = '$service_id'");
			
			if($result && $row = $result->fetch_array()) {
				if($row[1] != $problemString) {
					$db->query("UPDATE cron_problems SET message = '" . escape($problemString) . "' WHERE id = '{$row[0]}'");
					
					foreach($problems as $problem) {
						//$GLOBALS['status_updates'][] = $service_id . ": " . $problem;
					}
				}
			} else {
				foreach($problems as $problem) {
					//$GLOBALS['status_updates'][] = $service_id . ": " . $problem;
				}
				
				$db->query("INSERT INTO cron_problems (service_id, message) VALUES ('$service_id', '" . escape($problemString) . "')");
			}
		} else {
			$result = $db->query("SELECT id FROM cron_problems WHERE service_id = '$service_id'");
			if($result && $row = $result->fetch_array()) {
				$db->query("DELETE FROM cron_problems WHERE id = '{$row[0]}'");
				//$GLOBALS['status_updates'][] = "$service_id: recovery!";
			}
		}
	}
	
	if($type == "ghost") {
		echo "replaycron\n";
		//replay cron
		$path = $config['ghost_path'] . $params['id'] . '/replays';
		
		if(isset($params['replay_path'])) {
			$path = $params['replay_path'];
		}
		
		$info = dirInfo($path);

		//delete everything older than fourteen days
		foreach($info as $filePath => $array) {
			if(getExtension($filePath) == "w3g" && time() - $array[1] > 14 * 60 * 60 * 24) {
				unlink($filePath);
				unset($info[$filePath]);
			}
		}
	}
}

function updateBan($link) {
	# fix context for odd bans
	$link->query("UPDATE bans SET context = 'ttr.cloud' WHERE context = '' OR context IS NULL");
	
	# add unrecorded bans to the ban history, but only 1000 at a time
	$result = $link->query("SELECT id, server, name, ip, date, gamename, admin, reason, expiredate, botid FROM bans WHERE id > ( SELECT IFNULL(MAX(banid), 0) FROM ban_history ) AND context = 'ttr.cloud' ORDER BY id LIMIT 1000");

	while($result && $row = $result->fetch_array()) {
		$id = escape($row[0]);
		$server = escape($row[1]);
		$name = escape($row[2]);
		$ip = escape($row[3]);
		$date = escape($row[4]);
		$gamename = escape($row[5]);
		$admin = escape($row[6]);
		$reason = escape($row[7]);
		$expiredate = escape($row[8]);
		$botid = escape($row[9]);
		
		# insert into history table
		$link->query("INSERT INTO ban_history ( banid, server, name, ip, date, gamename, admin, reason, expiredate ) VALUES ('$id', '$server', '$name', '$ip', '$date', '$gamename', '$admin', '$reason', '$expiredate')");
		
		# put banid in ban cache so that bots can update to it
		$link->query("INSERT INTO bancache (banid, datetime, status) VALUES ('$id', NOW(), 0)"); # 0 means new ban, 1 means del ban
	}

	# update cache to reflect deleted bans
	$result = $link->query("UPDATE bancache SET status = '1', datetime = NOW() WHERE status = '0' AND (SELECT COUNT(*) FROM bans WHERE bans.id = banid) = 0");
}

function updateDotaScores($db_settings) {
	//first, write to the configuration file with our settings
	$fout = fopen('/lg/update_dota_elo/update_dota_elo.cfg', 'w');
	fwrite($fout, "db_mysql_server = {$db_settings['server']}
db_mysql_database = {$db_settings['name']}
db_mysql_user = {$db_settings['username']}
db_mysql_password = {$db_settings['password']}
db_mysql_port = 3306");
	fclose($fout);
	
	//now, execute the score update script
	exec("cd /lg/update_dota_elo && ./update_dota_elo");
}

function updateW3MMDScores($db_settings) {
	//first, write to the configuration file with our settings
	$fout = fopen('/lg/update_w3mmd_elo/update_w3mmd_elo.cfg', 'w');
	fwrite($fout, "db_mysql_server = {$db_settings['server']}
db_mysql_database = {$db_settings['name']}
db_mysql_user = {$db_settings['username']}
db_mysql_password = {$db_settings['password']}
db_mysql_port = 3306");
	fclose($fout);
	
	//now, execute the score update script
	exec("cd /lg/update_w3mmd_elo && ./update_w3mmd_elo");
}

function displayAds($link) {
	$result = $link->query("SELECT DISTINCT botid FROM gamelist WHERE gamename != ''");
	
	while($result && $row = $result->fetch_array()) {
		$link->query("INSERT INTO commands (botid, command) VALUES ('{$row[0]}', '!saygames Get uxpanel from https://github.com/uakfdotb/uxpanel !')");
	}
}

function gameTrack($link, $next_player) {
	# track last bots that player used
	
	# create table if not already there
	$link->query("CREATE TABLE `gametrack` (
  `name` varchar(15) DEFAULT NULL,
  `realm` varchar(100) DEFAULT NULL,
  `bots` varchar(40) DEFAULT NULL,
  `lastgames` varchar(100) DEFAULT NULL,
  `total_leftpercent` double DEFAULT NULL,
  `num_leftpercent` int(11) DEFAULT NULL,
  `num_games` int(11) DEFAULT NULL,
  `time_created` datetime DEFAULT NULL,
  `time_active` datetime DEFAULT NULL,
  `playingtime` INT DEFAULT NULL,
  KEY `name` (`name`),
  KEY `realm` (`realm`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;");
	
	# get the next 5000 players
	$result = $link->query("SELECT gameplayers.botid, name, spoofedrealm, gameid, gameplayers.id, (`left`/duration), duration FROM gameplayers LEFT JOIN games ON games.id = gameid WHERE gameplayers.id >= '$next_player' ORDER BY gameplayers.id LIMIT 5000");

	while($result && $row = $result->fetch_array()) {
		$botid = intval($row[0]);
		$name = escape($row[1]);
		$realm = escape($row[2]);
		$gameid = escape($row[3]);
		$leftpercent = escape($row[5]);
		$duration = escape($row[6]);
	
		# see if this player already has an entry, and retrieve if there is
		$checkResult = $link->query("SELECT bots, lastgames FROM gametrack WHERE name = '$name' AND realm = '$realm'");
		
		if($checkResult && $checkRow = $checkResult->fetch_array()) {
			# update bots and lastgames shifting-window arrays
			$bots = explode(',', $checkRow[0]);
			$lastgames = explode(',', $checkRow[1]);
		
			if(in_array($botid, $bots)) {
				$bots = array_diff($bots, array($botid));
			}
		
			$bots[] = $botid;
			$lastgames[] = $gameid;
		
			if(count($bots) > 10) {
				array_shift($bots);
			}
		
			if(count($lastgames) > 10) {
				array_shift($lastgames);
			}
		
			$botString = escape(implode(',', $bots));
			$lastString = escape(implode(',', $lastgames));
			$link->query("UPDATE gametrack SET bots = '$botString', lastgames = '$lastString', total_leftpercent = total_leftpercent + '$leftpercent', num_leftpercent = num_leftpercent + 1, num_games = num_games + 1, time_active = NOW(), playingtime = playingtime + '$duration' WHERE name = '$name' AND realm = '$realm'");
		} else {
			$botString = escape($botid);
			$lastString = escape($gameid);
			$link->query("INSERT INTO gametrack (name, realm, bots, lastgames, total_leftpercent, num_leftpercent, num_games, time_created, time_active, playingtime) VALUES ('$name', '$realm', '$botString', '$lastString', '$leftpercent', '1', '1', NOW(), NOW(), '$duration')");
		}
	
		$next_player = $row[4] + 1;
	}
	
	return $next_player;
}

function executeCronShutdown() {
	$lines = array();
	$ip = exec("/bin/hostname");
	exec("/sbin/ifconfig", $lines);
	$rx_max = 0;
	$tx_max = 0;
	if(!isset($GLOBALS['status_updates'])) {
		$GLOBALS['status_updates'] = array();
	}

	foreach($lines as $line) {
		$line = trim($line);
		if(strpos($line, "inet addr") !== false) {
			$ip .= ":" . $line;
		}
		if(strpos($line, "bytes") === false) continue;
		
		$line_parts = explode("bytes:", $line);
		
		if(count($line_parts) >= 3) {
			$rx_parts = explode(" ", $line_parts[1]);
			$tx_parts = explode(" ", $line_parts[2]);
			$rx = $rx_parts[0];
			$tx = $tx_parts[0];
			if($rx > $rx_max) $rx_max = $rx;
			if($tx > $tx_max) $tx_max = $tx;
		} else {
			$GLOBALS['status_updates'][] = "Um... couldn't get the bandwidth: " . $line;
		}
	}

	$max = $rx_max + $tx_max;

	$contents = file_get_contents("/lg/bandwidth.txt");
	if($contents === false) {
		$GLOBALS['status_updates'][] = "Weird, couldn't read the bandwidth :(";
		echo "Failed to get bandwidth\n";
	} else {
		$contents_array = explode(":", trim($contents));
		$time = $contents_array[1];
		$bandwidth = ($max - $contents_array[0]) / (time() - $time);
		echo "Detected bandwidth: $bandwidth\n";
		if(time() <= $time) {
			$GLOBALS['status_updates'][] = "Error: time is less than bandwidth time??";
		} else if($bandwidth <= 0 || $bandwidth > 1000000) {
			$GLOBALS['status_updates'][] = "Too high bandwidth shit (or otherwise out of range): $bandwidth";
		}
	}

	file_put_contents("/lg/bandwidth.txt", $max . ":" . time());

	if(isset($GLOBALS['status_updates']) && count($GLOBALS['status_updates']) > 0) {
		echo "We have " . count($GLOBALS['status_updates']) . " status updates!\n";
		$status_string = "<p>Status from $ip</p><ul>";
		
		foreach($GLOBALS['status_updates'] as $update) {
			$status_string .= "<li>" . $update . "</li>";
		}
		
		$status_string .= "</ul>";
		
		require_once "Mail.php";

		$host = "tls://example.com";
		$port = 465;
		$username = 'noreply';
		$password = 'securepassword!';
		$headers = array ('From' => 'noreply@example.com',
						  'To' => 'noreply@example.com',
						  'Subject' => "Alert from uxpanel",
						  'Content-Type' => 'text/html');
		$smtp = Mail::factory('smtp',
							  array ('host' => $host,
									 'port' => $port,
									 'auth' => true,
									 'username' => $username,
									 'password' => $password));
	
		$mail = $smtp->send("yourmail@example.com", $headers, $status_string);

		if (PEAR::isError($mail)) {
			echo "Error while mailing " . $mail->getMessage() . " :(\n";
		}
	}
}

?>
