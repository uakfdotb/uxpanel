<h1>Message configuration</h1>

<? if(isset($message) && $message != "") { ?>
<p><b><i><?= htmlspecialchars($message) ?></i></b></p>
<? } ?>

<p>You can edit messages such as welcome message, gameover message, and gameloaded message here. You can also edit the language configuration file.</p>

<form method="get" action="config_message.php">
<input type="hidden" name="id" value="<?= $service_id ?>" />
<select name="filename">
<? foreach($files as $i_filename) {
	$selected = $filename == $i_filename ? " selected" : ""; ?>
	<option value="<?= htmlspecialchars($i_filename) ?>"<?= $selected ?>><?= htmlspecialchars($i_filename) ?></option>
<? } ?>
</select>
<input type="submit" value="Edit" />
</form>

<? if($filename != "") { ?>
	<form method="post" action="config_message.php">
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="id" value="<?= $service_id ?>" />
	<input type="hidden" name="filename" value="<?= htmlspecialchars($filename) ?>" />
	<textarea rows="12" name="content" class="field span12"><?= htmlspecialchars($content) ?></textarea>
	<br /><input type="submit" value="Submit" />
	</form>
<? } ?>

