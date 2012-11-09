<?php

/* List all blocks */
function block_list() {
	global $core, $document, $db;
	$query = $db->make_query("blocks");
	$query->set_order("block_order ASC");
	$blocks_object = $query->execute();

	$document->append_template("simple_template",array('title'=>"Block List",'text'=>"All blocks on this website"));	
	$listhtml = '<a href="$siteurl/admin.php/blocks/add">Add new block</a><br/><br/>';

//Fetch all blocks from database
	while($block = $blocks_object->fetch_assoc()) {
		$listhtml .= $document->get_template("admin_block_item",$block);
	}

	//Make pretty
	$listhtml = $document->get_template("admin_block_list",array('blocks'=>$listhtml));
	$document->append_template("window",array('title'=>"Blocks",'content'=>$listhtml));
}

/* Add block form */
function block_add() {
	global $core, $document;
	
	//Main options
	$form = $document->make_form("addblock","addblock","/admin.php/blocks/add2");
	$form->start_fieldset("options","Add new block");
	$form->add_element("block_title","Title","text","","Title of the block");
	$form->add_element("block_content","Content","textarea","","Content of the block");
	$form->end_fieldset();

	//Other options
	$form->start_fieldset("moreoptions","Other options");
	$form->add_element_only("block_php","Use PHP","checkbox");
	$form->add_element("block_order","Order","text","","Block display order");
	$form->end_fieldset();

	//End
	$form->append('<div class="center">');
	$form->add_element_only("submit","Submit","submit","Create");
	$form->append('</div>');
	$formhtml = $form->output();

	$document->append_template("title",array('title'=>"Admin: Create Block"));
	$document->append_template("window",array('title'=>"Create Block",'content'=>$formhtml));
}

/* Edit block form */
function block_edit($id) {
	global $core, $document, $db;
	$query = $db->make_query("blocks");
	$query->add_condition("block_id","=",$id);
	$query->set_limit(1);
	$blocks = $query->execute();
	$block = $blocks->fetch_assoc();

	//Main options
	$form = $document->make_form("editblock","editblock","/admin.php/blocks/edit2");
	$form->start_fieldset("options","Editing block: " . $block["block_title"]);
	$form->add_element_only("block_id","ID","hidden",$block["block_id"]);
	$form->add_element("block_title","Title","text",$block["block_title"],"Title of the block");
	$form->add_element("block_content","Content","textarea",make_safe("text",$block["block_content"]),"Content of the block");
	$form->end_fieldset();

	//Other options
	$form->start_fieldset("moreoptions","Other options");
	$form->add_element_only("block_php","Use PHP","checkbox","","",($block["block_php"]) ? 'checked="checked"' : "");
	$form->add_element("block_order","Order","text",$block["block_order"],
		"Ordering of block");
	$form->end_fieldset();

	//End
	$form->append('<div class="center">');
	$form->add_element_only("submit","Submit","submit","Save");
	$form->append('</div>');
	$formhtml = $form->output();

	$document->append_template("title",array('title'=>"Admin: Edit Block"));
	$document->append_template("window",array('title'=>"Edit Block",'content'=>$formhtml));
}

/* Commit block edit to database */
function block_edit2($id) {
	global $db, $document;

	//Prepare variables
	$title = $_POST['block_title'];
	$text = $_POST['block_content'];
	$php = (isset($_POST['block_php'])) ? 1 : 0;
	$order = $_POST['block_order'];

	//Update database
	$query = $db->make_query("blocks","UPDATE");
	$query->add_data("block_title",$title);
	$query->add_data("block_content",$text);
	$query->add_data("block_php",$php);
	$query->add_data("block_order",$order);
	$query->add_condition("block_id","=",$id);
	$query->set_limit(1);
	$query->execute();

	//Preview
	block_list();
}

/* Commit block add to database */
function block_add2() {
	global $db, $document;

	//Prepare variables
	$title = $_POST['block_title'];
	$text = $_POST['block_content'];
	$php = (isset($_POST['block_php'])) ? 1 : 0;
	$order = $_POST['block_order'];

	//Update database
	$query = $db->make_query("blocks","INSERT");
	$query->add_data("block_title",$title);
	$query->add_data("block_content",$text);
	$query->add_data("block_php",$php);
	$query->add_data("block_order",$order);
	$query->execute();

	$id = $db->get_auto_id();

	//Preview
	block_list($id);
}

/* Are you sure you want to delete this block? */
function block_delete($id) {
	global $db, $document;

	$document->append_template("window",array('title'=>"Delete block?",'content'=>"Are you sure you want to delete this block?<br/><br/>" .
		'<a href="$siteurl/admin.php/blocks/delete2/' . $id . '">Yes</a> | <a href="$siteurl/index.php">No</a>'));

	block_list();
}

/* Commit block deletion to database */
function block_delete2($id) {
	global $db, $document;

	$query = $db->make_query("blocks","DELETE");
	$query->add_condition("block_id","=",$id);
	$query->set_limit(1);
	$query->execute();

	$document->append_template("simple_template",array('title'=>"Block deleted",'text'=>"The block has been deleted"));
}

?>

