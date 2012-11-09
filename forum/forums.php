<?php

/* Require global classes and functionality */
require_once('./include/global.php');
global $core, $document, $user, $db;

/* ================= Page Specific ====================== */
//Include forum functions
require_once('./modules/forums.php');

//Render page
$document->header("Forum Home");

//Get all boards and categories
$categories = new Categories($core);
$boards = new Boards($core);
$posts = new Posts($core);

$forum['forum_title'] = $core->get("title");
$forum['forum_description'] = $core->get("description");
$forum['categories'] = categories();

//Build breadcrumb
$forum['breadcrumb'] = '<p><img src="$siteurl/resources/images/$style/folder.gif"></img>&nbsp;';
$forum['breadcrumb'] .= '<a href="$siteurl/forums.php" title="Home">$title</a> / Index</p>';

//Show forums
$document->append_template("forum_front_view",$forum);

/* End page-specific */
$document->footer();
$document->output();

?>
