<?php
/**
 * AiMentor CLI helpers.
 *
 * @package AiMentor\CLI
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( defined( 'WP_CLI' ) && WP_CLI ) {
    if ( ! class_exists( 'AiMentor_Analytics_CLI_Command' ) ) {
        /**
         * Export AiMentor analytics from the command line.
         */
        class AiMentor_Analytics_CLI_Command extends WP_CLI_Command {
            /**
             * Export usage analytics for automation workflows.
             *
             * ## OPTIONS
             *
             * [--interval=<interval>]
             * : Interval to group analytics by (hour, day, or week). Defaults to day.
             *
             * [--range=<count>]
             * : Number of intervals to include. Defaults to 7.
             *
             * [--format=<format>]
             * : Output format (json or table). Defaults to json.
             *
             * [--include-errors=<bool>]
             * : Whether to include error counts in the summary. Defaults to true.
             *
             * ## EXAMPLES
             *
             *     wp aimentor analytics export --interval=day --range=14
             *     wp aimentor analytics export --format=table --interval=hour --range=24
             *
             * @when after_wp_load
             *
             * @param array $args       Positional arguments (unused).
             * @param array $assoc_args Associative arguments.
             */
            public function export( $args, $assoc_args ) {
                if ( ! function_exists( 'aimentor_get_usage_analytics' ) ) {
                    WP_CLI::error( __( 'Analytics helpers are unavailable.', 'aimentor' ) );
                }

                $interval       = sanitize_key( \WP_CLI\Utils\get_flag_value( $assoc_args, 'interval', 'day' ) );
                $range          = absint( \WP_CLI\Utils\get_flag_value( $assoc_args, 'range', 7 ) );
                $format         = strtolower( (string) \WP_CLI\Utils\get_flag_value( $assoc_args, 'format', 'json' ) );
                $include_errors = \WP_CLI\Utils\get_flag_value( $assoc_args, 'include-errors', true );
                $include_errors = wp_validate_boolean( $include_errors );

                $data = aimentor_get_usage_analytics(
                    [
                        'interval'       => $interval,
                        'range'          => $range,
                        'include_errors' => $include_errors,
                    ]
                );

                if ( 'table' === $format ) {
                    $rows = [];

                    foreach ( $data['groups'] as $group ) {
                        $rows[] = [
                            'period'  => $group['label'],
                            'success' => isset( $group['success'] ) ? absint( $group['success'] ) : 0,
                            'errors'  => isset( $group['errors'] ) ? absint( $group['errors'] ) : 0,
                            'tokens'  => isset( $group['tokens'] ) ? absint( $group['tokens'] ) : 0,
                        ];
                    }

                    if ( empty( $rows ) ) {
                        WP_CLI::log( __( 'No analytics data available for the requested interval.', 'aimentor' ) );
                    } else {
                        \WP_CLI\Utils\format_items( 'table', $rows, [ 'period', 'success', 'errors', 'tokens' ] );
                    }

                    $totals = isset( $data['totals'] ) ? $data['totals'] : [];
                    $line   = sprintf(
                        /* translators: 1: success count, 2: error count, 3: token count. */
                        __( 'Totals — success: %1$d, errors: %2$d, tokens: %3$d', 'aimentor' ),
                        absint( $totals['success'] ?? 0 ),
                        absint( $totals['errors'] ?? 0 ),
                        absint( $totals['tokens'] ?? 0 )
                    );

                    WP_CLI::log( $line );

                    if ( ! empty( $data['guardrails'] ) ) {
                        WP_CLI::log( sprintf( 'Guardrails: %s', wp_json_encode( $data['guardrails'] ) ) );
                    }

                    return;
                }

                WP_CLI::print_value(
                    $data,
                    [
                        'format' => 'json',
                    ]
                );
            }
        }
    }

    if ( class_exists( 'AiMentor_Analytics_CLI_Command' ) ) {
        WP_CLI::add_command( 'aimentor analytics', 'AiMentor_Analytics_CLI_Command' );
    }
}
