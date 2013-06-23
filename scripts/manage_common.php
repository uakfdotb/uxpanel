<?php

if(!isset($GLOBALS['IN_UXPANEL'])) {
	die("Access forbidden.");
}

function isInstalled($type) {
	if(file_exists("installed.txt")) {
		$fin = fopen('installed.txt', 'r');
		while (($buffer = fgets($fin, 4096)) !== false) {
			if(trim($buffer) == $type) {
				echo "Warning: skipping $type: already installed (modify installed.txt to change this)\n";
				return true;
			}
		}
		
		fclose($fin);
	}
	
	return false;
}

function installFinish($type) {
	//return to original directory
	chdir(realpath(dirname(__FILE__)));

	//updated installed.txt tracker
	$fout = fopen("installed.txt", 'a');
	fwrite($fout, $type . "\n");
	fclose($fout);
}

function rexec($command) {
	$descriptorspec = array(
		0 => array("pipe", "r"),   // stdin is a pipe that the child will read from
		1 => array("pipe", "w"),   // stdout is a pipe that the child will write to
		2 => array("pipe", "w")    // stderr is a pipe that the child will write to
	);
	
	print "*** EXECUTING: $command\n";
	$process = proc_open($command . " 2>&1", $descriptorspec, $pipes);

	if(is_resource($process)) {
		while ($s = fgets($pipes[1])) {
			print $s;
		}
	}
}

function installConfig() {
	require_once("../include/pbkdf2.php");
	$fout = fopen("../config_local.php_", 'w') or die("Could not write to local configuration file!");
	fwrite($fout, "<?php\n");
	
	fwrite($fout, '$config["site_name"] = "' . readline("Site name? ") . '";' . "\n");
	fwrite($fout, '$config["root_path"] = "' . readline("Root path (ex: /uxpanel/)? ") . '";' . "\n");
	fwrite($fout, '$config["mail_from"] = "' . readline("E-mail address to send as? ") . '";' . "\n");
	fwrite($fout, '$config["admin_username"] = "' . readline("Admin username? ") . '";' . "\n");
	fwrite($fout, '$config["admin_password"] = "' . pbkdf2_create_hash(readline("Admin password? ")) . '";' . "\n");
	fwrite($fout, '$config["admin_passwordtype"] = "pbkdf2";' . "\n");
	fwrite($fout, '$config["db_hostname"] = "' . readline("Database hostname? ") . '";' . "\n");
	fwrite($fout, '$config["db_name"] = "' . readline("Database name? ") . '";' . "\n");
	fwrite($fout, '$config["db_username"] = "' . readline("Database username? ") . '";' . "\n");
	fwrite($fout, '$config["db_password"] = "' . readline("Database password? ") . '";' . "\n");
	
	do {
		$slave = readline("Install as slave (y/n)? ");
	} while($slave != "y" && $slave != "n");
	$slave = $slave == "y";
	
	fwrite($fout, '$config["slave"] = ' . ($slave ? "true" : "false") . ';' . "\n");
	
	if($slave) {
		fwrite($fout, '$config["slave_master"] = "' . readline("Master URL (ex: http://master.yourdomain.com/uxpanel/)? ") . '";' . "\n");
		fwrite($fout, '$config["slave_id"] = ' . readline("Slave ID number? ") . ';' . "\n");
	}
	
	fwrite($fout, "?>\n");
	fclose($fout);
	rename("../config_local.php_", "../config_local.php");
}

function installPanelDependency() {
	if(isInstalled("deppanel")) {
		return;
	}
	
	rexec("apt-get -y install apache2 libapache2-mod-php5 zip unzip subversion build-essential");
	rexec("a2enmod php5");
	rexec("service apache2 restart");
	
	installFinish("deppanel");
}

function installGhostDependency() {
	if(isInstalled("depghost")) {
		return;
	}
	
	global $config;
	rexec("apt-get -y install libboost-all-dev libgmp3-dev libmysql++-dev mysql-client subversion libbz2-dev");
	
	chdir($config['root_path']);
	rexec("svn co http://ghostplusplus.googlecode.com/svn/trunk/bncsutil bncsutil");
	rexec("svn co http://ghostplusplus.googlecode.com/svn/trunk/StormLib StormLib");
	chdir($config['root_path'] . "bncsutil/src/bncsutil");
	rexec("make");
	rexec("make install");
	chdir($config['root_path'] . "StormLib/stormlib");
	rexec("make");
	rexec("make install");
	
	installFinish("depghost");
}

