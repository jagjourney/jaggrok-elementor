<?php
/**
 * Plugin Name: JagGrok Elementor
 * Plugin URI: https://jagjourney.com/
 * Description: 🚀 FREE AI Page Builder - Generate full Elementor layouts with Grok by xAI. One prompt = complete pages! By Jag Journey, LLC.
 * Version: 1.0.0
 * Author: Jag Journey, LLC
 * Author URI: https://jagjourney.com/
 * License: GPL v2 or later
 * Text Domain: jaggrok-elementor
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Elementor tested up to: 3.18
 * Elementor Pro tested up to: 3.18
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) exit;

// ============================================================================
// JAGJourney v1.0.0 - CORE PLUGIN
// ============================================================================

// Check Elementor
function jaggrok_check_dependencies() {
	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', function() {
			echo '<div class="notice notice-error"><p><strong>JagGrok Elementor</strong> requires Elementor to be installed and active.</p></div>';
		});
		return false;
	}
	return true;
}
add_action( 'plugins_loaded', 'jaggrok_check_dependencies' );

// Enqueue JS files
function jaggrok_enqueue_assets( $hook ) {
	wp_enqueue_script( 'jaggrok-admin-settings', plugin_dir_url( __FILE__ ) . 'js/admin-settings.js', array( 'jquery' ), '1.0.0', true );
	wp_localize_script( 'jaggrok-admin-settings', 'jaggrokAjax', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'jaggrok_test' )
	) );
}
add_action( 'admin_enqueue_scripts', 'jaggrok_enqueue_assets' );

// Include settings page
require_once plugin_dir_path( __FILE__ ) . 'includes/settings.php';

// Include Elementor widget (v1.0.0)
require_once plugin_dir_path( __FILE__ ) . 'includes/elementor-widget.php';

// Include uninstall
register_uninstall_hook( __FILE__, 'jaggrok_uninstall' );