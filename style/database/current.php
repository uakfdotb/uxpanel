<h1>Running games</h1>

<p>A list of running games should appear below.</p>

<script type="text/javascript">
function showDetails(usernames) {
	var n = usernames.split("\t");
	var result = "<table><tr><th>Name</th><th>Ping</th></tr>";
	
	for(var i = 0; i * 3 < n.length - 2; i++) {
		var username = n[i * 3];
		var realm = n[i * 3 + 1];
		
		if(username == "") {
			continue;
		}
		
		var ping = n[i * 3 + 2];
		
		result += "<tr><td>" + username + "@" + realm + "</td><td>" + ping + "</td></tr>";
	}
	
	document.getElementById('details').innerHTML = result;
}
</script>

<div id="details">
</div>

<table cellpadding="4">
<tr>
	<th>Bot ID</th>
	<th>Gamename</th>
	<th>Owner name</th>
	<th>Creator name</th>
	<th>Map</th>
	<th>Slots</th>
	<th>Details</th>
</tr>

<? foreach($games as $game) { ?>
<tr>
	<td><?= htmlspecialchars($game['botid']) ?></td>
	<td><?= htmlspecialchars($game['gamename']) ?></td>
	<td><?= htmlspecialchars($game['ownername']) ?></td>
	<td><?= htmlspecialchars($game['creatorname']) ?></td>
	<td><?= htmlspecialchars($game['map']) ?></td>
	<td><?= htmlspecialchars($game['slotstaken']) . "/" . htmlspecialchars($game['slotstotal']) ?></td>
	<td><a href="javascript:showDetails(<?= htmlspecialchars(json_encode($game['usernames'])) ?>)">details</a></td>
<? } ?>

</table>
