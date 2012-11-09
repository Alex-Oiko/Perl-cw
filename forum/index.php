<?php

/* Require global classes and functionality */
require_once('./include/global.php');
global $core, $document, $user, $db;

/* ================= Page Specific ====================== */

//Render page
$document->header("FailForum");

//Front page template
$document->append_template("front_page");

/* End page-specific */
$document->footer();
$document->output();

?>
