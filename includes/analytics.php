<?php
/**
 * AiMentor analytics helpers.
 *
 * @package AiMentor\Analytics
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function aimentor_get_analytics_option_name() {
    return 'aimentor_usage_analytics';
}

function aimentor_get_usage_guardrail_option_name() {
    return 'aimentor_usage_guardrails';
}

function aimentor_get_analytics_defaults() {
    return [
        'events'       => [],
        'generated_at' => current_time( 'timestamp' ),
    ];
}

function aimentor_get_usage_guardrail_defaults() {
    $defaults = [
        'daily_limit'      => 200,
        'hourly_limit'     => 50,
        'user_daily_limit' => 50,
        'warning_ratio'    => 0.8,
    ];

    return apply_filters( 'aimentor_usage_guardrail_defaults', $defaults );
}

function aimentor_get_analytics_cache_version() {
    static $version = null;

    if ( null === $version ) {
        $version = absint( get_option( 'aimentor_analytics_cache_version', 1 ) );

        if ( $version < 1 ) {
            $version = 1;
        }
    }

    return $version;
}

function aimentor_invalidate_analytics_cache() {
    $version = aimentor_get_analytics_cache_version() + 1;
    update_option( 'aimentor_analytics_cache_version', $version, false );
}

function aimentor_get_analytics_cache_key( $args = [] ) {
    $version = aimentor_get_analytics_cache_version();
    $hash    = md5( wp_json_encode( $args ) );

    return 'aimentor_analytics_' . $version . '_' . $hash;
}

function aimentor_sanitize_rate_limit_context( $payload ) {
    if ( ! is_array( $payload ) ) {
        return [];
    }

    $sanitized = [];

    foreach ( $payload as $key => $value ) {
        $normalized_key = sanitize_key( (string) $key );

        if ( '' === $normalized_key ) {
            continue;
        }

        if ( is_array( $value ) ) {
            $value = reset( $value );
        }

        if ( is_numeric( $value ) ) {
            $sanitized[ $normalized_key ] = 0 + $value;
            continue;
        }

        if ( is_scalar( $value ) ) {
            $sanitized[ $normalized_key ] = sanitize_text_field( (string) $value );
        }
    }

    return $sanitized;
}

function aimentor_sanitize_usage_event( $event ) {
    if ( ! is_array( $event ) ) {
        return null;
    }

    $timestamp = isset( $event['timestamp'] ) ? absint( $event['timestamp'] ) : current_time( 'timestamp' );

    if ( $timestamp <= 0 ) {
        $timestamp = current_time( 'timestamp' );
    }

    $type    = isset( $event['type'] ) ? sanitize_key( $event['type'] ) : 'generation';
    $status  = isset( $event['status'] ) ? sanitize_key( $event['status'] ) : 'success';
    $channel = isset( $event['channel'] ) ? sanitize_key( $event['channel'] ) : 'rest';

    if ( ! in_array( $status, [ 'success', 'error', 'blocked' ], true ) ) {
        $status = 'success';
    }

    $provider = isset( $event['provider'] ) ? sanitize_key( $event['provider'] ) : '';
    $task     = isset( $event['task'] ) ? sanitize_key( $event['task'] ) : '';
    $tier     = isset( $event['tier'] ) ? sanitize_key( $event['tier'] ) : '';
    $model    = isset( $event['model'] ) ? sanitize_text_field( $event['model'] ) : '';
    $origin   = isset( $event['origin'] ) ? sanitize_key( $event['origin'] ) : '';
    $user_id  = isset( $event['user_id'] ) ? absint( $event['user_id'] ) : 0;
    $tokens   = isset( $event['tokens'] ) ? absint( $event['tokens'] ) : 0;

    $rate_limit = isset( $event['rate_limit'] ) ? aimentor_sanitize_rate_limit_context( $event['rate_limit'] ) : [];

    return [
        'timestamp' => $timestamp,
        'type'      => $type ? $type : 'generation',
        'status'    => $status,
        'channel'   => $channel,
        'provider'  => $provider,
        'task'      => $task,
        'tier'      => $tier,
        'model'     => $model,
        'origin'    => $origin,
        'user_id'   => $user_id,
        'tokens'    => $tokens,
        'rate_limit' => $rate_limit,
    ];
}

function aimentor_get_usage_events() {
    $option = get_option( aimentor_get_analytics_option_name(), [] );

    if ( ! is_array( $option ) ) {
        $option = [];
    }

    $events    = isset( $option['events'] ) && is_array( $option['events'] ) ? $option['events'] : [];
    $sanitized = [];

    foreach ( $events as $event ) {
        $clean = aimentor_sanitize_usage_event( $event );

        if ( ! $clean ) {
            continue;
        }

        $sanitized[] = $clean;
    }

    if ( ! empty( $sanitized ) ) {
        usort(
            $sanitized,
            static function( $a, $b ) {
                if ( $a['timestamp'] === $b['timestamp'] ) {
                    return 0;
                }

                return ( $a['timestamp'] < $b['timestamp'] ) ? -1 : 1;
            }
        );
    }

    return $sanitized;
}

function aimentor_get_analytics_retention_window() {
    $retention_days = apply_filters( 'aimentor_analytics_retention_days', 30 );
    $retention_days = absint( $retention_days );

    if ( $retention_days < 1 ) {
        $retention_days = 1;
    }

    return DAY_IN_SECONDS * $retention_days;
}

function aimentor_trim_usage_events( $events ) {
    if ( ! is_array( $events ) ) {
        return [];
    }

    $now              = current_time( 'timestamp' );
    $retention_window = aimentor_get_analytics_retention_window();
    $min_timestamp    = $now - $retention_window;
    $max_events       = apply_filters( 'aimentor_analytics_max_events', 500 );
    $max_events       = absint( $max_events );

    if ( $max_events < 1 ) {
        $max_events = 500;
    }

    $trimmed = [];

    foreach ( $events as $event ) {
        if ( ! isset( $event['timestamp'] ) ) {
            continue;
        }

        if ( $event['timestamp'] < $min_timestamp ) {
            continue;
        }

        $trimmed[] = $event;
    }

    if ( count( $trimmed ) > $max_events ) {
        $trimmed = array_slice( $trimmed, -1 * $max_events );
    }

    return $trimmed;
}

function aimentor_record_usage_event( $event ) {
    $clean = aimentor_sanitize_usage_event( $event );

    if ( ! $clean ) {
        return;
    }

    $events   = aimentor_get_usage_events();
    $events[] = $clean;

    $events = aimentor_trim_usage_events( $events );

    $data = [
        'events'       => array_values( $events ),
        'generated_at' => current_time( 'timestamp' ),
    ];

    update_option( aimentor_get_analytics_option_name(), $data, false );
    aimentor_invalidate_analytics_cache();
}

function aimentor_get_usage_guardrail_settings() {
    $stored   = get_option( aimentor_get_usage_guardrail_option_name(), [] );
    $defaults = aimentor_get_usage_guardrail_defaults();

    if ( ! is_array( $stored ) ) {
        $stored = [];
    }

    $settings = array_merge( $defaults, $stored );

    $settings['daily_limit']      = max( 0, absint( $settings['daily_limit'] ) );
    $settings['hourly_limit']     = max( 0, absint( $settings['hourly_limit'] ) );
    $settings['user_daily_limit'] = max( 0, absint( $settings['user_daily_limit'] ) );
    $settings['warning_ratio']    = isset( $settings['warning_ratio'] ) ? floatval( $settings['warning_ratio'] ) : 0.8;

    if ( $settings['warning_ratio'] <= 0 || $settings['warning_ratio'] > 1 ) {
        $settings['warning_ratio'] = 0.8;
    }

    return apply_filters( 'aimentor_usage_guardrail_settings', $settings );
}

function aimentor_update_usage_guardrail_settings( $settings ) {
    $defaults = aimentor_get_usage_guardrail_defaults();
    $payload  = array_merge( $defaults, $settings );

    $payload['daily_limit']      = max( 0, absint( $payload['daily_limit'] ) );
    $payload['hourly_limit']     = max( 0, absint( $payload['hourly_limit'] ) );
    $payload['user_daily_limit'] = max( 0, absint( $payload['user_daily_limit'] ) );
    $payload['warning_ratio']    = isset( $payload['warning_ratio'] ) ? floatval( $payload['warning_ratio'] ) : 0.8;

    if ( $payload['warning_ratio'] <= 0 || $payload['warning_ratio'] > 1 ) {
        $payload['warning_ratio'] = 0.8;
    }

    update_option( aimentor_get_usage_guardrail_option_name(), $payload, false );
    aimentor_invalidate_analytics_cache();
}

function aimentor_get_guardrail_label( $key ) {
    switch ( $key ) {
        case 'hourly':
            return __( 'hourly', 'aimentor' );
        case 'user_daily':
            return __( 'per-user daily', 'aimentor' );
        case 'daily':
        default:
            return __( 'daily', 'aimentor' );
    }
}

function aimentor_enforce_usage_guardrails( $context = [] ) {
    $settings = aimentor_get_usage_guardrail_settings();

    $limits = [
        'daily'      => [
            'limit'  => $settings['daily_limit'],
            'window' => DAY_IN_SECONDS,
        ],
        'hourly'     => [
            'limit'  => $settings['hourly_limit'],
            'window' => HOUR_IN_SECONDS,
        ],
        'user_daily' => [
            'limit'  => $settings['user_daily_limit'],
            'window' => DAY_IN_SECONDS,
            'scope'  => 'user',
        ],
    ];

    $type    = isset( $context['type'] ) ? sanitize_key( $context['type'] ) : 'generation';
    $user_id = isset( $context['user_id'] ) ? absint( $context['user_id'] ) : get_current_user_id();

    $events = aimentor_get_usage_events();

    if ( empty( $events ) ) {
        return [
            'allowed'  => true,
            'warnings' => [],
            'state'    => [],
        ];
    }

    $now         = current_time( 'timestamp' );
    $max_window  = max( HOUR_IN_SECONDS, DAY_IN_SECONDS );
    $cutoff      = $now - $max_window;
    $warning_base = isset( $settings['warning_ratio'] ) ? floatval( $settings['warning_ratio'] ) : 0.8;

    if ( $warning_base <= 0 || $warning_base > 1 ) {
        $warning_base = 0.8;
    }

    $counters = [
        'daily'      => 0,
        'hourly'     => 0,
        'user_daily' => 0,
    ];

    foreach ( array_reverse( $events ) as $event ) {
        if ( $event['timestamp'] < $cutoff ) {
            break;
        }

        if ( $event['type'] !== $type ) {
            continue;
        }

        if ( 'success' !== $event['status'] ) {
            continue;
        }

        if ( ( $now - $event['timestamp'] ) <= DAY_IN_SECONDS ) {
            ++$counters['daily'];

            if ( $user_id && $event['user_id'] && $event['user_id'] === $user_id ) {
                ++$counters['user_daily'];
            }
        }

        if ( ( $now - $event['timestamp'] ) <= HOUR_IN_SECONDS ) {
            ++$counters['hourly'];
        }
    }

    $warnings = [];
    $state    = [];

    foreach ( $limits as $key => $limit ) {
        $limit_value = isset( $limit['limit'] ) ? absint( $limit['limit'] ) : 0;

        if ( $limit_value < 1 ) {
            $state[ $key ] = [
                'limit'     => 0,
                'count'     => 0,
                'threshold' => 0,
            ];
            continue;
        }

        $count     = isset( $counters[ $key ] ) ? absint( $counters[ $key ] ) : 0;
        $threshold = (int) ceil( $limit_value * $warning_base );
        $threshold = $threshold > $limit_value ? $limit_value : $threshold;

        $state_data = [
            'limit'     => $limit_value,
            'count'     => $count,
            'threshold' => $threshold,
        ];

        if ( $count >= $limit_value ) {
            $label   = aimentor_get_guardrail_label( $key );
            $message = sprintf(
                /* translators: 1: Guardrail label, 2: request count, 3: configured limit. */
                __( 'The %1$s limit has been reached (%2$d of %3$d requests).', 'aimentor' ),
                $label,
                $count,
                $limit_value
            );

            $state_data['status'] = 'blocked';
            $state[ $key ]        = $state_data;

            return new WP_Error(
                'aimentor_guardrail_exceeded',
                $message,
                [
                    'limit_key' => $key,
                    'state'     => $state,
                ]
            );
        }

        if ( $threshold > 0 && $count >= $threshold ) {
            $label      = aimentor_get_guardrail_label( $key );
            $warnings[] = sprintf(
                /* translators: 1: Guardrail label, 2: request count, 3: configured limit. */
                __( 'You are approaching the %1$s limit (%2$d of %3$d requests).', 'aimentor' ),
                $label,
                $count,
                $limit_value
            );
        }

        $state[ $key ] = $state_data;
    }

    return [
        'allowed'  => true,
        'warnings' => $warnings,
        'state'    => $state,
    ];
}

