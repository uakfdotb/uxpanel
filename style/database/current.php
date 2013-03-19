<h1>Running games</h1>

<p>A list of running games should appear below.</p>

<? foreach($games as $game) { ?>
	<h3><?= htmlspecialchars($game['gamename']) ?></h3>

	<ul>
	<li>Bot ID: <?= htmlspecialchars($game['botid']) ?></li>
	<li>Owner: <?= htmlspecialchars($game['ownername']) ?></li>
	<li>Creator: <?= htmlspecialchars($game['creatorname']) ?></li>
	<li>Map: <?= htmlspecialchars($game['map']) ?></li>
	</ul>

	<table>
	<tr>
		<th>Name</th>
		<th>Realm</th>
		<th>Ping</th>
	</tr>

	<?
	$username_array = explode("\t", $game['usernames']);

	for($i = 0; $i < count($username_array) - 2; $i += 3) {
	?>
		<tr>
			<td><?= htmlspecialchars($username_array[$i]) ?></td>
			<td><?= htmlspecialchars($username_array[$i + 1]) ?></td>
			<td><?= htmlspecialchars($username_array[$i + 2]) ?></td>
		</tr>
	<? } ?>
	</table>
<? } ?>
