<?php
/**
 * Automation job runner and helpers.
 *
 * @package AiMentorElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

function aimentor_get_automation_schedule_intervals() {
        return [
                'hourly'     => HOUR_IN_SECONDS,
                'twicedaily' => 12 * HOUR_IN_SECONDS,
                'daily'      => DAY_IN_SECONDS,
                'weekly'     => WEEK_IN_SECONDS,
        ];
}

function aimentor_get_automation_interval_for_job( $job ) {
        $intervals = aimentor_get_automation_schedule_intervals();
        $cadence   = isset( $job['cadence'] ) ? sanitize_key( $job['cadence'] ) : 'daily';

        return isset( $intervals[ $cadence ] ) ? absint( $intervals[ $cadence ] ) : DAY_IN_SECONDS;
}

function aimentor_automation_default_job_state() {
        return [
                'status'             => 'idle',
                'last_run'           => 0,
                'next_run'           => 0,
                'last_result'        => '',
                'last_error'         => '',
                'processed'          => 0,
                'pending'            => 0,
                'rate_limited_until' => 0,
        ];
}

function aimentor_automation_get_post_meta_key( $job_id, $suffix ) {
        $hash = substr( md5( (string) $job_id ), 0, 12 );

        return '_aimentor_automation_' . $hash . '_' . sanitize_key( $suffix );
}

function aimentor_automation_get_post_last_run( $post_id, $job_id ) {
        $meta_key = aimentor_automation_get_post_meta_key( $job_id, 'last_run' );

        return absint( get_post_meta( $post_id, $meta_key, true ) );
}

function aimentor_automation_set_post_last_run( $post_id, $job_id, $timestamp, $layout_id = 0 ) {
        $meta_key = aimentor_automation_get_post_meta_key( $job_id, 'last_run' );
        update_post_meta( $post_id, $meta_key, absint( $timestamp ) );

        if ( $layout_id ) {
                $layout_key = aimentor_automation_get_post_meta_key( $job_id, 'last_layout' );
                update_post_meta( $post_id, $layout_key, absint( $layout_id ) );
        }
}

function aimentor_automation_find_saved_prompt_text( $payload, $prompt_id ) {
        if ( '' === $prompt_id || ! is_array( $payload ) ) {
                return '';
        }

        foreach ( [ 'global', 'user' ] as $scope ) {
                if ( empty( $payload[ $scope ] ) || ! is_array( $payload[ $scope ] ) ) {
                        continue;
                }

                foreach ( $payload[ $scope ] as $prompt ) {
                        if ( ! is_array( $prompt ) ) {
                                continue;
                        }

                        if ( ! isset( $prompt['id'], $prompt['prompt'] ) ) {
                                continue;
                        }

                        if ( (string) $prompt_id === (string) $prompt['id'] ) {
                                return sanitize_textarea_field( $prompt['prompt'] );
                        }
                }
        }

        return '';
}

function aimentor_render_automation_prompt_template( $template, $post ) {
        if ( ! $post instanceof WP_Post ) {
                return '';
        }

        $template = (string) $template;

        if ( '' === trim( $template ) ) {
                return '';
        }

        $excerpt = $post->post_excerpt;

        if ( '' === trim( $excerpt ) ) {
                $excerpt = wp_trim_words( wp_strip_all_tags( $post->post_content ), 55, '…' );
        }

        $replacements = [
                '{{post_title}}'   => get_the_title( $post ),
                '{{post_excerpt}}' => $excerpt,
                '{{post_url}}'     => get_permalink( $post ),
                '{{post_author}}'  => get_the_author_meta( 'display_name', $post->post_author ),
                '{{post_content}}' => wp_strip_all_tags( $post->post_content ),
                '{{site_name}}'    => get_bloginfo( 'name' ),
                '{{site_tagline}}' => get_bloginfo( 'description' ),
        ];

        $rendered = strtr( $template, $replacements );

        if ( preg_match_all( '/{{post_meta:([^}]+)}}/', $rendered, $matches, PREG_SET_ORDER ) ) {
                foreach ( $matches as $match ) {
                        $meta_key = isset( $match[1] ) ? sanitize_key( $match[1] ) : '';

                        if ( '' === $meta_key ) {
                                continue;
                        }

                        $meta_value = get_post_meta( $post->ID, $meta_key, true );

                        if ( is_array( $meta_value ) ) {
                                $meta_value = implode( ', ', array_map( 'sanitize_text_field', $meta_value ) );
                        } else {
                                $meta_value = sanitize_text_field( (string) $meta_value );
                        }

                        $rendered = str_replace( $match[0], $meta_value, $rendered );
                }
        }

        return trim( $rendered );
}
function aimentor_collect_automation_job_candidates( $job, $automation_results ) {
        $post_type = isset( $job['post_type'] ) ? sanitize_key( $job['post_type'] ) : 'post';

        if ( ! post_type_exists( $post_type ) ) {
                return [];
        }

        $selection   = isset( $job['selection'] ) ? sanitize_key( $job['selection'] ) : 'recent';
        $limit       = isset( $job['limit'] ) ? max( 1, absint( $job['limit'] ) ) : 1;
        $post_ids    = isset( $job['post_ids'] ) && is_array( $job['post_ids'] ) ? array_filter( array_map( 'absint', $job['post_ids'] ) ) : [];
        $post_status = isset( $job['post_status'] ) ? sanitize_key( $job['post_status'] ) : 'publish';

        $posts_per_page = min( max( $limit * 3, $limit ), 30 );

        $query_args = [
                'post_type'      => $post_type,
                'post_status'    => 'any' === $post_status ? 'any' : $post_status,
                'posts_per_page' => $posts_per_page,
                'orderby'        => 'modified',
                'order'          => 'DESC',
                'no_found_rows'  => true,
        ];

        if ( 'ids' === $selection && ! empty( $post_ids ) ) {
                $query_args['post__in'] = $post_ids;
                $query_args['orderby']  = 'post__in';
        }

        $posts = get_posts( $query_args );

        if ( empty( $posts ) ) {
                return [];
        }

        $candidates = [];

        foreach ( $posts as $post ) {
                if ( ! $post instanceof WP_Post ) {
                        continue;
                }

                if ( 'ids' === $selection && ! in_array( $post->ID, $post_ids, true ) ) {
                        continue;
                }

                $pending_for_post = false;

                if ( is_array( $automation_results ) ) {
                        foreach ( $automation_results as $result ) {
                                if ( ! is_array( $result ) ) {
                                        continue;
                                }

                                if ( (string) $result['job_id'] !== (string) $job['id'] ) {
                                        continue;
                                }

                                if ( (int) $result['source_post_id'] !== (int) $post->ID ) {
                                        continue;
                                }

                                if ( 'pending' === $result['status'] ) {
                                        $pending_for_post = true;
                                        break;
                                }
                        }
                }

                if ( $pending_for_post ) {
                        continue;
                }

                $candidates[] = $post;

                if ( count( $candidates ) >= $limit ) {
                        break;
                }
        }

        return $candidates;
}

function aimentor_automation_count_pending_for_job( $job_id, $results ) {
        $count = 0;

        if ( empty( $results ) || ! is_array( $results ) ) {
                return $count;
        }

        foreach ( $results as $result ) {
                if ( ! is_array( $result ) ) {
                        continue;
                }

                if ( (string) $job_id !== (string) $result['job_id'] ) {
                        continue;
                }

                if ( 'pending' === $result['status'] ) {
                        $count++;
                }
        }

        return $count;
}

function aimentor_create_automation_layout_draft( $job, $post, $prompt, $payload ) {
        if ( ! post_type_exists( 'ai_layout' ) ) {
                return new WP_Error( 'aimentor_automation_missing_layout_post_type', __( 'AI Layout post type is not registered.', 'aimentor' ) );
        }

        $type = isset( $payload['type'] ) ? sanitize_key( $payload['type'] ) : 'content';

        if ( ! in_array( $type, [ 'canvas', 'content' ], true ) ) {
                $type = 'content';
        }

        if ( 'canvas' === $type ) {
                $content = isset( $payload['canvas_json'] ) ? $payload['canvas_json'] : '';

                if ( is_array( $content ) ) {
                        $content = wp_json_encode( $content );
                }
        } else {
                $content = isset( $payload['html'] ) ? (string) $payload['html'] : '';
        }

        if ( '' === trim( (string) $content ) ) {
                return new WP_Error( 'aimentor_automation_empty_payload', __( 'Provider returned an empty response.', 'aimentor' ) );
        }

        $title = sprintf(
                /* translators: 1: Source post title, 2: Automation job label. */
                __( '%1$s – %2$s draft', 'aimentor' ),
                get_the_title( $post ),
                isset( $job['label'] ) ? $job['label'] : __( 'Automation', 'aimentor' )
        );

        $excerpt = wp_trim_words( wp_strip_all_tags( $prompt ), 40, '…' );

        $meta_input = [
                '_aimentor_prompt'               => $prompt,
                '_aimentor_provider'             => isset( $payload['provider'] ) ? sanitize_key( $payload['provider'] ) : '',
                '_aimentor_model'                => isset( $payload['model'] ) ? sanitize_text_field( $payload['model'] ) : '',
                '_aimentor_generation_type'      => $type,
                '_aimentor_task'                 => isset( $job['task'] ) ? sanitize_key( $job['task'] ) : 'content',
                '_aimentor_tier'                 => isset( $job['tier'] ) ? sanitize_key( $job['tier'] ) : '',
                '_aimentor_automation_job_id'    => $job['id'],
                '_aimentor_automation_source_id' => $post->ID,
                '_aimentor_automation_status'    => 'pending',
        ];

        $post_data = [
                'post_type'    => 'ai_layout',
                'post_status'  => 'draft',
                'post_author'  => get_current_user_id(),
                'post_title'   => $title,
                'post_content' => $content,
                'post_excerpt' => $excerpt,
                'meta_input'   => array_filter( $meta_input ),
        ];

        $layout_id = wp_insert_post( $post_data, true );

        if ( is_wp_error( $layout_id ) ) {
                return $layout_id;
        }

        return $layout_id;
}

