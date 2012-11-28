<h1>Services</h1>

<? if(isset($message) && $message != "") { ?>
	<p><b><i><?= $message ?></i></b></p>
<? } ?>

<p>Email address: <?= $info[0] ?>
<br />Name: <?= $info[1] ?></p>

<form method="post" action="account.php?action=add&id=<?= $id ?>">
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
	<td><a href="service.php?id=<?= $service[0] ?>"><?= $service[1] ?></a></td>
	<td><?= $serviceExtra[$service[0]]['price'] ?></td>
	<td><?= $serviceExtra[$service[0]]['due'] ?></td>
	<td><a href="account.php?id=<?= $id ?>&action=delete&delete_id=<?= $service[0] ?">delete</a></td>
</tr>
<? } ?>

</table>
