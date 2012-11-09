<?php

/* Require global classes and functionality */
require_once('./include/global.php');
global $core, $document, $user, $db;

/* ================= Page Specific ====================== */

//Render page
$document->header("Help");

$innerhtml = "<p>BBCode can be used to add formatting to posts</p>";
$innerhtml .= '<table class="window" width="100%">';
$innerhtml .= '<tr><td class="title">Name</td><td class="title">Description</td><td class="title">Usage</td><td class="title">Result</td></tr>';

add_bb("Bold","Makes text bold","<b>[b]</b>text<b>[/b]</b>","<b>text</b>");
add_bb("Italic","Makes text italic","<b>[i]</b>text<b>[/i]</b>","<i>text</i>");
add_bb("Underline","Makes text underlined","<b>[u]</b>text<b>[/u]</b>","<u>text</u>");
add_bb("Coloured Text","Colour in text","<b>[color=red]</b>Text<b>[/color]</b>","<font color=\"red\">Text</font>");
add_bb("Image","Links to an image","<b>[img]</b>\$siteurl/resources/images/\$style/forum.png<b>[/img]</b>","<img src=\"\$siteurl/resources/images/\$style/forum.png\"></img>");
add_bb("Link","Link to another URL","<b>[url=http://ecs.soton.ac.uk]</b>ECS<b>[/url]</b>","<a href=\"http://ecs.soton.ac.uk\">ECS</a>");
add_bb("Quote","Quote another user","<b>[quote=Who]</b>Text<b>[/quote]</b>","<div class=\"quote\">Text</div>");


$innerhtml .= '</table>';

$page['title'] = "FailForum Help";
$page['text'] = $innerhtml;
$document->append_template("simple_template",$page);

/* End page-specific */
$document->footer();
$document->output();

function add_bb($name,$description,$code,$result) {
	global $document, $innerhtml;
	$innerhtml .= '<tr><td class="windowbg">' . $name . '</td><td class="windowbg">' . $description . '</td><td class="windowbg">' . $code . '</td><td class="windowbg">' . $result . '</td></tr>';
}



?>
