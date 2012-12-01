<h1>Account</h1>

<? if(isset($message) && $message != "") { ?>
<p><b><i><?= htmlspecialchars($message) ?></i></b></p>
<? } ?>

<table class="table">
<tr>
	<th>Name</th>
	<td><?= $name ?></td>
</tr>
<tr>
	<th>E-mail address</th>
	<td><?= $email ?></td>
</tr>
</table>

<p>If you would like to change your password, use the form below.</p>

<form method="post" action="account.php">
<input type="hidden" name="action" value="changepass" />
Old password: <input type="password" name="old_password" />
<br />New password: <input type="password" name="new_password" />
<br />Confirm password: <input type="password" name="new_password_conf" />
<br /><input type="submit" value="Change password" />
</form>
