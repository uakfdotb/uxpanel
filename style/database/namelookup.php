<h1>Name lookup</h1>

<p>Enter an IP address here, and I will look up the name. You can also enter a partial IP address, but make sure you have a leading dot. For example, "8.8.8.". But do not do "8.8.8", because you need leading dot or it won't do partial search.</p>

<form method="get" action="namelookup.php">
<input type="hidden" name="id" value="<?= $service_id ?>" />
IP: <input type="text" name="ip"> <input type="submit" value="Search">
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
