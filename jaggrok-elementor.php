<?php
/**
 * Plugin Name: JagGrok Elementor
 * Plugin URI: https://jagjourney.com/
 * Description: 🚀 FREE AI Page Builder - Generate full Elementor layouts with Grok by xAI. One prompt = complete pages! By Jag Journey, LLC.
 * Version: 1.2.2
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
// JAGJourney v1.2.1 - CORE PLUGIN (ASSETS + SETTINGS LINK + MODELS)
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

// PRO DETECTION
function jaggrok_is_pro_active() {
	return class_exists( '\ElementorPro\Plugin' ) || defined( 'ELEMENTOR_PRO_VERSION' );
}

// SETTINGS LINK under plugin name (v1.2.1)
function jaggrok_settings_link( $actions, $plugin_file ) {
	if ( $plugin_file === plugin_basename( __FILE__ ) ) {
		$settings_link = '<a href="' . admin_url( 'options-general.php?page=jaggrok-settings' ) . '">Settings</a>';
		array_unshift( $actions, $settings_link );
	}
	return $actions;
}
add_filter( 'plugin_action_links', 'jaggrok_settings_link', 10, 2 );

// Enqueue JS files (v1.2.1)
function jaggrok_enqueue_assets( $hook ) {
	wp_enqueue_script( 'jaggrok-admin-settings', plugin_dir_url( __FILE__ ) . 'js/admin-settings.js', array( 'jquery' ), '1.2.1', true );
	wp_enqueue_script( 'jaggrok-elementor-widget', plugin_dir_url( __FILE__ ) . 'js/elementor-widget.js', array( 'jquery', 'elementor-frontend' ), '1.2.1', true );
	wp_localize_script( 'jaggrok-elementor-widget', 'jaggrokAjax', array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'nonce' => wp_create_nonce( 'jaggrok_generate' )
	) );
}
add_action( 'admin_enqueue_scripts', 'jaggrok_enqueue_assets' );

// Include settings page (v1.2.1)
require_once plugin_dir_path( __FILE__ ) . 'includes/settings.php';

// Include Elementor widget (v1.2.1)
add_action( 'elementor/widgets/register', function() {
	if ( jaggrok_check_dependencies() ) {
		require_once plugin_dir_path( __FILE__ ) . 'includes/elementor-widget.php';
	}
});

// Include updater (v1.2.1)
if ( jaggrok_check_dependencies() ) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/updater.php';
}

// AJAX: Generate Page with Grok (v1.2.1 - USES SELECTED MODEL)
add_action( 'wp_ajax_jaggrok_generate_page', 'jaggrok_generate_page_ajax' );
function jaggrok_generate_page_ajax() {
	check_ajax_referer( 'jaggrok_generate', 'nonce' );

	$prompt = sanitize_textarea_field( $_POST['prompt'] );
	$api_key = get_option( 'jaggrok_xai_api_key' );
	$model = get_option( 'jaggrok_model', 'grok-beta' ); // NEW: Selected model
	$is_pro = jaggrok_is_pro_active();

	if ( empty( $api_key ) ) {
		wp_send_json_error( 'API key not configured' );
	}

	// Enhance prompt for Pro
	if ( $is_pro && ! empty( $_POST['pro_features'] ) ) {
		$prompt .= ' Output as structured Elementor JSON with dynamic content and forms.';
	} else {
		$prompt .= ' Output as clean HTML sections for Elementor.';
	}

	$response = wp_remote_post( 'https://api.x.ai/v1/chat/completions', [
		'headers' => [
			'Authorization' => 'Bearer ' . $api_key,
			'Content-Type' => 'application/json'
		],
		'body' => json_encode( [
			'model' => $model, // NEW: Dynamic model
			'messages' => [ [ 'role' => 'user', 'content' => $prompt ] ],
			'max_tokens' => get_option( 'jaggrok_max_tokens', 2000 )
		] )
	] );

	if ( is_wp_error( $response ) ) {
		wp_send_json_error( 'API request failed' );
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	$generated = $body['choices'][0]['message']['content'] ?? 'Generation failed';

	if ( $is_pro ) {
		$elementor_json = json_decode( $generated, true );
		if ( json_last_error() === JSON_ERROR_NONE ) {
			wp_send_json_success( [ 'canvas_json' => $elementor_json ] );
		}
	}

	wp_send_json_success( [ 'html' => $generated ] );
}

// Include uninstall
register_uninstall_hook( __FILE__, 'jaggrok_uninstall' );