function aimentor_get_usage_analytics( $args = [] ) {
    $args = wp_parse_args(
        $args,
        [
            'interval'       => apply_filters( 'aimentor_analytics_default_interval', 'day' ),
            'range'          => 7,
            'include_errors' => true,
            'type'           => 'generation',
        ]
    );

    $allowed_intervals = apply_filters( 'aimentor_analytics_allowed_intervals', [ 'hour', 'day', 'week' ] );

    $interval = sanitize_key( $args['interval'] );

    if ( ! in_array( $interval, $allowed_intervals, true ) ) {
        $interval = apply_filters( 'aimentor_analytics_default_interval', 'day' );
    }

    $range = absint( $args['range'] );

    if ( $range < 1 ) {
        $range = 7;
    }

    $cache_key = aimentor_get_analytics_cache_key(
        [
            'interval' => $interval,
            'range'    => $range,
            'errors'   => $args['include_errors'] ? '1' : '0',
            'type'     => $args['type'],
        ]
    );

    $ttl    = apply_filters( 'aimentor_analytics_cache_ttl', 5 * MINUTE_IN_SECONDS, $args );
    $cached = get_transient( $cache_key );

    if ( false !== $cached ) {
        return $cached;
    }

    $events = aimentor_get_usage_events();

    if ( empty( $events ) ) {
        $result = [
            'interval'     => $interval,
            'range'        => $range,
            'generated_at' => current_time( 'timestamp' ),
            'groups'       => [],
            'totals'       => [
                'success'   => 0,
                'errors'    => 0,
                'tokens'    => 0,
                'providers' => [],
            ],
            'guardrails'   => aimentor_get_usage_guardrail_settings(),
        ];

        set_transient( $cache_key, $result, $ttl );

        return $result;
    }

    $interval_config = [
        'hour' => [
            'seconds' => HOUR_IN_SECONDS,
            'format'  => 'M j H:00',
        ],
        'day'  => [
            'seconds' => DAY_IN_SECONDS,
            'format'  => 'M j',
        ],
        'week' => [
            'seconds' => WEEK_IN_SECONDS,
            'format'  => 'M j',
        ],
    ];

    $interval_config = apply_filters( 'aimentor_analytics_interval_config', $interval_config, $interval, $args );

    if ( ! isset( $interval_config[ $interval ] ) ) {
        $interval = apply_filters( 'aimentor_analytics_default_interval', 'day' );
    }

    $config         = isset( $interval_config[ $interval ] ) ? $interval_config[ $interval ] : $interval_config['day'];
    $bucket_seconds = isset( $config['seconds'] ) ? absint( $config['seconds'] ) : DAY_IN_SECONDS;

    if ( $bucket_seconds < 1 ) {
        $bucket_seconds = DAY_IN_SECONDS;
    }

    $bucket_seconds = apply_filters( 'aimentor_analytics_bucket_seconds', $bucket_seconds, $interval, $args, $config );

    if ( $bucket_seconds < 1 ) {
        $bucket_seconds = DAY_IN_SECONDS;
    }

    $date_format = isset( $config['format'] ) ? $config['format'] : 'M j';
    $date_format = apply_filters( 'aimentor_analytics_date_format', $date_format, $interval, $args, $config );

    $now            = current_time( 'timestamp' );
    $start_boundary = $now - ( $bucket_seconds * ( $range - 1 ) );
    $start_boundary = apply_filters( 'aimentor_analytics_start_boundary', $start_boundary, $interval, $range, $bucket_seconds, $args );

    if ( ! is_numeric( $start_boundary ) ) {
        $start_boundary = $now - ( $bucket_seconds * ( $range - 1 ) );
    }

    $start_boundary = intval( $start_boundary );
    $groups         = [];
    $totals         = [
        'success'   => 0,
        'errors'    => 0,
        'tokens'    => 0,
        'providers' => [],
    ];

    foreach ( $events as $event ) {
        if ( $event['timestamp'] < $start_boundary ) {
            continue;
        }

        if ( $event['type'] !== $args['type'] ) {
            continue;
        }

        $bucket_start = intval( floor( $event['timestamp'] / $bucket_seconds ) * $bucket_seconds );
        $bucket_start = apply_filters( 'aimentor_analytics_bucket_start', $bucket_start, $event, $bucket_seconds, $interval, $args );

        if ( ! is_numeric( $bucket_start ) ) {
            $bucket_start = floor( $event['timestamp'] / $bucket_seconds ) * $bucket_seconds;
        }

        $bucket_start = intval( $bucket_start );

        if ( ! isset( $groups[ $bucket_start ] ) ) {
            $groups[ $bucket_start ] = [
                'period_start' => $bucket_start,
                'label'        => date_i18n( $date_format, $bucket_start ),
                'success'      => 0,
                'errors'       => 0,
                'tokens'       => 0,
                'providers'    => [],
            ];
        }

        if ( 'success' === $event['status'] ) {
            ++$groups[ $bucket_start ]['success'];
            ++$totals['success'];
        } elseif ( $args['include_errors'] ) {
            ++$groups[ $bucket_start ]['errors'];
            ++$totals['errors'];
        }

        if ( $event['tokens'] > 0 ) {
            $groups[ $bucket_start ]['tokens'] += $event['tokens'];
            $totals['tokens']                  += $event['tokens'];
        }

        $provider = $event['provider'] ? $event['provider'] : 'unknown';

        if ( ! isset( $groups[ $bucket_start ]['providers'][ $provider ] ) ) {
            $groups[ $bucket_start ]['providers'][ $provider ] = 0;
        }

        if ( ! isset( $totals['providers'][ $provider ] ) ) {
            $totals['providers'][ $provider ] = 0;
        }

        ++$groups[ $bucket_start ]['providers'][ $provider ];
        ++$totals['providers'][ $provider ];
    }

    ksort( $groups );

    $result = [
        'interval'     => $interval,
        'range'        => $range,
        'generated_at' => current_time( 'timestamp' ),
        'groups'       => array_values( $groups ),
        'totals'       => $totals,
        'guardrails'   => aimentor_get_usage_guardrail_settings(),
    ];

    set_transient( $cache_key, $result, $ttl );

    return $result;
}

