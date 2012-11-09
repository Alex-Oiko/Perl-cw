<?php

/* Document object */
class Document {

	/* Core object */
	public $core;

	/* HTLM generated */
	public $html = "";

	/* Variable array */
	public $variables = array();

	/* Constructor */
	function Document(&$core) { 
		$this->core =& $core;
	}

	/* Initialisation */
	public function init() { 
		//Setup initial values
		$this->variables['title'] = $this->core->get('title');
		$this->variables['username'] = $this->core->user->get('user_name');
		$this->variables['style'] = $this->core->user->get('user_style');
		$this->variables['siteurl'] = $this->core->config['Paths']['web'];
	}

	/* Header */
	public function header($page="") { 
		global $user;
		$variables['page'] = $page;

		$this->append_template("header",$variables);		
		$this->append_template("menu_" . $user->get('user_type'));

		//Do sidebar hooks
		$this->core->do_hooks('sidebar');

		$this->append_template("content_header",$variables);		
	}

	/* Footer */
	public function footer() {
		$this->append_template("content_footer");
		$this->append_template("footer");
	}

	/* Clear generated document and start afresh */
	public function clear() { $this->html = ""; }

	/* Set variable to be used as replacement  */
	public function set($name,$value) { $this->variables[$name] = $value; }

	/* Get variable */
	public function get($name) { return $this->variables[$name]; }

	/* Output document */
	public function output() {
		print $this->html;
	}

	/* Append text to document */
	public function append($newhtml) {
		$this->html .= $newhtml;
	}

	/* Append template to document */
	public function append_template($name,$variables=array()) {
		$this->append($this->get_template($name,$variables));
	}

	/* Get template from database */
	public function get_template($name,$variables=array()) {
		$database = $this->core->db;
		$query = $database->make_query("template");
		$query->set_fields("template_text,template_php");
		$query->add_condition("template_name","=",$name);
		$query->set_limit(1);
		$result = $query->execute();

		//Fail if no template, otherwise continue
		if($result->num_rows == 0) { fatal_error("Invalid template: $name"); }
		$resultarray = $result->fetch_assoc();
		$template = $resultarray['template_text'];
		$template_php = $resultarray['template_php'];

		//Substitute and return template
		$template = $this->template_substitution($template,$variables);

		//Handle PHP templates by evaluating their contents - currently disabled for safety
		if ($template_php) { ob_start(); eval($template); $template = ob_get_contents(); ob_end_clean(); }

		return $template;
	}

	/* Replace template variables with substitutions */
	private function template_substitution($html,$variables=array())
	{
		foreach ($variables as $variable=>$value) { $html = str_replace('$' . $variable,$value,$html); }
		foreach ($this->variables as $variable=>$value) { $html = str_replace('$' . $variable,$value,$html); }
		return $html;
	}


	/* Mini functions that map to templates */
	public function make_form($id,$name,$action="",$method="post",$upload=false) {
		$siteurl = $this->core->config['Paths']['web'];
		$action = "$siteurl$action";
		if ($id == "" || $name == "") { fatal_error("Missing form data for form creation"); }
		$form = new Form($this,$id,$name,$action,$method,$upload);
		return $form;
	}

	/* ToString */
	public function __toString() { $length = str_len($this->html); return "Document Object(Length:$length)"; }
}

class Form {
	/* Hold form data */
	private $form_html = "";
	private $document;
	private $form_details;

	/* Create a new form */
	public function Form(&$document,$id,$name,$action,$method,$upload=false) {
		$this->document =& $document;
		$this->form_details['id'] = $id;
		$this->form_details['name'] = $name;
		$this->form_details['action'] = $action;
		$this->form_details['method'] = $method;
		$this->form_details['additional'] = ($upload) ? 'enctype="multipart/form-data"' : "";
	}

	/* Append more form HTML */
	public function append($html) { $this->form_html .= $html; }

	/* Add new form element */
	public function add_element($id,$title,$type,$default="",$description="",$additional="") {
		if($id == "" || $title == "" || $type == "") { fatal_error("Invalid form element creation data"); }

		//Create element
		$element_variables['default'] = $default;
		$element_variables['additional'] = $additional;
		$element_variables['id'] = $id;
		$element = $this->document->get_template("form_element_$type",$element_variables);

		//Create template
		$variables['title'] = $title;
		$variables['element'] = $element;
		$variables['description'] = $description;
		$result = $this->document->get_template("form_element",$variables);
		$this->append($result);
	}

	/* Add new form element without form template */
	public function add_element_only($id,$title,$type,$default="",$description="",$additional="") {
		if($id == "" || $title == "" || $type == "") { fatal_error("Invalid form element creation data"); }

		//Create element
		$element_variables['default'] = $default;
		$element_variables['additional'] = $additional;
		$element_variables['id'] = $id;
		$element_variables['title'] = $title;
		$element_variables['description'] = $description;
		$element = $this->document->get_template("form_element_$type",$element_variables);
		$this->append($element);
	}

	/* Add title and description formatting */
	public function add_formatting($title,$description) {
		$variables['title'] = $title;
		$variables['description'] = $description;
		$this->append($this->document->get_template("form_element_only",$variables));
	}

	/* Start a set of fields */
	public function start_fieldset($id,$title) {
		$fieldset['title'] = $title;
		$fieldset['id'] = $id;
		$this->append($this->document->get_template("form_element_fieldset",$fieldset));
	}

	/* End a set of fields */
	public function end_fieldset() {
		$this->append($this->document->get_template("form_element_fieldset_end"));
	}

	/* Output the form */
	public function output() {
		$this->form_details['elements'] = $this->form_html;
		$form = $this->document->get_template("form",$this->form_details);
		return $form;
	}
}


?>
