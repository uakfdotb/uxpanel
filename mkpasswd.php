<html>
<body>
<h1>uxpanel mkpasswd</h1>

<?php

include("include/pbkdf2.php");
include("include/common.php");

if(isset($_REQUEST['password'])) {
	$password = $_REQUEST['password'];
	$format = "pbkdf2";
	
	if(isset($_REQUEST['format'])) {
		if($_REQUEST['format'] == "hash") $format = "hash";
		else if($_REQUEST['format'] == "plain") $format = "plain";
	}
	
	if($format == "pbkdf2") {
		$password = pbkdf2_create_hash($password);
	} else if($format == "hash") {
		$password = chash($password);
	}
	
	echo "<p>mkpasswd result: $password</p>";
}

?>

<form method="POST" action="mkpasswd.php">
Password: <input type="password" name="password" />
<br />Format: <select name="format">
	<option value="pbkdf2">PBKDF2 (recommended)</option>
	<option value="hash">SHA-512</option>
	<option value="plain">Plain text</option>
<br /><input type="submit" value="mkpasswd" />
</form>
</body>
</html>
