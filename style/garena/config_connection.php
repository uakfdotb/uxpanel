<h1>Room connection configuration</h1>

<? if(isset($message) && $message != "") { ?>
<p><b><i><?= htmlspecialchars($message) ?></i></b></p>
<? } ?>

<p>The room configuration tool lets you add, remove, and modify your Garena connection's rooms. To get started, add a room by typing in the room name and click "Add new room". See <a href="http://gcb.googlecode.com/svn/trunk/rooms.txt">this list</a> to find a room (the name you enter must be exact, like "USA DotA Room 01").</p>

<p>To edit or delete an existing room connection, select the connection in the dropdown list and hit "Edit existing". Then, either modify the values and press the submit button at the bottom of the page or scroll down and hit delete.</p>

<form method="post" action="config_connection.php">
<input type="hidden" name="action" value="add" />
<input type="hidden" name="id" value="<?= $service_id ?>" />
Room name: <input type="text" name="room" />
<input type="submit" value="Add new room" />
example: USA DotA Room 01
</form>

<? if(count($connections) > 0) { ?>
	<form method="get" action="config_connection.php">
	<input type="hidden" name="id" value="<?= $service_id ?>" />
	<select name="connection">
	<? foreach($connections as $i_connection_id => $roomname) {
		$selected = $connection_id == $i_connection_id ? " selected" : ""; ?>
		<option value="<?= htmlspecialchars($i_connection_id )?>"<?= $selected ?>><?= htmlspecialchars($roomname) ?></option>
	<? } ?>
	</select>
	<input type="submit" value="Edit existing" />
	</form>
<? } else { ?>
	<p>You do not currently have any existing room connections. Add one using the form above.</p>
<? } ?>

<? if($connection_id != 0 && $gconfig !== false) { ?>
	<form method="post" action="config_connection.php">
	<input type="hidden" name="action" value="update" />
	<input type="hidden" name="id" value="<?= $service_id ?>" />
	<input type="hidden" name="connection" value="<?= htmlspecialchars($connection_id) ?>" />

	<table cellpadding="4">
	<?
	foreach($gconfig as $k => $v) {
		garenaDisplayConfiguration($k, $v, $parameters);
	}
	?>
	</table>

	<input type="submit" value="Submit" />
	</form>
	
	<form method="post" action="config_connection.php">
	<input type="hidden" name="action" value="remove" />
	<input type="hidden" name="id" value="<?= $service_id ?>" />
	<input type="hidden" name="connection" value="<?= htmlspecialchars($connection_id) ?>" />
	<input type="submit" value="Delete room" />
	</form>
<? } ?>

