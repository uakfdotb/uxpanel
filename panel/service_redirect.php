<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");

if(isset($_SESSION['account_id']) && isset($_REQUEST['id'])) {
	$service_id = $_REQUEST['id'];
	if(getServiceOwner($service_id) == $_SESSION['account_id']) {
		$service_info = getService($service_id);
		$_SESSION['is_' . $service_id . '_' . $service_info['type']] = true;
		header("Location: ../{$service_info['type']}/?id=$service_id");
	}
} else {
	header("Location: ../");
}

?>
