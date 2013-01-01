<h1>Bans manager</h1>

<p>Welcome to the ban manager. Select an option below to continue.</p>

<ul>
<li><a href="search.php?id=<?= $service_id ?>">Search a user by username</a></li>
<li><a href="ban.php?id=<?= $service_id ?>">Ban or unban a user</a></li>
<li><a href="banlist.php?id=<?= $service_id ?>">List bans</a></li>
<li><a href="alias.php?id=<?= $service_id ?>">Find player aliases</a></li>
<li><a href="namelookup.php?id=<?= $service_id ?>">Find players who have used a given IP address</a></li>
<li><a href="iplookup.php?id=<?= $service_id ?>">Find IP addresses that a given player has used</a></li>
</ul>

<form method="post" action="ban.php">
<input type="hidden" name="id" value="{SERVICE_ID}" />
<input type="hidden" name="doclearbans" value="do" />
<input type="submit" name="clearbans" value="Clear all bans" />
</form>
