<?php
/*
Plugin Name: Mos CSV Import/Export
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A brief description of the Plugin.
Version: 1.0.0
Author: Md. Mostak Shahid
Author URI: http://URI_Of_The_Plugin_Author
License: GPL2
.
Any other notes about the plugin go here
.
*/
//This function will put image name into alt field when Upload

require_once ( plugin_dir_path( __FILE__ ) . 'mos-csv-import-export-functions.php' );
require_once ( plugin_dir_path( __FILE__ ) . 'mos-csv-import-export-settings.php' );
require_once('plugins/update/plugin-update-checker.php');
$pluginInit = Puc_v4_Factory::buildUpdateChecker(
	'https://raw.githubusercontent.com/mostak-shahid/update/master/mos-csv-import-export.json',
	__FILE__,
	'mos-csv-import-export'
);