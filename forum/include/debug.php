<?php

/* Require global classes and functionality */
chdir("..");
require_once('./include/global.php');
global $core, $document, $user, $db;

/* ================= Page Specific ====================== */

$document->header("Test Page");
$document->append("<h2>Testing</h2>");
$document->output();

print_r($_SERVER);

/* End page-specific */
$document->clear();
$document->footer();
$document->output();

?>
