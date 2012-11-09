<?php

/* Require global classes and functionality */
chdir("..");
require_once('./include/global.php');
global $core, $document, $user, $db;

/* ================= Page Specific ====================== */
/* For testing only - don't deploy! */
$document->header("PHPInfo");
$document->append("<h2>PHPInfo</h2>");
$document->output();

phpinfo();

/* End page-specific */
$document->clear();
$document->footer();
$document->output();

?>
