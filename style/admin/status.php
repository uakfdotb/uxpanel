<h1>Status</h1>

<h2>Overview</h2>

<table class="table table-bordered">
<? foreach($status as $k => $v) { ?>
<tr>
	<th><?= htmlentities($k) ?></th>
	<td><?= htmlentities($v) ?> </td>
</tr>
<? } ?>
</table>

<h2>Overdue services</h2>

<table class="table table-bordered">
<tr>
	<th>Due</th>
	<th>Price</th>
	<th>Service</th>
	<th>Account</th>
	<th>Email</th>
</tr>
<? foreach($overdue as $service) { ?>
<tr>
	<td><?= htmlentities(date($GLOBALS['config']['format_date'], $service['due'])) ?></td>
	<td><?= htmlentities($service['price']) ?></td>
	<td><a href="service.php?id=<?= htmlentities($service['service_id']) ?>"><?= htmlentities($service['service']) ?></a></td>
	<td><a href="account.php?id=<?= htmlentities($service['account_id']) ?>"><?= htmlentities($service['name']) ?></a></td>
	<td><?= htmlentities($service['email']) ?></td>
</tr>
<? } ?>
</table>

<h2>Due services</h2>

<table class="table table-bordered">
<tr>
	<th>Due</th>
	<th>Price</th>
	<th>Service</th>
	<th>Account</th>
	<th>Email</th>
</tr>
<? foreach($duesoon as $service) { ?>
<tr>
	<td><?= htmlentities(date($GLOBALS['config']['format_date'], $service['due'])) ?></td>
	<td><?= htmlentities($service['price']) ?></td>
	<td><a href="service.php?id=<?= htmlentities($service['service_id']) ?>"><?= htmlentities($service['service']) ?></a></td>
	<td><a href="account.php?id=<?= htmlentities($service['account_id']) ?>"><?= htmlentities($service['name']) ?></a></td>
	<td><?= htmlentities($service['email']) ?></td>
</tr>
<? } ?>
</table>
