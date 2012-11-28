<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/announce.php");

if(isset($_SESSION['account_id'])) {
	$announcements = announceGet();
	get_page("announce", "panel", array('announcements' => $announcements));
} else {
	header("Location: ../");
}

?>
