<?php

/* Require global classes and functionality */
require_once('./include/global.php');
global $core, $document, $user, $db;

/* Above page-specific */
$document->header("New Thread"); 

/* ================= Page Specific ====================== */
//Include forum functions
require('./modules/forums.php');

//You need to be an administrator to do admin actions
if ($user->get('user_type') < 2) {
	fatal_error("Only administrators can use administrative functions");
}

//Ensure an action is specified
if (!isset($_GET['do'])) {
	fatal_error("No admin action specified");
} else {
	//Delete a thread
	if ($_GET['do'] == "delete") {
		if (!isset($_GET['t'])) { fatal_error("No thread specified"); }
		
		$threadid = make_safe("int",$_GET['t']);
		$thread = $threads->get_thread($threadid);
		$boardid = $thread['board_id'];
		if (!$thread) {
			fatal_error("Invalid thread specified");	
		}

		$threads->remove_thread($threadid);
		$core->do_redirect("forumdisplay.php?f=$boardid");
	//Delete a post
	} elseif ($_GET['do'] == "deletepost") {
		if (!isset($_GET['p'])) { fatal_error("No post specified"); }
			
		$postid = make_safe("int",$_GET['p']);
		$post = $posts->get_post($postid);
		$threadid = $post['thread_id'];
		$posts->delete_post($postid);
		$core->do_redirect("showthread.php?t=$threadid");
	//Invalid action
	} else {
		fatal_error("Invalid action specified");
	}
}


/* End page-specific */
$document->footer();
$document->output();

?>
