<?php

if(php_sapi_name() !== 'cli') {
	die("Access forbidden.");
}

$command = $argv[1];

$pid = pcntl_fork();

if ($pid === -1) {
	die('First fork failed!');
} else if ($pid) { // if parent
	exit(0);
}

/* Fork again (as session leader, so that session leader quits) */

posix_setsid(); // become session leader

$pid = pcntl_fork();

if ($pid === -1) {
	die('Second fork failed!');
} else if ($pid) { // if parent
	exit(0);
}

echo exec($command);

?>
