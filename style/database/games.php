<h1>Game log</h1>

<table>

<tr>
	<td><a href="games.php?id=<?= $service_id ?>&start=<?= max($start - 30, 0) ?>">&lt;</a></td>
	<td colspan="4" style="text-align:center;">Displaying results <?= $start ?> to <?= $start + 30 ?></td>
	<td><a href="games.php?id=<?= $service_id ?>&start=<?= $start + 30 ?>">&gt;</a></td>
</tr>
<tr>
	<th>Gamename</th>
	<th>Creator</th>
	<th>Owner</th>
	<th>Map</th>
	<th>Date</th>
	<th>Duration</th>
</tr>

<? foreach($games as $game) { ?>
<tr>
	<td><a href="game.php?id=<?= $service_id ?>&gid=<?= $game['id'] ?>"><?= htmlspecialchars($game['gamename']) ?></a></td>
	<td><?= htmlspecialchars($game['creatorname']) ?></td>
	<td><?= htmlspecialchars($game['ownername']) ?></td>
	<td><?= htmlspecialchars($game['map']) ?></td>
	<td><?= htmlspecialchars($game['datetime']) ?></td>
	<td><?= htmlspecialchars($game['duration']) ?></td>
</tr>
<? } ?>

</table>
