<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/announce.php");

if(isset($_SESSION['admin'])) {
	if(isset($_REQUEST['action'])) {
		if($_REQUEST['action'] == "delete" && isset($_REQUEST['delete_id'])) {
			announceDelete($_REQUEST['delete_id']);
		} else if($_REQUEST['action'] == "add" && isset($_REQUEST['title']) && isset($_REQUEST['body'])) {
			announceAdd($_REQUEST['title'], $_REQUEST['body']);
		}
		
		//don't want that post data remaining, instead redirect back
		header("Location: announce.php");
		return;
	}
	
	$announcements = announceGet();
	
	//display
	get_page("announce", "admin", array('announcements' => $announcements));
} else {
	header("Location: ./");
}

?>
