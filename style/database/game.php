<h1>Game information</h1>

<table>
<tr>
	<td>Gamename</td>
	<td><?= htmlspecialchars($game['gamename']) ?></td>
</tr>
<tr>
	<td>Bot ID</td>
	<td><?= htmlspecialchars($game['botid']) ?></td>
</tr>
<tr>
	<td>Owner</td>
	<td><?= htmlspecialchars($game['ownername']) ?></td>
</tr>
<tr>
	<td>Creator</td>
	<td><?= htmlspecialchars($game['creatorname']) ?></td>
</tr>
<tr>
	<td>Date</td>
	<td><?= htmlspecialchars($game['datetime']) ?></td>
</tr>
<tr>
	<td>Duration</td>
	<td><?= htmlspecialchars($game['duration']) ?></td>
</tr>
<tr>
	<td>Map</td>
	<td><?= htmlspecialchars($game['map']) ?></td>
</tr>
</table>

<p>Players in the game are listed below.</p>

<table>
<tr>
	<th>Username</th>
	<th>Realm</th>
	<th>IP</th>
	<th>Left time (sec)</th>
	<th>Left reason</th>
</tr>

<? foreach($game['players'] as $player) { ?>
<tr>
	<td><a href="search.php?id=<?= $service_id ?>&username=<?= urlencode($player['name']) ?>&realm=<?= urlencode($player['spoofedrealm']) ?>"><?= htmlspecialchars($player['name']) ?></a></td>
	<td><?= htmlspecialchars($player['spoofedrealm']) ?></td>
	<td><?= htmlspecialchars($player['ip']) ?></td>
	<td><?= htmlspecialchars($player['left']) ?></td>
	<td><?= htmlspecialchars($player['leftreason']) ?></td>
</tr>
<? } ?>

</table>
