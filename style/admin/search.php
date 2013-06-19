<h1>Search</h1>

<p>Use the search form below to search for a service.</p>

<form action="search.php" method="get">
Service name: <input type="text" name="filter_name" />
<br />Service description: <input type="text" name="filter_description" />
<br />Service type: <input type="text" name="filter_type" />
<br />Parameter key: <input type="text" name="filter_sk" />
<br />Parameter value: <input type="text" name="filter_sv" />
<br /><input type="submit" value="Apply filters" />
</form>

<table class="table table-bordered">
<tr>
	<th>Name</th>
	<th>Email</th>
	<th>Service</th>
	<th>Type</th>
</tr>
<? foreach($result as $service) { ?>
<tr>
	<td><a href="account.php?id=<?= htmlentities($service['account_id']) ?>"><?= htmlentities($service['account_name']) ?></a></td>
	<td><a href="mailto:<?= htmlentities($service['account_email']) ?>"><?= htmlentities($service['account_email']) ?></a></td>
	<td><a href="service.php?id=<?= htmlentities($service['service_id']) ?>"><?= htmlentities($service['service_name']) ?></a></td>
	<td><?= htmlentities($service['service_type']) ?></td>
</tr>
<? } ?>
</table>

<? if(!empty($result)) { ?>
	<h3>Email list</h3>
	<pre><?
	$emails = array();
	$first = true;
	
	foreach($result as $service) {
		$email = $service['account_email'];
		if(in_array($email, $emails)) {
			continue;
		} else {
			$emails[] = $email;
		}
		
		if($first) {
			$first = false;
		} else {
			echo ", ";
		}
		
		echo htmlentities($email);
	}
	?></pre>
<? } ?>
