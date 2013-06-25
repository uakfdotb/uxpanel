<h1>Log file</h1>

<? if(!empty($message)) { ?>
<p><b><i><?= htmlspecialchars($message) ?></i></b></p>
<? } ?>

<p>Your log file appears below.</p>

<? if($log !== false) { ?>
	<textarea rows="30" id="log" name="content" class="field span12" readonly="true"><? foreach($log as $line) { echo htmlspecialchars($line) . "\n"; } ?></textarea>
	
	<script type="text/javascript">
	var textarea = document.getElementById('log');
	textarea.scrollTop = textarea.scrollHeight;
	</script>
<? } else { ?>
	<p><b><i>Error while reading log: probably doesn't exist.</i></b></p>
<? } ?>

<form method="post" action="log.php" class="form-inline">
<input type="hidden" name="id" value="<?= $service_id ?>" />
<input type="text" name="command" class="input-xlarge" />
<input type="submit" value="Submit rcon command" />
</form>
