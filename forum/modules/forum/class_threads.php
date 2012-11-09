<?php

class Threads {

	/* Instantiate a new threads system */
	public function Threads(&$core) {
		$this->core =& $core;
	}

	/* Get recent threads */
	public function get_recent_threads($limit = 5) {
		global $core;
		$query = $this->core->db->make_query("threads,user,boards");
		$query->add_condition_direct("`threads`.`user_id` = `user`.`user_id`");
		$query->add_condition_direct("`threads`.`board_id` = `boards`.`board_id`");
		$query->set_order("thread_timestamp DESC");
		$query->set_limit($limit);
		$result = $query->execute();
		return $result;
	}

	/* Get threads for a board */
	public function get_threads($boardid) {
		global $core;
		$query = $this->core->db->make_query("threads,user");
		$query->add_condition_direct("`threads`.`user_id` = `user`.`user_id`");
		$query->add_condition("board_id","=",$boardid);
		$query->set_order("thread_timestamp DESC");
		$result = $query->execute();
		return $result;
	}

	/* Get threads by a user */
	public function get_user_threads($userid,$limit = 25) {
		global $core;
		$query = $this->core->db->make_query("threads,user");
		$query->add_condition_direct("`threads`.`user_id` = `user`.`user_id`");
		$query->add_condition_direct("`threads`.`user_id` = $userid");
		$query->set_order("thread_timestamp DESC");
		$query->set_limit($limit);
		$result = $query->execute();
		return $result;
	}

	/* Get a single thread */
	public function get_thread($threadid) {
		global $core;
		$query = $this->core->db->make_query("threads,user");
		$query->add_condition_direct("`threads`.`user_id` = `user`.`user_id`");
		$query->add_condition("thread_id","=",$threadid);
		$query->set_limit(1);
		$result = $query->execute();
		return $result->fetch_assoc();
	}

	/* Create a new thread */
	public function new_thread($userid,$boardid,$postname) {
		global $core, $boards;

		$query = $this->core->db->make_query("threads","INSERT");
		$query->add_data("user_id",$userid);
		$query->add_data("board_id",$boardid);
		$query->add_data("thread_name",$postname);
		$query->execute();		
		
		//Get ID of new thread
		$id = $this->core->db->get_auto_id();

		//Now increase statistics
		$boards->update_stat($boardid,"threads");

		//Return thread ID
		return $id;
	}

	/* Update statistics on a thread */
	public function update_stat($threadid,$stat='replies',$direction="up") {
		global $core;

		//Get the old replies
		$query = $this->core->db->make_query("threads");
		$query->set_fields("thread_$stat");
		$query->add_condition("thread_id","=",$threadid);
		$result = $query->execute()->fetch_assoc();
		
		//Increment the replies
		$replies = $result["thread_$stat"];
		if ($direction == "up") { $replies++; }
		elseif ($direction == "zero") { $replies = 1; }
		else { $replies--; }

		//Update the database
		$query = $this->core->db->make_query("threads","UPDATE");
		$query->add_condition("thread_id","=",$threadid);
		$query->add_data("thread_$stat",$replies);
		$query->set_limit(1);
		$query->execute();
	}

	/* Remove a thread and all associated posts */
	public function remove_thread($threadid) {
		global $core, $threads, $posts, $boards;
		$thread = $threads->get_thread($threadid);
		$boardid = $thread['board_id'];

		//Delete the posts		
		$posts->delete_posts($threadid);

		//Delete the thread
		$query = $this->core->db->make_query("threads","DELETE");
		$query->add_condition("thread_id","=",$threadid);
		$query->execute();

		//Update statistics
		$boards->refresh_stats($boardid);
		$boards->update_last_post_unknown($boardid);
	}

}

?>
