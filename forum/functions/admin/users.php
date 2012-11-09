<?php

/* List all user */
function user_list() {
	global $core, $document, $db;
	$query = $db->make_query("user");
	$query->set_order("user_name DESC");
	$users = $query->execute();

	$document->append_template("simple_template",array('title'=>"User List",'text'=>"All users on this website"));	
	$listhtml = "";

	//Fetch all pages from database
	while ($current_user = $users->fetch_assoc()) {
		//For security
		$current_user['password'] = "";
		$current_user['cookie'] = "";

		//Render template
		$listhtml .= $document->get_template("admin_user_item",$current_user);
	}

	//Make pretty
	$listhtml = $document->get_template("admin_user_list",array('users'=>$listhtml));
	$document->append_template("window",array('title'=>"Users",'content'=>$listhtml));
}


?>
