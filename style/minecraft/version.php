<h1>Server version</h1>

<? if(isset($message) && $message != "") { ?>
<p><b><i><?= htmlspecialchars($message) ?></i></b></p>
<? } ?>

<p>Change your current server version using the form below. You can either link a version from our repository or upload your own Minecraft server JAR (note: upload is disabled on some panels).</p>

<form enctype="multipart/form-data" action="version.php" method="post">
<input type="hidden" name="id" value="<?= $service_id ?>" />
<input type="hidden" name="upload" value="yes" />
<input type="hidden" name="action" value="upload" />
<input type="hidden" name="MAX_FILE_SIZE" value="20000000" />
Choose a file to upload: <input name="uploaded_file" type="file" />
<input type="submit" value="Upload" />
</form>

<form action="version.php" method="post">
<input type="hidden" name="id" value="<?= $service_id ?>" />
<input type="hidden" name="action" value="link" />
<select name="filename">
	<? foreach($repositoryPlugins as $name) { ?>
	<option value="<?= htmlspecialchars($name) ?>"><?= htmlspecialchars($name) ?></option>
	<? } ?>
	</select>
<input type="submit" value="Link from repository" />
</form>

</table>