function aimentor_apply_automation_layout_to_post( $layout_id, $result ) {
        $layout = get_post( $layout_id );

        if ( ! $layout || 'ai_layout' !== $layout->post_type ) {
                return new WP_Error( 'aimentor_automation_invalid_layout', __( 'The selected automation draft is no longer available.', 'aimentor' ) );
        }

        $source_post_id = isset( $result['source_post_id'] ) ? absint( $result['source_post_id'] ) : 0;

        if ( ! $source_post_id ) {
                return new WP_Error( 'aimentor_automation_missing_source', __( 'Unable to determine the source post for this automation result.', 'aimentor' ) );
        }

        $source_post = get_post( $source_post_id );

        if ( ! $source_post ) {
                return new WP_Error( 'aimentor_automation_missing_source', __( 'The source post for this automation result no longer exists.', 'aimentor' ) );
        }

        $type = get_post_meta( $layout_id, '_aimentor_generation_type', true );

        if ( '' === $type ) {
                $type = isset( $result['generation_type'] ) ? sanitize_key( $result['generation_type'] ) : 'content';
        }

        $content = $layout->post_content;

        if ( 'canvas' === $type ) {
                update_post_meta( $source_post_id, '_aimentor_automation_canvas_payload', $content );

                return true;
        }

        update_post_meta( $source_post_id, '_aimentor_automation_previous_content', $source_post->post_content );

        $update = wp_update_post(
                [
                        'ID'           => $source_post_id,
                        'post_content' => $content,
                ],
                true
        );

        if ( is_wp_error( $update ) ) {
                return $update;
        }

        return true;
}
function aimentor_register_automation_result_entry( $layout_id, $job_id, $post_id, $payload ) {
        $results  = aimentor_get_automation_results();
        $now      = current_time( 'timestamp' );
        $type     = isset( $payload['type'] ) ? sanitize_key( $payload['type'] ) : '';
        $provider = isset( $payload['provider'] ) ? sanitize_key( $payload['provider'] ) : '';
        $model    = isset( $payload['model'] ) ? sanitize_text_field( $payload['model'] ) : '';
        $summary  = isset( $payload['summary'] ) ? sanitize_text_field( $payload['summary'] ) : '';

        $results[ $layout_id ] = [
                'layout_id'       => $layout_id,
                'job_id'          => $job_id,
                'source_post_id'  => $post_id,
                'status'          => 'pending',
                'generation_type' => $type,
                'provider'        => $provider,
                'model'           => $model,
                'summary'         => $summary,
                'created_at'      => $now,
                'updated_at'      => $now,
                'applied_at'      => 0,
                'notes'           => '',
        ];

        aimentor_store_automation_results( $results );

        return $results;
}

