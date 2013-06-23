<?php

if(php_sapi_name() !== 'cli' || !isset($argv)) {
	die("This is a command-line tool.\n");
} else if(count($argv) < 3) {
	echo "Usage: php manage.php action specifier\n";
	return;
} else if(!file_exists("../include/common.php")) {
	die("Error: script must be executed in the directory that it is located in.\n");
}

//since we're running from CLI and are in a subdirectory, we need to manually set the base path
$OVERRIDE_BASEPATH = "..";

include("../include/common.php");
include("../config.php");
include("manage_common.php");

$action = $argv[1];
$spec = $argv[2];

if($action == "install") {
	if($spec == "config") {
		installConfig();
	} else if($spec == "panel") {
		if(!in_array("nodep", $argv)) {
			installPanelDependency();
		}
		
		installPanel();
	} else if($spec == "ghost") {
		if(!in_array("nodep", $argv)) {
			installGhostDependency();
		}
		
		installGhost();
	} else if($spec == "channel") {
		if(!in_array("nodep", $argv)) {
			installChannelDependency();
		}
		
		installChannel();
	} else if($spec == "minecraft") {
		if(!in_array("nodep", $argv)) {
			installMinecraftDependency();
		}
		
		installMinecraft();
	} else if($spec == "garena") {
		if(!in_array("nodep", $argv)) {
			installGarenaDependency();
		}
		
		installGarena();
	}
} else if($action == "jail") {
	if(count($argv) < 4) {
		echo "Usage: php manage.php jail id username\n";
		return;
	}
	
	include("root_jail.php");
	$username = $argv[3];
	rootJail(intval($spec), $username);
}

?>
