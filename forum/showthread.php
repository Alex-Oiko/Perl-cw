<?php

/* Require global classes and functionality */
require_once('./include/global.php');
global $core, $document, $user, $db;

/* ================= Page Specific ====================== */
//Include forum functions
require('./modules/forums.php');

//Get this thread
if (!isset($_GET['t'])) {
	fatal_error("No thread specified");
}
	
$threadid = $_GET['t'];
$threadid = make_safe("int",$threadid);

//Get the thread
$thread = $threads->get_thread($threadid);
if (!$thread) { fatal_error("Invalid thread"); }
$threadname = $thread['thread_name'];

//Increment the view counter
$threads->update_stat($threadid,"views");

//Get the board the thread is in
$boardid = $thread['board_id'];
$board = $boards->get_board($boardid);
$boardname = $board['board_name'];

//Render posts
$posts_html = posts($threadid);
$thread['posts'] = $posts_html;

//Build breadcrumb
$thread['breadcrumb'] = '<p><img src="$siteurl/resources/images/$style/folder.gif"></img>&nbsp;';
$thread['breadcrumb'] .= '<a href="$siteurl/forums.php" title="Home">$title</a> / ';
$thread['breadcrumb'] .= '<a href="$siteurl/forumdisplay.php?f=' . $boardid . '">' . $boardname . '</a> / ';
$thread['breadcrumb'] .= '<a href="$siteurl/showthread.php?t=' . $threadid . '">' . $threadname . '</a></p>';

$buttons = "";
//Build buttons (admin)
if ($user->get('user_type') >= 2) {
$buttons .= $document->get_template("forum_button",array('action' => "adminthread.php?do=delete&amp;t=$threadid", 
							 'image' => 'deletethread.png',
							 'name' => "Delete Thread"));
}
//Build buttons (member)
if ($user->get('user_type') >= 1) {
$buttons .= $document->get_template("forum_button",array('action' => "newthread.php?b=$boardid", 
							'image' => "newthread.png",
							'name' => "New Thread"));
$buttons .= $document->get_template("forum_button",array('action' => "newreply.php?t=$threadid", 
							 'image' => 'reply.png',
							 'name' => "Post Reply"));
}
//Build buttons (guest)
 

$thread['buttons'] = $buttons;

//Render page
$document->header($threadname);

//Show forums
$document->append_template("forum_posts_view",$thread);

/* End page-specific */
$document->footer();
$document->output();

?>
