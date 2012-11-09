<?php

function do_sidebar() {
	/* Hook called page */
	global $core, $document, $user, $db;

	$query = $core->db->make_query("blocks");
	$query->set_order("block_order ASC");
	$result = $query->execute();

	$blockhtml = "";
	while ($db_block = $result->fetch_assoc()) {
		$block['title'] = $db_block['block_title'];
		$block['content'] = $db_block['block_content'];

		//If PHP block, evaluate
		if ($db_block['block_php']) { 
			ob_start();
			eval($db_block['block_content']);
			$block['content'] = ob_get_contents();
			ob_end_clean();		
		}
		if ($block['content'] != "") { $blockhtml .= $document->get_template("block",$block); }	
	}

	//Generate sidebar template
	$variables['blocks'] = $blockhtml;
	$document->append_template("sidebar",$variables);
}

?>