function aimentor_mark_automation_result_status( $layout_id, $status, $extra = [] ) {
        $results = aimentor_get_automation_results();

        if ( ! isset( $results[ $layout_id ] ) ) {
                return new WP_Error( 'aimentor_automation_result_missing', __( 'Automation result not found.', 'aimentor' ) );
        }

        $allowed_statuses = [ 'pending', 'approved', 'dismissed', 'applied' ];

        if ( ! in_array( $status, $allowed_statuses, true ) ) {
                $status = 'pending';
        }

        $now = current_time( 'timestamp' );

        $results[ $layout_id ] = array_merge(
                $results[ $layout_id ],
                [
                        'status'     => $status,
                        'updated_at' => $now,
                ],
                $extra
        );

        aimentor_store_automation_results( $results );

        return $results[ $layout_id ];
}
function aimentor_run_scheduled_automation_jobs() {
        if ( ! function_exists( 'aimentor_automation_enabled' ) || ! aimentor_automation_enabled() ) {
                return;
        }

        $jobs = aimentor_get_automation_jobs();

        if ( empty( $jobs ) ) {
                return;
        }

        $queue_state           = aimentor_get_automation_queue_state();
        $automation_results    = aimentor_get_automation_results();
        $saved_prompts_payload = aimentor_get_saved_prompts_payload();
        $now                   = current_time( 'timestamp' );

        foreach ( $jobs as $job ) {
                if ( 'active' !== $job['status'] ) {
                        continue;
                }

                $job_id = $job['id'];

                $job_state = isset( $queue_state['jobs'][ $job_id ] ) && is_array( $queue_state['jobs'][ $job_id ] )
                        ? wp_parse_args( $queue_state['jobs'][ $job_id ], aimentor_automation_default_job_state() )
                        : aimentor_automation_default_job_state();

                if ( $job_state['rate_limited_until'] && $now < $job_state['rate_limited_until'] ) {
                        continue;
                }

                if ( $job_state['next_run'] && $now < $job_state['next_run'] ) {
                        continue;
                }

                $job_state['status']     = 'running';
                $job_state['last_error'] = '';
                $queue_state['jobs'][ $job_id ] = $job_state;
                aimentor_store_automation_queue_state( $queue_state );

                $candidates      = aimentor_collect_automation_job_candidates( $job, $automation_results );
                $processed_count = 0;
                $job_error       = '';

                foreach ( $candidates as $post ) {
                        $interval = aimentor_get_automation_interval_for_job( $job );
                        $last_run = aimentor_automation_get_post_last_run( $post->ID, $job_id );

                        if ( $last_run && ( $now - $last_run ) < $interval ) {
                                continue;
                        }

                        $prompt_text = '';

                        if ( 'saved_prompt' === $job['prompt_type'] ) {
                                $prompt_text = aimentor_automation_find_saved_prompt_text( $saved_prompts_payload, $job['saved_prompt_id'] );
                        } elseif ( 'manual' === $job['prompt_type'] ) {
                                $prompt_text = aimentor_render_automation_prompt_template( $job['prompt_template'], $post );
                        }

                        if ( '' === $prompt_text ) {
                                continue;
                        }

                        $provider_key = isset( $job['provider'] ) ? sanitize_key( $job['provider'] ) : '';

                        $result = aimentor_perform_generation_request(
                                $prompt_text,
                                $provider_key,
                                [
                                        'task'          => $job['task'],
                                        'tier'          => $job['tier'],
                                        'origin'        => 'automation',
                                        'store_history' => false,
                                        'knowledge_ids' => isset( $job['knowledge_ids'] ) ? $job['knowledge_ids'] : [],
                                        'user_id'       => 0,
                                ]
                        );

                        if ( is_wp_error( $result ) ) {
                                $job_error = $result->get_error_message();

                                if ( function_exists( 'aimentor_log_error' ) ) {
                                        aimentor_log_error(
                                                sprintf( __( 'Automation job %1$s failed: %2$s', 'aimentor' ), $job['label'], $job_error ),
                                                [
                                                        'provider' => $provider_key,
                                                        'task'     => $job['task'],
                                                        'tier'     => $job['tier'],
                                                        'origin'   => 'automation',
                                                        'user_id'  => 0,
                                                ]
                                        );
                                }

                                $error_data  = $result->get_error_data();
                                $retry_after = 0;

                                if ( is_array( $error_data ) && isset( $error_data['retry_after'] ) ) {
                                        $retry_after = absint( $error_data['retry_after'] );
                                }

                                if ( ! $retry_after && false !== strpos( $result->get_error_code(), 'rate' ) ) {
                                        $retry_after = HOUR_IN_SECONDS;
                                }

                                if ( $retry_after > 0 ) {
                                        $job_state['rate_limited_until'] = $now + $retry_after;
                                }

                                break;
                        }

                        $layout_id = aimentor_create_automation_layout_draft( $job, $post, $prompt_text, $result );

                        if ( is_wp_error( $layout_id ) ) {
                                $job_error = $layout_id->get_error_message();

                                if ( function_exists( 'aimentor_log_error' ) ) {
                                        aimentor_log_error(
                                                sprintf( __( 'Automation job %1$s failed to save the generated layout: %2$s', 'aimentor' ), $job['label'], $job_error ),
                                                [
                                                        'provider' => isset( $result['provider'] ) ? $result['provider'] : $provider_key,
                                                        'task'     => $job['task'],
                                                        'tier'     => $job['tier'],
                                                        'origin'   => 'automation',
                                                        'user_id'  => 0,
                                                ]
                                        );
                                }

                                break;
                        }

                        $automation_results = aimentor_register_automation_result_entry( $layout_id, $job_id, $post->ID, $result );
                        aimentor_automation_set_post_last_run( $post->ID, $job_id, $now, $layout_id );
                        $processed_count++;

                        if ( ! empty( $result['rate_limit'] ) && is_array( $result['rate_limit'] ) ) {
                                $rate_limit   = $result['rate_limit'];
                                $retry_after  = isset( $rate_limit['retry_after'] ) ? absint( $rate_limit['retry_after'] ) : 0;
                                $reset_window = isset( $rate_limit['reset'] ) ? absint( $rate_limit['reset'] ) - $now : 0;

                                if ( $reset_window > 0 && ! $retry_after ) {
                                        $retry_after = max( $reset_window, $retry_after );
                                }

                                if ( $retry_after > 0 ) {
                                        $job_state['rate_limited_until'] = max( $job_state['rate_limited_until'], $now + $retry_after );

                                        if ( function_exists( 'aimentor_log_error' ) ) {
                                                aimentor_log_error(
                                                        sprintf( __( 'Automation job %s reached the provider rate limit. Cooling down before the next run.', 'aimentor' ), $job['label'] ),
                                                        [
                                                                'provider' => isset( $result['provider'] ) ? $result['provider'] : $provider_key,
                                                                'task'     => $job['task'],
                                                                'tier'     => $job['tier'],
                                                                'origin'   => 'automation',
                                                                'user_id'  => 0,
                                                        ]
                                                );
                                        }
                                }
                        }
                }

                $job_state['last_run']  = $now;
                $job_state['status']    = '' === $job_error ? 'idle' : 'error';
                $job_state['last_result'] = '' === $job_error ? 'success' : 'error';
                $job_state['last_error']  = $job_error;
                $job_state['processed']   = $processed_count;
                $job_state['pending']     = aimentor_automation_count_pending_for_job( $job_id, $automation_results );
                $job_state['next_run']    = $now + aimentor_get_automation_interval_for_job( $job );

                $queue_state['jobs'][ $job_id ] = $job_state;
                aimentor_store_automation_queue_state( $queue_state );
        }
}
function aimentor_handle_automation_result_action() {
        if ( ! current_user_can( 'manage_options' ) ) {
                wp_die( esc_html__( 'You do not have permission to manage automation results.', 'aimentor' ) );
        }

        check_admin_referer( 'aimentor_automation_result_action', '_aimentor_automation_nonce' );

        $layout_id = isset( $_POST['layout_id'] ) ? absint( $_POST['layout_id'] ) : 0;
        $decision  = isset( $_POST['decision'] ) ? sanitize_key( wp_unslash( $_POST['decision'] ) ) : '';
        $redirect  = isset( $_POST['redirect_to'] ) ? esc_url_raw( wp_unslash( $_POST['redirect_to'] ) ) : '';

        if ( ! $redirect ) {
                $redirect = add_query_arg(
                        [
                                'page' => 'aimentor-settings',
                                'tab'  => 'logs',
                        ],
                        admin_url( 'admin.php' )
                );
        }

        if ( ! $layout_id ) {
                $redirect = add_query_arg(
                        [
                                'aimentor_automation_message' => 'missing',
                                'aimentor_automation_status'  => 'error',
                        ],
                        $redirect
                );

                wp_safe_redirect( $redirect );
                exit;
        }

        $results = aimentor_get_automation_results();

        if ( ! isset( $results[ $layout_id ] ) ) {
                $redirect = add_query_arg(
                        [
                                'aimentor_automation_message' => 'missing',
                                'aimentor_automation_status'  => 'error',
                        ],
                        $redirect
                );

                wp_safe_redirect( $redirect );
                exit;
        }

        $result = $results[ $layout_id ];
        $job_id = $result['job_id'];
        $now    = current_time( 'timestamp' );
        $status = 'dismissed';
        $message_code = 'dismissed';
        $extra = [];

        if ( 'approve' === $decision || 'apply' === $decision ) {
                $apply_result = aimentor_apply_automation_layout_to_post( $layout_id, $result );

                if ( is_wp_error( $apply_result ) ) {
                        $redirect = add_query_arg(
                                [
                                        'aimentor_automation_message' => 'error',
                                        'aimentor_automation_status'  => 'error',
                                ],
                                $redirect
                        );

                        wp_safe_redirect( $redirect );
                        exit;
                }

                update_post_meta( $layout_id, '_aimentor_automation_status', 'approved' );
                update_post_meta( $layout_id, '_aimentor_automation_applied_at', $now );
                update_post_meta( $layout_id, '_aimentor_automation_applied_by', get_current_user_id() );

                $status       = 'approved';
                $message_code = 'applied';
                $extra        = [
                        'applied_at' => $now,
                ];
        } else {
                update_post_meta( $layout_id, '_aimentor_automation_status', 'dismissed' );
                update_post_meta( $layout_id, '_aimentor_automation_dismissed_at', $now );
                update_post_meta( $layout_id, '_aimentor_automation_dismissed_by', get_current_user_id() );
        }

        $updated_result = aimentor_mark_automation_result_status( $layout_id, $status, $extra );

        if ( is_wp_error( $updated_result ) ) {
                $redirect = add_query_arg(
                        [
                                'aimentor_automation_message' => 'error',
                                'aimentor_automation_status'  => 'error',
                        ],
                        $redirect
                );

                wp_safe_redirect( $redirect );
                exit;
        }

        $queue_state = aimentor_get_automation_queue_state();

        if ( isset( $queue_state['jobs'][ $job_id ] ) ) {
                $queue_state['jobs'][ $job_id ]['pending'] = aimentor_automation_count_pending_for_job( $job_id, aimentor_get_automation_results() );
                aimentor_store_automation_queue_state( $queue_state );
        }

        $redirect = add_query_arg(
                [
                        'aimentor_automation_message' => $message_code,
                        'aimentor_automation_status'  => 'success',
                ],
                $redirect
        );

        wp_safe_redirect( $redirect );
        exit;
}

