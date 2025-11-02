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

function jaggrok_get_provider_labels() {
        return [
                'grok'   => __( 'xAI Grok', 'jaggrok-elementor' ),
                'openai' => __( 'OpenAI', 'jaggrok-elementor' ),
        ];
}

function jaggrok_get_provider_test_statuses() {
        $providers = array_keys( jaggrok_get_provider_labels() );
        $defaults  = [];

        foreach ( $providers as $provider ) {
                $defaults[ $provider ] = [
                        'status'    => '',
                        'message'   => '',
                        'timestamp' => 0,
                ];
        }

        $stored = get_option( 'jaggrok_provider_test_statuses', [] );

        if ( ! is_array( $stored ) ) {
                return $defaults;
        }

        foreach ( $providers as $provider ) {
                $entry = isset( $stored[ $provider ] ) && is_array( $stored[ $provider ] ) ? $stored[ $provider ] : [];

                $status = isset( $entry['status'] ) && in_array( $entry['status'], [ 'success', 'error' ], true )
                        ? $entry['status']
                        : '';

                $message = isset( $entry['message'] ) ? sanitize_text_field( $entry['message'] ) : '';
                $timestamp = isset( $entry['timestamp'] ) ? absint( $entry['timestamp'] ) : 0;

                $defaults[ $provider ] = [
                        'status'    => $status,
                        'message'   => $message,
                        'timestamp' => $timestamp,
                ];
        }

        return $defaults;
}

function jaggrok_get_default_options() {
        $provider_defaults = jaggrok_get_provider_model_defaults();

        return [
                'jaggrok_provider'        => 'grok',
                'jaggrok_xai_api_key'     => '',
                'jaggrok_openai_api_key'  => '',
                'jaggrok_auto_insert'     => 'yes',
                'jaggrok_theme_style'     => 'modern',
                'jaggrok_max_tokens'      => 2000,
                'jaggrok_provider_models' => $provider_defaults,
                'jaggrok_model'           => $provider_defaults['grok'],
                'jaggrok_openai_model'    => $provider_defaults['openai'],
        ];
}

function jaggrok_seed_default_options() {
        $defaults = jaggrok_get_default_options();

        foreach ( $defaults as $option => $default ) {
                $current = get_option( $option, false );

                if ( false === $current ) {
                        add_option( $option, $default );
                        continue;
                }

                if ( is_array( $default ) ) {
                        if ( ! is_array( $current ) || empty( $current ) ) {
                                update_option( $option, $default );
                        }

                        continue;
                }

                if ( '' === $current && '' !== $default ) {
                        update_option( $option, $default );
                }
        }
}

function jaggrok_update_provider_test_status( $provider_key, $status, $message ) {
        $allowed_statuses = [ 'success', 'error' ];

        if ( ! in_array( $provider_key, array_keys( jaggrok_get_provider_labels() ), true ) ) {
                return;
        }

        $statuses = get_option( 'jaggrok_provider_test_statuses', [] );

        if ( ! is_array( $statuses ) ) {
                $statuses = [];
        }

        $sanitized_status = in_array( $status, $allowed_statuses, true ) ? $status : '';

        $statuses[ $provider_key ] = [
                'status'    => $sanitized_status,
                'message'   => sanitize_text_field( $message ),
                'timestamp' => current_time( 'timestamp' ),
        ];

        update_option( 'jaggrok_provider_test_statuses', $statuses );
}

