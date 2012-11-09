<?php

class Categories {

	/* Instantiate a new posts system */
	public function Categories(&$core) {
		$this->core =& $core;
	}

	/* Get posts in the system for a thread */
	public function get_categories() {
		global $core;
		$query = $this->core->db->make_query("categories");
		$query->set_order("category_order ASC");
		$result = $query->execute();
		return $result;
	}

	public function get_category($categoryid) {
		global $core;
		$query = $this->core->db->make_query("categories");
		$query->add_condition("category_id",'=',$categoryid);
		$query->set_limit(1);
		return $result->fetch_assoc();
	}



}

?>
