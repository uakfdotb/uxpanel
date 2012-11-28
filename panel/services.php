<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/account.php");

if(isset($_SESSION['account_id'])) {
	//get array of services, elements are array(service id, name, description, type)
	$services = getServices($_SESSION['account_id']);
	
	//get additional service parameters for each service
	$serviceExtra = getServiceExtra($services);
	
	//display
	get_page("services", "panel", array('services' => $services, 'serviceExtra' => $serviceExtra));
} else {
	header("Location: ../");
}

?>
