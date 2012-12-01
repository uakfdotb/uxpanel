<h1>Aliases</h1>

<p>Enter player name and realm and you'll see aliases. Make sure to use format, name@realm. For example, player@uswest.battle.net. The ".battle.net" is optional for official realms.</p>

<form method="get" action="alias.php">
<input type="hidden" name="id" value="<?= $service_id ?>" />
Player (name@realm): <input type="text" name="player"> <input type="submit" value="Search">
</form>

<table>
<tr>
	<th>Name</th>
	<th>Realm</th>
	<th>Last seen</th>
</tr>

<? foreach($players as $player) { ?>
<tr>
	<td><a href="search.php?id=<?= $service_id ?>&username=<?= urlencode($player[0]) ?>&realm=<?= urlencode($player[1]) ?>"><?= htmlspecialchars($player[0]) ?></a></td>
	<td><?= htmlspecialchars($player[1]) ?></td>
	<td><?= htmlspecialchars($player[2]) ?></td>
</tr>
<? } ?>

</table>
