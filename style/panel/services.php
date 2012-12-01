<h1>Services</h1>
<p>Your products and services are listed below. Select a service for details and management options.</p>

<table cellpadding="4">
<tr>
	<th>Service name</th>
	<th>Price</th>
	<th>Expiration/due date</th>
</tr>

<? foreach($services as $service) { ?>
<tr>
	<td><a href="<?= htmlspecialchars($serviceExtra[$service['id']]['link']) ?>"><?= htmlspecialchars($service['name']) ?></a></td>
	<td><?= htmlspecialchars($serviceExtra[$service['id']]['price']) ?></td>
	<td><?= htmlspecialchars($serviceExtra[$service['id']]['due']) ?></td>
</tr>
<? } ?>

</table>
