<?php
	function classesAutoload($classname){
		//Can't use __DIR__ as it's only in PHP 5.3+
		$filename = dirname(__FILE__) . DIRECTORY_SEPARATOR. '' . strtolower($classname) . '.php';
		if (is_readable($filename)) {
			require $filename;
		}
	}
	if (version_compare(PHP_VERSION, '5.1.2', '>=')) {
		//SPL autoloading was introduced in PHP 5.1.2
		if (version_compare(PHP_VERSION, '5.3.0', '>=')) {
			spl_autoload_register('classesAutoload', true, true);
		} else {
			spl_autoload_register('classesAutoload');
		}
	}
	
	// Creates the site controller object, passing it all the resources it
    // needs to succesfully run and manage the application, that is: 
	// the current configuration, the template, the view, and the model managers.
	// This allows for easy change of the applications configuration and appearance
	// by simply unleashing a totally different configuration, template, or layout.
	$CTRL = new controller(new config(), new template(), new layout(), new process());
?>