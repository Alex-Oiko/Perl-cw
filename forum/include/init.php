<?php

//Include functions
require_once("./include/functions.php");

//Include core classes
require_once("./include/class_core.php");
require_once("./include/class_database.php");
require_once("./include/class_document.php");
require_once("./include/class_user.php");
require_once("./include/class_session.php");

//Initialise core
$core = new Core();
$core->init();

//Initialise database
$database = new Database($core);
$core->db =& $database;

//Load configuration
$core->load_config();
$core->load_hooks();

//Initialise session
$session = new Session($core);
$core->session =& $session;
$session->init();

//Initialise user
$user = new User($core);
$core->user =& $user;
$user->init();

//Initialise document
$document = new Document($core);
$core->document =& $document;
$document->init();

//Set error handler
set_error_handler("handle_error");
$core->state = 1;

?>
