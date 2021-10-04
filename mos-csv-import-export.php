<?php
/*
Plugin Name: Mos CSV Import Export
Plugin URI: https://www.mdmostakshahid.me/
Description: A brief description of the Plugin.
Version: 1.0.2
Author: Md. Mostak Shahid
Author URI: https://www.mdmostakshahid.me/
License: GPL3
.
Any other notes about the plugin go here
.
*/
// This function will put image name into alt field when Upload
if ( ! defined( 'ABSPATH' ) ) {
    exit;
    // Exit if accessed directly.
}

// Define MOS_MCIE_FILE.
if ( ! defined( 'MOS_MCIE_FILE' ) ) {
    define( 'MOS_MCIE_FILE', __FILE__ );
}

// Define MOS_MCIE_SETTINGS.
if ( ! defined( 'MOS_MCIE_SETTINGS' ) ) {
    //define( 'MOS_MCIE_SETTINGS', admin_url( '/edit.php?post_type=post_type&page=plugin_settings' ) );
    //define( 'MOS_MCIE_SETTINGS', admin_url( '/options-general.php?page=mos_mcie_settings' ) );
    define( 'MOS_MCIE_SETTINGS', admin_url( '/admin.php?page=mos-csv-importer-export-options' ) );
}

$mos_mcie_option = get_option( 'mos_mcie_option' );
$mcie = plugin_basename( MOS_MCIE_FILE );
register_activation_hook( MOS_MCIE_FILE, 'mos_mcie_activate' );
add_action( 'admin_init', 'mos_mcie_redirect' );

function mos_mcie_activate() {
    $mos_mcie_option = array();
    // $mos_mcie_option['mos_login_type'] = 'basic';
    // update_option( 'mos_mcie_option', $mos_mcie_option, false );
    add_option( 'mos_mcie_do_activation_redirect', true );
}

function mos_mcie_redirect() {
    if ( get_option( 'mos_mcie_do_activation_redirect', false ) ) {
        delete_option( 'mos_mcie_do_activation_redirect' );
        if ( !isset( $_GET['activate-multi'] ) ) {
            wp_safe_redirect( MOS_MCIE_SETTINGS );
        }
    }
}

// Add settings link on plugin page

function mos_mcie_settings_link( $links ) {
    $settings_link[] = '<a href="'.MOS_MCIE_SETTINGS.'">Settings</a>';
    $settings_link[] = '<a href="#">Support</a>';
    $settings_link[] = '<a href="#">Documentation</a>';
    $settings_link[] = '<a href="#" target="_blank" style="color: #39b54a; font-weight: bold;">Go Pro</a>';
    //array_unshift( $links, $settings_link );
    $links = array_merge( $links, $settings_link );
    return $links;
}
add_filter( "plugin_action_links_$mcie", 'mos_mcie_settings_link' );

require_once ( plugin_dir_path( __FILE__ ) . 'mos-csv-import-export-functions.php' );
require_once ( plugin_dir_path( __FILE__ ) . 'mos-csv-import-export-settings.php' );
require_once( 'plugins/update/plugin-update-checker.php' );
$pluginInit = Puc_v4_Factory::buildUpdateChecker(
    'https://raw.githubusercontent.com/mostak-shahid/update/master/mos-csv-import-export.json',
    __FILE__,
    'mos-csv-import-export'
);