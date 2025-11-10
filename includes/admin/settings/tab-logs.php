<?php
$automation_jobs_display   = isset( $automation_jobs_map ) && is_array( $automation_jobs_map ) ? $automation_jobs_map : [];
$automation_queue_jobs     = isset( $automation_queue_state['jobs'] ) && is_array( $automation_queue_state['jobs'] ) ? $automation_queue_state['jobs'] : [];
$automation_results_list   = isset( $automation_results ) && is_array( $automation_results ) ? $automation_results : [];
$automation_action_url     = admin_url( 'admin-post.php' );
$logs_tab_url              = add_query_arg( [ 'page' => 'aimentor-settings', 'tab' => 'logs' ], admin_url( 'admin.php' ) );
$automation_results_sorted = array_values( $automation_results_list );

usort(
        $automation_results_sorted,
        static function( $a, $b ) {
                $a_time = isset( $a['created_at'] ) ? absint( $a['created_at'] ) : 0;
                $b_time = isset( $b['created_at'] ) ? absint( $b['created_at'] ) : 0;

                return $b_time <=> $a_time;
        }
);
?>
<div class="aimentor-settings-layout">
    <div class="aimentor-settings-main">
        <div class="aimentor-automation-card">
            <h2><?php esc_html_e( 'Automation Queue', 'aimentor' ); ?></h2>
            <?php if ( empty( $automation_queue_jobs ) ) : ?>
            <p class="description"><?php esc_html_e( 'No automation jobs have been scheduled yet. Configure a job on the Automation tab to populate this queue.', 'aimentor' ); ?></p>
            <?php else : ?>
            <table class="widefat striped aimentor-automation-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Job', 'aimentor' ); ?></th>
                        <th><?php esc_html_e( 'Last run', 'aimentor' ); ?></th>
                        <th><?php esc_html_e( 'Next run', 'aimentor' ); ?></th>
                        <th><?php esc_html_e( 'Pending drafts', 'aimentor' ); ?></th>
                        <th><?php esc_html_e( 'Status', 'aimentor' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $automation_queue_jobs as $job_id => $job_state ) :
                            $job_label = isset( $automation_jobs_display[ $job_id ]['label'] ) && $automation_jobs_display[ $job_id ]['label']
                                    ? $automation_jobs_display[ $job_id ]['label']
                                    : $job_id;
                            $last_run = isset( $job_state['last_run'] ) ? absint( $job_state['last_run'] ) : 0;
                            $next_run = isset( $job_state['next_run'] ) ? absint( $job_state['next_run'] ) : 0;
                            $last_result = isset( $job_state['last_result'] ) ? sanitize_key( $job_state['last_result'] ) : '';
                            $last_error  = isset( $job_state['last_error'] ) ? $job_state['last_error'] : '';
                            ?>
                    <tr>
                        <td><?php echo esc_html( $job_label ); ?></td>
                        <td>
                            <?php
                            if ( $last_run ) {
                                    $last_time = function_exists( 'wp_date' )
                                            ? wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $last_run )
                                            : date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $last_run );
                                    echo esc_html( $last_time );
                            } else {
                                    esc_html_e( 'Not yet run', 'aimentor' );
                            }
                            ?>
                        </td>
                        <td>
                            <?php
                            if ( $next_run ) {
                                    $label = function_exists( 'human_time_diff' ) ? human_time_diff( current_time( 'timestamp' ), $next_run ) : '';
                                    if ( $label ) {
                                            printf( esc_html__( 'In %s', 'aimentor' ), esc_html( $label ) );
                                    } else {
                                            echo esc_html( $next_run );
                                    }
                            } else {
                                    echo '&mdash;';
                            }
                            ?>
                        </td>
                        <td><?php echo esc_html( isset( $job_state['pending'] ) ? absint( $job_state['pending'] ) : 0 ); ?></td>
                        <td>
                            <span class="aimentor-automation-status aimentor-automation-status--<?php echo esc_attr( $last_result ? $last_result : 'idle' ); ?>">
                                <?php
                                if ( 'error' === $last_result && $last_error ) {
                                        echo esc_html( $last_error );
                                } elseif ( 'success' === $last_result ) {
                                        esc_html_e( 'Success', 'aimentor' );
                                } elseif ( 'error' === $last_result ) {
                                        esc_html_e( 'Error', 'aimentor' );
                                } else {
                                        esc_html_e( 'Idle', 'aimentor' );
                                }
                                ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
        <div class="aimentor-automation-results">
            <h2><?php esc_html_e( 'Automation Outputs', 'aimentor' ); ?></h2>
            <?php if ( empty( $automation_results_sorted ) ) : ?>
                <p class="description"><?php esc_html_e( 'No automation drafts have been generated yet.', 'aimentor' ); ?></p>
            <?php else : ?>
            <table class="widefat striped aimentor-automation-results__table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Created', 'aimentor' ); ?></th>
                        <th><?php esc_html_e( 'Job', 'aimentor' ); ?></th>
                        <th><?php esc_html_e( 'Source', 'aimentor' ); ?></th>
                        <th><?php esc_html_e( 'Status', 'aimentor' ); ?></th>
                        <th><?php esc_html_e( 'Actions', 'aimentor' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $automation_results_sorted as $result ) :
                            $layout_id   = isset( $result['layout_id'] ) ? absint( $result['layout_id'] ) : 0;
                            $job_id      = isset( $result['job_id'] ) ? $result['job_id'] : '';
                            $job_label   = isset( $automation_jobs_display[ $job_id ]['label'] ) ? $automation_jobs_display[ $job_id ]['label'] : $job_id;
                            $source_id   = isset( $result['source_post_id'] ) ? absint( $result['source_post_id'] ) : 0;
                            $layout_post = $layout_id ? get_post( $layout_id ) : null;
                            $source_post = $source_id ? get_post( $source_id ) : null;
                            $layout_title = $layout_post ? get_the_title( $layout_post ) : sprintf( __( 'Layout %d', 'aimentor' ), $layout_id );
                            $source_title = $source_post ? get_the_title( $source_post ) : __( '(deleted)', 'aimentor' );
                            $created_at   = isset( $result['created_at'] ) ? absint( $result['created_at'] ) : 0;
                            $status       = isset( $result['status'] ) ? sanitize_key( $result['status'] ) : 'pending';
                            $status_label = ucfirst( $status );
                            $type_label   = isset( $result['generation_type'] ) && 'canvas' === $result['generation_type'] ? __( 'Canvas', 'aimentor' ) : __( 'Content', 'aimentor' );
                            ?>
                    <tr>
                        <td>
                            <?php
                            if ( $created_at ) {
                                    $created_display = function_exists( 'wp_date' )
                                            ? wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $created_at )
                                            : date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $created_at );
                                    echo esc_html( $created_display );
                            } else {
                                    echo '&mdash;';
                            }
                            ?>
                        </td>
                        <td><?php echo esc_html( $job_label ); ?><br /><span class="description"><?php echo esc_html( $type_label ); ?></span></td>
                        <td>
                            <?php if ( $source_post ) : ?>
                                <a href="<?php echo esc_url( get_edit_post_link( $source_post ) ); ?>"><?php echo esc_html( $source_title ); ?></a>
                            <?php else : ?>
                                <?php echo esc_html( $source_title ); ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo esc_html( $status_label ); ?></td>
                        <td>
                            <?php if ( 'pending' === $status ) : ?>
                            <div class="aimentor-automation-results__actions">
                                <form method="post" action="<?php echo esc_url( $automation_action_url ); ?>" class="aimentor-automation-results__form">
                                    <?php wp_nonce_field( 'aimentor_automation_result_action', '_aimentor_automation_nonce' ); ?>
                                    <input type="hidden" name="action" value="aimentor_automation_result_action" />
                                    <input type="hidden" name="layout_id" value="<?php echo esc_attr( $layout_id ); ?>" />
                                    <input type="hidden" name="decision" value="approve" />
                                    <input type="hidden" name="redirect_to" value="<?php echo esc_url( $logs_tab_url ); ?>" />
                                    <button type="submit" class="button button-primary"><?php esc_html_e( 'Approve & Apply', 'aimentor' ); ?></button>
                                </form>
                                <form method="post" action="<?php echo esc_url( $automation_action_url ); ?>" class="aimentor-automation-results__form">
                                    <?php wp_nonce_field( 'aimentor_automation_result_action', '_aimentor_automation_nonce' ); ?>
                                    <input type="hidden" name="action" value="aimentor_automation_result_action" />
                                    <input type="hidden" name="layout_id" value="<?php echo esc_attr( $layout_id ); ?>" />
                                    <input type="hidden" name="decision" value="dismiss" />
                                    <input type="hidden" name="redirect_to" value="<?php echo esc_url( $logs_tab_url ); ?>" />
                                    <button type="submit" class="button button-link-delete"><?php esc_html_e( 'Dismiss', 'aimentor' ); ?></button>
                                </form>
                            </div>
                            <?php else : ?>
                                <span class="description"><?php esc_html_e( 'No actions available.', 'aimentor' ); ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>
        <div class="aimentor-history-card">
            <h2 class="aimentor-history-card__title"><?php esc_html_e( 'Recent Generations', 'aimentor' ); ?></h2>
            <?php if ( empty( $history_entries ) ) : ?>
            <p class="description"><?php esc_html_e( 'No generation history recorded yet. Generate content to see it appear here.', 'aimentor' ); ?></p>
            <?php else : ?>
            <table class="widefat striped aimentor-history-table">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Generated', 'aimentor' ); ?></th>
                        <th><?php esc_html_e( 'Provider', 'aimentor' ); ?></th>
                        <th><?php esc_html_e( 'Prompt', 'aimentor' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ( $history_entries as $entry ) :
                        $provider_key   = $entry['provider'];
                        $provider_label = isset( $provider_meta[ $provider_key ]['label'] ) ? $provider_meta[ $provider_key ]['label'] : ucfirst( $provider_key );
                        $badge_text     = isset( $provider_meta[ $provider_key ]['badgeText'] ) ? $provider_meta[ $provider_key ]['badgeText'] : $provider_label;
                        $badge_color    = isset( $provider_meta[ $provider_key ]['badgeColor'] ) ? $provider_meta[ $provider_key ]['badgeColor'] : '#444444';
                        $timestamp      = absint( $entry['timestamp'] );
                        $prompt_text    = (string) $entry['prompt'];
                        $prompt_title   = trim( preg_replace( '/\s+/', ' ', $prompt_text ) );
                        $prompt_excerpt = wp_trim_words( $prompt_text, 30, '…' );
                        $time_format    = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
                        $formatted_time = '';

                        if ( $timestamp ) {
                                if ( function_exists( 'wp_date' ) ) {
                                        $formatted_time = wp_date( $time_format, $timestamp );
                                } else {
                                        $formatted_time = date_i18n( $time_format, $timestamp );
                                }
                        }
                    ?>
                    <tr>
                        <td><?php echo '' !== $formatted_time ? esc_html( $formatted_time ) : '&mdash;'; ?></td>
                        <td>
                            <span class="aimentor-history-provider-label"><?php echo esc_html( $provider_label ); ?></span>
                            <span class="aimentor-provider-badge" style="background-color:<?php echo esc_attr( $badge_color ); ?>;">
                                <?php echo esc_html( $badge_text ); ?>
                            </span>
                        </td>
                        <td>
                            <span title="<?php echo esc_attr( $prompt_title ); ?>"><?php echo esc_html( $prompt_excerpt ); ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php endif; ?>
        </div>

        <div class="aimentor-error-log-header">
            <h2><?php esc_html_e( 'Error Log', 'aimentor' ); ?></h2>
            <div class="aimentor-error-log-actions">
                <button type="button" class="button button-secondary" id="aimentor-download-log"><?php esc_html_e( 'Download Log', 'aimentor' ); ?></button>
                <button type="button" class="button button-link-delete" id="aimentor-clear-log"><?php esc_html_e( 'Clear Log', 'aimentor' ); ?></button>
            </div>
        </div>
        <p id="aimentor-error-log-feedback" class="aimentor-error-log-feedback" role="status" aria-live="polite" hidden></p>
        <form id="aimentor-error-log-form" class="aimentor-error-log-form" method="get" action="" data-nonce="<?php echo esc_attr( $error_log_nonce ); ?>">
            <div class="aimentor-error-log-filters">
                <label for="aimentor-error-log-provider"><?php esc_html_e( 'Provider', 'aimentor' ); ?></label>
                <select name="provider" id="aimentor-error-log-provider">
                    <option value="" <?php selected( '', $log_filter_provider ); ?>><?php esc_html_e( 'All Providers', 'aimentor' ); ?></option>
                    <?php foreach ( aimentor_get_provider_labels() as $provider_key => $provider_label ) : ?>
                    <option value="<?php echo esc_attr( $provider_key ); ?>" <?php selected( $log_filter_provider, $provider_key ); ?>><?php echo esc_html( $provider_label ); ?></option>
                    <?php endforeach; ?>
                </select>
                <label for="aimentor-error-log-keyword"><?php esc_html_e( 'Keyword', 'aimentor' ); ?></label>
                <input type="search" name="keyword" id="aimentor-error-log-keyword" value="<?php echo esc_attr( $log_filter_keyword ); ?>" placeholder="<?php esc_attr_e( 'Search messages…', 'aimentor' ); ?>" />
                <button type="submit" class="button"><?php esc_html_e( 'Apply Filters', 'aimentor' ); ?></button>
            </div>
            <table class="widefat striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Timestamp', 'aimentor' ); ?></th>
                        <th><?php esc_html_e( 'Provider', 'aimentor' ); ?></th>
                        <th><?php esc_html_e( 'Error Message', 'aimentor' ); ?></th>
                    </tr>
                </thead>
                <tbody id="aimentor-error-log-rows">
                    <?php echo $log_rows; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </tbody>
            </table>
        </form>
        <?php if ( ! empty( $log_file ) ) :
            $display_path = wp_normalize_path( $log_file );
            if ( defined( 'ABSPATH' ) ) {
                    $display_path = str_replace( wp_normalize_path( ABSPATH ), '', $display_path );
            }
        ?>
        <p class="description">
            <?php
            printf(
                    /* translators: %s: path to the AiMentor error log file */
                    esc_html__( 'Error logs are stored at %s.', 'aimentor' ),
                    '<code>' . esc_html( $display_path ) . '</code>'
            );
            ?>
        </p>
        <?php endif; ?>
    </div>
    <?php include plugin_dir_path( __FILE__ ) . 'sidebar-support.php'; ?>
</div>
