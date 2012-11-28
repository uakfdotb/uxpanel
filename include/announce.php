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

//returns a sorted array of (announcement id, title, body, time)
function announceGet() {
	global $config, $db;
	
	$result = mysql_query("SELECT id, title, body, time FROM announcements ORDER BY time", $db);
	$array = array();
	
	while($row = mysql_fetch_row($result)) {
		$array[] = array($row[0], $row[1], $row[2], date($config['format_date'], $row[3]));
	}
	
	return $array;
}

?>
