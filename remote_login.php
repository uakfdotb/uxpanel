<?php

include("include/common.php");
include("config.php");
include("include/session.php");
include("include/dbconnect.php");

include("include/auth.php");

if(isset($_REQUEST['user_id']) && isset($_REQUEST['service_id']) && isset($_REQUEST['ip']) && isset($_REQUEST['token'])) {
	//authenticate
	authRemote($_REQUEST['user_id'], $_REQUEST['service_id'], $_REQUEST['ip'], $_REQUEST['token']);
	
	//let service redirect handle the rest
	header("Location: panel/service_redirect.php?id=" . urlencode($_REQUEST['service_id']));
}

?>
