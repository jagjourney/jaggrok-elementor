<?php
/**
 * Plugin Name: JagGrok Elementor
 * Plugin URI: https://jagjourney.com/
 * Description: 🚀 FREE AI Page Builder - Generate full Elementor layouts with Grok by xAI. One prompt = complete pages! By Jag Journey, LLC.
 * Version: 1.2.9
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
// JAGJourney v1.2.9 - CORE PLUGIN (AJAXURL FIXED + WORKING!)
// ============================================================================

// WIDGET REGISTRATION - BULLETPROOF
add_action( 'elementor/widgets/register', function( $widgets_manager ) {
	require_once __DIR__ . '/includes/elementor-widget.php';
	$widgets_manager->register( new JagGrok_AI_Generator_Widget() );
}, 5, 1 );

// Check Elementor
function jaggrok_check_dependencies() {
	if ( ! did_action( 'elementor/loaded' ) ) return false;
	return true;
}
add_action( 'plugins_loaded', 'jaggrok_check_dependencies' );

// PRO DETECTION
function jaggrok_is_pro_active() {
	return class_exists( '\ElementorPro\Plugin' ) || defined( 'ELEMENTOR_PRO_VERSION' );
}

// SETTINGS LINK
function jaggrok_settings_link( $actions, $plugin_file ) {
	if ( $plugin_file === plugin_basename( __FILE__ ) ) {
		$settings_link = '<a href="' . admin_url( 'options-general.php?page=jaggrok-settings' ) . '">Settings</a>';
		array_unshift( $actions, $settings_link );
	}
	return $actions;
}
add_filter( 'plugin_action_links', 'jaggrok_settings_link', 10, 2 );

// Enqueue JS files (v1.2.9 - LOCALIZE AJAXURL FOR EDITOR)
function jaggrok_enqueue_assets( $hook ) {
	wp_enqueue_script( 'jaggrok-admin-settings', plugin_dir_url( __FILE__ ) . 'js/admin-settings.js', array( 'jquery' ), '1.2.9', true );

	// EDITOR ONLY - LOCALIZE AJAXURL + NONCE
	if ( ( 'post.php' === $hook || 'post-new.php' === $hook ) && did_action( 'elementor/editor/after_enqueue_scripts' ) ) {
		wp_enqueue_script( 'jaggrok-elementor-widget', plugin_dir_url( __FILE__ ) . 'js/elementor-widget.js', array( 'jquery', 'elementor-editor' ), '1.2.9', true );
		wp_localize_script( 'jaggrok-elementor-widget', 'jaggrokAjax', array(
			'ajaxurl' => admin_url( 'admin-ajax.php' ),  // FIXED: AJAXURL LOCALIZED
			'nonce' => wp_create_nonce( 'jaggrok_generate' )
		) );
	}
}
add_action( 'admin_enqueue_scripts', 'jaggrok_enqueue_assets' );

// Include settings page
require_once plugin_dir_path( __FILE__ ) . 'includes/settings.php';

// Include updater
if ( jaggrok_check_dependencies() ) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/updater.php';
}

// AJAX: Generate Page with Grok (v1.2.9 - ERROR HANDLING)
add_action( 'wp_ajax_jaggrok_generate_page', 'jaggrok_generate_page_ajax' );
function jaggrok_generate_page_ajax() {
	check_ajax_referer( 'jaggrok_generate', 'nonce' );

	$prompt = sanitize_textarea_field( $_POST['prompt'] );
	$api_key = get_option( 'jaggrok_xai_api_key' );
	$model = get_option( 'jaggrok_model', 'grok-beta' );

	if ( empty( $api_key ) ) {
		wp_send_json_error( 'API key required. Go to Settings > JagGrok Elementor.' );
	}

	if ( empty( $prompt ) ) {
		wp_send_json_error( 'Prompt required.' );
	}

	$response = wp_remote_post( 'https://api.x.ai/v1/chat/completions', [
		'headers' => [ 'Authorization' => 'Bearer ' . $api_key, 'Content-Type' => 'application/json' ],
		'body' => json_encode( [
			'model' => $model,
			'messages' => [ [ 'role' => 'user', 'content' => $prompt ] ],
			'max_tokens' => get_option( 'jaggrok_max_tokens', 500 )
		] )
	]);

	if ( is_wp_error( $response ) ) {
		wp_send_json_error( 'API request failed: ' . $response->get_error_message() );
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( isset( $body['choices'][0]['message']['content'] ) ) {
		wp_send_json_success( [ 'html' => $body['choices'][0]['message']['content'] ] );
	} else {
		wp_send_json_error( 'Invalid response from Grok API. Check your API key.' );
	}
}

register_uninstall_hook( __FILE__, 'jaggrok_uninstall' );