function aimentor_render_automation_admin_notices() {
        if ( ! isset( $_GET['page'] ) || 'aimentor-settings' !== sanitize_key( wp_unslash( $_GET['page'] ) ) ) {
                return;
        }

        if ( empty( $_GET['aimentor_automation_message'] ) ) {
                return;
        }

        $message_key = sanitize_key( wp_unslash( $_GET['aimentor_automation_message'] ) );
        $status      = isset( $_GET['aimentor_automation_status'] ) ? sanitize_key( wp_unslash( $_GET['aimentor_automation_status'] ) ) : 'success';

        $messages = [
                'applied'   => __( 'Automation output applied to the source post.', 'aimentor' ),
                'dismissed' => __( 'Automation output dismissed.', 'aimentor' ),
                'missing'   => __( 'Unable to locate the selected automation result.', 'aimentor' ),
                'error'     => __( 'Automation action failed. Check the logs for details.', 'aimentor' ),
        ];

        if ( ! isset( $messages[ $message_key ] ) ) {
                return;
        }

        $class = 'notice notice-' . ( 'error' === $status ? 'error' : 'success' );

        printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $messages[ $message_key ] ) );
}

add_action( 'admin_post_aimentor_automation_result_action', 'aimentor_handle_automation_result_action' );
add_action( 'admin_notices', 'aimentor_render_automation_admin_notices' );
