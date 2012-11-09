<?php

/* Session object */
class Session {

	/* Core object */
	public $core;

	/* Settings */
	public $settings;

	/* Constructor */
	public function Session(&$core) { 
		$this->core =& $core;
	}

	/* Initialisation for current session */
	public function init() {
		session_start();
		$this->settings =& $_SESSION;

		//Set defaults if no session
		if (!$this->get("user_id")) { $this->set_defaults(); }
	}

	/* Set variable */
	public function set($key,$value) {
		$this->settings[$key] = $value;
	}

	/* Get variable */
	public function get($key) {
		if (!isset($this->settings[$key])) { return false; }
		return $this->settings[$key];
	}

	/* Unset */
	public function remove($key) {		
		unset($_SESSION[$key]);
	}

	/* Clear */
	public function clear() {
		$this->set_defaults();
	}

	/* Set session defaults */
	public function set_defaults() {
		global $core;
		$this->settings['user_id'] = -1;
		$this->settings['user_name'] = "Guest";
		$this->settings['user_style'] = $core->get('default_style');
		$this->settings['user_cookie'] = 0;
	}

}
