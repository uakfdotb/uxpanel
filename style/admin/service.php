<h1>Service Manager</h1>

<? if(isset($message) && $message != "") { ?>
	<p><b><i><?= htmlspecialchars($message) ?></i></b></p>
<? } ?>

<p>Name: <?= htmlspecialchars($service['name']) ?>
<br />Description: <?= htmlspecialchars($service['description']) ?>
<br />Type: <?= htmlspecialchars($service['type']) ?></p>

<form method="post" action="service.php?action=set&id=<?= $id ?>">
Key: <input type="text" name="k" />
<br />Value: <input type="text" name="v" />
<br /><input type="checkbox" name="delete" value="delete" /> Delete this parameter
<br /><input type="submit" value="Set parameter" />
</form>

<table cellpadding="4">
<tr>
	<th>Key</th>
	<th>Value</th>
</tr>

<? foreach($parameters as $k => $v) { ?>
<tr>
	<td><?= htmlspecialchars($k) ?></td>
	<td><?= htmlspecialchars($v) ?></td>
</tr>
<? } ?>

<? if($service['type'] == "database") { ?>
	<script type="text/javascript">
	function submitSetupForm() {
	  document.setupForm.submit();
	}
	</script>
	
	<p>This appears to be a database service. If this has not yet been set up, <a href="javascript:submitSetupForm()">click here to do so</a>.</p>
	
	<form name="setupForm" method="post" action="service.php">
	<input type="hidden" name="action" value="setup" />
	<input type="hidden" name="id" value="<?= $id ?>" />
	</form>
<? } ?>

</table>
