<?php

/* Require global classes and functionality */
require_once('./include/global.php');
global $core, $document, $user, $db;


$document->header("Admin");

/* ================= Page Specific ====================== */

//DIE without admin
if (!$user->is_admin()) { fatal_user_error("Access denied","You do not have permission to view this page"); }

//Work out what page the user wants from dynamic page system
$admin_info = $_SERVER['REQUEST_URI'] ? $_SERVER['REQUEST_URI'] : $_SERVER['PHP_SELF'];
$admin_info = preg_replace("/.*admin[.]php/","",$admin_info);
$admin_info = explode("/",$admin_info);

$function = ""; $action = ""; $id = "";
if (count($admin_info) > 1) { $function = strtolower($admin_info[1]); }
if (count($admin_info) > 2) { $action = strtolower($admin_info[2]); }
if (count($admin_info) > 3) { $id = strtolower($admin_info[3]); }

//Make safe
$id = make_safe("int",$id);
$action = make_safe("text",$action);
$function = make_safe("text",$function);

//Template management
if ($function == "templates") {
	require_once("./functions/admin/templates.php");

	//Create a new templates
	if ($action == "add") { templates_add(); }
	else if ($action == "add2") { templates_add2(); }

	//Edit a templates
	else if ($action == "edit") {
		if ($id == "") { fatal_user_error("Invalid ID specified"); }
		templates_edit($id);
	}
	else if ($action == "edit2") {
		if (!isset($_POST['template_id']) || $_POST['template_id'] == "") { fatal_user_error("Invalid ID specified"); }
		$id = make_safe("int",$_POST['template_id']);
		templates_edit2($id);
	}

	//Delete a templates
	else if ($action == "delete") {
		if ($id == "") { fatal_user_error("Invalid ID specified"); }
		templates_delete($id);
	}
	//Delete a templates (confirmed)
	else if ($action == "delete2") {
		if ($id == "") { fatal_user_error("Invalid ID specified"); }
		templates_delete2($id);
	}

	//Otherwise list templates
	else {
		templates_list();
	}
}

//Category management
else if ($function == "categories") {
	require_once("./functions/admin/categories.php");

	//Create new categories
	if ($action == "add") { category_add(); }
	else if ($action == "add2") { category_add2(); }

	//Edit categories
	else if ($action == "edit") {
		if ($id == "") { fatal_user_error("Invalid ID specified"); }
		category_edit($id);
	}
	else if ($action == "edit2") {
		if (!isset($_POST['category_id']) || $_POST['category_id'] == "") { fatal_user_error("Invalid ID specified"); }
		$id = make_safe("int",$_POST['category_id']);
		category_edit2($id);
	}

	//Delete categories
	else if ($action == "delete") {
		if ($id == "") { fatal_user_error("Invalid ID specified"); }
		category_delete($id);
	}
	//Delete categories (confirmed)
	else if ($action == "delete2") {
		if ($id == "") { fatal_user_error("Invalid ID specified"); }
		category_delete2($id);
	}

	//Otherwise list categories
	else {
		category_list();
	}
}

//Category management
else if ($function == "boards") {
	require_once("./functions/admin/boards.php");

	//Create new boards
	if ($action == "add") { board_add(); }
	else if ($action == "add2") { board_add2(); }

	//Edit boards
	else if ($action == "edit") {
		if ($id == "") { fatal_user_error("Invalid ID specified"); }
		board_edit($id);
	}
	else if ($action == "edit2") {
		if (!isset($_POST['board_id']) || $_POST['board_id'] == "") { fatal_user_error("Invalid ID specified"); }
		$id = make_safe("int",$_POST['board_id']);
		board_edit2($id);
	}

	//Delete boards
	else if ($action == "delete") {
		if ($id == "") { fatal_user_error("Invalid ID specified"); }
		board_delete($id);
	}
	//Delete boards (confirmed)
	else if ($action == "delete2") {
		if ($id == "") { fatal_user_error("Invalid ID specified"); }
		board_delete2($id);
	}

	//Otherwise list boards
	else {
		board_list();
	}
}




//Block management
else if ($function == "blocks") {
	require_once("./functions/admin/blocks.php");

	//Create a new blocks
	if ($action == "add") { block_add(); }
	else if ($action == "add2") { block_add2(); }

	//Edit a blocks
	else if ($action == "edit") {
		if ($id == "") { fatal_user_error("Invalid ID specified"); }
		block_edit($id);
	}
	else if ($action == "edit2") {
		if (!isset($_POST['block_id']) || $_POST['block_id'] == "") { fatal_user_error("Invalid ID specified"); }
		$id = make_safe("int",$_POST['block_id']);
		block_edit2($id);
	}

	//Delete a blocks
	else if ($action == "delete") {
		if ($id == "") { fatal_user_error("Invalid ID specified"); }
		block_delete($id);
	}
	//Delete a blocks (confirmed)
	else if ($action == "delete2") {
		if ($id == "") { fatal_user_error("Invalid ID specified"); }
		block_delete2($id);
	}

	//Otherwise list blocks
	else {
		block_list();
	}
}


//User management
else if ($function == "users") {
	require_once("./functions/admin/users.php");
	user_list();
}

else if ($function == "config") {
	require_once("./functions/admin/config.php");
	if ($action == "save") {
		config_save();	
	}
	config_list();
}

//No action - just show the list
else if ($function == "") {
	$document->append_template("title",array('title'=>"Administration Centre"));
}

//Invalid action
else { fatal_user_error("Invalid administration action"); }

//Always show the admin box
$admin = "<p>Welcome to the Administration Centre</p>";
$admin .= $document->get_template("admin");

$document->append_template("window",array('title'=>"Administration Panel",'content'=>$admin));



/* End page-specific */
$document->footer();
$document->output();

?>
