<?php
// ============================================================================
// AiMentor SETTINGS PAGE v1.3.8 (MODEL UPDATE + TIMEOUT LOG)
// ============================================================================

function aimentor_get_provider_model_defaults() {
        return [
                'grok'   => [
                        'canvas'  => [
                                'fast'    => 'grok-3-mini',
                                'quality' => 'grok-4-code',
                        ],
                        'content' => [
                                'fast'    => 'grok-3-beta',
                                'quality' => 'grok-4',
                        ],
                ],
                'openai' => [
                        'canvas'  => [
                                'fast'    => 'gpt-4.1-nano',
                                'quality' => 'o4-mini',
                        ],
                        'content' => [
                                'fast'    => 'gpt-4o-mini',
                                'quality' => 'gpt-4o',
                        ],
                ],
        ];
}

function aimentor_get_model_labels() {
        return [
                'grok'   => [
                        'grok-3-mini' => __( 'Grok 3 Mini (Fast)', 'aimentor' ),
                        'grok-3-beta' => __( 'Grok 3 Beta (Balanced) ★', 'aimentor' ),
                        'grok-3'      => __( 'Grok 3 (Standard)', 'aimentor' ),
                        'grok-4-mini' => __( 'Grok 4 Mini (Premium)', 'aimentor' ),
                        'grok-4'      => __( 'Grok 4 (Flagship)', 'aimentor' ),
                        'grok-4-code' => __( 'Grok 4 Code', 'aimentor' ),
                ],
                'openai' => [
                        'gpt-4o-mini'  => __( 'GPT-4o mini (Balanced) ★', 'aimentor' ),
                        'gpt-4o'       => __( 'GPT-4o (Flagship)', 'aimentor' ),
                        'gpt-4.1'      => __( 'GPT-4.1 (Reasoning)', 'aimentor' ),
                        'gpt-4.1-mini' => __( 'GPT-4.1 mini (Fast)', 'aimentor' ),
                        'gpt-4.1-nano' => __( 'GPT-4.1 nano (Edge)', 'aimentor' ),
                        'o4-mini'      => __( 'o4-mini (Preview)', 'aimentor' ),
                        'o4'           => __( 'o4 (Preview)', 'aimentor' ),
                ],
        ];
}

function aimentor_get_allowed_provider_models() {
        return [
                'grok'   => [
                        'canvas'  => [
                                'fast'    => [ 'grok-3-mini', 'grok-3-beta', 'grok-4-mini', 'grok-4-code' ],
                                'quality' => [ 'grok-4-code', 'grok-4-mini', 'grok-4' ],
                        ],
                        'content' => [
                                'fast'    => [ 'grok-3-mini', 'grok-3-beta', 'grok-3', 'grok-4-mini' ],
                                'quality' => [ 'grok-3', 'grok-4-mini', 'grok-4', 'grok-4-code' ],
                        ],
                ],
                'openai' => [
                        'canvas'  => [
                                'fast'    => [ 'gpt-4.1-nano', 'gpt-4.1-mini', 'gpt-4o-mini' ],
                                'quality' => [ 'o4-mini', 'o4', 'gpt-4o', 'gpt-4.1' ],
                        ],
                        'content' => [
                                'fast'    => [ 'gpt-4o-mini', 'gpt-4.1-mini', 'gpt-4.1-nano' ],
                                'quality' => [ 'gpt-4o', 'gpt-4.1', 'o4-mini', 'o4' ],
                        ],
                ],
        ];
}

function aimentor_flatten_allowed_models_for_provider( $provider ) {
        $allowed = aimentor_get_allowed_provider_models();

        if ( ! isset( $allowed[ $provider ] ) || ! is_array( $allowed[ $provider ] ) ) {
                return [];
        }

        $flattened = [];

        foreach ( $allowed[ $provider ] as $tasks ) {
                if ( ! is_array( $tasks ) ) {
                        continue;
                }

                foreach ( $tasks as $models ) {
                        if ( ! is_array( $models ) ) {
                                continue;
                        }

                        foreach ( $models as $model ) {
                                $flattened[] = $model;
                        }
                }
        }

        return array_values( array_unique( $flattened ) );
}

function aimentor_get_model_presets() {
        $defaults = aimentor_get_provider_model_defaults();
        $stored   = get_option( 'aimentor_model_presets', [] );

        if ( ! is_array( $stored ) ) {
                $stored = [];
        }

        $merged = array_replace_recursive( $defaults, $stored );

        return aimentor_sanitize_model_presets( $merged );
}

