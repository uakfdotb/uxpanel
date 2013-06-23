<?php

require_once("../include/account.php");
require_once("../include/dbconnect.php");

function rootJail($service_id, $username) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));

	if($id === false) {
		die("Error: identifier for this service has not been set!\n");
	}
	
	//get service type
	$type = getServiceType($service_id);
	
	if($type !== "ghost" && $type !== "minecraft" && $type !== "garena" && $type !== "channel") {
		//not a process-based service, seems like it can't be jailed?
		die("Error: service doesn't seem to be process-based (type=$type)!\n");
	}
	
	//add the system user if not exists
	if(!file_exists("/home/$username/")) {
		rexec('adduser --disabled-password --gecos "" ' . escapeshellarg($username));
	}
	
	//copy to own directory, and set permissions of result so that username is owner
	//we use cp to ensure proper handling of symlinks (PHP documentation doesn't guarantee this)
	$source_path = $config[$type . "_path"] . $id . "/";
	$target_path = "/home/$username/$id/";
	
	rexec("cp -r " . escapeshellarg($source_path) . " " . escapeshellarg($target_path));
	rexec("chown -R  " . escapeshellarg($username . ":" . $username) . " " . escapeshellarg($target_path));
	
	//also, depending on the service, user might need to access files in the service_path directory
	//so set permissions on that as well
	rexec("chown -R " . escapeshellarg(":" . $username) . " " . escapeshellarg($source_path));
	rexec("chmod -R 770 " . escapeshellarg($source_path));
	
	//depending on the service type, we may wish to rewrite some configuration files
	if($type == "ghost") {
		//the "maps" and "replays" directory should be changed over to use absolute path to the subdirectory of source
		// (since this is how include/ghost.php handles it)
		$escaped_source_path = str_replace(array('$', '/', '['), array('\\$', '\\/', '\\['), $source_path);
		rexec("sed -i " . escapeshellarg("s/bot_mappath = maps/bot_mappath = {$escaped_source_path}maps/") . " " . escapeshellarg($target_path . "default.cfg"));
		rexec("sed -i " . escapeshellarg("s/bot_replaypath = replays/bot_replaypath = {$escaped_source_path}replays/") . " " . escapeshellarg($target_path . "default.cfg"));
	}
}

?>
