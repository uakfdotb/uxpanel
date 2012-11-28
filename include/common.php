<?php

function string_begins_with($string, $search)
{
	return (strncmp($string, $search, strlen($search)) == 0);
}

function escape($str) {
	return mysql_real_escape_string($str);
}

function escapePHP($str) {
	return addslashes($str);
}

function chash($str) {
	return hash('sha512', $str);
}

function getExtension($file_name) {
  return substr(strrchr($file_name,'.'),1);  
}

function uid($length) {
	$characters = "0123456789abcdefghijklmnopqrstuvwxyz";
	$string = "";	

	for ($p = 0; $p < $length; $p++) {
		$string .= $characters[mt_rand(0, strlen($characters) - 1)];
	}

	return $string;
}

function unlink_if_exists($str) {
	if(file_exists($str)) {
		unlink($str);
		return true;
	}

	return false;
}

function isAlphaNumeric($str) {
	return ctype_alnum($str);
}

function stripAlphaNumeric($str) {
	return preg_replace("/[^a-zA-Z0-9\s]/", "", $str);
}

function recursiveCopy( $path, $dest )
{
	if( is_dir($path) )
	{
		mkdir( $dest );
		$objects = scandir($path);
		if( sizeof($objects) > 0 )
		{
			foreach( $objects as $file )
			{
				if( $file == "." || $file == ".." )
					continue;
				// go on
				if( is_dir( $path.DIRECTORY_SEPARATOR.$file ) )
				{
					recursiveCopy( $path.DIRECTORY_SEPARATOR.$file, $dest.DIRECTORY_SEPARATOR.$file );
				}
				else
				{
					copy( $path.DIRECTORY_SEPARATOR.$file, $dest.DIRECTORY_SEPARATOR.$file );
				}
			}
		}
		return true;
	}
	elseif( is_file($path) )
	{
		return copy($path, $dest);
	}
	else
	{
		return false;
	}
}

function delete_directory($dirname) {
	if (is_dir($dirname))
		$dir_handle = opendir($dirname);
	if (!$dir_handle)
		return false;
	while($file = readdir($dir_handle)) {
		if ($file != "." && $file != "..") {
			if (!is_dir($dirname."/".$file))
				unlink($dirname."/".$file);
			else
				delete_directory($dirname.'/'.$file);	 
		}
	}
	closedir($dir_handle);
	rmdir($dirname);
	return true;
}

function list_directory($dirname) {
	$results = array();
	$handler = opendir($dirname);

	// open directory and walk through the filenames
	while ($file = readdir($handler)) {
		if (substr($file, -4) == ".pdf") {
			$results[] = $file;
		}
	}

	closedir($handler);
	return $results;
}

//0: success; 1: less than 6 characters
function validPassword($password) {
	if(strlen($password) < 6) {
		return 1;
	}
	
	return 0;
}

//returns an absolute path to the include directory, without trailing slash
function includePath() {
	$self = __FILE__;
	$lastSlash = strrpos($self, "/");
	return substr($self, 0, $lastSlash);
}

//returns a relative path to the base / directory, without trailing slash
function basePath() {
	$commonPath = __FILE__;
	$requestPath = $_SERVER['SCRIPT_FILENAME'];
	
	//count the number of slashes
	// number of .. needed for include level is numslashes(request) - numslashes(common)
	// then add one more to get to base
	$commonSlashes = substr_count($commonPath, '/');
	$requestSlashes = substr_count($requestPath, '/');
	$numParent = $requestSlashes - $commonSlashes + 1;
	
	$basePath = ".";
	for($i = 0; $i < $numParent; $i++) {
		$basePath .= "/..";
	}
	
	return $basePath;
}

function timeString($time = -1) {
	global $config;
	
	if($time == -1) $time = time();
	return date($config['format_time'], $time);
}

function dateString($time = -1) {
	global $config;
	
	if($time == -1) $time = time();
	return date($config['format_date'], $time);
}

function ux_mail($subject, $body, $to) { //returns true=ok, false=notok
	$config = $GLOBALS['config'];
	$from = $config['mail_fromname'] . "<" . filter_var($config['mail_from'], FILTER_VALIDATE_EMAIL) . ">";
	$to = filter_var($to, FILTER_VALIDATE_EMAIL);
	
	if(!$to) {
		return;
	}
	
	if(isset($config['mail_smtp']) && $config['mail_smtp']) {
		require_once "Mail.php";

		$host = $config['mail_smtp_host'];
		$port = $config['mail_smtp_port'];
		$username = $config['mail_username'];
		$password = $config['mail_password'];
		$headers = array ('From' => $from,
						  'To' => $to,
						  'Subject' => $subject,
						  'Content-Type' => 'text/html');
		$smtp = Mail::factory('smtp',
							  array ('host' => $host,
									 'port' => $port,
									 'auth' => true,
									 'username' => $username,
									 'password' => $password));

		$mail = $smtp->send($to, $headers, $body);

		if (PEAR::isError($mail)) {
			return false;
		} else {
			return true;
		}
	} else {
		$headers = "From: $from\r\n";
		$headers .= "To: $to\r\n";
		$headers .= "Content-type: text/html\r\n";
		return mail($to, $subject, $body, $headers);
	}
}

