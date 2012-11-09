<?php

/* List all user */
function user_list() {
	global $core, $document, $db;
	$query = $db->make_query("user");
	$query->set_order("user_name DESC");
	$users = $query->execute();

	$listhtml = "";

	//Fetch all pages from database
	while ($current_user = $users->fetch_assoc()) {
		//For security
		$current_user['password'] = "";
		$current_user['cookie'] = "";

		//Render template
		$listhtml .= $document->get_template("forum_member",$current_user);

	}

	return $listhtml;
}


?>
