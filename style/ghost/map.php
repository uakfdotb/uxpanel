<h1>Maps</h1>

<? if(isset($message) && $message != "") { ?>
<p><b><i><?= htmlspecialchars($message) ?></i></b></p>
<? } ?>

<p>Here, you can manage the maps on your bot. You can add maps either by uploading one from your computer or linking one from our repository.</p>

<form enctype="multipart/form-data" action="map.php" method="post">
<input type="hidden" name="id" value="<?= $service_id ?>" />
<input type="hidden" name="upload" value="yes" />
<input type="hidden" name="action" value="upload" />
<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
Choose a file to upload: <input name="uploaded_file" type="file" />
<input type="submit" value="Upload" />
</form>

<form action="map.php" method="post">
<input type="hidden" name="id" value="<?= $service_id ?>" />
<input type="hidden" name="action" value="link" />
<select name="filename">
	<? foreach($repositoryMaps as $name) { ?>
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

<? foreach($userMaps as $name) { ?>
<tr>
	<td><a href="map.php?id=<?= $service_id ?>&action=download&filename=<?= urlencode($name) ?>"><?= htmlspecialchars($name) ?></a></td>
	<td>
		<form method="post" action="map.php">
		<input type="hidden" name="id" value="<?= $service_id ?>" />
		<input type="hidden" name="action" value="remove" />
		<input type="hidden" name="filename" value="<?= htmlspecialchars($name) ?>" />
		<input type="submit" value="Delete" />
		</form>
	</td>
</tr>
<? } ?>
</table>
