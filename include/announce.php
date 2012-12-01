<?php

function announceAdd($title, $body) {
	global $db;
	
	$title = escape($title);
	$body = escape($body);
	mysql_query("INSERT INTO announcements (title, body, time) VALUES ('$title', '$body', '" . time() . "')", $db);
	return mysql_insert_id();
}

function announceDelete($id) {
	global $db;
	$id = escape($id);
	mysql_query("DELETE FROM announcements WHERE id = '$id'", $db);
}

//returns a sorted array of (id, title, body, time)
function announceGet() {
	global $config, $db;
	
	$result = mysql_query("SELECT id, title, body, time FROM announcements ORDER BY time", $db);
	$array = array();
	
	while($row = mysql_fetch_row($result)) {
		$array[] = array('id' => $row[0], 'title' => $row[1], 'body' => $row[2], 'time' => date($config['format_date'], $row[3]));
	}
	
	return $array;
}

?>
