<h1>Log file</h1>

<p>Your log file appears below.</p>

<? if($log !== false) { ?>
	<textarea rows="30" name="content" class="field span12" readonly="true"><? foreach($log as $line) { echo htmlspecialchars($line) . "\n"; } ?></textarea>
<? } else { ?>
	<p><b><i>Error while reading log: probably doesn't exist.</i></b></p>
<? } ?>
