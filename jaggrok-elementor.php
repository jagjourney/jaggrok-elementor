<?php
/**
 * Plugin Name: JagGrok Elementor
 * Version: 1.2.8
 * Description: 🚀 FREE AI Page Builder - Generate full Elementor layouts with Grok by xAI.
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) exit;

// WIDGET REGISTRATION - SIMPLE
add_action( 'elementor/widgets/register', function( $widgets_manager ) {
	require_once __DIR__ . '/includes/elementor-widget.php';
	$widgets_manager->register( new JagGrok_AI_Generator_Widget() );
});

// SETTINGS LINK
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), function( $links ) {
	$links[] = '<a href="' . admin_url( 'options-general.php?page=jaggrok-settings' ) . '">Settings</a>';
	return $links;
});

// AJAX Generate
add_action( 'wp_ajax_jaggrok_generate_page', function() {
	check_ajax_referer( 'jaggrok_generate', 'nonce' );
	$prompt = sanitize_textarea_field( $_POST['prompt'] );
	$api_key = get_option( 'jaggrok_xai_api_key' );

	if ( empty( $api_key ) ) wp_send_json_error( 'API key required' );

	$response = wp_remote_post( 'https://api.x.ai/v1/chat/completions', [
		'headers' => [ 'Authorization' => 'Bearer ' . $api_key, 'Content-Type' => 'application/json' ],
		'body' => json_encode( [
			'model' => get_option( 'jaggrok_model', 'grok-beta' ),
			'messages' => [ [ 'role' => 'user', 'content' => $prompt ] ],
			'max_tokens' => 500
		] )
	]);

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	wp_send_json_success( [ 'html' => $body['choices'][0]['message']['content'] ?? 'Test generated!' ] );
});

// Settings page
require_once __DIR__ . '/includes/settings.php';
register_uninstall_hook( __FILE__, 'jaggrok_uninstall' );