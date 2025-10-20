<?php
// ============================================================================
// JAGJourney SETTINGS PAGE v1.3.3 (SSL BYPASS TOGGLE)
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
	register_setting( 'jaggrok_settings', 'jaggrok_model', 'grok-beta' );
	register_setting( 'jaggrok_settings', 'jaggrok_ssl_bypass', false ); // NEW: SSL Bypass Toggle
}
add_action( 'admin_init', 'jaggrok_register_settings' );

function jaggrok_settings_page_callback() {
	include plugin_dir_path( __FILE__ ) . 'settings-template.php';
}

// AJAX Test API (v1.3.3 - SSL BYPASS)
function jaggrok_test_api_connection() {
	check_ajax_referer( 'jaggrok_test', 'nonce' );
	$api_key = sanitize_text_field( $_POST['api_key'] );
	update_option( 'jaggrok_xai_api_key', $api_key );
	$ssl_bypass = get_option( 'jaggrok_ssl_bypass', false );

	$args = array(
		'headers' => array(
			'Authorization' => 'Bearer ' . $api_key,
			'Content-Type' => 'application/json'
		),
		'body' => json_encode( array(
			'model' => 'grok-beta',
			'messages' => array( array( 'role' => 'user', 'content' => 'Hello JagGrok!' ) ),
			'max_tokens' => 10
		) ),
		'sslverify' => ! $ssl_bypass // BYPASS SSL IF TOGGLED
	);

	$response = wp_remote_post( 'https://api.x.ai/v1/chat/completions', $args );

	if ( is_wp_error( $response ) ) {
		$error = $response->get_error_message();
		jaggrok_log_error( 'API Test Failed: ' . $error );
		wp_send_json_error( 'Connection failed: ' . $error );
	}

	$code = wp_remote_retrieve_response_code( $response );
	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( $code !== 200 ) {
		$error = 'HTTP ' . $code . ' - ' . ( $body['error']['message'] ?? 'Unknown error' );
		jaggrok_log_error( 'API Test Response Error: ' . $error . ' | Full Body: ' . print_r( $body, true ) );
		wp_send_json_error( $error );
	}

	if ( isset( $body['choices'] ) ) {
		update_option( 'jaggrok_api_tested', true );
		wp_send_json_success();
	} else {
		$error = 'Invalid response: ' . print_r( $body, true );
		jaggrok_log_error( 'API Test Invalid Response: ' . $error );
		wp_send_json_error( 'Invalid API key' );
	}
}
add_action( 'wp_ajax_jaggrok_test_api', 'jaggrok_test_api_connection' );

// ERROR LOGGING FUNCTION (v1.3.3)
function jaggrok_log_error( $message ) {
	$log_file = plugin_dir_path( __FILE__ ) . 'jaggrok-errors.log';
	$timestamp = gmdate( 'Y-m-d H:i:s' );
	file_put_contents( $log_file, $timestamp . ' - ' . $message . "\n", FILE_APPEND | LOCK_EX );
}