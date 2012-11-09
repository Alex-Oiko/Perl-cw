<?php

/* Fail Forum 0.1 */

//Stylesheet amalgamater
//So we can have a common stylesheet and specific stylesheets

//Set mode
header("Content-type: text/css");

//Load style
$style = trim($_GET['style']);

//Load the common shared file
$common = file_get_contents("./common.css");

//Load the style file
$style = file_get_contents("./$style");

//Combine the two
$style = $common . $style;

//Output the result
print $style;

?>
