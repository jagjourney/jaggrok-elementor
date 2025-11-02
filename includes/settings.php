<?php
// ============================================================================
// JAGJourney SETTINGS PAGE v1.3.8 (MODEL UPDATE + TIMEOUT LOG)
// ============================================================================

function jaggrok_get_provider_model_defaults() {
        return [
                'grok'   => 'grok-3-beta',
                'openai' => 'gpt-4o-mini',
        ];
}

function jaggrok_get_allowed_provider_models() {
        return [
                'grok'   => [ 'grok-3-mini', 'grok-3-beta', 'grok-3', 'grok-4-mini', 'grok-4', 'grok-4-code' ],
                'openai' => [ 'gpt-4o-mini', 'gpt-4o', 'gpt-4.1', 'gpt-4.1-mini', 'gpt-4.1-nano', 'o4-mini', 'o4' ],
        ];
}

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
        $defaults = [
                'jaggrok_provider'        => 'grok',
                'jaggrok_xai_api_key'     => '',
                'jaggrok_openai_api_key'  => '',
                'jaggrok_auto_insert'     => 'yes',
                'jaggrok_theme_style'     => 'modern',
                'jaggrok_max_tokens'      => 2000,
                'jaggrok_provider_models' => jaggrok_get_provider_model_defaults(),
                'jaggrok_model'           => 'grok-3-beta',
                'jaggrok_openai_model'    => 'gpt-4o-mini',
        ];

        register_setting(
                'jaggrok_settings',
                'jaggrok_xai_api_key',
                [
                        'sanitize_callback' => 'jaggrok_sanitize_api_key',
                        'default' => $defaults['jaggrok_xai_api_key'],
                ]
        );

        register_setting(
                'jaggrok_settings',
                'jaggrok_openai_api_key',
                [
                        'sanitize_callback' => 'jaggrok_sanitize_api_key',
                        'default' => $defaults['jaggrok_openai_api_key'],
                ]
        );

        register_setting(
                'jaggrok_settings',
                'jaggrok_auto_insert',
                [
                        'sanitize_callback' => 'jaggrok_sanitize_auto_insert',
                        'default' => $defaults['jaggrok_auto_insert'],
                ]
        );

        register_setting(
                'jaggrok_settings',
                'jaggrok_theme_style',
                [
                        'sanitize_callback' => 'jaggrok_sanitize_theme_style',
                        'default' => $defaults['jaggrok_theme_style'],
                ]
        );

        register_setting(
                'jaggrok_settings',
                'jaggrok_max_tokens',
                [
                        'sanitize_callback' => 'jaggrok_sanitize_max_tokens',
                        'default' => $defaults['jaggrok_max_tokens'],
                ]
        );

        register_setting(
                'jaggrok_settings',
                'jaggrok_provider_models',
                [
                        'sanitize_callback' => 'jaggrok_sanitize_provider_models',
                        'default' => $defaults['jaggrok_provider_models'],
                        'type' => 'array',
                ]
        );

        register_setting(
                'jaggrok_settings',
                'jaggrok_model',
                [
                        'sanitize_callback' => 'jaggrok_sanitize_model',
                        'default' => $defaults['jaggrok_model'],
                ]
        ); // v1.4.0: Better default

        register_setting(
                'jaggrok_settings',
                'jaggrok_openai_model',
                [
                        'sanitize_callback' => 'jaggrok_sanitize_openai_model',
                        'default' => $defaults['jaggrok_openai_model'],
                ]
        );

        register_setting(
                'jaggrok_settings',
                'jaggrok_provider',
                [
                        'sanitize_callback' => 'jaggrok_sanitize_provider',
                        'default' => $defaults['jaggrok_provider'],
                ]
        );

        foreach ( $defaults as $option => $default ) {
                $current = get_option( $option, false );
                if ( false === $current ) {
                        add_option( $option, $default );
                } elseif ( '' === $current && '' !== $default ) {
                        update_option( $option, $default );
                }
        }
}
add_action( 'admin_init', 'jaggrok_register_settings' );

function jaggrok_sanitize_api_key( $value ) {
        return sanitize_text_field( $value );
}

function jaggrok_sanitize_auto_insert( $value ) {
        $allowed = [ 'yes', 'no' ];
        return in_array( $value, $allowed, true ) ? $value : 'yes';
}

function jaggrok_sanitize_theme_style( $value ) {
        $allowed = [ 'modern', 'bold', 'minimal' ];
        return in_array( $value, $allowed, true ) ? $value : 'modern';
}

function jaggrok_sanitize_max_tokens( $value ) {
        $value = absint( $value );
        if ( $value < 500 ) {
                $value = 500;
        } elseif ( $value > 8000 ) {
                $value = 8000;
        }

        return $value > 0 ? $value : 2000;
}

