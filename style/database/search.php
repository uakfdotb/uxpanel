<h1>User search</h1>

<p>You can search for a user on this page using the form below. All realms separately will display statistics for the username on each realm, while all realms aggregated will ignore the realm completely and just look up based on username. When using all realms separately or aggregated, you will also be able to see statistics when the username was not spoof checked; for the former, this will be labeled like "username@", with nothing following the @ symbol.</p>

<p>All times are GMT (UTC).</p>

<form method="GET" action="search.php">
<input type="hidden" name="id" value="<?= $service_id ?>" />
Username: <input type="text" name="username" />
<br />Realm: <select name="realm">
	<option value="">All realms separately</option>
	<option value="*">All realms aggregated</option>
	<? foreach($realms as $realm) { ?>
	<option value="<?= htmlspecialchars($realm) ?>"><?= htmlspecialchars($realm) ?></option>
	<? } ?>
	</select>
<br /><input type="submit" value="Search" />
</form>

<?

if($result != false) {
	foreach($result as $realm => $array) {
		echo "<h2>" . htmlspecialchars($username) . "@" . htmlspecialchars($realm) . "</h2>";
		
		if($array['totalgames'] != 0) {
			echo "<h3>General statistics</h3>";
			echo "<table>";
			echo "<tr><th>Field</th><th>Value</th></tr>";
			echo "<tr><td>First game</td><td>" . $array['firstgame'] . "</td></tr>";
			echo "<tr><td>Last game</td><td>" . $array['lastgame'] . "</td></tr>";
			echo "<tr><td>Total games</td><td>" . $array['totalgames'] . "</td></tr>";
			echo "<tr><td>Left percent</td><td>" . $array['leftpercent'] . "</td></tr>";
			echo "</table>";
		
			//last few games
			echo "<h3>Last few games</h3>";
			echo "<ul>";
			
			foreach($array['lastgames'] as $gid => $gamename) {
				echo "<li><a href=\"game.php?id=" . urlencode($service_id) . "&gid=" . urlencode($gid) . "\">" . htmlspecialchars($gamename) . "</a></li>";
			}
			echo "</ul>";
			
			//ban history
			if(count($array['bans']) > 0) {
				echo "<h3>Ban history</h3>";
			
				echo "<table cellpadding=\"2\">";
				echo "<tr><th>Admin</th><th>Reason</th><th>Gamename</th><th>Date</th><th>Expire</th></tr>";
				
				foreach($array['bans'] as $ban) {
					echo "<tr>";
					echo "<td>" . htmlspecialchars($ban['admin']) . "</td>";
					echo "<td>" . htmlspecialchars($ban['reason']) . "</td>";
					echo "<td>" . htmlspecialchars($ban['gamename']) . "</td>";
					echo "<td>" . htmlspecialchars($ban['date']) . "</td>";
					echo "<td>" . htmlspecialchars($ban['expiredate']) . "</td>";
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
