<h1>Map configurations</h1>

<? if(isset($message) && $message != "") { ?>
<p><b><i><?= htmlspecialchars($message) ?></i></b></p>
<? } ?>

<p>This tool allows you to manage your map configuration files. To get started, add a configuration file using the form below.</p>

<? if($edit != "") { ?>
<p><b>Editing map configuration file: <?= $edit ?></b></p>
<form action="mapcfg.php" method="post">
<input type="hidden" name="id" value="<?= $service_id ?>" />
<input type="hidden" name="action" value="edit" />
<input type="hidden" name="filename" value="<?= $edit ?>" />
<textarea textarea rows="13" name="content" class="field span8"><?= htmlspecialchars($content) ?></textarea>
<br /><input type="submit" value="Update map config" />
</form>
<? } ?>

<form action="mapcfg.php" method="post">
<input type="hidden" name="id" value="<?= $service_id ?>" />
<input type="hidden" name="action" value="add" />
<input type="text" name="filename" />
<input type="submit" value="Add map config" />
</form>

<table>
<tr>
	<th>Filename</th>
	<th>Delete</th>
</tr>

<? foreach($list as $name) { ?>
<tr>
	<td><a href="mapcfg.php?id=<?= $service_id ?>&edit_filename=<?= urlencode($name) ?>"><?= htmlspecialchars($name) ?></a></td>
	<td>
		<form method="post" action="mapcfg.php">
		<input type="hidden" name="id" value="<?= $service_id ?>" />
		<input type="hidden" name="action" value="remove" />
		<input type="hidden" name="filename" value="<?= htmlspecialchars($name) ?>" />
		<input type="submit" value="Delete" />
		</form>
	</td>
</tr>
<? } ?>
</table>
