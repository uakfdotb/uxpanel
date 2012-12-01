<?php

include("../include/common.php");
include("../config.php");
include("../include/session.php");
include("../include/dbconnect.php");

include("../include/announce.php");

if(isset($_SESSION['admin'])) {
	if(isset($_POST['action'])) {
		if($_POST['action'] == "delete" && isset($_POST['delete_id'])) {
			announceDelete($_POST['delete_id']);
		} else if($_POST['action'] == "add" && isset($_POST['title']) && isset($_POST['body'])) {
			announceAdd($_POST['title'], $_POST['body']);
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
