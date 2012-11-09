<?php

/* List all categories */
function category_list() {
	global $core, $document, $db;
	$query = $db->make_query("categories");
	$query->set_order("category_order ASC");
	$categories_object = $query->execute();

	$document->append_template("simple_template",array('title'=>"Category List",'text'=>"All categories on this website"));	
	$listhtml = '<a href="$siteurl/admin.php/categories/add">Add new category</a><br/><br/>';

//Fetch all categories from database
	while($category = $categories_object->fetch_assoc()) {
		$listhtml .= $document->get_template("admin_category_item",$category);
	}

	//Make pretty
	$listhtml = $document->get_template("admin_category_list",array('categories'=>$listhtml));
	$document->append_template("window",array('title'=>"Categorys",'content'=>$listhtml));
}

/* Add category form */
function category_add() {
	global $core, $document;
	
	//Main options
	$form = $document->make_form("addcategory","addcategory","/admin.php/categories/add2");
	$form->start_fieldset("options","Add new category");
	$form->add_element("category_name","Title","text","","Title of the category");
	$form->add_element("category_description","Content","textarea","","Content of the category");
	$form->end_fieldset();

	//Other options
	$form->start_fieldset("moreoptions","Other options");
	$form->add_element("category_order","Order","number","","Category display order");
	$form->end_fieldset();

	//End
	$form->append('<div class="center">');
	$form->add_element_only("submit","Submit","submit","Create");
	$form->append('</div>');
	$formhtml = $form->output();

	$document->append_template("title",array('title'=>"Admin: Create Category"));
	$document->append_template("window",array('title'=>"Create Category",'content'=>$formhtml));
}

/* Edit category form */
function category_edit($id) {
	global $core, $document, $db;
	$query = $db->make_query("categories");
	$query->add_condition("category_id","=",$id);
	$query->set_limit(1);
	$categories = $query->execute();
	$category = $categories->fetch_assoc();

	//Main options
	$form = $document->make_form("editcategory","editcategory","/admin.php/categories/edit2");
	$form->start_fieldset("options","Editing category: " . $category["category_name"]);
	$form->add_element_only("category_id","ID","hidden",$category["category_id"]);
	$form->add_element("category_name","Title","text",$category["category_name"],"Title of the category");
	$form->add_element("category_description","Content","textarea",make_safe("text",$category["category_description"]),"Description of the category");
	$form->end_fieldset();

	//Other options
	$form->start_fieldset("moreoptions","Other options");
	$form->add_element("category_order","Order","number",$category["category_order"],
		"Ordering of category");
	$form->end_fieldset();

	//End
	$form->append('<div class="center">');
	$form->add_element_only("submit","Submit","submit","Save");
	$form->append('</div>');
	$formhtml = $form->output();

	$document->append_template("title",array('title'=>"Admin: Edit Category"));
	$document->append_template("window",array('title'=>"Edit Category",'content'=>$formhtml));
}

/* Commit category edit to database */
function category_edit2($id) {
	global $db, $document;

	//Prepare variables
	$title = $_POST['category_name'];
	$text = $_POST['category_description'];
	$order = $_POST['category_order'];

	//Update database
	$query = $db->make_query("categories","UPDATE");
	$query->add_data("category_name",$title);
	$query->add_data("category_description",$text);
	$query->add_data("category_order",$order);
	$query->add_condition("category_id","=",$id);
	$query->set_limit(1);
	$query->execute();

	//Preview
	category_list();
}

/* Commit category add to database */
function category_add2() {
	global $db, $document;

	//Prepare variables
	$title = $_POST['category_name'];
	$text = $_POST['category_description'];
	$order = $_POST['category_order'];

	//Update database
	$query = $db->make_query("categories","INSERT");
	$query->add_data("category_name",$title);
	$query->add_data("category_description",$text);
	$query->add_data("category_order",$order);
	$query->execute();

	$id = $db->get_auto_id();

	//Preview
	category_list($id);
}

/* Are you sure you want to delete this category? */
function category_delete($id) {
	global $db, $document;

	$document->append_template("window",array('title'=>"Delete category?",'content'=>"Are you sure you want to delete this category?<br/><br/>" .
		'<a href="$siteurl/admin.php/categories/delete2/' . $id . '">Yes</a> | <a href="$siteurl/index.php">No</a>'));

	category_list();
}

/* Commit category deletion to database */
function category_delete2($id) {
	global $db, $document;

	$query = $db->make_query("categories","DELETE");
	$query->add_condition("category_id","=",$id);
	$query->set_limit(1);
	$query->execute();

	$document->append_template("simple_template",array('title'=>"Category deleted",'text'=>"The category has been deleted"));
}

?>