function installChannelDependency() {
	if(isInstalled("depchannel")) {
		return;
	}
	
	installGhostDependency();
	rexec("apt-get -y install python2.7-minimal python2.7-mysqldb");
	
	installFinish("depchannel");
}

function installMinecraftDependency() {
	if(isInstalled("depminecraft")) {
		return;
	}
	
	rexec("apt-get -y install openjdk-7-jre");
	
	installFinish("depminecraft");
}

function installGarenaDependency() {
	if(isInstalled("depgarena")) {
		return;
	}
	
	rexec("apt-get -y install openjdk-7-jre");
	
	installFinish("depgarena");
}

function installPanel() {
	global $config;
	
	if(isInstalled("panel")) {
		return;
	}
	
	# separate mkdir/chmod statements in case directory already exists
	mkdir($config['root_path']);
	chmod($config['root_path'], 0755);
	
	print "WARNING: if this is not running as the web server account, change the permissions of {$config['root_path']} to the web server account!\n";
	
	installFinish("panel");
}

function installGhost() {
	global $config;
	
	if(isInstalled("ghost")) {
		return;
	}
	
	chdir($config['root_path']);
	rexec("svn co http://ghostplusplus.googlecode.com/svn/trunk/ghost ghost-src");
	chdir($config['root_path'] . "ghost-src");
	rexec("make");
	mkdir($config['ghost_path']);
	chmod($config['ghost_path'], 0755);
	mkdir($config['ghost_path'] . "maps");
	chmod($config['ghost_path'] . "maps", 0755);
	copy($config['root_path'] . "ghost-src/ghost++", $config['ghost_path'] . "ghost++");
	rexec("wget -O " . escapeshellarg($config['ghost_path'] . "install.sql") . " http://ghostplusplus.googlecode.com/svn/trunk/mysql_create_tables_v2.sql");
	rexec("wget -O " . escapeshellarg($config['ghost_path'] . "language.cfg") . " http://ghostplusplus.googlecode.com/svn/trunk/language.cfg");
	
	installFinish("ghost");
}

function installChannel() {
	global $config;
	
	if(isInstalled("channel")) {
		return;
	}
	
	chdir($config['root_path']);
	rexec("svn co http://pychop.googlecode.com/svn/trunk/pychop channel-src");
	chdir($config['root_path'] . "channel-src/chop");
	rexec("make");
	mkdir($config['channel_path']);
	chmod($config['channel_path'], 0755);
	mkdir($config['channel_path'] . "plugins");
	chmod($config['channel_path'] . "plugins", 0755);
	copy($config['root_path'] . "channel-src/chop/chop++", $config['channel_path'] . "chop++");
	copy($config['root_path'] . "channel-src/language.cfg", $config['channel_path'] . "language.cfg");
	recursiveCopy($config['root_path'] . "channel-src/plugins", $config['channel_path'] . "plugins");
	
	installFinish("channel");
}

function installMinecraft() {
	global $config;
	
	if(isInstalled("minecraft")) {
		return;
	}
	
	mkdir($config['minecraft_path']);
	chmod($config['minecraft_path'], 0755);
	mkdir($config['minecraft_path'] . "plugins");
	chmod($config['minecraft_path'] . "plugins", 0755);
	mkdir($config['minecraft_path'] . "versions");
	chmod($config['minecraft_path'] . "versions", 0755);
	chdir($config['minecraft_path']);
	rexec("wget -O " . escapeshellarg($config['minecraft_path'] . "minecraft.jar") . " https://s3.amazonaws.com/MinecraftDownload/launcher/minecraft_server.jar");
	
	installFinish("minecraft");
}

function installGarena() {
	global $config;
	
	if(isInstalled("garena")) {
		return;
	}
	
	chdir($config['root_path']);
	rexec("svn co http://gcb.googlecode.com/svn/trunk/bin gcb-bin");
	mkdir($config['garena_path']);
	chmod($config['garena_path'], 0755);
	mkdir($config['garena_path'] . "lib");
	chmod($config['garena_path'] . "lib", 0755);
	copy($config['root_path'] . "gcb-bin/gcb.jar", $config['garena_path'] . "gcb.jar");
	copy($config['root_path'] . "gcb-bin/gcbrooms.txt", $config['garena_path'] . "gcbrooms.txt");
	copy($config['root_path'] . "gcb-bin/gkey.pem", $config['garena_path'] . "gkey.pem");
	copy($config['root_path'] . "gcb-bin/gcb.cfg", $config['garena_path'] . "gcb.cfg");
	recursiveCopy($config['root_path'] . "gcb-bin/lib", $config['garena_path'] . "lib");
	
	installFinish("garena");
}

?>
