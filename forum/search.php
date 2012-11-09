<?php

/* Require global classes and functionality */
require_once('./include/global.php');
global $core, $document, $user, $db;

/* Above page-specific */
$document->header("Search");

/* ================= Page Specific ====================== */
require_once('./functions/forum/forms.php');
require_once('./functions/forum/display.php');

if (!isset($_GET['do'])) {
//Show search form
	$search['heading'] = "Search";
	$search['description'] = "Search for posts on the form";
	$search['breadcrumb'] = '<p><img src="$siteurl/resources/images/$style/folder.gif"></img>&nbsp;';
	$search['breadcrumb'] .= '<a href="$siteurl/forums.php" title="Home">$title</a> / ';
	$search['breadcrumb'] .= 'Search </p>';
	$search['content'] = search_form();
	$document->append_template("forum_form",$search);
} else {
//Do search
	$text = make_safe("text",$_POST['text']);
	$search['heading'] = "Search Results";
	$search['description'] = "These are the results to your search for \"$text\"";
	$search['breadcrumb'] = '<p><img src="$siteurl/resources/images/$style/folder.gif"></img>&nbsp;';
	$search['breadcrumb'] .= '<a href="$siteurl/forums.php" title="Home">$title</a> / ';
	$search['breadcrumb'] .= 'Search Results </p>';
	$search['content'] = search_results($text);
	$document->append_template("forum_form",$search);
}


/* End page-specific */
$document->footer();
$document->output()

?>
