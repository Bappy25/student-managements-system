<?php
	ob_start();
	session_start();

		// Declaring controller calling function
	function call($action, $route){
		if(file_exists('app/Http/Controllers/'.$route['class'].'.php')){

			require_once('app/Http/Controllers/'.$route['class'].'.php');

			if(method_exists($route['class'] , $action)) {	
				$controller = new $route['class']($action);		
			}
			else{
				echo "Action not found!";
				die();
			}
		}
		else{
			echo "Controller class not found!";
			die();
		}
	}

		// Include composer
	include("vendor/autoload.php");

		// Include project configurations
	foreach (glob("config/*.php") as $files){
	    $configurations = include $files;
	    foreach ($configurations as $key=>$value){
	    	$GLOBALS['config'][$key] = $value;
	    }
	}
			
		// Set default parameters
	$default = include("routes/default.php");

		// Include Routes
    $routes = include("routes/web.php");

	if(!empty($_GET)){

			// Sanitize parameters
		foreach ($_GET as $key => $value) {
		  $_GET[$key][$value] = preg_replace('/[^-a-zA-Z0-9_]/', '', $_GET[$key][$value]);
		}

			// Set parameters
		if (isset($_GET['controller']) && isset($_GET['action'])) {
		  $controller = $_GET['controller'];
		  $action     = $_GET['action'];
		}
	} 
	else{
		$controller = $default['landing']['controller'];
		$action = $default['landing']['action'];
	}

	if (array_key_exists($controller, $routes)){
		$route = $routes[$controller];
	    if (in_array($action, $route['methods'])){
			call($action, $route);
		} 
		else {
			call($default['error']['action'], $routes[$default['error']['controller']]);
	    }
	} 
	else {
		call($default['error']['action'], $routes[$default['error']['controller']]);
	}
	
	ob_end_flush();
?>