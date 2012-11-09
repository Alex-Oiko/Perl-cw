<?php

class Boards {

	/* Instantiate a new boards system */
	public function Boards(&$core) {
		$this->core =& $core;
	}

	/* Get all boards */
	public function get_all_boards() {
		global $core;
		$query = $this->core->db->make_query("boards,categories");
		$query->add_condition_direct("`boards`.`category_id` = `categories`.`category_id`");
		$query->set_order("board_order ASC");
		$result = $query->execute();
		return $result;
	}

	/* Get all boards in a category */
	public function get_boards($category) {
		global $core;
		$query = $this->core->db->make_query("boards");
		$query->add_condition("category_id",'=',$category);
		$query->set_order("board_order ASC");
		$result = $query->execute();
		return $result;
	}

	/* Get a specific board */
	public function get_board($boardid) {
		global $core;
		$query = $this->core->db->make_query("boards,categories");
		$query->add_condition_direct("`boards`.`category_id` = `categories`.`category_id`");
		$query->add_condition("board_id",'=',$boardid);
		$query->set_limit(1);
		$result = $query->execute();
		return $result->fetch_assoc();
	}

	/* Update statistics on a thread */
	public function update_stat($boardid,$stat='posts') {
		global $core;

		//Get the old replies
		$query = $this->core->db->make_query("boards");
		$query->set_fields("board_$stat");
		$query->add_condition("board_id","=",$boardid);
		$result = $query->execute()->fetch_assoc();
		
		//Increment the replies
		$replies = $result["board_$stat"];
		$replies++;

		//Update the database
		$query = $this->core->db->make_query("boards","UPDATE");
		$query->add_condition("board_id","=",$boardid);
		$query->add_data("board_$stat",$replies);
		$query->set_limit(1);
		$query->execute();
	}

	/* Update the last post on a board */
	public function update_last_post($boardid,$postid) {
		global $core;
		
		$query = $this->core->db->make_query("boards","UPDATE");
		$query->add_data("board_lastpost",$postid);		
		$query->add_condition("board_id","=",$boardid);
		$query->set_limit(1);
		$query->execute();
	}	

	/* Update the last post on a board if its not known */
	public function update_last_post_unknown($boardid) {
		$lastpost = $this->get_last_post($boardid);
		$lastpostid = $lastpost['post_id'];
		$this->update_last_post($boardid,$lastpostid);		
	}

	/* Find the last post on a board */
	public function get_last_post($boardid) {
		global $core;

		$query = $this->core->db->make_query("threads,posts");
		$query->add_condition_direct("`posts`.`thread_id` = `threads`.`thread_id`");
		$query->add_condition("board_id","=",$boardid);
		$query->set_order("posts.post_timestamp DESC");
		$query->set_limit(1);
		$result = $query->execute();

		return $result->fetch_assoc();
	}
	
	//Rebuild the stats for a board
	public function refresh_stats($boardid) {
		global $core;
		
		//Get the number of threads
		$query = $this->core->db->make_query("threads");
		$query->set_fields_statement("COUNT(*)");
		$query->add_condition("board_id","=",$boardid);
		$result = $query->execute();
		$resultarray = $result->fetch_array();
		$num_threads = $resultarray[0];

		//Get the number of posts
		$query = $this->core->db->make_query("threads,posts");
		$query->set_fields_statement("COUNT(*)");
		$query->add_condition_direct("`posts`.`thread_id` = `threads`.`thread_id`");
		$query->add_condition("board_id","=",$boardid);
		$result = $query->execute();
		$resultarray = $result->fetch_array();
		$num_posts = $resultarray[0];

		//Update the stats
		$query = $this->core->db->make_query("boards","UPDATE");
		$query->add_condition("board_id","=",$boardid);
		$query->add_data("board_posts",$num_posts);
		$query->add_data("board_threads",$num_threads);
		$query->execute();
	}

}

?>
