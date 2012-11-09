<?php

class Posts {

	/* Instantiate a new posts system */
	public function Posts(&$core) {
		$this->core =& $core;
	}

	/* Get all recent posts in the system */
	public function get_recent_posts($limit = 5) {
		global $core;
		$query = $this->core->db->make_query("posts,threads,boards,user");
		$query->add_condition_direct("`posts`.`user_id` = `user`.`user_id`");
		$query->add_condition_direct("`posts`.`thread_id` = `threads`.`thread_id`");
		$query->add_condition_direct("`threads`.`board_id` = `boards`.`board_id`");
		$query->set_order("posts.post_timestamp DESC");
		$query->set_limit($limit);
		$result = $query->execute();
		return $result;
	}

	/* Get the last post in a thread */
	public function get_last_post($threadid) {
		global $core;
		$query = $this->core->db->make_query("posts,threads,boards,user");
		$query->add_condition_direct("`posts`.`user_id` = `user`.`user_id`");
		$query->add_condition_direct("`posts`.`thread_id` = `threads`.`thread_id`");
		$query->add_condition_direct("`threads`.`board_id` = `boards`.`board_id`");
		$query->add_condition_direct("`posts`.`thread_id` = $threadid");
		$query->set_order("posts.post_timestamp DESC");
		$query->set_limit(1);
		$result = $query->execute();
		return $result->fetch_assoc();
	}

	/* Get posts in the system for a thread */
	public function get_posts($threadid) {
		global $core;
		$query = $this->core->db->make_query("posts,threads,boards,user");
		$query->add_condition_direct("`posts`.`user_id` = `user`.`user_id`");
		$query->add_condition_direct("`posts`.`thread_id` = `threads`.`thread_id`");
		$query->add_condition_direct("`threads`.`board_id` = `boards`.`board_id`");
		$query->add_condition_direct("`posts`.`thread_id` = $threadid");
		$query->set_order("posts.post_timestamp ASC");
		$result = $query->execute();
		return $result;
	}

	/* Get posts in the system for a thread */
	public function get_user_posts($userid) {
		global $core;
		$query = $this->core->db->make_query("posts,threads,boards,user");
		$query->add_condition_direct("`posts`.`user_id` = `user`.`user_id`");
		$query->add_condition_direct("`posts`.`thread_id` = `threads`.`thread_id`");
		$query->add_condition_direct("`threads`.`board_id` = `boards`.`board_id`");
		$query->add_condition_direct("`posts`.`user_id` = $threadid");
		$query->set_order("posts.post_timestamp DESC");
		$result = $query->execute();
		return $result;
	}


	/* Get a post from the system */
	public function get_post($postid) {
		global $core, $user;
		$query = $this->core->db->make_query("posts,user");
		$query->set_limit(1);
		$query->add_condition_direct("`posts`.`user_id` = `user`.`user_id`");
		$query->add_condition_direct("`posts`.`post_id` = $postid");
		$result = $query->execute();
		return $result->fetch_assoc();
	}

	/* Search through all posts */
	public function search_posts($search) {
		global $core;

		//Make wildcards work
		$search = str_replace("*","%",$search);

		$dbp = $this->core->db->db_prefix;

		//TODO: Should be moved to use the abstraction, just doing this way to test it works
		$result = $this->core->db->query("SELECT * FROM {$dbp}posts,{$dbp}threads,{$dbp}boards,{$dbp}user WHERE {$dbp}posts.user_id = {$dbp}user.user_id AND {$dbp}posts.thread_id = {$dbp}threads.thread_id AND {$dbp}threads.board_id = {$dbp}boards.board_ID AND {$dbp}posts.post_message LIKE \"%$search%\" ORDER BY {$dbp}posts.post_timestamp DESC");

		return $result;
	}

	/* Create a new post */
	public function new_post($userid,$boardid,$threadid,$postname,$postmessage) {
		global $core, $boards, $threads;

		$query = $this->core->db->make_query("posts","INSERT");
		$query->add_data("user_id",$userid);
		$query->add_data("thread_id",$threadid);
		$query->add_data("post_name",$postname);
		$query->add_data("post_message",$postmessage);
		$query->execute();		
		
		//Get ID of new thread
		$id = $this->core->db->get_auto_id();		

		//Now increase statistics
		$threads->update_stat($threadid);
		$boards->update_stat($boardid);
		$boards->update_last_post($boardid,$id);

		//Return thread ID
		return $id;
	}

	/* Edit a specific post */	
	public function edit_post($postid,$postname,$postmessage) {
		global $core;

		$query = $this->core->db->make_query("posts","UPDATE");
		$query->add_data("post_name",$postname);
		$query->add_data("post_message",$postmessage);
		$query->add_condition("post_id","=",$postid);
		$query->execute();		
	}

	/* Delete all the posts from a thread */
	public function delete_posts($threadid) {
		global $core, $threads, $boards;

		$thread = $threads->get_thread($threadid);
		$boardid = $thread['board_id'];

		$query = $this->core->db->make_query("posts","DELETE");
		$query->add_condition("thread_id","=",$threadid);
		$query->execute();

		//Now update statistics
		$threads->update_stat($threadid,"replies","zero");
		$boards->update_stat($boardid);
		$boards->update_last_post_unknown($boardid);
	}	

	/* Delete a post from a thread */
	public function delete_post($postid) {
		global $core, $threads, $boards, $boards, $posts;

		$post = $posts->get_post($postid);
		$threadid = $post['thread_id'];

		$thread = $threads->get_thread($threadid);
		$boardid = $thread['board_id'];

		$query = $this->core->db->make_query("posts","DELETE");
		$query->add_condition("post_id","=",$postid);
		$query->execute();

		//Now update statistics
		$threads->update_stat($threadid,"replies","down");
		$boards->refresh_stats($boardid);
		$boards->update_last_post_unknown($boardid);
	}	

}

?>
