<h1>Account manager</h1>

<form method="post" action="accounts.php?action=register">
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
</tr>

<? foreach($accounts as $id => $info) { //info is array(email, name) ?>
<tr>
	<td><a href="account.php?id=<?= $id ?>"><?= $info[0] ?></a></td>
	<td><?= $info[1] ?></td>
	<td><a href="accounts.php?action=delete&delete_id=<?= $id ?>">delete</a></td>
</tr>
<? } ?>

</table>
