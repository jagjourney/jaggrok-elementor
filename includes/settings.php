<?php
// ============================================================================
// JAGJourney SETTINGS PAGE v1.2.1 (GROK MODELS + BUG FIXED)
// ============================================================================

function jaggrok_add_settings_page() {
	add_options_page(
		'JagGrok Elementor Settings',
		'JagGrok Elementor',
		'manage_options',
		'jaggrok-settings',
		'jaggrok_settings_page_callback'
	);
}
add_action( 'admin_menu', 'jaggrok_add_settings_page' );

function jaggrok_register_settings() {
	register_setting( 'jaggrok_settings', 'jaggrok_xai_api_key' );
	register_setting( 'jaggrok_settings', 'jaggrok_auto_insert', 'yes' );
	register_setting( 'jaggrok_settings', 'jaggrok_theme_style', 'modern' );
	register_setting( 'jaggrok_settings', 'jaggrok_max_tokens', 2000 );
	register_setting( 'jaggrok_settings', 'jaggrok_model', 'grok-beta' ); // NEW v1.2.1
}
add_action( 'admin_init', 'jaggrok_register_settings' );

function jaggrok_settings_page_callback() {
	include plugin_dir_path( __FILE__ ) . 'settings-template.php';
}

// AJAX Test API
function jaggrok_test_api_connection() {
	check_ajax_referer( 'jaggrok_test', 'nonce' );
	$api_key = sanitize_text_field( $_POST['api_key'] );
	update_option( 'jaggrok_xai_api_key', $api_key );

	$response = wp_remote_post( 'https://api.x.ai/v1/chat/completions', [
		'headers' => [ 'Authorization' => 'Bearer ' . $api_key, 'Content-Type' => 'application/json' ],
		'body' => json_encode( [ 'model' => 'grok-beta', 'messages' => [ ['role' => 'user', 'content' => 'Hello JagGrok!' ] ], 'max_tokens' => 10 ] )
	]);

	if ( is_wp_error( $response ) ) {
		wp_send_json_error( 'Connection failed: ' . $response->get_error_message() );
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( isset( $body['choices'] ) ) {
		update_option( 'jaggrok_api_tested', true );
		wp_send_json_success();
	} else {
		wp_send_json_error( 'Invalid API key' );
	}
}
add_action( 'wp_ajax_jaggrok_test_api', 'jaggrok_test_api_connection' );

// REMOVED: jaggrok_settings_link() - ALREADY IN MAIN FILE (BUG FIX v1.2.1)