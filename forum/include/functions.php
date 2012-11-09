<?php

/* Output user errors */
function fatal_user_error($text,$details="If the problem persists, please contact the website administrator.") {
	//If everything should be fine for a pretty error, use that
	global $core;

	//Log error
	log_error("User error: $text");

	if ($core->state == 1) {
		$core->state = 2;
		pretty_error($text,$details,false);
	}
	//Otherwise, non-pretty error and stop
	else {
		echo "<br /><strong>User error</strong>: $text<br />";
		print parse_stack_tree(debug_backtrace());
	}
	die();
}

/* Output fatal errors */
function fatal_error($text) {
	//Log error
	log_error("Internal error: $text");

	//If everything should be fine for a pretty error, use that
	global $core, $user;
	if ($core->state == 1) {
		$core->state = 2;
		pretty_error($text,parse_stack_tree(debug_backtrace()));
	}
	//Otherwise, non-pretty error and stop
	else {
		echo "<br /><strong>Internal error</strong>: $text<br />";
		print parse_stack_tree(debug_backtrace());
	}
	die();
}

/* Pretty error */
function pretty_error($text,$trace,$internal=true) {
	global $core, $document, $user;
	$error['error'] = $text;
	$error['details'] = $trace;
	$template = ($internal) ? "fatal_error" : "user_error";
	$document->clear();
	$document->header("Error");
	$document->append_template($template,$error);
	$document->footer();
	$document->output();
}

/* Stack tree formatter */
function parse_stack_tree($entries) {
	//Only admins get a stack tree
	global $user;

	$output = "<strong>Stack trace:</strong><br /><small>";
	$counter = 1;
	foreach($entries as $entry) {
		if (isset($entry['file'])) {
			
			//Function
			$function = $entry['function'] . "(";
			if (isset($entry['args'])) { $function .= parse_arguments($entry['args']); }
			$function .= ")";

			//File
			$file = $entry['file'] . ':' . $entry['line'];

			//Output
			$output .= "$counter: $function in $file\n<br />";
		}
		else { $output .= "$counter: $function in eval()'d code\n<br />"; }
		
		$counter++;
	}
	$output .= "</small>";
	if($user->is_admin()){
		return $output;
	}
	else return "";
}

/* Parse function arguments into printable format */
function parse_arguments($arguments) {
	$list = "";
	foreach($arguments as $argument) {
		if (is_object($argument)) { $list .= ", (Object)" . get_class($argument); }
		else { $list .= ", (" . gettype($argument) . ")" . $argument; }
	}
	return ($list != "") ? substr($list,2) : "";
}

/* Global error handler */
function handle_error($errno, $errstr, $errfile, $errline) {
	if (!error_reporting() OR !ini_get('display_errors')) { return; }

	global $user;
	switch ($errno) {
		case E_NOTICE:
		case E_USER_NOTICE:
			print_error("Notice",$errstr,$errfile,$errline);
			break;
		case E_WARNING:
		case E_USER_WARNING:
			print_error("Warning",$errstr,$errfile,$errline);
			break;
		case E_ERROR:
		case E_USER_ERROR:
			if (!headers_sent()) { header('HTTP/1.1 500 Internal Server Error'); }
			print_error("Fatal error",$errstr,$errfile,$errline,true);
			exit;
			break;
		default:
			print_error("Error",$errstr,$errfile,$errline);
			break;
	}
}

/* Print out error message caught by handler */
function print_error($type, $errstr, $errfile, $errline, $fatal=false) {
	global $core, $user;

	$error = "<br /><strong>$type</strong>: $errstr in <strong>$errfile</strong> on line <strong>$errline</strong><br /><br />\n";

	//If possible, use a pretty error if its fatal
	if ($fatal && $core->state == 1) {
		$core->state = 2;
		pretty_error($error,parse_stack_tree(debug_backtrace()));
	}
	//Otherwise, if not fatal its for admin eyes only
	else if (!$fatal) {
		//Only print out errors and warnings to admins unless its an emergency
			echo "<br /><strong>$type</strong>: $errstr in <strong>$errfile</strong> 
				on line <strong>$errline</strong><br /><br />\n";
			print parse_stack_tree(debug_backtrace());
			print "<br /><br />\n";
	}
	//Otherwise if fatal, we'd better print it regardless
	else {
		echo "<br /><strong>$type</strong>: $errstr in <strong>$errfile</strong> 
			on line <strong>$errline</strong><br /><br />\n";
		print parse_stack_tree(debug_backtrace());
		print "<br /><br />\n";
	}

	log_error("$type: $errstr in $errfile on line $errline");
}

function log_error($error) {
	error_log("PHP $error");
}
