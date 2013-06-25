<h1>Ban User</h1>

<? if(isset($message) && $message != "") { ?>
<p><b><i><?= $message ?></i></b></p>
<? } ?>

<p>Fill out the form below to ban a user. You may want to make sure (using the <a href="search.php?id=<?= $service_id ?>">User Search</a> function) that other players do not have the same username on other realms. Because this bans IP addresses that the player has used (unless you uncheck the <b>ban IP addresses</b> option), it is advised that you simply select the realm on which the user played in the game in which he or she was banned.</p>

<p>If you wish to <b>unban</b> a user, then check the unban box. If this is not selected it will instead ban the user.</p>

<p>If <b>ban aliases</b> is checked, then any aliases found using the <a href="alias.php?id=<?= $service_id ?>">alias tool</a> will be automatically banned.</p>

<form method="POST" action="ban.php">
<input type="hidden" name="id" value="<?= $service_id ?>" />
Username: <input type="text" name="username" />
<br />Reason: <input type="text" name="reason" />
<br />Realm: <select name="realm">
	<? foreach($realms as $realm) { ?>
	<option value="<?= htmlspecialchars($realm) ?>"><?= empty($realm) ? "LAN/Garena/Not spoofchecked" : htmlspecialchars($realm) ?></option>
	<? } ?>
	<option value="">All realms (use with caution!)</option>
	</select>
<br />Duration: <select name="duration">
	<option value="48">Two days (default)</option>
	<option value="2">Two hours</option>
	<option value="4">Four hours</option>
	<option value="8">Eight hours</option>
	<option value="12">Twelve hours</option>
	<option value="24">One day</option>
	<option value="36">Thirty-six hours</option>
	<option value="48">Two days</option>
	<option value="120">Five days</option>
	<option value="168">Seven days</option>
	<option value="240">Ten days</option>
	<option value="336">Two weeks</option>
	<option value="720">One month</option>
	<option value="2160">Three months</option>
	<option value="8640">One year</option>
	<option value="99999">Permanent</option>
	</select>
<br /><input type="checkbox" name="unban" value="unban" /> Unban user (if not checked, user will be banned)
<br /><input type="checkbox" name="nameonly" value="nameonly" /> Only ban by name (don't ban IP addresses)
<br /><input type="checkbox" name="aliases" value="aliases" /> Ban aliases as well
<br /><input type="submit" value="Ban user" />
</form>
