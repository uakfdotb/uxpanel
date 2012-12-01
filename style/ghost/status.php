<h1>Status</h1>

<p><b>Bot status: <font color="<?= $status['color'] ?>"><?= $status['status'] ?></font></b></p>

<? if(count($status['err']) > 0) { ?>
	<p>Errors:</p>
	<ul>
	<? foreach($status['err'] as $err) { ?>
		<li><?= htmlspecialchars($err) ?></li>
	<? } ?>
	</ul>
<? } ?>
