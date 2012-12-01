<h1>IP lookup</h1>

<p>You can enter a player name here and get his or her IP address.</p>

<form method="get" action="iplookup.php">
<input type="hidden" name="id" value="<?= $service_id ?>" />
Player (name@realm): <input type="text" name="player"> <input type="submit" value="Search">
</form>

<table>
<tr>
	<th>IP</th>
</tr>

<?php

foreach($ips as $ip) {
	echo "<tr>\n";
	echo "\t<td>{$ip}</td>\n";
	echo "</tr>\n";
}

?>

</table>