function aimentor_rest_get_analytics_summary( WP_REST_Request $request ) {
    if ( ! current_user_can( 'manage_options' ) ) {
        return new WP_Error( 'aimentor_forbidden', __( 'You do not have permission to view analytics.', 'aimentor' ), [ 'status' => 403 ] );
    }

    $interval = $request->get_param( 'interval' );
    $range    = $request->get_param( 'range' );

    $data = aimentor_get_usage_analytics(
        [
            'interval' => $interval,
            'range'    => $range,
        ]
    );

    return new WP_REST_Response( $data, 200 );
}

function aimentor_rest_get_guardrails( WP_REST_Request $request ) {
    if ( ! current_user_can( 'manage_options' ) ) {
        return new WP_Error( 'aimentor_forbidden', __( 'You do not have permission to view guardrails.', 'aimentor' ), [ 'status' => 403 ] );
    }

    return new WP_REST_Response(
        [
            'guardrails' => aimentor_get_usage_guardrail_settings(),
        ],
        200
    );
}

function aimentor_rest_update_guardrails( WP_REST_Request $request ) {
    if ( ! current_user_can( 'manage_options' ) ) {
        return new WP_Error( 'aimentor_forbidden', __( 'You do not have permission to update guardrails.', 'aimentor' ), [ 'status' => 403 ] );
    }

    $parameters = $request->get_json_params();

    if ( ! is_array( $parameters ) ) {
        $parameters = [];
    }

    $payload = [
        'daily_limit'      => isset( $parameters['daily_limit'] ) ? $parameters['daily_limit'] : null,
        'hourly_limit'     => isset( $parameters['hourly_limit'] ) ? $parameters['hourly_limit'] : null,
        'user_daily_limit' => isset( $parameters['user_daily_limit'] ) ? $parameters['user_daily_limit'] : null,
        'warning_ratio'    => isset( $parameters['warning_ratio'] ) ? $parameters['warning_ratio'] : null,
    ];

    aimentor_update_usage_guardrail_settings( $payload );

    return new WP_REST_Response(
        [
            'guardrails' => aimentor_get_usage_guardrail_settings(),
        ],
        200
    );
}

function aimentor_register_analytics_routes() {
    register_rest_route(
        'aimentor/v1',
        '/analytics/summary',
        [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'aimentor_rest_get_analytics_summary',
                'permission_callback' => '__return_true',
                'args'                => [
                    'interval' => [
                        'type'              => 'string',
                        'required'          => false,
                        'sanitize_callback' => 'sanitize_key',
                    ],
                    'range'    => [
                        'type'              => 'integer',
                        'required'          => false,
                        'validate_callback' => 'is_numeric',
                    ],
                ],
            ],
        ]
    );

    register_rest_route(
        'aimentor/v1',
        '/analytics/guardrails',
        [
            [
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'aimentor_rest_get_guardrails',
                'permission_callback' => '__return_true',
            ],
            [
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => 'aimentor_rest_update_guardrails',
                'permission_callback' => '__return_true',
            ],
        ]
    );
}
add_action( 'rest_api_init', 'aimentor_register_analytics_routes' );
