<h1>World backup and restore</h1>

<? if(isset($message) && $message != "") { ?>
<p><b><i><?= htmlspecialchars($message) ?></i></b></p>
<? } ?>

<p>This tool allows you to backup or restore your world files.</p>

<form action="backup.php" method="post">
<input type="hidden" name="id" value="<?= $service_id ?>" />
<input type="hidden" name="action" value="backup" />
Backup label: <input type="text" name="label" />
<input type="submit" value="Backup world to archive" />
</form>

<table>
<tr>
	<th>Filename</th>
	<th>Restore</th>
	<th>Delete</th>
</tr>

<? foreach($backups as $name) { ?>
<tr>
	<td><?= htmlspecialchars($name) ?></td>
	<td>
		<form method="post" action="backup.php">
		<input type="hidden" name="id" value="<?= $service_id ?>" />
		<input type="hidden" name="action" value="restore" />
		<input type="hidden" name="filename" value="<?= htmlspecialchars($name) ?>" />
		<input type="submit" value="Restore" />
		</form>
	</td>
	<td>
		<form method="post" action="backup.php">
		<input type="hidden" name="id" value="<?= $service_id ?>" />
		<input type="hidden" name="action" value="remove" />
		<input type="hidden" name="filename" value="<?= htmlspecialchars($name) ?>" />
		<input type="submit" value="Delete" />
		</form>
	</td>
</tr>
<? } ?>
</table>
