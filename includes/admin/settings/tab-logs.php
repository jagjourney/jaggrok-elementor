<div class="aimentor-settings-layout">
    <div class="aimentor-settings-main">
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
