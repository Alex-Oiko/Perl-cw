<?php

/* Generate the list of categories and populate with boards */
function categories() {
	global $core, $user, $document, $categories;
	$all = $categories->get_categories();
	$category_html = "";
	while ($category = $all->fetch_assoc()) {
		$board_html = boards($category['category_id']);
		$category['boards'] = $board_html;
		$category_html .= $document->get_template("forum_category",$category);
	}
	return $category_html;
}

/* Generate the list of boards for a category */
function boards($category) {
	global $core, $user, $document, $boards, $posts;
	
	$all = $boards->get_boards($category);
	$board_html = "";
	while ($board = $all->fetch_assoc()) {

		//Fill in the last post details
		$lastpost = $posts->get_post($board['board_lastpost']);
	
		//No last post
		if (!$lastpost) { 
			$lastpost_html = $document->get_template("forum_lastpost_nobody");
		} else {
			//Make the last post timestamp pretty
			$lastpost['post_timestamp'] = date('d M Y, G:m',strtotime($lastpost['post_timestamp']));
	
			$lastpost_html = $document->get_template("forum_lastpost",$lastpost);
		}

		$board['lastpost'] = $lastpost_html;
		$board_html .= $document->get_template("forum_board",$board);
	}


	return $board_html;
}

/* Generate a list of threads in a board */
function threads($boardid,$board) {
	global $core, $user, $document, $threads, $posts;
	
	$all = $threads->get_threads($boardid);
	$inner_thread_html = "";

	$counter = 0;
	
	//Loop through all the threads
	while ($thread = $all->fetch_assoc()) {
		$counter++;

		//Get the last post from the thread
		$lastpost = $posts->get_last_post($thread['thread_id']);			$thread['last_post_time'] = date('d M Y, G:m',strtotime($lastpost['post_timestamp']));
		$thread['last_user_name'] = $lastpost['user_name'];
		$thread['last_user_id'] = $lastpost['user_id'];

		//Rrplies rather than posts
		$thread['thread_replies'] = $thread['thread_replies'] - 1;
		if ($thread['thread_replies'] < 0) { $thread['thread_replies'] = 0; }
		
		//Render the thread
		$inner_thread_html .= $document->get_template("forum_thread",$thread);
	}

	if ($counter == 0) {
		$inner_thread_html = $document->get_template("forum_board_empty");
	}

	$forum_threads['board_name'] = $board['board_name'];
	$forum_threads['posts'] = $inner_thread_html;
	$thread_html = $document->get_template("forum_threads",$forum_threads);
	return $thread_html;
}

/* Generate a list of all the posts in a thread */
function posts($threadid) {
	global $core, $user, $document, $threads, $posts;
	
	$all = $posts->get_posts($threadid);
	$inner_post_html = "";

	//Loop through all the posts
	$counter = 0;
	while ($post = $all->fetch_assoc()) {
		$counter++;		

		//Make the timestamp pretty
		$post['post_counter'] = $counter;
		$post['post_timestamp'] = date('l jS \of F Y G:i:s',strtotime($post['post_timestamp']));
		$post['user_joined'] = date('d/m/Y',strtotime($post['user_timestamp']));
		$post['user_type'] = user_type($post['user_type']);

		//Build buttons
		$buttons = "";
		
		//Admin or original author
		if ($user->get('user_id') == $post['user_id'] || $user->get('user_type') > 1) {
			$buttons .= $document->get_template("forum_button",array('action' => "newreply.php?do=edit&amp;p=" . $post['post_id'], 
										'image' => "editpost.png", 
										'name' => "Edit Post"));
		}
		//Admins only
		if ($user->get('user_type') > 1) {
			$buttons .= $document->get_template("forum_button",array('action' => "adminthread.php?do=deletepost&amp;p=" . $post['post_id'], 
										'image' => "deletepost.png", 
										'name' => "Delete Post"));
		}
		//Buttons for Registered user
		if ($user->get('user_type') > 0) {
			$buttons .= $document->get_template("forum_button",array('action' => "newreply.php?p=" . $post['post_id'], 
										'image' => "quote.png", 
										'name' => "Quote Reply"));
		}

		//Set buttons
		$post['post_buttons'] = $buttons;

		//Apply post formatting and BB code
		$post['post_message'] = format_message($post['post_message']);

		//Return the post
		$inner_post_html .= $document->get_template("forum_post",$post);
	}	

	$forum_posts['posts'] = $inner_post_html;
	$post_html = $document->get_template("forum_posts",$forum_posts);

	return $post_html;
}

