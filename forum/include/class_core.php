<?php

/* Core object which holds everything relevant to the current script execution */
class Core {

	/* Database object */
	public $db;
	
	/* User object */
	public $user;

	/* Session object */
	public $session;

	/* Configuration object */
	public $config;

	/* Hooks */
	public $hooks = array();

	/* State */
	public $state = 0;

	/* Constructor for Core object */
	public function Core() { }

	/* Initialise Core object */
	public function init() { 
		//Load configuration into config variable
		if (!file_exists('./include/config.php')) { fatal_error("Missing configuration file config.php"); }
		include('./include/config.php');
		$this->config =& $config;
	}

	/* Set a config property */
	public function set($key,$value) {
		$this->config[$key] = $value;
		//Update DB
		$query = $this->db->make_query("config","UPDATE");
		$query->add_condition("config_key","=",$key);
		$query->add_data("config_value",$value);
		$query->execute();		
	}

	/* Set multiple config properties */
	public function set_all($data) {
		foreach($data as $field=>$value) {
			$this->set($field,$value);
		}
	}

	/* Get a config property */
	public function get($key) { 
		//Check for valid property or default
		if (!isset($this->config[$key])) { fatal_error("Attempting to access non-existent user property: $key"); }
		return $this->config[$key];
	}

	/* Get a config property with default */
	public function get_default($key,$default="") {
		if (!isset($this->config[$key])) { return $default; }
		return $this->config[$key];
	}

	/* Load configuration values from database */
	public function load_config() {
		$query = $this->db->make_query("config");
		$query->set_fields("config_key,config_value");
		$result = $query->execute();

		//Populate configuration variables
		while ($row = $result->fetch_assoc()) {
			$key = $row['config_key'];
			$value = $row['config_value'];
			$this->config[$key] = $value;
		}
		$result->close();
	}

	/* Load hooks from database */
	public function load_hooks() {
		$query = $this->db->make_query("hooks");
		$result = $query->execute();

		//Populate configuration variables
		while ($row = $result->fetch_assoc()) {
			$key = $row['hook_name'];
			$value = $row['hook_code'];
			$this->hooks[$key] = $value;
		}
		$result->close();
	}

	/* Execute hooks if they exist */
	public function do_hooks($name) {
		if (isset($this->hooks[$name])) { eval($this->hooks[$name]); }
	}

	/* Validation */
		/* Format the given item in a pretty way */
	public function make_formatted($type,$text) {
		switch ($type) {
			case "plaintext":
				$text = nl2br($text);
				return $text;
				break;
			default: fatal_error("Invalid formatting type: $type");
				
		}
	}

	/* Handle uploads */
	public function do_upload($file) {	
		global $core;

		//Setup variables
		$target_dir = $this->get("upload_dir");
		$filename = $_FILES[$file]['name'];
		$tmpname = $_FILES[$file]['tmp_name'];

		//Disallow dangerous files
		if ($filename == '.htaccess') { fatal_error("Computer says no"); }
	
		//Set target
		$target_file = $target_dir . "/$filename";
		
		//Attempt to move uploaded file to uploads directory
		if (move_uploaded_file($tmpname,$target_file)) {
			return $this->get("upload_webdir") . "/" . $filename;
		}
		else {
			fatal_error("Upload of file $filename ($tmp_name) 
			failed.<br/><br/>Debug:<br/>" . print_r($_FILES,true));
		}

	}

	/* Redirect to a page */
	public function do_redirect($page) {
		global $core;
		$siteurl = $core->config['Paths']['web'];
		header("Location: $siteurl/$page");
		exit(0);
	}

}

?>
