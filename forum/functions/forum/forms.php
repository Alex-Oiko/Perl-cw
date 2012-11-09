<?php

/* New thread window */
function new_thread_form($boardid) {
	global $core, $document, $db;

	//Main form body	
	$form = $document->make_form("newthread","newthread","/newthread.php?do=post");
	$form->start_fieldset("post","Post new thread");
	$form->add_element_only("board_id","Board ID","hidden",$boardid,"The board for the thread");
	$form->add_element("post_name","Thread subject","text","","The subject for the new thread");
	$form->add_element("post_message","Message","textarea","","The message for your thread. Remember you can use <a href=\"\$siteurl/help.php\">BBCode</a>");
	$form->end_fieldset();

	//End
	$form->append('<div class="center">');
	$form->add_element_only("submit","Post Thread","submit","Post Thread");
	$form->append('</div>');
	
	//Build form
	$formhtml = $form->output();
	$output = $document->get_template("window",array('title'=>"New Thread",'content'=>$formhtml));
	
	return $output;
}

/* New reply window */
function new_reply_form($boardid,$threadid,$subject = "",$quotemsg="",$quoteauthor="") {
	global $core, $document, $db;

	$initialtext = "";
	if ($quotemsg != "") {
		$initialtext = "[quote=$quoteauthor]$quotemsg" . '[/quote]';
	}

	//Main form body	
	$form = $document->make_form("newreply","newreply","/newreply.php?do=post");
	$form->start_fieldset("post","Post new reply");
	$form->add_element_only("board_id","Board ID","hidden",$boardid,"The board for the thread");
	$form->add_element_only("thread_id","Thread ID","hidden",$threadid,"The thread for the post");
	$form->add_element("post_name","Reply subject","text",$subject,"The subject for the new post");
	$form->add_element("post_message","Message","textarea",$initialtext,"The message for the post. Remember you can use <a href=\"\$siteurl/help.php\">BBCode</a>");
	$form->end_fieldset();

	//End
	$form->append('<div class="center">');
	$form->add_element_only("submit","Post Reply","submit","Post Reply");
	$form->append('</div>');
	
	//Build form
	$formhtml = $form->output();
	$output = $document->get_template("window",array('title'=>"Post Reply",'content'=>$formhtml));
	
	return $output;
}

/* Edit reply window */
function edit_reply_form($boardid,$threadid,$postid,$subject="",$message="") {
	global $core, $document, $db;

	//Main form body	
	$form = $document->make_form("editreply","editreply","/newreply.php?do=edit2");
	$form->start_fieldset("post","Edit post");
	$form->add_element_only("board_id","Board ID","hidden",$boardid,"The board for the thread");
	$form->add_element_only("thread_id","Thread ID","hidden",$threadid,"The thread for the post");
	$form->add_element_only("post_id","Post ID","hidden",$postid,"The post that is being edited");
	$form->add_element("post_name","Reply subject","text",$subject,"The subject for the post");
	$form->add_element("post_message","Message","textarea",$message,"The message for the post. Remember you can use <a href=\"\$siteurl/help.php\">BBCode</a>");
	$form->end_fieldset();

	//End
	$form->append('<div class="center">');
	$form->add_element_only("submit","Edit Message","submit","Edit Message");
	$form->append('</div>');
	
	//Build form
	$formhtml = $form->output();
	$output = $document->get_template("window",array('title'=>"Edit Message",'content'=>$formhtml));
	
	return $output;
}

/* Search form */
function search_form() {
	global $core, $document, $db;

	//Make form body
	$form = $document->make_form("search","search","/search.php?do=search");
	$form->start_fieldset("search","Search posts");
	$form->add_element("text","Search terms","text","","The search terms to search for");
	$form->end_fieldset();
	
	//End
	$form->append('<div class="center">');
	$form->add_element_only("submit","Search","submit","Search");
	$form->append('</div>');
	
	$formhtml = $form->output();
	$output = $document->get_template("window",array('title'=>"Search Posts",'content'=>$formhtml));

	return $output;
}

?>
