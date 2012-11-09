<?php

/* Require global classes and functionality */
require_once('./include/global.php');
global $core, $document, $user, $db;

$boardid = 3;

/* ================= Page Specific ====================== */
//Include forum functions
require('./modules/forums.php');

//Get this board
if (!isset($_GET['f'])) {
	fatal_error("No board specified");
}
	
$boardid = $_GET['f'];
$boardid = make_safe("int",$boardid);

//Get the board
$board = $boards->get_board($boardid);
if (!$board) { fatal_error("Invalid board"); }
$boardname = $board['board_name'];

//Build breadcrumb
$board['breadcrumb'] = '<p><img src="$siteurl/resources/images/$style/folder.gif"></img>&nbsp;';
$board['breadcrumb'] .= '<a href="$siteurl/forums.php" title="Home">$title</a> / ';
$board['breadcrumb'] .= '<a href="$siteurl/forumdisplay.php?f=' . $boardid . '">' . $boardname . '</a></p>';

//Build buttons
$buttons = "";

//Registered users
if ($user->get('user_type') > 0) {
	$buttons .= $document->get_template("forum_button",array('action' => "newthread.php?b=$boardid",
							'name' => "New Thread", 
							'image' => "newthread.png"));
}

$board['buttons'] = $buttons;

//Get the threads 
$threads = threads($boardid,$board);
$board['threads'] = $threads;

//Render page
$document->header("$boardname");

//Show forums
$document->append_template("forum_board_view",$board);

/* End page-specific */
$document->footer();
$document->output();

?>
