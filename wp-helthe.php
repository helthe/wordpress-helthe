<?php

/*
Plugin Name: Helthe Monitor for WordPress
Plugin URI: http://www.helthe.co
Description: World class error monitoring for WordPress.
Version: Beta
Author: Carl Alexander
Author URI: https://helthe.co
License: GPL3
*/

// Setup class autoloader
require_once __DIR__ . '/Helthe/Autoloader.php';
Helthe_Autoloader::register();

// Load Helthe
$helthe = new Helthe_Plugin(__FILE__);
$helthe->load();