function search_results($search) {
	global $core, $user, $document, $threads, $posts;
	$results = $posts->search_posts($search);
	$resultshtml = "";
	$counter = 0;

	while ($post = $results->fetch_assoc()) {
		$counter++;		

		//Make the timestamp pretty
		$post['post_counter'] = $counter;
		$post['post_timestamp'] = date('l jS \of F Y G:i:s',strtotime($post['post_timestamp']));
		$post['user_joined'] = date('d/m/Y',strtotime($post['user_timestamp']));
		$post['user_type'] = user_type($post['user_type']);
		$post['post_message'] = format_message($post['post_message']);

		//Add the search result
		$resultshtml .= $document->get_template("forum_search_result",$post);
	}

	return $resultshtml;
}

/* Turn a user type into a type of user */
function user_type($type) {
	switch ($type) {
		case 0:
			return "Unregistered"; break;
		case 1:
			return "Member"; break;
		case 2:
			return "Administrator"; break;
		default:
			return "Unknown"; break;
	}
}

/* Turn messages into pretty messages */
function format_message($message) {
	//Turn new lines into new lines
	$message = nl2br($message);

	//Quotes
	while (preg_match('%\[quote=([^\]]+)\](.+?)\[/quote\]%sim',$message)) {
		$message = preg_replace('%\[quote=([^\]]+)\](.+?)\[/quote\]%sim', '<div class="quote"><p style="font-weight: bold;">Quote from \1:</p><p>\2</p></div>', $message);
	}

	while (preg_match('%\[quote\](.+?)\[/quote\]%sim',$message)) {
		$message = preg_replace('%\[quote\](.+?)\[/quote\]%sim', '<div class="quote"><p style="font-weight: bold;">Quote:</p><p>\1</p></div>', $message);
	}

	//Images
	while (preg_match('%\[img\](http[^[]+)\[/img\]%sim', $message)) {
		$message = preg_replace('%\[img\](http[^[]+)\[/img\]%sim', '<img src="\1" alt="\1" title="\1"/>', $message);
	}

	//Bold, italic, underline
	while (preg_match('%\[b\](.+?)[/b]%sim', $message)) { $message = preg_replace('%\[b\](.+?)\[/b\]%sim', '<b>\1</b>', $message); }
	while (preg_match('%\[u\](.+?)[/u]%sim', $message)) { $message = preg_replace('%\[u\](.+?)\[/u\]%sim', '<u>\1</u>', $message); }
	while (preg_match('%i\[i\](.+?)[/i]%sim', $message)) { $message = preg_replace('%\[i\](.+?)\[/i\]%sim', '<i>\1</i>', $message); }

	//Colours
	while (preg_match('%\[color=([^\]]+)\](.+?)\[/color\]%sim', $message)) {
		$message = preg_replace('%\[color=([^\]]+)\](.+?)\[/color\]%sim', '<font color="\1">\2</font>', $message); 
	}

	//Links
	while (preg_match('%\[url=([^\]]+)\](.+?)\[/url\]%sim', $message)) {
		$message = preg_replace('%\[url=([^\]]+)\](.+?)\[/url\]%sim', '<a href="\1" target="_blank">\2</a>', $message);
	}

	return $message;
}

?>
