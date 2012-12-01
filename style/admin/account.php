<h1>Services</h1>

<? if(isset($message) && $message != "") { ?>
	<p><b><i><?= htmlspecialchars($message) ?></i></b></p>
<? } ?>

<p>Email address: <?= htmlspecialchars($info['email']) ?>
<br />Name: <?= htmlspecialchars($info['name']) ?></p>

<form method="post" action="account.php?id=<?= $id ?>">
<input type="hidden" name="action" value="add" />
Name: <input type="text" name="name" />
<br />Description: <input type="text" name="description" />
<br />Type: <input type="text" name="type" />
<br />Identifier: <input type="text" name="identifier" />
<br />Price: <input type="text" name="price" value="N/A" />
<br />Due: <input type="text" name="due" value="N/A" />
<br />ID3 (optional): <input type="text" name="id3" />
<br /><input type="submit" value="Add service" />
</form>

<table cellpadding="4">
<tr>
	<th>Service name</th>
	<th>Price</th>
	<th>Expiration/due date</th>
	<th>Delete</th>
</tr>

<? foreach($services as $service) { ?>
<tr>
	<td><a href="service.php?id=<?= urlencode($service['id']) ?>"><?= htmlspecialchars($service['name']) ?></a></td>
	<td><?= htmlspecialchars($serviceExtra[$service['id']]['price']) ?></td>
	<td><?= htmlspecialchars($serviceExtra[$service['id']]['due']) ?></td>
	<td>
		<form method="post" action="account.php?id=<?= $id ?>">
		<input type="hidden" name="action" value="delete" />
		<input type="hidden" name="delete_id" value="<?= htmlspecialchars($service['id']) ?>" />
		<input type="submit" value="delete" />
		</form>
	</td>
</tr>
<? } ?>

</table>
