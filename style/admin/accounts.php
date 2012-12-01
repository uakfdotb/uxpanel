<h1>Account manager</h1>

<form method="post" action="accounts.php">
<input type="hidden" name="action" value="register" />
Email address: <input type="text" name="email" />
<br />Password: <input type="text" name="password" />
<br />Name: <input type="text" name="name" />
<br /><input type="submit" value="Add account" />
</form>

<table>
<tr>
	<th>Email address</th>
	<th>Name</th>
	<th>Delete</th>
	<th>Morph</th>
</tr>

<? foreach($accounts as $id => $info) { //info is array(email, name) ?>
<tr>
	<td><a href="account.php?id=<?= $id ?>"><?= htmlspecialchars($info['email']) ?></a></td>
	<td><?= htmlspecialchars($info['name']) ?></td>
	<td>
		<form method="post" action="accounts.php">
		<input type="hidden" name="action" value="delete" />
		<input type="hidden" name="delete_id" value="<?= $id ?>" />
		<input type="submit" value="delete" />
		</form>
	</td>
	<td>
		<form method="post" action="accounts.php">
		<input type="hidden" name="action" value="morph" />
		<input type="hidden" name="morph_email" value="<?= htmlspecialchars($info['email']) ?>" />
		<input type="submit" value="morph" />
		</form>
	</td>
</tr>
<? } ?>

</table>
