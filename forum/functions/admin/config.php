<?php

function config_list() {
	global $core, $db, $document;
	$query = $db->make_query("config");
	$query->set_order("config_id ASC");
	$items = $query->execute();
	
	$document->append_template("simple_template",array('title'=>"Configuration",
	'text'=>"All configuration values on this website"));	
	$listhtml = "";

	while ($item = $items->fetch_assoc()) {
		$type = $item['config_type'];
		$listhtml .= $document->get_template("admin_config_item_$type",$item);
	}
	
	$listhtml = $document->get_template("admin_config_list",array('config'=>$listhtml));
	$document->append_template("window",array('title'=>"Configuration",'content'=>$listhtml));
}

function config_save() {
	global $core, $db, $document;

	//Set all options based on POST
	$core->set_all($_POST);

	$document->append_template("window",array('title'=>"Configuration",'content'=>"Configuration updated succesfully"));
	$document->append("<br/>");
}

?>