function jaggrok_format_provider_status_for_display( $provider_key, $status_data ) {
        $labels          = jaggrok_get_provider_labels();
        $provider_label  = $labels[ $provider_key ] ?? ucfirst( $provider_key );
        $badge_labels    = [
                'success' => __( 'Connected', 'jaggrok-elementor' ),
                'error'   => __( 'Error', 'jaggrok-elementor' ),
                'idle'    => __( 'Not tested', 'jaggrok-elementor' ),
                'pending' => __( 'Testing', 'jaggrok-elementor' ),
        ];
        $state           = isset( $status_data['status'] ) && in_array( $status_data['status'], [ 'success', 'error' ], true )
                ? $status_data['status']
                : 'idle';
        $timestamp       = isset( $status_data['timestamp'] ) ? absint( $status_data['timestamp'] ) : 0;
        $message         = isset( $status_data['message'] ) ? $status_data['message'] : '';
        $description     = __( 'No tests have been run yet.', 'jaggrok-elementor' );

        if ( $timestamp > 0 ) {
                $relative = human_time_diff( $timestamp, current_time( 'timestamp' ) );

                if ( 'success' === $state ) {
                        $default_message = sprintf( __( '%s API key is valid.', 'jaggrok-elementor' ), $provider_label );
                        $description     = sprintf(
                                __( 'Last tested %1$s ago — %2$s', 'jaggrok-elementor' ),
                                $relative,
                                $message ? $message : $default_message
                        );
                } else {
                        $default_message = sprintf( __( 'Unable to connect to %s.', 'jaggrok-elementor' ), $provider_label );
                        $description     = sprintf(
                                __( 'Last attempt %1$s ago — %2$s', 'jaggrok-elementor' ),
                                $relative,
                                $message ? $message : $default_message
                        );
                }
        }

        if ( ! isset( $badge_labels[ $state ] ) ) {
                $state = 'idle';
        }

        return [
                'badge_state' => $state,
                'badge_label' => $badge_labels[ $state ],
                'description' => $description,
                'timestamp'   => $timestamp,
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
        $defaults = jaggrok_get_default_options();

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

        jaggrok_seed_default_options();
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
                $incoming       = isset( $value[ $provider ] ) ? sanitize_text_field( $value[ $provider ] ) : '';
                $allowed_models = isset( $allowed[ $provider ] ) && is_array( $allowed[ $provider ] ) ? $allowed[ $provider ] : [];

                $sanitized[ $provider ] = in_array( $incoming, $allowed_models, true )
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
        $grok    = isset( $allowed['grok'] ) && is_array( $allowed['grok'] ) ? $allowed['grok'] : [];

        return in_array( $value, $grok, true ) ? $value : jaggrok_get_provider_model_defaults()['grok'];
}

function jaggrok_sanitize_openai_model( $value ) {
        $allowed = jaggrok_get_allowed_provider_models();
        $value   = sanitize_text_field( $value );
        $openai  = isset( $allowed['openai'] ) && is_array( $allowed['openai'] ) ? $allowed['openai'] : [];

        return in_array( $value, $openai, true ) ? $value : jaggrok_get_provider_model_defaults()['openai'];
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

        $provider_key    = isset( $_POST['provider'] ) ? sanitize_text_field( wp_unslash( $_POST['provider'] ) ) : 'grok';
        $provider_labels = jaggrok_get_provider_labels();

        if ( ! array_key_exists( $provider_key, $provider_labels ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'Invalid provider selected.', 'jaggrok-elementor' ),
                                'code'    => 'jaggrok_invalid_provider',
                                'badge_state' => 'error',
                                'badge_label' => __( 'Error', 'jaggrok-elementor' ),
                                'description' => __( 'Select a valid provider and try again.', 'jaggrok-elementor' ),
                                'provider'    => $provider_key,
                        ],
                        400
                );
        }

	$api_key = isset( $_POST['api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['api_key'] ) ) : '';
	$label   = $provider_labels[ $provider_key ];

	if ( '' === $api_key ) {
		$message = sprintf( __( '%s API key is required to test the connection.', 'jaggrok-elementor' ), $label );
		jaggrok_update_provider_test_status( $provider_key, 'error', $message );
		$status = jaggrok_get_provider_test_statuses();
		$view   = jaggrok_format_provider_status_for_display( $provider_key, $status[ $provider_key ] );
		$view['provider'] = $provider_key;
		$view['message']  = $message;
		wp_send_json_error( $view, 400 );
	}

	$models        = jaggrok_get_provider_models();
	$model_default = jaggrok_get_provider_model_defaults();
	$model         = $models[ $provider_key ] ?? ( $model_default[ $provider_key ] ?? '' );

	switch ( $provider_key ) {
		case 'openai':
			update_option( 'jaggrok_openai_api_key', $api_key );
			$model = $models['openai'] ?? ( $model_default['openai'] ?? '' );
			break;
		case 'grok':
		default:
			update_option( 'jaggrok_xai_api_key', $api_key );
			$model = $models['grok'] ?? ( $model_default['grok'] ?? '' );
			break;
	}

	$provider = jaggrok_get_active_provider( $provider_key );

	if ( ! $provider instanceof JagGrok_Provider_Interface ) {
		$message = __( 'Provider configuration error.', 'jaggrok-elementor' );
		jaggrok_log_error(
			$message,
			[
				'provider' => $provider_key,
				'model'    => $model,
				'user_id'  => get_current_user_id(),
			]
		);
		jaggrok_update_provider_test_status( $provider_key, 'error', $message );
		$status = jaggrok_get_provider_test_statuses();
		$view   = jaggrok_format_provider_status_for_display( $provider_key, $status[ $provider_key ] );
		$view['provider'] = $provider_key;
		$view['message']  = $message;
		wp_send_json_error( $view );
	}

	$result = $provider->request(
		__( 'Respond with a short confirmation to verify the JagGrok Elementor integration.', 'jaggrok-elementor' ),
		[
			'api_key'    => $api_key,
			'model'      => $model,
			'max_tokens' => 32,
			'timeout'    => 20,
		]
	);

	if ( is_wp_error( $result ) ) {
		$error_message = sprintf( __( '%1$s connection failed: %2$s', 'jaggrok-elementor' ), $label, $result->get_error_message() );
		jaggrok_log_error(
			$error_message . ' | Details: ' . wp_json_encode( $result->get_error_data() ),
			[
				'provider' => $provider_key,
				'model'    => $model,
				'user_id'  => get_current_user_id(),
			]
		);
		jaggrok_update_provider_test_status( $provider_key, 'error', $error_message );
		$status = jaggrok_get_provider_test_statuses();
		$view   = jaggrok_format_provider_status_for_display( $provider_key, $status[ $provider_key ] );
		$view['provider'] = $provider_key;
		$view['message']  = $error_message;
		wp_send_json_error( $view );
	}

	if ( ! is_array( $result ) || ! isset( $result['type'] ) ) {
		$error_message = sprintf( __( '%s returned an unexpected response.', 'jaggrok-elementor' ), $label );
		jaggrok_log_error(
			$error_message . ' | Result: ' . wp_json_encode( $result ),
			[
				'provider' => $provider_key,
				'model'    => $model,
				'user_id'  => get_current_user_id(),
			]
		);
		jaggrok_update_provider_test_status( $provider_key, 'error', $error_message );
		$status = jaggrok_get_provider_test_statuses();
		$view   = jaggrok_format_provider_status_for_display( $provider_key, $status[ $provider_key ] );
		$view['provider'] = $provider_key;
		$view['message']  = $error_message;
		wp_send_json_error( $view );
	}

        $success_message = sprintf( __( '%s API key verified successfully.', 'jaggrok-elementor' ), $label );
        jaggrok_update_provider_test_status( $provider_key, 'success', $success_message );
        update_option( 'jaggrok_api_tested', true );

        $status = jaggrok_get_provider_test_statuses();
        $view   = jaggrok_format_provider_status_for_display( $provider_key, $status[ $provider_key ] );
        $view['provider'] = $provider_key;

        wp_send_json_success( $view );
}
add_action( 'wp_ajax_jaggrok_test_api', 'jaggrok_test_api_connection' );

// ERROR LOGGING FUNCTION (v1.3.8)
function jaggrok_log_error( $message, $context = [] ) {
	$log_file  = plugin_dir_path( __FILE__ ) . 'jaggrok-errors.log';
	$timestamp = gmdate( 'Y-m-d H:i:s' );
	$log_entry = $message;

	if ( is_array( $context ) && ! empty( $context ) ) {
		$allowed_keys = [ 'provider', 'model', 'user_id' ];
		$context_data = [];

		foreach ( $allowed_keys as $key ) {
			if ( array_key_exists( $key, $context ) && null !== $context[ $key ] && '' !== $context[ $key ] ) {
				$context_data[ $key ] = $context[ $key ];
			}
		}

		if ( ! empty( $context_data ) ) {
			$log_entry = wp_json_encode(
				[
					'message' => $message,
					'context' => $context_data,
				]
			);
		}
	}

	file_put_contents( $log_file, $timestamp . ' - ' . $log_entry . "\n", FILE_APPEND | LOCK_EX );
}
