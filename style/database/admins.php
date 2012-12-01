<h1>Admins</h1>

<p>You can view, add, and remove admins using this tool.</p>

<form method="post" action="admins.php">
<input type="hidden" name="id" value="<?= $service_id ?>" />
<input type="hidden" name="action" value="add" />
Username: <input type="text" name="name" />
<br />Realm: <input type="text" name="server" /> example: uswest.battle.net
<br />
<input type="submit" value="Add admin" />
</form>

<table>
<tr>
	<th>Name</th>
	<th>Realm</th>
	<th>Delete</th>
</tr>

<? foreach($admins as $a_id => $admin) { ?>
<tr>
	<td><?= htmlspecialchars($admin['name']) ?></td>
	<td><?= htmlspecialchars($admin['realm']) ?></td>
	<td>
		<form method="post" action="admins.php">
		<input type="hidden" name="id" value="<?= $service_id ?>" />
		<input type="hidden" name="action" value="delete" />
		<input type="hidden" name="delete_id" value="<?= $a_id ?>" />
		<input type="submit" value="delete" />
		</form>
	</td>
</tr>
<? } ?>

</table>
