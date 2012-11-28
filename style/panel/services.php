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
	<td><a href="<?= $serviceExtra[$service[0]]['link'] ?>"><?= $service[1] ?></a></td>
	<td><?= $serviceExtra[$service[0]]['price'] ?></td>
	<td><?= $serviceExtra[$service[0]]['due'] ?></td>
</tr>
<? } ?>

</table>
