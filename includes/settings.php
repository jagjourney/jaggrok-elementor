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

function aimentor_get_allowed_provider_models() {
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

function aimentor_flatten_allowed_models_for_provider( $provider_key ) {
        $allowed = aimentor_get_allowed_provider_models();

        if ( ! isset( $allowed[ $provider_key ] ) || ! is_array( $allowed[ $provider_key ] ) ) {
                return [];
        }

        $flattened = [];

        $walker = static function( $items ) use ( &$walker, &$flattened ) {
                foreach ( $items as $key => $value ) {
                        if ( is_array( $value ) ) {
                                $walker( $value );
                                continue;
                        }

                        if ( is_string( $key ) && '' !== $key ) {
                                $flattened[] = $key;
                                continue;
                        }

                        if ( is_string( $value ) && '' !== $value ) {
                                $flattened[] = $value;
                        }
                }
        };

        $walker( $allowed[ $provider_key ] );

        return array_values( array_unique( $flattened ) );
}

function aimentor_map_presets_to_legacy_defaults( $presets ) {
        $mapped = [];

        if ( ! is_array( $presets ) ) {
                return $mapped;
        }

        foreach ( $presets as $provider => $tasks ) {
                $fallback = '';

                if ( isset( $tasks['content']['fast'] ) && is_string( $tasks['content']['fast'] ) ) {
                        $fallback = $tasks['content']['fast'];
                } else {
                        foreach ( $tasks as $task ) {
                                if ( ! is_array( $task ) ) {
                                        continue;
                                }

                                foreach ( $task as $model ) {
                                        if ( is_string( $model ) && '' !== $model ) {
                                                $fallback = $model;
                                                break 2;
                                        }
                                }
                        }
                }

                $mapped[ $provider ] = $fallback;
        }

        return $mapped;
}

function aimentor_get_model_labels() {
        return aimentor_get_allowed_provider_models();
}

function aimentor_get_provider_labels() {
        return [
                'grok'   => __( 'xAI Grok', 'aimentor' ),
                'openai' => __( 'OpenAI', 'aimentor' ),
        ];
}

function aimentor_get_usage_transient_key() {
        return 'aimentor_provider_usage_snapshot';
}

function aimentor_get_usage_defaults() {
        $providers = array_keys( aimentor_get_provider_labels() );
        $defaults  = [
                'providers'   => [],
                'generated_at' => current_time( 'timestamp' ),
        ];

        foreach ( $providers as $provider ) {
                $defaults['providers'][ $provider ] = [
                        'success_count' => 0,
                        'error_count'   => 0,
                        'last_success'  => 0,
                        'last_error'    => 0,
                        'last_event'    => 0,
                        'last_model'    => '',
                        'last_task'     => '',
                        'last_tier'     => '',
                        'last_origin'   => '',
                ];
        }

        return $defaults;
}

function aimentor_get_provider_usage_data() {
        $transient_key = aimentor_get_usage_transient_key();
        $stored        = get_transient( $transient_key );

        if ( ! is_array( $stored ) ) {
                $stored = aimentor_get_usage_defaults();
        }

        $defaults = aimentor_get_usage_defaults();

        foreach ( $defaults['providers'] as $provider => $template ) {
                if ( ! isset( $stored['providers'][ $provider ] ) || ! is_array( $stored['providers'][ $provider ] ) ) {
                        $stored['providers'][ $provider ] = $template;
                        continue;
                }

                $stored['providers'][ $provider ] = array_merge( $template, $stored['providers'][ $provider ] );
        }

        if ( ! isset( $stored['generated_at'] ) ) {
                $stored['generated_at'] = current_time( 'timestamp' );
        }

        set_transient( $transient_key, $stored, DAY_IN_SECONDS );

        return $stored;
}

function aimentor_record_provider_usage( $provider_key, $status, $context = [] ) {
        $provider_key = sanitize_key( $provider_key );
        $status       = in_array( $status, [ 'success', 'error' ], true ) ? $status : 'success';
        $providers    = aimentor_get_provider_labels();

        if ( ! array_key_exists( $provider_key, $providers ) ) {
                return;
        }

        $data      = aimentor_get_provider_usage_data();
        $timestamp = current_time( 'timestamp' );

        if ( ! isset( $data['providers'][ $provider_key ] ) ) {
                $data['providers'][ $provider_key ] = aimentor_get_usage_defaults()['providers'][ $provider_key ];
        }

        if ( 'success' === $status ) {
                $data['providers'][ $provider_key ]['success_count'] = absint( $data['providers'][ $provider_key ]['success_count'] ) + 1;
                $data['providers'][ $provider_key ]['last_success']  = $timestamp;
        } else {
                $data['providers'][ $provider_key ]['error_count'] = absint( $data['providers'][ $provider_key ]['error_count'] ) + 1;
                $data['providers'][ $provider_key ]['last_error']  = $timestamp;
        }

        $context = is_array( $context ) ? $context : [];

        if ( isset( $context['model'] ) ) {
                $data['providers'][ $provider_key ]['last_model'] = sanitize_text_field( $context['model'] );
        }

        if ( isset( $context['task'] ) ) {
                $data['providers'][ $provider_key ]['last_task'] = sanitize_key( $context['task'] );
        }

        if ( isset( $context['tier'] ) ) {
                $data['providers'][ $provider_key ]['last_tier'] = sanitize_key( $context['tier'] );
        }

        if ( isset( $context['origin'] ) ) {
                $allowed_origins = [ 'generation', 'test' ];
                $origin          = sanitize_key( $context['origin'] );
                $data['providers'][ $provider_key ]['last_origin'] = in_array( $origin, $allowed_origins, true ) ? $origin : '';
        }

        $data['providers'][ $provider_key ]['last_event'] = $timestamp;
        $data['generated_at']                              = $timestamp;

        set_transient( aimentor_get_usage_transient_key(), $data, DAY_IN_SECONDS );
}

function aimentor_get_provider_usage_summary() {
        $data            = aimentor_get_provider_usage_data();
        $labels          = aimentor_get_provider_labels();
        $allowed_models  = aimentor_get_allowed_provider_models();
        $now             = current_time( 'timestamp' );
        $separator       = _x( ' • ', 'separator between task and tier', 'aimentor' );
        $origin_labels   = [
                'generation' => __( 'Generation', 'aimentor' ),
                'test'       => __( 'Connection test', 'aimentor' ),
        ];

        $generated_at = isset( $data['generated_at'] ) ? absint( $data['generated_at'] ) : 0;

        $summary = [
                'generated_at'        => $generated_at ? $generated_at : $now,
                'generated_at_human'  => $generated_at ? sprintf( __( '%s ago', 'aimentor' ), human_time_diff( $generated_at, $now ) ) : __( 'Just now', 'aimentor' ),
                'providers'           => [],
        ];

        foreach ( $labels as $provider_key => $label ) {
                $provider_data = $data['providers'][ $provider_key ] ?? [];
                $success_total = absint( $provider_data['success_count'] ?? 0 );
                $error_total   = absint( $provider_data['error_count'] ?? 0 );
                $last_event    = absint( $provider_data['last_event'] ?? 0 );
                $last_success  = absint( $provider_data['last_success'] ?? 0 );
                $last_error    = absint( $provider_data['last_error'] ?? 0 );
                $last_model    = isset( $provider_data['last_model'] ) ? sanitize_text_field( $provider_data['last_model'] ) : '';
                $last_task     = isset( $provider_data['last_task'] ) ? sanitize_key( $provider_data['last_task'] ) : '';
                $last_tier     = isset( $provider_data['last_tier'] ) ? sanitize_key( $provider_data['last_tier'] ) : '';
                $last_origin   = isset( $provider_data['last_origin'] ) ? sanitize_key( $provider_data['last_origin'] ) : '';

                $model_label = '';

                if ( $last_model && isset( $allowed_models[ $provider_key ][ $last_model ] ) ) {
                        $model_label = $allowed_models[ $provider_key ][ $last_model ];
                } elseif ( $last_model ) {
                        $model_label = strtoupper( $last_model );
                }

                $task_label = '';

                if ( 'canvas' === $last_task ) {
                        $task_label = __( 'Canvas', 'aimentor' );
                } elseif ( 'content' === $last_task ) {
                        $task_label = __( 'Content', 'aimentor' );
                }

                $tier_label = '';

                if ( 'quality' === $last_tier ) {
                        $tier_label = __( 'Quality', 'aimentor' );
                } elseif ( 'fast' === $last_tier ) {
                        $tier_label = __( 'Fast', 'aimentor' );
                }

                $context_parts = array_filter( [ $task_label, $tier_label ] );
                $context_text  = '';

                if ( ! empty( $context_parts ) ) {
                        $context_text = implode( $separator, $context_parts );
                }

                if ( $model_label ) {
                        $context_text = $context_text
                                ? sprintf( __( '%1$s via %2$s', 'aimentor' ), $context_text, $model_label )
                                : sprintf( __( 'via %s', 'aimentor' ), $model_label );
                }

                $summary['providers'][ $provider_key ] = [
                        'label'              => $label,
                        'success_total'      => $success_total,
                        'error_total'        => $error_total,
                        'total_requests'     => $success_total + $error_total,
                        'last_event'         => $last_event,
                        'last_event_human'   => $last_event ? sprintf( __( '%s ago', 'aimentor' ), human_time_diff( $last_event, $now ) ) : __( 'No activity yet', 'aimentor' ),
                        'last_success'       => $last_success,
                        'last_success_human' => $last_success ? sprintf( __( '%s ago', 'aimentor' ), human_time_diff( $last_success, $now ) ) : __( 'Never', 'aimentor' ),
                        'last_error'         => $last_error,
                        'last_error_human'   => $last_error ? sprintf( __( '%s ago', 'aimentor' ), human_time_diff( $last_error, $now ) ) : __( 'Never', 'aimentor' ),
                        'context_summary'    => $context_text,
                        'last_origin'        => $last_origin,
                        'origin_label'       => $origin_labels[ $last_origin ] ?? '',
                ];
        }

        return $summary;
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
        $provider_defaults = aimentor_get_provider_model_defaults();
        $legacy_defaults   = aimentor_map_presets_to_legacy_defaults( $provider_defaults );

        return [
                'aimentor_provider'                  => 'grok',
                'aimentor_xai_api_key'               => '',
                'aimentor_openai_api_key'            => '',
                'aimentor_auto_insert'               => 'yes',
                'aimentor_theme_style'               => 'modern',
                'aimentor_max_tokens'                => 2000,
                'aimentor_provider_models'           => $legacy_defaults,
                'aimentor_model_presets'             => $provider_defaults,
                'aimentor_model'                     => $legacy_defaults['grok'] ?? '',
                'aimentor_openai_model'              => $legacy_defaults['openai'] ?? '',
                'aimentor_default_generation_type'   => 'content',
                'aimentor_default_performance'       => 'fast',
                'aimentor_api_tested'                => false,
                'aimentor_onboarding_dismissed'      => 'no',
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
                'AiMentor Elementor Settings',
                'AiMentor Elementor',
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

        aimentor_seed_default_options();
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
        $value   = sanitize_key( $value );
        $allowed = [ 'content', 'canvas' ];

        return in_array( $value, $allowed, true ) ? $value : 'content';
}

function aimentor_sanitize_performance_tier( $value ) {
        $value   = sanitize_key( $value );
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

        if ( ! is_array( $value ) ) {
                $value = [];
        }

        $sanitized = [];

        foreach ( $defaults as $provider => $tasks ) {
                $sanitized[ $provider ] = [];
                $allowed_models          = aimentor_flatten_allowed_models_for_provider( $provider );

                foreach ( $tasks as $task => $tiers ) {
                        $sanitized[ $provider ][ $task ] = [];

                        foreach ( $tiers as $tier => $default_model ) {
                                $incoming = isset( $value[ $provider ][ $task ][ $tier ] )
                                        ? sanitize_text_field( $value[ $provider ][ $task ][ $tier ] )
                                        : '';

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

function aimentor_get_model_presets() {
        $stored = get_option( 'aimentor_model_presets', [] );

        if ( ! is_array( $stored ) ) {
                $stored = [];
        }

        $presets = array_replace_recursive( aimentor_get_provider_model_defaults(), $stored );

        return aimentor_sanitize_model_presets( $presets );
}

function aimentor_get_provider_models() {
        $stored = get_option( 'aimentor_provider_models', [] );

        if ( ! is_array( $stored ) ) {
                $stored = [];
        }

        return aimentor_sanitize_provider_models( $stored );
}

function aimentor_sanitize_model( $value ) {
        $value     = sanitize_text_field( $value );
        $allowed   = aimentor_flatten_allowed_models_for_provider( 'grok' );
        $defaults  = aimentor_map_presets_to_legacy_defaults( aimentor_get_provider_model_defaults() );
        $fallback  = $defaults['grok'] ?? '';

        return in_array( $value, $allowed, true ) ? $value : $fallback;
}

function aimentor_sanitize_openai_model( $value ) {
        $value     = sanitize_text_field( $value );
        $allowed   = aimentor_flatten_allowed_models_for_provider( 'openai' );
        $defaults  = aimentor_map_presets_to_legacy_defaults( aimentor_get_provider_model_defaults() );
        $fallback  = $defaults['openai'] ?? '';

        return in_array( $value, $allowed, true ) ? $value : $fallback;
}

function aimentor_sync_legacy_model_options( $value, $old_value ) {
        $sanitized = aimentor_sanitize_provider_models( $value );
        $presets   = aimentor_get_provider_model_defaults();

        foreach ( $sanitized as $provider => $model ) {
                if ( '' === $model || ! isset( $presets[ $provider ] ) ) {
                        continue;
                }

                foreach ( $presets[ $provider ] as $task => $tiers ) {
                        if ( ! is_array( $tiers ) ) {
                                continue;
                        }

                        if ( isset( $presets[ $provider ][ $task ]['fast'] ) ) {
                                $presets[ $provider ][ $task ]['fast'] = $model;
                        }
                }
        }

        $sanitized_presets = aimentor_sanitize_model_presets( $presets );
        update_option( 'aimentor_model_presets', $sanitized_presets );

        if ( isset( $sanitized['grok'] ) ) {
                update_option( 'aimentor_model', aimentor_sanitize_model( $sanitized['grok'] ) );
        }

        if ( isset( $sanitized['openai'] ) ) {
                update_option( 'aimentor_openai_model', aimentor_sanitize_openai_model( $sanitized['openai'] ) );
        }

        return $sanitized;
}
add_filter( 'pre_update_option_aimentor_provider_models', 'aimentor_sync_legacy_model_options', 10, 2 );

function aimentor_sanitize_provider( $value ) {
        $allowed = [ 'grok', 'openai' ];
        return in_array( $value, $allowed, true ) ? $value : 'grok';
}

function aimentor_parse_error_log_entry( $log_line ) {
        $log_line = trim( (string) $log_line );

        if ( '' === $log_line ) {
                return null;
        }

        $parts     = explode( ' - ', $log_line, 2 );
        $timestamp = isset( $parts[0] ) ? trim( (string) $parts[0] ) : __( 'Unknown', 'aimentor' );
        $raw_entry = isset( $parts[1] ) ? trim( (string) $parts[1] ) : '';
        $provider  = '';
        $message   = '' !== $raw_entry ? $raw_entry : $log_line;

        if ( '' !== $raw_entry ) {
                $decoded = json_decode( $raw_entry, true );

                if ( is_array( $decoded ) && isset( $decoded['message'] ) ) {
                        $message = (string) $decoded['message'];

                        if ( isset( $decoded['context']['provider'] ) ) {
                                $provider = (string) $decoded['context']['provider'];
                        }
                }
        }

        return [
                'timestamp' => '' !== $timestamp ? $timestamp : __( 'Unknown', 'aimentor' ),
                'provider'  => $provider,
                'message'   => $message,
        ];
}

function aimentor_get_error_log_entries( $args = [] ) {
        $defaults = [
                'provider' => '',
                'keyword'  => '',
                'limit'    => 10,
        ];

        $args = wp_parse_args( $args, $defaults );

        $provider_filter = sanitize_key( $args['provider'] );
        $keyword_filter  = sanitize_text_field( $args['keyword'] );
        $limit           = intval( $args['limit'] );

        if ( function_exists( 'aimentor_get_error_log_path' ) ) {
                $log_file = aimentor_get_error_log_path();
        } else {
                $log_file = plugin_dir_path( __FILE__ ) . 'aimentor-errors.log';
        }

        $is_readable = is_readable( $log_file );

        if ( ! $is_readable ) {
                return [
                        'entries'       => [],
                        'total_entries' => 0,
                        'log_file'      => $log_file,
                        'readable'      => false,
                ];
        }

        $lines = file( $log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );

        if ( ! is_array( $lines ) || empty( $lines ) ) {
                return [
                        'entries'       => [],
                        'total_entries' => 0,
                        'log_file'      => $log_file,
                        'readable'      => true,
                ];
        }

        $entries       = [];
        $total_entries = 0;
        $keyword_match = function_exists( 'mb_stripos' ) ? 'mb_stripos' : 'stripos';

        foreach ( array_reverse( $lines ) as $line ) {
                $entry = aimentor_parse_error_log_entry( $line );

                if ( ! $entry ) {
                        continue;
                }

                ++$total_entries;

                if ( '' !== $provider_filter ) {
                        $entry_provider = sanitize_key( $entry['provider'] );

                        if ( '' === $entry_provider || $provider_filter !== $entry_provider ) {
                                continue;
                        }
                }

                if ( '' !== $keyword_filter ) {
                        $haystack = implode( ' ', array_filter( [ $entry['timestamp'], $entry['provider'], $entry['message'] ] ) );

                        if ( false === $keyword_match( $haystack, $keyword_filter ) ) {
                                continue;
                        }
                }

                $entries[] = $entry;

                if ( $limit > 0 && count( $entries ) >= $limit ) {
                        break;
                }
        }

        return [
                'entries'       => $entries,
                'total_entries' => $total_entries,
                'log_file'      => $log_file,
                'readable'      => true,
        ];
}

function aimentor_build_error_log_rows_html( $entries, $context = [] ) {
        $context = wp_parse_args(
                $context,
                [
                        'readable'      => true,
                        'had_filters'   => false,
                        'total_entries' => 0,
                ]
        );

        if ( ! $context['readable'] ) {
                return '<tr><td colspan="3">' . esc_html__( 'No errors logged yet or log file unavailable.', 'aimentor' ) . '</td></tr>';
        }

        if ( empty( $entries ) ) {
                if ( $context['had_filters'] && $context['total_entries'] > 0 ) {
                        return '<tr><td colspan="3">' . esc_html__( 'No log entries match your filters.', 'aimentor' ) . '</td></tr>';
                }

                return '<tr><td colspan="3">' . esc_html__( 'No errors logged yet or log file unavailable.', 'aimentor' ) . '</td></tr>';
        }

        $rows = '';

        foreach ( $entries as $entry ) {
                $rows .= '<tr>';
                $rows .= '<td>' . esc_html( $entry['timestamp'] ) . '</td>';
                $rows .= '<td>' . ( '' !== $entry['provider'] ? esc_html( $entry['provider'] ) : '&mdash;' ) . '</td>';
                $rows .= '<td>' . esc_html( $entry['message'] ) . '</td>';
                $rows .= '</tr>';
        }

        return $rows;
}

function aimentor_get_error_logs_ajax() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

        if ( ! wp_verify_nonce( $nonce, 'aimentor_error_log' ) && ! wp_verify_nonce( $nonce, 'jaggrok_error_log' ) ) {
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
                                'message' => __( 'Insufficient permissions to view the error log.', 'aimentor' ),
                                'code'    => 'aimentor_insufficient_permissions',
                        ],
                        403
                );
        }

        $provider = isset( $_POST['provider'] ) ? sanitize_key( wp_unslash( $_POST['provider'] ) ) : '';
        $keyword  = isset( $_POST['keyword'] ) ? sanitize_text_field( wp_unslash( $_POST['keyword'] ) ) : '';

        $entries = aimentor_get_error_log_entries(
                [
                        'provider' => $provider,
                        'keyword'  => $keyword,
                ]
        );

        $rows = aimentor_build_error_log_rows_html(
                $entries['entries'],
                [
                        'readable'      => $entries['readable'],
                        'had_filters'   => ( '' !== $provider || '' !== $keyword ),
                        'total_entries' => $entries['total_entries'],
                ]
        );

        wp_send_json_success(
                [
                        'rows'  => $rows,
                        'nonce' => wp_create_nonce( 'aimentor_error_log' ),
                ]
        );
}
add_action( 'wp_ajax_aimentor_get_error_logs', 'aimentor_get_error_logs_ajax' );
add_action( 'wp_ajax_jaggrok_get_error_logs', 'aimentor_get_error_logs_ajax' );

function aimentor_settings_page_callback() {
        $aimentor_usage_metrics = aimentor_get_provider_usage_summary();
        include plugin_dir_path( __FILE__ ) . 'settings-template.php';
}

function aimentor_dismiss_onboarding_notice() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

        if ( ! wp_verify_nonce( $nonce, 'aimentor_onboarding' ) && ! wp_verify_nonce( $nonce, 'jaggrok_onboarding' ) ) {
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
                                'message' => __( 'Insufficient permissions to update onboarding state.', 'aimentor' ),
                                'code'    => 'aimentor_insufficient_permissions',
                        ],
                        403
                );
        }

        update_option( 'aimentor_onboarding_dismissed', 'yes' );

        wp_send_json_success();
}
add_action( 'wp_ajax_aimentor_dismiss_onboarding', 'aimentor_dismiss_onboarding_notice' );
add_action( 'wp_ajax_jaggrok_dismiss_onboarding', 'aimentor_dismiss_onboarding_notice' );

