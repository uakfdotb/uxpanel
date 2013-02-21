<?php

# this file provides jailing functionality to give extra security to uxpanel
# to use jailing functionality:
#  1. Create a separate Linux user for the panel user
#  2. Create the uxpanel service
#  3. Copy the uxpanel service files to the panel user's directory
#  4. Set parameters:
#      jail = 1
#      jail_user = Linux username
#      jail_path = path to service files with trailing slash
#  5. Delete the service files in the ghost_path directory, but leave the main directory there
#  6. Add to /etc/sudoers:
#      www-data ALL=(<Linux username>) NOPASSWD: ALL
#      (remove the brackets but leave the commas)
#  7. Maps directory is not jailed. So change ghost.cfg mappath to original directory.
#     Make sure that the directory is readable by the other user.
#  8. Repeat 7 for replays.

//returns true if jail is enabled and should be used, false otherwise
function jailEnabled($service_id) {
	return getServiceParam($service_id, "jail") !== false;
}

function jailPath($service_id) {
	return getServiceParam($service_id, "jail_path");
}

# filename must be escaped beforehand, and must be relative to the service base directory
# context is ghost, channel, or minecraft
# THIS IS NOT SUITABLE FOR LARGE FILES!
function jailFileOpen($service_id, $context, $filename) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return;
	}
	
	$jail_user = getServiceParam($service_id, "jail_user");
	$jail_path = getServiceParam($service_id, "jail_path");
	
	//identify the relative directory
	$local_file = $config[$context . "_path"] . $id . "/" . $filename;
	$lastSlash = strrpos($local_file, "/");
	$fDirectory = substr($local_file, 0, $lastSlash);
	
	//make the local directory if not exists
	if(!file_exists($fDirectory)) {
		mkdir($fDirectory, 0700, true);
	}
	
	//copy from remote directory to local directory
	// note that the local file must be written by www-data!
	$jail_file = $jail_path . $filename;
	exec("sudo -u " . escapeshellarg($jail_user) . " cat " . escapeshellarg($jail_file) . " > " . escapeshellarg($local_file));
	
	//now the caller can open this file locally
}

# see jailFileOpen for details
# this can also be used independently from jailFileOpen to write a file
function jailFileClose($service_id, $context, $filename, $write = true) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return;
	}
	
	$local_file = $config[$context . "_path"] . $id . "/" . $filename;
	
	if($write) {
		$jail_user = getServiceParam($service_id, "jail_user");
		$jail_path = getServiceParam($service_id, "jail_path");
	
		//copy from local directory to remote directory
		// note that the local file must be read by www-data, while the remote must be written by jail user
		$jail_file = $jail_path . $filename;
		exec("cat " . escapeshellarg($local_file) . " | sudo -u " . escapeshellarg($jail_user) . " tee " . escapeshellarg($jail_file));
	}
	
	//delete the local file so that no one can steal it and shit
	unlink($local_file);
}

# returns whether or not a file exists in the jail
function jailFileExists($service_id, $filename) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return;
	}
	
	$jail_path = getServiceParam($service_id, "jail_path");
	$jail_file = $jail_path . $filename;
	$array = array();
	return jailExecute($service_id, "[ -f " . escapeshellarg($jail_file) . " ] && echo \"1\" || echo \"0\"", $array, "bash") == "1";
}

function jailFileDelete($service_id, $filename) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return;
	}
	
	$jail_user = getServiceParam($service_id, "jail_user");
	$jail_path = getServiceParam($service_id, "jail_path");
	$jail_file = $jail_path . $filename;
	exec("sudo -u " . escapeshellarg($jail_user) . " rm " . escapeshellarg($jail_file));
}

function jailFileMove($service_id, $filename, $new_filename, $command = "mv") {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return;
	}
	
	$jail_user = getServiceParam($service_id, "jail_user");
	$jail_path = getServiceParam($service_id, "jail_path");
	$jail_file = $jail_path . $filename;
	$jail_file_new = $jail_path . $new_filename;
	exec("sudo -u " . escapeshellarg($jail_user) . " $command " . escapeshellarg($jail_file) . " " . escapeshellarg($jail_file_new));
}

function jailSymlink($service_id, $source, $relativeFilename) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return;
	}
	
	$jail_user = getServiceParam($service_id, "jail_user");
	$jail_path = getServiceParam($service_id, "jail_path");
	$jail_file = $jail_path . $relativeFilename;
	exec("sudo -u " . escapeshellarg($jail_user) . " ln -s " . escapeshellarg($source) . " " . escapeshellarg($jail_file));
}

function jailDirList($service_id, $relativePath, $extensions = array()) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return;
	}
	
	$jail_user = getServiceParam($service_id, "jail_user");
	$jail_path = getServiceParam($service_id, "jail_path");
	$jail_dir = $jail_path . $relativePath;
	
	$grepString = "";
	
	if(count($extensions) > 0) {
		$grepFor = implode("|", $extensions);
		$grepString = " | grep " . escapeshellarg($grepFor);
	}
	
	$array = array();
	exec("sudo -u " . escapeshellarg($jail_user) . " ls " . escapeshellarg($jail_dir) . $grepString, $array);
	return $array;
}

function jailDirCount($service_id, $relativePath, $extensions = array()) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return;
	}
	
	$jail_user = getServiceParam($service_id, "jail_user");
	$jail_path = getServiceParam($service_id, "jail_path");
	$jail_dir = $jail_path . $relativePath;
	
	$grepString = "";
	
	if(count($extensions) > 0) {
		$grepFor = implode("|", $extensions);
		$grepString = " | grep " . escapeshellarg($grepFor);
	}
	
	return exec("sudo -u " . escapeshellarg($jail_user) . " ls " . escapeshellarg($jail_dir) . $grepString . " | wc -l");
}

# executes a command as the jail user
function jailExecute($service_id, $command, &$array = NULL, $shell = "sh") {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return;
	}
	
	$jail_user = getServiceParam($service_id, "jail_user");
	return exec("sudo -u " . escapeshellarg($jail_user) . " $shell -c " . escapeshellarg($command), $array);
}

# executes a command as the jail user in background
function jailExecuteBackground($service_id, $command) {
	global $config;
	
	//get the identifier
	$id = stripAlphaNumeric(getServiceParam($service_id, "id"));
	
	if($id === false) {
		return;
	}
	
	$jail_user = getServiceParam($service_id, "jail_user");
	return execBackground("sudo -u " . escapeshellarg($jail_user) . " sh -c " . escapeshellarg($command));
}

?>
