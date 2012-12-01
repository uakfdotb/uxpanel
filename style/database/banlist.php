<h1>Bans</h1>

<table cellpadding="2">
<tr>
	<th>Username</th>
	<th>Realm</th>
	<th>IP</th>
	<th>Admin</th>
	<th>Gamename</th>
	<th>Date</th>
	<th>Expires on</th>
	<th>Reason</th>
</tr>

<?

foreach($bans as $ban) {
	echo "<tr>";
	echo "<td><a href=\"search.php?id=$service_id&username=" . urlencode($ban['name']) . "&realm=" . urlencode($ban['server']) . "\">" . htmlspecialchars($ban['name']) . "</a></td>";
	
	$server = $ban['server'];
	
	if($server == "useast.battle.net") $server = "USEast";
	else if($server == "uswest.battle.net") $server = "USWest";
	else if($server == "asia.battle.net") $server = "Asia";
	else if($server == "europe.battle.net") $server = "Europe";
	else if($server == "cloud.ghostclient.com") $server = "Cloud";
	
	echo "<td>" . htmlspecialchars($server) . "</td>";
	echo "<td>" . htmlspecialchars($ban['ip']) . "</td>";
	echo "<td>" . htmlspecialchars($ban['admin']) . "</td>";
	
	//link to games search, but gamename might be blank in which case don't
	echo "<td>" . htmlspecialchars($ban['gamename']) . "</td>";
	
	echo "<td>" . htmlspecialchars($ban['date']) . "</td>";
	echo "<td>" . htmlspecialchars($ban['expiredate']) . "</td>";
	echo "<td>" . htmlspecialchars($ban['reason']) . "</td>";
	echo "</tr>";
}

?>

</table>
