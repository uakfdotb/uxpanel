<h1>Replays</h1>

<? if(isset($message) && $message != "") { ?>
<p><b><i><?= htmlspecialchars($message) ?></i></b></p>
<? } ?>

<p>You can manage and download replays below. Note that there is a default limit of two hundred replays; after that, the oldest replays will be deleted automatically.</p>

<table>
<tr>
	<th>Filename</th>
	<th>Delete</th>
</tr>

<? foreach($replays as $name) { ?>
<tr>
	<td><a href="replay.php?id=<?= $service_id ?>&action=download&replay=<?= urlencode($name) ?>"><?= htmlspecialchars($name) ?></a></td>
	<td>
		<form method="post" action="replay.php">
		<input type="hidden" name="id" value="<?= $service_id ?>" />
		<input type="hidden" name="action" value="remove" />
		<input type="hidden" name="replay" value="<?= htmlspecialchars($name) ?>" />
		<input type="submit" value="Delete" />
		</form>
	</td>
</tr>
<? } ?>
</table>
