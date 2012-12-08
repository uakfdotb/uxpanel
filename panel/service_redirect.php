<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");
include("../include/auth.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id'])) {
	$service_id = $_REQUEST['id'];
	if(getServiceOwner($service_id) == $_SESSION['account_id']) {
		$service_info = getService($service_id);
		$_SESSION['is_' . $service_id . '_' . $service_info['type']] = true;
		
		//check if we need to redirect to a slave uxpanel instance
		$slaveRedirect = getServiceParam($service_id, "slave");
		
		if(!$config['slave_enabled'] && $slaveRedirect != false) {
			//ok, register stuff into database and redirect to the other instance
			$ip = $_SERVER['REMOTE_ADDR'];
			$token = authRemoteRegister($_SESSION['account_id'], $service_id, $ip);
			
			header("Location: " . $slaveRedirect . "remote_login.php?user_id={$_SESSION['account_id']}&service_id=$service_id&ip=" . urlencode($ip) . "&token=$token");
		} else {
			header("Location: ../{$service_info['type']}/?id=$service_id");
		}
	}
} else {
	header("Location: ../");
}

?>