function aimentor_get_usage_metrics_ajax() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

        if ( ! wp_verify_nonce( $nonce, 'aimentor_usage_metrics' ) && ! wp_verify_nonce( $nonce, 'jaggrok_usage_metrics' ) ) {
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
                                'message' => __( 'Insufficient permissions to view usage metrics.', 'aimentor' ),
                                'code'    => 'aimentor_insufficient_permissions',
                        ],
                        403
                );
        }

        wp_send_json_success(
                [
                        'metrics' => aimentor_get_provider_usage_summary(),
                ]
        );
}
add_action( 'wp_ajax_aimentor_get_usage_metrics', 'aimentor_get_usage_metrics_ajax' );
add_action( 'wp_ajax_jaggrok_get_usage_metrics', 'aimentor_get_usage_metrics_ajax' );

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

	$models        = aimentor_get_provider_models();
	$model_default = aimentor_get_provider_model_defaults();
	$model         = $models[ $provider_key ] ?? ( $model_default[ $provider_key ] ?? '' );

	switch ( $provider_key ) {
		case 'openai':
			update_option( 'aimentor_openai_api_key', $api_key );
			$model = $models['openai'] ?? ( $model_default['openai'] ?? '' );
			break;
		case 'grok':
		default:
			update_option( 'aimentor_xai_api_key', $api_key );
			$model = $models['grok'] ?? ( $model_default['grok'] ?? '' );
			break;
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
                aimentor_record_provider_usage(
                        $provider_key,
                        'error',
                        [
                                'model'  => $model,
                                'origin' => 'test',
                        ]
                );

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
                aimentor_record_provider_usage(
                        $provider_key,
                        'error',
                        [
                                'model'  => $model,
                                'origin' => 'test',
                        ]
                );

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

        aimentor_record_provider_usage(
                $provider_key,
                'success',
                [
                        'model'  => $model,
                        'origin' => 'test',
                ]
        );

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
        if ( function_exists( 'aimentor_get_error_log_path' ) ) {
                $log_file = aimentor_get_error_log_path();
        } else {
                $log_file = plugin_dir_path( __FILE__ ) . 'aimentor-errors.log';
        }

        $log_dir   = dirname( $log_file );
        $timestamp = gmdate( 'Y-m-d H:i:s' );
        $log_entry = $message;

        if ( ! is_dir( $log_dir ) ) {
                if ( function_exists( 'wp_mkdir_p' ) ) {
                        wp_mkdir_p( $log_dir );
                } else {
                        @mkdir( $log_dir, 0755, true );
                }

                if ( ! is_dir( $log_dir ) ) {
                        return;
                }
        }

        if ( ! is_writable( $log_dir ) ) {
                return;
        }

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
        'aimentor_model_presets',
        'aimentor_openai_model',
        'aimentor_provider_models',
        'aimentor_api_tested',
        'aimentor_provider_test_statuses',
        'aimentor_onboarding_dismissed',
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
