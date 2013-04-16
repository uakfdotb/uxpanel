<?php

function announceAdd($title, $body) {
	global $db;
	
	$title = escape($title);
	$body = escape($body);
	$db->query("INSERT INTO announcements (title, body, time) VALUES ('$title', '$body', '" . time() . "')");
	return $db->insert_id;
}

function announceDelete($id) {
	global $db;
	$id = escape($id);
	$db->query("DELETE FROM announcements WHERE id = '$id'");
}

//returns a sorted array of (id, title, body, time)
function announceGet() {
	global $config, $db;
	
	$result = $db->query("SELECT id, title, body, time FROM announcements ORDER BY time DESC");
	$array = array();
	
	while($row = $result->fetch_array()) {
		$array[] = array('id' => $row[0], 'title' => $row[1], 'body' => $row[2], 'time' => date($config['format_date'], $row[3]));
	}
	
	$result->close();
	return $array;
}

?>