function jaggrok_sanitize_provider_models( $value ) {
        $defaults = jaggrok_get_provider_model_defaults();
        $allowed  = jaggrok_get_allowed_provider_models();

        if ( ! is_array( $value ) ) {
                $value = [];
        }

        $sanitized = [];

        foreach ( $defaults as $provider => $default_model ) {
                $incoming = isset( $value[ $provider ] ) ? sanitize_text_field( $value[ $provider ] ) : '';
                $sanitized[ $provider ] = ( isset( $allowed[ $provider ] ) && in_array( $incoming, $allowed[ $provider ], true ) )
                        ? $incoming
                        : $default_model;
        }

        return $sanitized;
}

function jaggrok_get_provider_models() {
        $stored = get_option( 'jaggrok_provider_models', [] );

        if ( ! is_array( $stored ) ) {
                $stored = [];
        }

        return jaggrok_sanitize_provider_models( array_merge( jaggrok_get_provider_model_defaults(), $stored ) );
}

function jaggrok_sanitize_model( $value ) {
        $allowed = jaggrok_get_allowed_provider_models();
        $value   = sanitize_text_field( $value );

        return in_array( $value, $allowed['grok'], true ) ? $value : jaggrok_get_provider_model_defaults()['grok'];
}

function jaggrok_sanitize_openai_model( $value ) {
        $allowed = jaggrok_get_allowed_provider_models();
        $value   = sanitize_text_field( $value );

        return in_array( $value, $allowed['openai'], true ) ? $value : jaggrok_get_provider_model_defaults()['openai'];
}

function jaggrok_sync_legacy_model_options( $value, $old_value ) {
        if ( is_array( $value ) ) {
                if ( isset( $value['grok'] ) ) {
                        update_option( 'jaggrok_model', jaggrok_sanitize_model( $value['grok'] ) );
                }

                if ( isset( $value['openai'] ) ) {
                        update_option( 'jaggrok_openai_model', jaggrok_sanitize_openai_model( $value['openai'] ) );
                }
        }

        return $value;
}
add_filter( 'pre_update_option_jaggrok_provider_models', 'jaggrok_sync_legacy_model_options', 10, 2 );

function jaggrok_sanitize_provider( $value ) {
        $allowed = [ 'grok', 'openai' ];
        return in_array( $value, $allowed, true ) ? $value : 'grok';
}

function jaggrok_settings_page_callback() {
	include plugin_dir_path( __FILE__ ) . 'settings-template.php';
}

// AJAX Test API (v1.3.8 - MODEL UPDATE + TIMEOUT LOG)
function jaggrok_test_api_connection() {
        check_ajax_referer( 'jaggrok_test', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'Insufficient permissions to test the API connection.', 'jaggrok-elementor' ),
                                'code'    => 'jaggrok_insufficient_permissions',
                        ],
                        403
                );
        }

        $api_key = sanitize_text_field( $_POST['api_key'] );
        update_option( 'jaggrok_xai_api_key', $api_key );

	$response = wp_remote_post( 'https://api.x.ai/v1/chat/completions', [
		'headers' => [ 'Authorization' => 'Bearer ' . $api_key, 'Content-Type' => 'application/json' ],
		'body' => json_encode( [ 'model' => 'grok-3-beta', 'messages' => [ ['role' => 'user', 'content' => 'Hello JagGrok!' ] ], 'max_tokens' => 10 ] ),
		'timeout' => 30 // FIXED: TIMEOUT INCREASED
	]);

	if ( is_wp_error( $response ) ) {
		$error = $response->get_error_message();
		jaggrok_log_error( 'API Test Failed: ' . $error );
		wp_send_json_error( 'Connection failed: ' . $error );
	}

	$code = wp_remote_retrieve_response_code( $response );
	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	if ( $code !== 200 ) {
		$error = 'HTTP ' . $code . ' - ' . ( $body['error']['message'] ?? 'Unknown error' );
		jaggrok_log_error( $error . ' | Full Body: ' . print_r( $body, true ) );
		wp_send_json_error( $error );
	}

	if ( isset( $body['choices'] ) ) {
		update_option( 'jaggrok_api_tested', true );
		wp_send_json_success();
	} else {
		$error = 'Invalid response: ' . print_r( $body, true );
		jaggrok_log_error( $error );
		wp_send_json_error( 'Invalid API key' );
	}
}
add_action( 'wp_ajax_jaggrok_test_api', 'jaggrok_test_api_connection' );

// ERROR LOGGING FUNCTION (v1.3.8)
function jaggrok_log_error( $message ) {
	$log_file = plugin_dir_path( __FILE__ ) . 'jaggrok-errors.log';
	$timestamp = gmdate( 'Y-m-d H:i:s' );
	file_put_contents( $log_file, $timestamp . ' - ' . $message . "\n", FILE_APPEND | LOCK_EX );
}