function get_page($page, $context, $args = array()) {
	//let pages use some variables
	extract($args);
	$config = $GLOBALS['config'];
	$timeString = timeString();
	$basePath = basePath();
	
	//figure out what tabs to display in navbar
	if($context == "main") {
		$navbar = array();
	} else if($context == "ghost") {
		$navbar = array('./' => "Status", 'config.php' => "Configuration", 'map.php' => "Manage maps", 'execute.php' => "Execute command", 'log.php' => "View log", 'replay.php' => "Manage replays", '../panel/index.php?action=logout' => "Logout");
	} else if($context == "panel") {
		$navbar = array('./' => "Home", 'services.php' => "Services", 'announce.php' => "Announcements", 'index.php?action=logout' => "Logout");
	} else if($context == "database") {
		$navbar = array('./' => "Home", 'current.php' => 'Running games', 'games.php' => 'Game log', 'bans.php' => 'Ban management', 'admins.php' => 'Admins', '../panel/index.php?action=logout' => "Logout");
	} else if($context == "admin") {
		$navbar = array('./' => "Home", 'accounts.php' => "Accounts", 'status.php' => "Status", 'announce.php' => "Announcements", 'index.php?action=logout' => "Logout");
	} else {
		//oops, context should be one of the above
		return;
	}
	
	include("$basePath/style/header.php");
	include("$basePath/style/$context/$page.php");
	include("$basePath/style/footer.php");
}

//hex2bin, if it doesn't exist already
if(!function_exists('hex2bin')) {
	function hex2bin($h) {
		if (!is_string($h)) {
			return null;
		}
	
		$r = '';
		for($a = 0; $a < strlen($h); $a += 2) {
			$r .= chr(hexdec($h{$a} . $h{($a + 1)}));
		}
		return $r;
	}
}

//secure_random_bytes from https://github.com/GeorgeArgyros/Secure-random-bytes-in-PHP
/*
* The function is providing, at least at the systems tested :),
* $len bytes of entropy under any PHP installation or operating system.
* The execution time should be at most 10-20 ms in any system.
*/
function secure_random_bytes($len = 10) {
 
   /*
* Our primary choice for a cryptographic strong randomness function is
* openssl_random_pseudo_bytes.
*/
   $SSLstr = '4'; // http://xkcd.com/221/
   if (function_exists('openssl_random_pseudo_bytes') &&
       (version_compare(PHP_VERSION, '5.3.4') >= 0 ||
substr(PHP_OS, 0, 3) !== 'WIN'))
   {
      $SSLstr = openssl_random_pseudo_bytes($len, $strong);
      if ($strong)
         return $SSLstr;
   }

   /*
* If mcrypt extension is available then we use it to gather entropy from
* the operating system's PRNG. This is better than reading /dev/urandom
* directly since it avoids reading larger blocks of data than needed.
* Older versions of mcrypt_create_iv may be broken or take too much time
* to finish so we only use this function with PHP 5.3 and above.
*/
   if (function_exists('mcrypt_create_iv') &&
      (version_compare(PHP_VERSION, '5.3.0') >= 0 ||
       substr(PHP_OS, 0, 3) !== 'WIN'))
   {
      $str = mcrypt_create_iv($len, MCRYPT_DEV_URANDOM);
      if ($str !== false)
         return $str;	
   }


   /*
* No build-in crypto randomness function found. We collect any entropy
* available in the PHP core PRNGs along with some filesystem info and memory
* stats. To make this data cryptographically strong we add data either from
* /dev/urandom or if its unavailable, we gather entropy by measuring the
* time needed to compute a number of SHA-1 hashes.
*/
   $str = '';
   $bits_per_round = 2; // bits of entropy collected in each clock drift round
   $msec_per_round = 400; // expected running time of each round in microseconds
   $hash_len = 20; // SHA-1 Hash length
   $total = $len; // total bytes of entropy to collect

   $handle = @fopen('/dev/urandom', 'rb');
   if ($handle && function_exists('stream_set_read_buffer'))
      @stream_set_read_buffer($handle, 0);

   do
   {
      $bytes = ($total > $hash_len)? $hash_len : $total;
      $total -= $bytes;

      //collect any entropy available from the PHP system and filesystem
      $entropy = rand() . uniqid(mt_rand(), true) . $SSLstr;
      $entropy .= implode('', @fstat(@fopen( __FILE__, 'r')));
      $entropy .= memory_get_usage();
      if ($handle)
      {
         $entropy .= @fread($handle, $bytes);
      }
      else
      {	
         // Measure the time that the operations will take on average
         for ($i = 0; $i < 3; $i ++)
         {
            $c1 = microtime(true);
            $var = sha1(mt_rand());
            for ($j = 0; $j < 50; $j++)
            {
               $var = sha1($var);
            }
            $c2 = microtime(true);
     $entropy .= $c1 . $c2;
         }

         // Based on the above measurement determine the total rounds
         // in order to bound the total running time.
         $rounds = (int)($msec_per_round*50 / (int)(($c2-$c1)*1000000));

         // Take the additional measurements. On average we can expect
         // at least $bits_per_round bits of entropy from each measurement.
         $iter = $bytes*(int)(ceil(8 / $bits_per_round));
         for ($i = 0; $i < $iter; $i ++)
         {
            $c1 = microtime();
            $var = sha1(mt_rand());
            for ($j = 0; $j < $rounds; $j++)
            {
               $var = sha1($var);
            }
            $c2 = microtime();
            $entropy .= $c1 . $c2;
         }
            
      }
      // We assume sha1 is a deterministic extractor for the $entropy variable.
      $str .= sha1($entropy, true);
   } while ($len > strlen($str));
   
   if ($handle)
      @fclose($handle);
   
   return substr($str, 0, $len);
}

?>