function aimentor_map_presets_to_legacy_defaults( $presets ) {
        $legacy = [];

        foreach ( $presets as $provider => $tasks ) {
                if ( ! is_array( $tasks ) ) {
                        continue;
                }

                $legacy[ $provider ] = '';

                if ( isset( $tasks['content']['fast'] ) ) {
                        $legacy[ $provider ] = $tasks['content']['fast'];
                        continue;
                }

                foreach ( $tasks as $task ) {
                        if ( ! is_array( $task ) ) {
                                continue;
                        }

                        foreach ( $task as $model ) {
                                if ( '' !== $model ) {
                                        $legacy[ $provider ] = $model;
                                        break 2;
                                }
                        }
                }
        }

        return $legacy;
}

function aimentor_get_provider_labels() {
        return [
                'grok'   => __( 'xAI Grok', 'aimentor' ),
                'openai' => __( 'OpenAI', 'aimentor' ),
        ];
}

function aimentor_get_provider_test_statuses() {
        $providers = array_keys( aimentor_get_provider_labels() );
        $defaults  = [];

        foreach ( $providers as $provider ) {
                $defaults[ $provider ] = [
                        'status'    => '',
                        'message'   => '',
                        'timestamp' => 0,
                ];
        }

        $stored = get_option( 'aimentor_provider_test_statuses', [] );

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

function aimentor_get_default_options() {
        $model_presets    = aimentor_get_provider_model_defaults();
        $legacy_defaults  = aimentor_map_presets_to_legacy_defaults( $model_presets );

        return [
                'aimentor_provider'        => 'grok',
                'aimentor_xai_api_key'     => '',
                'aimentor_openai_api_key'  => '',
                'aimentor_auto_insert'     => 'yes',
                'aimentor_theme_style'     => 'modern',
                'aimentor_max_tokens'      => 2000,
                'aimentor_model_presets'   => $model_presets,
                'aimentor_provider_models' => $legacy_defaults,
                'aimentor_model'           => $legacy_defaults['grok'] ?? '',
                'aimentor_openai_model'    => $legacy_defaults['openai'] ?? '',
                'aimentor_default_generation_type' => 'content',
                'aimentor_default_performance'     => 'fast',
        ];
}

function aimentor_seed_default_options() {
        $defaults = aimentor_get_default_options();

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

function aimentor_update_provider_test_status( $provider_key, $status, $message ) {
        $allowed_statuses = [ 'success', 'error' ];

        if ( ! in_array( $provider_key, array_keys( aimentor_get_provider_labels() ), true ) ) {
                return;
        }

        $statuses = get_option( 'aimentor_provider_test_statuses', [] );

        if ( ! is_array( $statuses ) ) {
                $statuses = [];
        }

        $sanitized_status = in_array( $status, $allowed_statuses, true ) ? $status : '';

        $statuses[ $provider_key ] = [
                'status'    => $sanitized_status,
                'message'   => sanitize_text_field( $message ),
                'timestamp' => current_time( 'timestamp' ),
        ];

        update_option( 'aimentor_provider_test_statuses', $statuses );
}

function aimentor_format_provider_status_for_display( $provider_key, $status_data ) {
        $labels          = aimentor_get_provider_labels();
        $provider_label  = $labels[ $provider_key ] ?? ucfirst( $provider_key );
        $badge_labels    = [
                'success' => __( 'Connected', 'aimentor' ),
                'error'   => __( 'Error', 'aimentor' ),
                'idle'    => __( 'Not tested', 'aimentor' ),
                'pending' => __( 'Testing', 'aimentor' ),
        ];
        $state           = isset( $status_data['status'] ) && in_array( $status_data['status'], [ 'success', 'error' ], true )
                ? $status_data['status']
                : 'idle';
        $timestamp       = isset( $status_data['timestamp'] ) ? absint( $status_data['timestamp'] ) : 0;
        $message         = isset( $status_data['message'] ) ? $status_data['message'] : '';
        $description     = __( 'No tests have been run yet.', 'aimentor' );

        if ( $timestamp > 0 ) {
                $relative = human_time_diff( $timestamp, current_time( 'timestamp' ) );

                if ( 'success' === $state ) {
                        $default_message = sprintf( __( '%s API key is valid.', 'aimentor' ), $provider_label );
                        $description     = sprintf(
                                __( 'Last tested %1$s ago — %2$s', 'aimentor' ),
                                $relative,
                                $message ? $message : $default_message
                        );
                } else {
                        $default_message = sprintf( __( 'Unable to connect to %s.', 'aimentor' ), $provider_label );
                        $description     = sprintf(
                                __( 'Last attempt %1$s ago — %2$s', 'aimentor' ),
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

function aimentor_add_settings_page() {
        add_options_page(
                __( 'AiMentor Control Center', 'aimentor' ),
                __( 'AiMentor', 'aimentor' ),
                'manage_options',
                'aimentor-settings',
                'aimentor_settings_page_callback'
        );
}
add_action( 'admin_menu', 'aimentor_add_settings_page' );

function aimentor_register_settings() {
        $defaults = aimentor_get_default_options();

        register_setting(
                'aimentor_settings',
                'aimentor_xai_api_key',
                [
                        'sanitize_callback' => 'aimentor_sanitize_api_key',
                        'default' => $defaults['aimentor_xai_api_key'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_openai_api_key',
                [
                        'sanitize_callback' => 'aimentor_sanitize_api_key',
                        'default' => $defaults['aimentor_openai_api_key'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_auto_insert',
                [
                        'sanitize_callback' => 'aimentor_sanitize_auto_insert',
                        'default' => $defaults['aimentor_auto_insert'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_theme_style',
                [
                        'sanitize_callback' => 'aimentor_sanitize_theme_style',
                        'default' => $defaults['aimentor_theme_style'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_max_tokens',
                [
                        'sanitize_callback' => 'aimentor_sanitize_max_tokens',
                        'default' => $defaults['aimentor_max_tokens'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_default_generation_type',
                [
                        'sanitize_callback' => 'aimentor_sanitize_generation_type',
                        'default' => $defaults['aimentor_default_generation_type'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_default_performance',
                [
                        'sanitize_callback' => 'aimentor_sanitize_performance_tier',
                        'default' => $defaults['aimentor_default_performance'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_model_presets',
                [
                        'sanitize_callback' => 'aimentor_sanitize_model_presets',
                        'default' => $defaults['aimentor_model_presets'],
                        'type' => 'array',
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_provider_models',
                [
                        'sanitize_callback' => 'aimentor_sanitize_provider_models',
                        'default' => $defaults['aimentor_provider_models'],
                        'type' => 'array',
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_model',
                [
                        'sanitize_callback' => 'aimentor_sanitize_model',
                        'default' => $defaults['aimentor_model'],
                ]
        ); // v1.4.0: Better default

        register_setting(
                'aimentor_settings',
                'aimentor_openai_model',
                [
                        'sanitize_callback' => 'aimentor_sanitize_openai_model',
                        'default' => $defaults['aimentor_openai_model'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_provider',
                [
                        'sanitize_callback' => 'aimentor_sanitize_provider',
                        'default' => $defaults['aimentor_provider'],
                ]
        );

        aimentor_seed_default_options();
        aimentor_migrate_legacy_model_presets();
}
add_action( 'admin_init', 'aimentor_register_settings' );

function aimentor_sanitize_api_key( $value ) {
        return sanitize_text_field( $value );
}

function aimentor_sanitize_auto_insert( $value ) {
        $allowed = [ 'yes', 'no' ];
        return in_array( $value, $allowed, true ) ? $value : 'yes';
}

function aimentor_sanitize_theme_style( $value ) {
        $allowed = [ 'modern', 'bold', 'minimal' ];
        return in_array( $value, $allowed, true ) ? $value : 'modern';
}

function aimentor_sanitize_generation_type( $value ) {
        $allowed = [ 'canvas', 'content' ];

        if ( ! aimentor_is_pro_active() && 'canvas' === $value ) {
                return 'content';
        }

        return in_array( $value, $allowed, true ) ? $value : 'content';
}

function aimentor_sanitize_performance_tier( $value ) {
        $allowed = [ 'fast', 'quality' ];
        return in_array( $value, $allowed, true ) ? $value : 'fast';
}

function aimentor_sanitize_max_tokens( $value ) {
        $value = absint( $value );
        if ( $value < 500 ) {
                $value = 500;
        } elseif ( $value > 8000 ) {
                $value = 8000;
        }

        return $value > 0 ? $value : 2000;
}

function aimentor_sanitize_model_presets( $value ) {
        $defaults = aimentor_get_provider_model_defaults();
        $allowed  = aimentor_get_allowed_provider_models();

        if ( ! is_array( $value ) ) {
                $value = [];
        }

        $sanitized = [];

        foreach ( $defaults as $provider => $tasks ) {
                $sanitized[ $provider ] = [];

                foreach ( $tasks as $task => $tiers ) {
                        $sanitized[ $provider ][ $task ] = [];

                        foreach ( $tiers as $tier => $default_model ) {
                                $incoming = isset( $value[ $provider ][ $task ][ $tier ] )
                                        ? sanitize_text_field( $value[ $provider ][ $task ][ $tier ] )
                                        : '';

                                $allowed_models = $allowed[ $provider ][ $task ][ $tier ] ?? [];
                                if ( ! is_array( $allowed_models ) ) {
                                        $allowed_models = [];
                                }

                                $sanitized[ $provider ][ $task ][ $tier ] = in_array( $incoming, $allowed_models, true )
                                        ? $incoming
                                        : $default_model;
                        }
                }
        }

        return $sanitized;
}

function aimentor_sanitize_provider_models( $value ) {
        $defaults = aimentor_map_presets_to_legacy_defaults( aimentor_get_provider_model_defaults() );

        if ( ! is_array( $value ) ) {
                $value = [];
        }

        $sanitized = [];

        foreach ( $defaults as $provider => $default_model ) {
                $incoming       = isset( $value[ $provider ] ) ? sanitize_text_field( $value[ $provider ] ) : '';
                $allowed_models = aimentor_flatten_allowed_models_for_provider( $provider );

                $sanitized[ $provider ] = in_array( $incoming, $allowed_models, true )
                        ? $incoming
                        : $default_model;
        }

        return $sanitized;
}

function aimentor_get_provider_models() {
        $stored = get_option( 'aimentor_provider_models', [] );

        if ( ! is_array( $stored ) ) {
                $stored = [];
        }

        $defaults = aimentor_map_presets_to_legacy_defaults( aimentor_get_provider_model_defaults() );

        return aimentor_sanitize_provider_models( array_merge( $defaults, $stored ) );
}

function aimentor_sanitize_model( $value ) {
        $value   = sanitize_text_field( $value );
        $allowed = aimentor_flatten_allowed_models_for_provider( 'grok' );
        $defaults = aimentor_get_provider_model_defaults();
        $fallback = $defaults['grok']['content']['fast'] ?? '';

        return in_array( $value, $allowed, true ) ? $value : $fallback;
}

function aimentor_sanitize_openai_model( $value ) {
        $value   = sanitize_text_field( $value );
        $allowed = aimentor_flatten_allowed_models_for_provider( 'openai' );
        $defaults = aimentor_get_provider_model_defaults();
        $fallback = $defaults['openai']['content']['fast'] ?? '';

        return in_array( $value, $allowed, true ) ? $value : $fallback;
}

function aimentor_sync_legacy_model_options( $value, $old_value ) {
        $sanitized = aimentor_sanitize_provider_models( is_array( $value ) ? $value : [] );

        if ( isset( $sanitized['grok'] ) ) {
                update_option( 'aimentor_model', aimentor_sanitize_model( $sanitized['grok'] ) );
        }

        if ( isset( $sanitized['openai'] ) ) {
                update_option( 'aimentor_openai_model', aimentor_sanitize_openai_model( $sanitized['openai'] ) );
        }

        $presets          = aimentor_get_model_presets();
        $preset_defaults  = aimentor_get_provider_model_defaults();

        foreach ( $preset_defaults as $provider => $tasks ) {
                $legacy_model = $sanitized[ $provider ] ?? '';

                if ( '' === $legacy_model || ! isset( $presets[ $provider ] ) ) {
                        continue;
                }

                foreach ( $tasks as $task => $tiers ) {
                        foreach ( $tiers as $tier => $_model ) {
                                $presets[ $provider ][ $task ][ $tier ] = $legacy_model;
                        }
                }
        }

        update_option( 'aimentor_model_presets', aimentor_sanitize_model_presets( $presets ) );

        return $sanitized;
}
add_filter( 'pre_update_option_aimentor_provider_models', 'aimentor_sync_legacy_model_options', 10, 2 );

function aimentor_migrate_legacy_model_presets() {
        $stored        = get_option( 'aimentor_model_presets', false );
        $presets       = false !== $stored ? aimentor_sanitize_model_presets( is_array( $stored ) ? $stored : [] ) : aimentor_get_provider_model_defaults();
        $legacy_models = aimentor_sanitize_provider_models( get_option( 'aimentor_provider_models', [] ) );
        $legacy_grok   = aimentor_sanitize_model( get_option( 'aimentor_model', '' ) );
        $legacy_openai = aimentor_sanitize_openai_model( get_option( 'aimentor_openai_model', '' ) );

        if ( '' !== $legacy_grok ) {
                $legacy_models['grok'] = $legacy_grok;
        }

        if ( '' !== $legacy_openai ) {
                $legacy_models['openai'] = $legacy_openai;
        }

        $changed = false;

        foreach ( $presets as $provider => $tasks ) {
                $legacy_model = $legacy_models[ $provider ] ?? '';

                if ( '' === $legacy_model ) {
                        continue;
                }

                foreach ( $tasks as $task => $tiers ) {
                        foreach ( array_keys( $tiers ) as $tier ) {
                                if ( $presets[ $provider ][ $task ][ $tier ] !== $legacy_model ) {
                                        $presets[ $provider ][ $task ][ $tier ] = $legacy_model;
                                        $changed                                 = true;
                                }
                        }
                }
        }

        if ( false === $stored || $changed ) {
                update_option( 'aimentor_model_presets', aimentor_sanitize_model_presets( $presets ) );
        }
}

function aimentor_sanitize_provider( $value ) {
        $allowed = [ 'grok', 'openai' ];
        return in_array( $value, $allowed, true ) ? $value : 'grok';
}

function aimentor_settings_page_callback() {
	include plugin_dir_path( __FILE__ ) . 'settings-template.php';
}

// AJAX Test API (v1.3.8 - MODEL UPDATE + TIMEOUT LOG)
function aimentor_test_api_connection() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

        if ( ! wp_verify_nonce( $nonce, 'aimentor_test' ) && ! wp_verify_nonce( $nonce, 'jaggrok_test' ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'Security check failed.', 'aimentor' ),
                                'code'    => 'aimentor_invalid_nonce',
                        ],
                        403
                );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'Insufficient permissions to test the API connection.', 'aimentor' ),
                                'code'    => 'aimentor_insufficient_permissions',
                        ],
                        403
                );
        }

        $provider_key    = isset( $_POST['provider'] ) ? sanitize_text_field( wp_unslash( $_POST['provider'] ) ) : 'grok';
        $provider_labels = aimentor_get_provider_labels();

        if ( ! array_key_exists( $provider_key, $provider_labels ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'Invalid provider selected.', 'aimentor' ),
                                'code'    => 'aimentor_invalid_provider',
                                'badge_state' => 'error',
                                'badge_label' => __( 'Error', 'aimentor' ),
                                'description' => __( 'Select a valid provider and try again.', 'aimentor' ),
                                'provider'    => $provider_key,
                        ],
                        400
                );
        }

	$api_key = isset( $_POST['api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['api_key'] ) ) : '';
	$label   = $provider_labels[ $provider_key ];

	if ( '' === $api_key ) {
		$message = sprintf( __( '%s API key is required to test the connection.', 'aimentor' ), $label );
		aimentor_update_provider_test_status( $provider_key, 'error', $message );
		$status = aimentor_get_provider_test_statuses();
		$view   = aimentor_format_provider_status_for_display( $provider_key, $status[ $provider_key ] );
		$view['provider'] = $provider_key;
		$view['message']  = $message;
		wp_send_json_error( $view, 400 );
	}

        $presets       = aimentor_get_model_presets();
        $model_default = aimentor_get_provider_model_defaults();
        $model         = $presets[ $provider_key ]['content']['fast'] ?? ( $model_default[ $provider_key ]['content']['fast'] ?? '' );

        switch ( $provider_key ) {
                case 'openai':
                        update_option( 'aimentor_openai_api_key', $api_key );
                        $model = $presets['openai']['content']['fast'] ?? ( $model_default['openai']['content']['fast'] ?? '' );
                        break;
                case 'grok':
                default:
                        update_option( 'aimentor_xai_api_key', $api_key );
                        $model = $presets['grok']['content']['fast'] ?? ( $model_default['grok']['content']['fast'] ?? '' );
                        break;
        }

        if ( empty( $model ) ) {
                $legacy_fallbacks = aimentor_map_presets_to_legacy_defaults( $model_default );
                $model            = $legacy_fallbacks[ $provider_key ] ?? '';
        }

	$provider = aimentor_get_active_provider( $provider_key );

	if ( ! $provider instanceof AiMentor_Provider_Interface ) {
		$message = __( 'Provider configuration error.', 'aimentor' );
		aimentor_log_error(
			$message,
			[
				'provider' => $provider_key,
				'model'    => $model,
				'user_id'  => get_current_user_id(),
			]
		);
		aimentor_update_provider_test_status( $provider_key, 'error', $message );
		$status = aimentor_get_provider_test_statuses();
		$view   = aimentor_format_provider_status_for_display( $provider_key, $status[ $provider_key ] );
		$view['provider'] = $provider_key;
		$view['message']  = $message;
		wp_send_json_error( $view );
	}

	$result = $provider->request(
            __( 'Respond with a short confirmation to verify the AiMentor Elementor integration.', 'aimentor' ),
		[
			'api_key'    => $api_key,
			'model'      => $model,
			'max_tokens' => 32,
			'timeout'    => 20,
		]
	);

	if ( is_wp_error( $result ) ) {
		$error_message = sprintf( __( '%1$s connection failed: %2$s', 'aimentor' ), $label, $result->get_error_message() );
		aimentor_log_error(
			$error_message . ' | Details: ' . wp_json_encode( $result->get_error_data() ),
			[
				'provider' => $provider_key,
				'model'    => $model,
				'user_id'  => get_current_user_id(),
			]
		);
		aimentor_update_provider_test_status( $provider_key, 'error', $error_message );
		$status = aimentor_get_provider_test_statuses();
		$view   = aimentor_format_provider_status_for_display( $provider_key, $status[ $provider_key ] );
		$view['provider'] = $provider_key;
		$view['message']  = $error_message;
		wp_send_json_error( $view );
	}

	if ( ! is_array( $result ) || ! isset( $result['type'] ) ) {
		$error_message = sprintf( __( '%s returned an unexpected response.', 'aimentor' ), $label );
		aimentor_log_error(
			$error_message . ' | Result: ' . wp_json_encode( $result ),
			[
				'provider' => $provider_key,
				'model'    => $model,
				'user_id'  => get_current_user_id(),
			]
		);
		aimentor_update_provider_test_status( $provider_key, 'error', $error_message );
		$status = aimentor_get_provider_test_statuses();
		$view   = aimentor_format_provider_status_for_display( $provider_key, $status[ $provider_key ] );
		$view['provider'] = $provider_key;
		$view['message']  = $error_message;
		wp_send_json_error( $view );
	}

        $success_message = sprintf( __( '%s API key verified successfully.', 'aimentor' ), $label );
        aimentor_update_provider_test_status( $provider_key, 'success', $success_message );
        update_option( 'aimentor_api_tested', true );

        $status = aimentor_get_provider_test_statuses();
        $view   = aimentor_format_provider_status_for_display( $provider_key, $status[ $provider_key ] );
        $view['provider'] = $provider_key;

        wp_send_json_success( $view );
}
add_action( 'wp_ajax_aimentor_test_api', 'aimentor_test_api_connection' );
add_action( 'wp_ajax_jaggrok_test_api', 'aimentor_test_api_connection' );

// ERROR LOGGING FUNCTION (v1.3.8)
function aimentor_log_error( $message, $context = [] ) {
	$log_file  = plugin_dir_path( __FILE__ ) . 'aimentor-errors.log';
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

function aimentor_mirror_option_to_legacy( $modern_option, $value ) {
        if ( 0 !== strpos( $modern_option, 'aimentor_' ) ) {
                return;
        }

        $legacy_option = str_replace( 'aimentor_', 'jaggrok_', $modern_option );

        if ( $legacy_option === $modern_option ) {
                return;
        }

        update_option( $legacy_option, $value );
}

$aimentor_options_to_mirror = [
        'aimentor_provider',
        'aimentor_xai_api_key',
        'aimentor_openai_api_key',
        'aimentor_auto_insert',
        'aimentor_theme_style',
        'aimentor_max_tokens',
        'aimentor_model',
        'aimentor_openai_model',
        'aimentor_provider_models',
        'aimentor_api_tested',
        'aimentor_provider_test_statuses',
];

foreach ( $aimentor_options_to_mirror as $option_name ) {
        add_action(
                "update_option_{$option_name}",
                function( $old_value, $value ) use ( $option_name ) {
                        aimentor_mirror_option_to_legacy( $option_name, $value );
                },
                10,
                2
        );

        add_action(
                "add_option_{$option_name}",
                function( $option, $value ) use ( $option_name ) {
                        aimentor_mirror_option_to_legacy( $option_name, $value );
                },
                10,
                2
        );
}
