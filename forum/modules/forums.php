<?php

/* Initialise all the forum components */

global $core;

//Include forum modules
require_once("./modules/forum/class_categories.php");
require_once("./modules/forum/class_boards.php");
require_once("./modules/forum/class_threads.php");
require_once("./modules/forum/class_posts.php");

//Include forum functions
require_once("./functions/forum/display.php");
require_once("./functions/forum/forms.php");

$categories = new Categories($core);
$boards = new Boards($core);
$threads = new Threads($core);
$posts = new Posts($core);

global $categories,$boards,$threads,$posts;

?>
