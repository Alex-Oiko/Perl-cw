<?php

/* List templates */
function templates_list() {
	global $core, $document, $db;
	$query = $db->make_query("template");
	$query->set_order("template_name ASC");
	$templates = $query->execute();

	$document->append_template("simple_template",array('title'=>"Template List",'text'=>"All templates on this website"));	

	//Add links to create new
	$listhtml = '<a href="$siteurl/admin.php/templates/add">Add new template</a><br/><br/>';

	$listhtml .= "<div>";

	//Fetch all pages from database
	$block = "";
	while($template = $templates->fetch_assoc()) {
		$template_bits = explode("_",$template['template_name']);
		if ($block != $template_bits[0]) { $listhtml .= "</div><div class=\"item\">";
			$block = $template_bits[0]; 
		}
		
		$listhtml .= $document->get_template("admin_template_item",$template);
	}
	$listhtml .= "</div>";

	//Make pretty
	$listhtml = $document->get_template("admin_template_list",array('templates'=>$listhtml));
	$document->append_template("window",array('title'=>"Templates",'content'=>$listhtml));

}

/* Edit template form */
function templates_edit($id) {
	global $core, $document, $db;
	$query = $db->make_query("template");
	$query->set_limit(1);
	$query->add_condition("template_id","=",$id);
	$templates = $query->execute();
	$template = $templates->fetch_assoc();

	//Fix multiple replacements - pre
	$template["template_text"] = str_replace('$','${_DOLLAR_}',$template["template_text"]);

	//Main options
	$form = $document->make_form("edittemplate","edittemplate","/admin.php/templates/edit2");
	$form->start_fieldset("options","Editing template: " . $template["template_name"]);
	$form->add_element_only("template_id","ID","hidden",$template["template_id"]);
	$form->add_element("template_name","Name","text",$template["template_name"],"Template identification name");
	$form->add_element("template_text","Content","textarea",make_safe("text",$template["template_text"]),"Content of the template");
	$form->add_element_only("template_php","Use PHP","checkbox","","",($template["template_php"] ? 'checked="checked"' : ""));
	$form->append('<div class="center">');
	$form->add_element_only("submit","Submit","submit","Save");
	$form->append('</div>');
	$formhtml = $form->output();

	$document->append_template("title",array('title'=>"Admin: Edit Template"));
	$output = $document->get_template("window",array('title'=>"Edit Template",'content'=>$formhtml));
	
	//Fix multiple replacements - post
	$output = str_replace('${_DOLLAR_}','$',$output);

	$document->append($output);
}

/* Add template form */
function templates_add() {
	global $core, $document, $db;
	
	//Main options
	$form = $document->make_form("addtemplate","addtemplate","/admin.php/templates/add2");
	$form->start_fieldset("options","Add template");
	$form->add_element("template_name","Name","text","","Template identification name");
	$form->add_element("template_text","Content","textarea","","Content of the template");
	$form->add_element_only("template_php","Use PHP","checkbox");
	$form->append('<div class="center">');
	$form->add_element_only("submit","Submit","submit","Create");
	$form->append('</div>');
	$formhtml = $form->output();

	$document->append_template("title",array('title'=>"Admin: Create Template"));
	$document->append_template("window",array('title'=>"Create Template",'content'=>$formhtml));
}

/* Commit template edit to database */
function templates_edit2($id) {
	global $db, $document;

	//Prepare variables
	$name = $_POST['template_name'];
	$text = $_POST['template_text'];
	$php = (isset($_POST['template_php'])) ? 1 : 0;

	//Update database
	$query = $db->make_query("template","UPDATE");
	$query->add_data("template_name",$name);
	$query->add_data("template_text",$text);
	$query->add_data("template_php",$php);
	$query->add_condition("template_id","=",$id);
	$query->set_limit(1);
	$query->execute();

	//Return to list
	$document->append_template("window",array('title'=>"Template Administration",'content'=>"Template edited succesfully"));
	$document->append("<br/>");
	templates_list();
}

/* Commit template edit to database */
function templates_add2() {
	global $db, $document;

	//Prepare variables
	$name = $_POST['template_name'];
	$text = $_POST['template_text'];
	$php = (isset($_POST['template_php'])) ? 1 : 0;

	//Update database
	$query = $db->make_query("template","INSERT");
	$query->add_data("template_name",$name);
	$query->add_data("template_text",$text);
	$query->add_data("template_php",$php);
	$query->execute();

	//Return to list
	$document->append_template("window",array('title'=>"Template Administration",'content'=>"Template added succesfully"));
	$document->append("<br/>");
	templates_list();
}

/* Are you sure you want to delete this template? */
function templates_delete($id) {
	global $db, $document;

	$document->append_template("window",array('title'=>"Delete template?",'content'=>"Are you sure you want to delete this template?<br/><br/>" .
		'<a href="$siteurl/admin.php/templates/delete2/' . $id . '">Yes</a> | <a href="$siteurl/index.php">No</a>'));

}

/* Commit template deletion to database */
function templates_delete2($id) {
	global $db, $document;

	$query = $db->make_query("template","DELETE");
	$query->add_condition("template_id","=",$id);
	$query->set_limit(1);
	$query->execute();

	$document->append_template("window",array('title'=>"Template Administration",'content'=>"Template deleted succesfully"));
	$document->append("<br/>");
	templates_list();
}


?>
