<?php

/* Document object */
class User {

	/* Core object */
	public $core;

	/* User settings */
	public $info;

	/* Constructor */
	public function User(&$core) { 
		$this->core =& $core;
		$this->set_defaults();
	}

	/* Initialisation for current user */
	public function init() {
		$currentuser = $this->core->session->get("user_id");
		//Check for active session
		if ($currentuser && $currentuser != -1) {
			$currentuser = $this->core->session->get("user_id");
			$this->load_from_userid($currentuser);
		}
		//Check for active cookie
		else {
			$name = $this->core->get('cookie_name');
			//If cookie exists
			if (isset($_COOKIE[$name])) {
				require_once("./functions/core/login.php");
				check_cookie($_COOKIE[$name]);
			}
		}
	
		//If we have a valid user now, update the session
		if ($this->info['user_id'] != -1) { $this->update_session(); }
	}

	/* Clear user */
	public function clear() { $this->set_defaults(); }

	/* Restore defaults */
	public function set_defaults() {
		global $core;
		$this->info = array();
		//Set defaults
		$this->info['user_name'] = "Guest";
		$this->info['user_id'] = -1;
		$this->info['user_type'] = 0;
		$this->info['user_style'] = $core->get('default_style');
	}

	/* Set a user property */
	public function set($key,$value) {
		$this->info[$key] = $value;
		//Update DB if not guest
		if ($this->info['user_id'] != -1) {
			$query = $this->core->db->make_query("user","UPDATE");
			$query->add_condition("user_id","=",$this->info['user_id']);
			$query->add_data($key,$value);
			$query->execute();		
		}
	}

	/* Get a user property */
	public function get($key) { 
		//Check for valid property or default
		if (!isset($this->info[$key])) { fatal_error("Attempting to access non-existent user property: $key"); }
		return $this->info[$key];
	}

	/* Get a user property with default */
	public function get_default($key,$default="") {
		if (!isset($this->info[$key])) { return $default; }
		if ($this->info[$key] == "") { return $default; }
		return $this->info[$key];
	}

	/* Set multiple user properties */
	public function set_all($data) {
		$query = $this->core->db->make_query("user","UPDATE");
		foreach($data as $field=>$value) {
			$this->info[$field] = $value;
			$query->add_data($field,$value);
		}
		//Push to DB if not guest
		if ($this->info['user_id'] != -1) { $query->add_condition("user_id","=",$this->info['user_id']); $query->execute(); }
	}

	/* Load data from a user name */
	public function load_from_username($username) {
		$id = $this->get_userid($username);
		return $this->load_from_userid($id);
	}

	/* Load data from a user ID */
	public function load_from_userid($userid,$error="true") {
		$db = $this->core->db;
		$query = $db->make_query("user");
		$query->add_condition("user_id","=",$userid);
		$query->set_limit(1);
		$result = $query->execute();
		
		//Check for valid user returned - if we want to error on this, error - otherwise return false
		if ($result->num_rows == 0) { 
			if ($error) { fatal_error("Invalid user specified - no user details returned"); } 
			else { return false; }
		}

		//Populate user info
		$fields = $result->fetch_assoc();
		foreach ($fields as $field=>$value) { if ($value != "") { $this->info[$field] = $value; } }
		return true;
	}

	/* Update session when user information has changed */
	public function update_session() {
		global $session;
		$session->set("user_id",$this->info['user_id']);
		$session->set("user_name",$this->info['user_name']);
		$style = $this->get_default('user_style',$this->core->get("default_style"));
		$session->set("style",$style);
	}
	
	/* Get user ID from user name */
	public function get_userid($username) {
		$db = $this->core->db;
		$query = $db->make_query("user");
		$query->set_fields("user_id");
		$query->add_condition("user_name","=",$username);
		$result = $query->execute();

		$user = $result->fetch_array();
		return ($result->num_rows > 0) ? $user['user_id'] : false;
	}

	/* Is administrator? */
	public function is_admin() { return ($this->get_default("user_type","-1") == 2); }

	/* Is registered? */
	public function is_registered() { return ($this->get_default("user_type","-1") > 0); }

	/* Check if user exists */
	public function exists($username) { return $this->get_userid($username,false); }

	/* ToString */
	public function __toString() { return $this->info['user_name']; }
}
