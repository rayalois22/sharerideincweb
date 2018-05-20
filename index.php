<?php
	//starts a new session.
	session_start();
	
	//configures appropriate error reporting and logging levels for production environment.
	ini_set('display_errors', 1);
	ini_set('log_errors', 1);
	
	//loads all classes dynamically, then creates a site-wide controller object named $CTRL
	require_once 'class/autoloader.php';
	
	//starts, manages, and listens to all requests made to the application then responds accordingly.
	$CTRL->control();
?>
