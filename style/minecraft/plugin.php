<h1>Plugins</h1>

<? if(isset($message) && $message != "") { ?>
<p><b><i><?= htmlspecialchars($message) ?></i></b></p>
<? } ?>

<p>Here, you can manage the plugins on your Minecraft server. You can add plugins either by uploading one from your computer or linking one from our repository (note: upload is disabled on some panels).</p>

<form enctype="multipart/form-data" action="plugin.php" method="post">
<input type="hidden" name="id" value="<?= $service_id ?>" />
<input type="hidden" name="upload" value="yes" />
<input type="hidden" name="action" value="upload" />
<input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
Choose a file to upload: <input name="uploaded_file" type="file" />
<input type="submit" value="Upload" />
</form>

<form action="plugin.php" method="post">
<input type="hidden" name="id" value="<?= $service_id ?>" />
<input type="hidden" name="action" value="link" />
<select name="filename">
	<? foreach($repositoryPlugins as $name) { ?>
	<option value="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($name) ?></option>
	<? } ?>
	</select>
<input type="submit" value="Link from repository" />
</form>

<table>
<tr>
	<th>Filename</th>
	<th>Delete</th>
</tr>

<? foreach($userPlugins as $name) { ?>
<tr>
	<td><?= htmlspecialchars($name) ?></td>
	<td>
		<form method="post" action="plugin.php">
		<input type="hidden" name="id" value="<?= $service_id ?>" />
		<input type="hidden" name="action" value="remove" />
		<input type="hidden" name="filename" value="<?= htmlspecialchars($name) ?>" />
		<input type="submit" value="Delete" />
		</form>
	</td>
</tr>
<? } ?>
</table>
