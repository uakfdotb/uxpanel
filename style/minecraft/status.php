<h1>Status</h1>

<? if(isset($message) && $message != "") { ?>
<p><b><i><?= htmlspecialchars($message) ?></i></b></p>
<? } ?>

<p><b>Server status: </b> <?= $botStatus ?>
<br /><b>Connection status: <font color="<?= $status['color'] ?>"><?= $status['status'] ?></font></b></p>

<p><b>Memory utilization: </b> <?= $resources[1] ?> / <?= $resources[2] ?> MB
<br /><b>CPU utilization: </b> <?= $resources[0] ?>%

<? if(count($status['err']) > 0) { ?>
	<p>Errors:</p>
	<ul>
	<? foreach($status['err'] as $err) { ?>
		<li><?= htmlspecialchars($err) ?></li>
	<? } ?>
	</ul>
<? } ?>

<p>Press a button below to control your bot's status.</p>

<form method="post" action="index.php">
<input type="hidden" name="id" value="<?= $service_id ?>" />
<button type="submit" name="action" value="start">Startup</button>
<button type="submit" name="action" value="restart">Restart</button>
<button type="submit" name="action" value="stop">Stop</button>
</form>
