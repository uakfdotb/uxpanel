<h1>Realm configuration</h1>

<? if(isset($message) && $message != "") { ?>
<p><b><i><?= htmlspecialchars($message) ?></i></b></p>
<? } ?>

<p>The realm configuration tool lets you add, remove, and modify your hosting bot's realm connections. To get started, add a realm by typing in either the server hostname or IP address and click "Add new realm connection". The official Battle.net realm hostnames are uswest.battle.net (Lordaeron), useast.battle.net (Azeroth), europe.battle.net (Northrend), and asia.battle.net (Kalimador).</p>

<p>To edit or delete an existing realm connection, select the connection in the dropdown list and hit "Edit existing". Then, either modify the values and press the submit button at the bottom of the page or scroll down and hit delete.</p>

<form method="post" action="config_realm.php">
<input type="hidden" name="action" value="add" />
<input type="hidden" name="id" value="<?= $service_id ?>" />
Server or IP address: <input type="text" name="server" />
<input type="submit" value="Add new realm connection" />
example: uswest.battle.net
</form>

<? if(count($bnets) > 0) { ?>
	<form method="get" action="config_realm.php">
	<input type="hidden" name="id" value="<?= $service_id ?>" />
	<select name="bnet">
	<? foreach($bnets as $i_bnet_id => $server) {
		$selected = $bnet_id == $i_bnet_id ? " selected" : ""; ?>
		<option value="<?= htmlspecialchars($i_bnet_id )?>"<?= $selected ?>><?= htmlspecialchars($server) ?></option>
	<? } ?>
	</select>
	<input type="submit" value="Edit existing" />
	</form>
<? } else { ?>
	<p>You do not currently have any existing realm connections. Add one using the form above.</p>
<? } ?>

<? if($bnet_id != 0 && $bconfig !== false) { ?>
	<form method="post" action="config_realm.php">
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="id" value="<?= $service_id ?>" />
	<input type="hidden" name="bnet" value="<?= htmlspecialchars($bnet_id) ?>" />

	<table cellpadding="4">
	<?
	foreach($bconfig as $k => $v) {
		ghostDisplayConfiguration($k, $v, $parameters);
	}
	?>
	</table>

	<input type="submit" value="Submit" />
	</form>
	
	<form method="post" action="config_realm.php">
	<input type="hidden" name="action" value="remove" />
	<input type="hidden" name="id" value="<?= $service_id ?>" />
	<input type="hidden" name="bnet" value="<?= htmlspecialchars($bnet_id) ?>" />
	<input type="submit" value="Delete realm" />
	</form>
<? } ?>

