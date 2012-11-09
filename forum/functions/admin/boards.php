<?php

/* List all boards */
function board_list() {
	global $core, $document, $db;
	$query = $db->make_query("boards");
	$query->set_order("board_order ASC");
	$boards_object = $query->execute();

	$document->append_template("simple_template",array('title'=>"Board List",'text'=>"All boards on this website"));	
	$listhtml = '<a href="$siteurl/admin.php/boards/add">Add new board</a><br/><br/>';

//Fetch all boards from database
	while($board = $boards_object->fetch_assoc()) {
		$listhtml .= $document->get_template("admin_board_item",$board);
	}

	//Make pretty
	$listhtml = $document->get_template("admin_board_list",array('boards'=>$listhtml));
	$document->append_template("window",array('title'=>"Boards",'content'=>$listhtml));
}

/* Add board form */
function board_add() {
	global $core, $document, $db;

	//Get categories
	$query = $db->make_query("categories");
	$query->set_order("category_order ASC");
	$categories_object = $query->execute();
	$category_html = "";
	while ($category = $categories_object->fetch_assoc()) {
		$selected = "";
		$category_html .= '<option value="' . $category['category_id'] . '" ' . $selected . '>' . $category['category_name'] . '</option>';
	}

	//Main options
	$form = $document->make_form("addboard","addboard","/admin.php/boards/add2");
	$form->start_fieldset("options","Add new board");
	$form->add_element("board_name","Title","text","","Title of the board");
	$form->add_element("category_id","Category","list",$category_html,"Category of the board");
	$form->add_element("board_description","Content","textarea","","Content of the board");
	$form->end_fieldset();

	//Other options
	$form->start_fieldset("moreoptions","Other options");
	$form->add_element("board_order","Order","number","","Board display order");
	$form->end_fieldset();

	//End
	$form->append('<div class="center">');
	$form->add_element_only("submit","Submit","submit","Create");
	$form->append('</div>');
	$formhtml = $form->output();

	$document->append_template("title",array('title'=>"Admin: Create Board"));
	$document->append_template("window",array('title'=>"Create Board",'content'=>$formhtml));
}

/* Edit board form */
function board_edit($id) {
	global $core, $document, $db;

	//Get board to edit
	$query = $db->make_query("boards");
	$query->add_condition("board_id","=",$id);
	$query->set_limit(1);
	$boards = $query->execute();
	$board = $boards->fetch_assoc();

	//Get categories
	$query = $db->make_query("categories");
	$query->set_order("category_order ASC");
	$categories_object = $query->execute();
	$category_html = "";
	while ($category = $categories_object->fetch_assoc()) {
		$selected = "";
		if ($category['category_id'] == $board['category_id']) { $selected = "selected=selected"; }
		$category_html .= '<option value="' . $category['category_id'] . '" ' . $selected . '>' . $category['category_name'] . '</option>';
	}

	//Main options
	$form = $document->make_form("editboard","editboard","/admin.php/boards/edit2");
	$form->start_fieldset("options","Editing board: " . $board["board_name"]);
	$form->add_element_only("board_id","ID","hidden",$board["board_id"]);
	$form->add_element("board_name","Title","text",$board["board_name"],"Title of the board");
	$form->add_element("category_id","Category","list",$category_html,"Category of the board");
	$form->add_element("board_description","Content","textarea",make_safe("text",$board["board_description"]),"Description of the board");
	$form->end_fieldset();

	//Other options
	$form->start_fieldset("moreoptions","Other options");
	$form->add_element("board_order","Order","number",$board["board_order"],
		"Ordering of board");
	$form->end_fieldset();

	//End
	$form->append('<div class="center">');
	$form->add_element_only("submit","Submit","submit","Save");
	$form->append('</div>');
	$formhtml = $form->output();

	$document->append_template("title",array('title'=>"Admin: Edit Board"));
	$document->append_template("window",array('title'=>"Edit Board",'content'=>$formhtml));
}

/* Commit board edit to database */
function board_edit2($id) {
	global $db, $document;

	//Prepare variables
	$title = $_POST['board_name'];
	$text = $_POST['board_description'];
	$category = $_POST['category_id'];
	$order = $_POST['board_order'];

	//Update database
	$query = $db->make_query("boards","UPDATE");
	$query->add_data("board_name",$title);
	$query->add_data("category_id",$category);
	$query->add_data("board_description",$text);
	$query->add_data("board_order",$order);
	$query->add_condition("board_id","=",$id);
	$query->set_limit(1);
	$query->execute();

	//Preview
	board_list();
}

/* Commit board add to database */
function board_add2() {
	global $db, $document;

	//Prepare variables
	$title = $_POST['board_name'];
	$text = $_POST['board_description'];
	$order = $_POST['board_order'];
	$category = $_POST['category_id'];

	//Update database
	$query = $db->make_query("boards","INSERT");
	$query->add_data("board_name",$title);
	$query->add_data("board_description",$text);
	$query->add_data("board_order",$order);
	$query->add_data("category_id",$category);
	$query->execute();

	$id = $db->get_auto_id();

	//Preview
	board_list($id);
}

/* Are you sure you want to delete this board? */
function board_delete($id) {
	global $db, $document;

	$document->append_template("window",array('title'=>"Delete board?",'content'=>"Are you sure you want to delete this board?<br/><br/>" .
		'<a href="$siteurl/admin.php/boards/delete2/' . $id . '">Yes</a> | <a href="$siteurl/index.php">No</a>'));

	board_list();
}

/* Commit board deletion to database */
function board_delete2($id) {
	global $db, $document;

	$query = $db->make_query("boards","DELETE");
	$query->add_condition("board_id","=",$id);
	$query->set_limit(1);
	$query->execute();

	$document->append_template("simple_template",array('title'=>"Board deleted",'text'=>"The board has been deleted"));
}

?>

