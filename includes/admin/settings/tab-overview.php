<?php
$usage_metrics  = isset( $usage_metrics ) ? $usage_metrics : [];
$usage_providers = isset( $usage_providers ) ? $usage_providers : [];
$generated_at     = isset( $usage_metrics['generated_at'] ) ? $usage_metrics['generated_at'] : '';
$generated_at_human = isset( $usage_metrics['generated_at_human'] ) ? $usage_metrics['generated_at_human'] : '';
?>
<div class="aimentor-settings-layout">
    <div class="aimentor-settings-main">
        <p class="description"><?php esc_html_e( 'Connect to your preferred AI provider and unlock AI-powered page building!', 'aimentor' ); ?> <a href="https://jagjourney.com/" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'By AiMentor', 'aimentor' ); ?></a></p>

        <div class="notice notice-info" style="max-width: 720px;">
            <p>
                <strong><?php esc_html_e( 'Remote generation endpoint', 'aimentor' ); ?></strong>
                <?php
                printf(
                        /* translators: %s: REST endpoint URL */
                        esc_html__( 'Send authenticated POST requests to %s with prompt and provider parameters to trigger AiMentor remotely.', 'aimentor' ),
                        '<code>' . esc_html( $rest_endpoint ) . '</code>'
                );
                ?>
            </p>
            <p class="description">
                <?php esc_html_e( 'Use an X-WP-Nonce header created with wp_create_nonce(\'wp_rest\') for browser-based requests or authenticate with a WordPress Application Password for server-to-server integrations.', 'aimentor' ); ?>
            </p>
        </div>

        <?php if ( ! empty( $should_show_onboarding ) ) :
                $onboarding_steps = [
                        [
                                'label'       => __( 'Add your API key', 'aimentor' ),
                                'description' => __( 'Paste your xAI, Anthropic, or OpenAI key into the fields below.', 'aimentor' ),
                                'completed'   => ! empty( $has_api_key ),
                        ],
                        [
                                'label'       => __( 'Test your provider', 'aimentor' ),
                                'description' => __( 'Use “Test Connection” to confirm the integration is ready.', 'aimentor' ),
                                'completed'   => ! empty( $provider_tested ),
                        ],
                ];
        ?>
        <div class="notice notice-info is-dismissible aimentor-onboarding-card" data-dismissible="aimentor-onboarding-card">
            <button type="button" class="notice-dismiss aimentor-onboarding-dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss this onboarding checklist', 'aimentor' ); ?></span></button>
            <h2><?php esc_html_e( 'Quick start checklist', 'aimentor' ); ?></h2>
            <p class="aimentor-onboarding-card__lead"><?php esc_html_e( 'Complete these steps to start generating with AiMentor.', 'aimentor' ); ?></p>
            <ul class="aimentor-onboarding-card__steps">
                <?php foreach ( $onboarding_steps as $step ) :
                        $state      = $step['completed'] ? 'is-complete' : 'is-pending';
                        $icon_class = $step['completed'] ? 'dashicons-yes' : 'dashicons-marker';
                ?>
                <li class="aimentor-onboarding-card__step <?php echo esc_attr( $state ); ?>">
                    <span class="dashicons <?php echo esc_attr( $icon_class ); ?>" aria-hidden="true"></span>
                    <div>
                        <strong><?php echo esc_html( $step['label'] ); ?></strong>
                        <p><?php echo esc_html( $step['description'] ); ?></p>
                    </div>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <section id="aimentor-usage-metrics" class="aimentor-usage-card jaggrok-usage-card" data-generated-at="<?php echo esc_attr( $generated_at ); ?>" aria-live="polite">
            <header class="aimentor-usage-card__header jaggrok-usage-card__header">
                <h2 class="aimentor-usage-card__title jaggrok-usage-card__title"><?php esc_html_e( 'Usage snapshot', 'aimentor' ); ?></h2>
                <?php if ( $generated_at_human ) : ?>
                <span class="aimentor-usage-card__timestamp jaggrok-usage-card__timestamp" data-metric="generated_at">
                    <?php echo esc_html( sprintf( __( 'Updated %s', 'aimentor' ), $generated_at_human ) ); ?>
                </span>
                <?php endif; ?>
            </header>
            <p class="description aimentor-usage-card__description jaggrok-usage-card__description"><?php esc_html_e( 'Counts reset automatically after the transient expires (roughly every 24 hours). Data never leaves your site.', 'aimentor' ); ?></p>
            <div class="aimentor-usage-grid jaggrok-usage-grid">
                <?php foreach ( $usage_providers as $usage_provider => $usage_data ) :
                        $usage_label     = isset( $usage_data['label'] ) ? $usage_data['label'] : ucfirst( $usage_provider );
                        $total_requests  = isset( $usage_data['total_requests'] ) ? absint( $usage_data['total_requests'] ) : 0;
                        $success_total   = isset( $usage_data['success_total'] ) ? absint( $usage_data['success_total'] ) : 0;
                        $error_total     = isset( $usage_data['error_total'] ) ? absint( $usage_data['error_total'] ) : 0;
                        $last_event_label = isset( $usage_data['last_event_human'] ) ? $usage_data['last_event_human'] : '';
                        $origin_label    = isset( $usage_data['origin_label'] ) ? $usage_data['origin_label'] : '';
                        $context_summary = isset( $usage_data['context_summary'] ) ? $usage_data['context_summary'] : '';
                        $event_display   = $last_event_label ? $last_event_label : __( 'No activity yet', 'aimentor' );
                        if ( $origin_label ) {
                                $event_display .= ' — ' . $origin_label;
                        }
                ?>
                <div class="aimentor-usage-provider jaggrok-usage-provider" data-provider="<?php echo esc_attr( $usage_provider ); ?>">
                    <h3 class="aimentor-usage-provider__title jaggrok-usage-provider__title"><?php echo esc_html( $usage_label ); ?></h3>
                    <div class="aimentor-usage-provider__stats jaggrok-usage-provider__stats">
                        <div class="aimentor-usage-stat jaggrok-usage-stat">
                            <span class="aimentor-usage-stat__label jaggrok-usage-stat__label"><?php esc_html_e( 'Requests', 'aimentor' ); ?></span>
                            <span class="aimentor-usage-stat__value jaggrok-usage-stat__value" data-metric="total_requests"><?php echo esc_html( number_format_i18n( $total_requests ) ); ?></span>
                        </div>
                        <div class="aimentor-usage-stat jaggrok-usage-stat">
                            <span class="aimentor-usage-stat__label jaggrok-usage-stat__label"><?php esc_html_e( 'Completed', 'aimentor' ); ?></span>
                            <span class="aimentor-usage-stat__value jaggrok-usage-stat__value" data-metric="success_total"><?php echo esc_html( number_format_i18n( $success_total ) ); ?></span>
                        </div>
                        <div class="aimentor-usage-stat jaggrok-usage-stat">
                            <span class="aimentor-usage-stat__label jaggrok-usage-stat__label"><?php esc_html_e( 'Errors', 'aimentor' ); ?></span>
                            <span class="aimentor-usage-stat__value jaggrok-usage-stat__value" data-metric="error_total"><?php echo esc_html( number_format_i18n( $error_total ) ); ?></span>
                        </div>
                    </div>
                    <p class="aimentor-usage-provider__meta jaggrok-usage-provider__meta" data-metric="last_event_summary"><?php echo esc_html( $event_display ); ?></p>
                    <p class="aimentor-usage-provider__context jaggrok-usage-provider__context" data-metric="context_summary">
                        <?php echo '' !== $context_summary ? esc_html( $context_summary ) : esc_html__( 'Most recent context unavailable.', 'aimentor' ); ?>
                    </p>
                </div>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
    <?php include plugin_dir_path( __FILE__ ) . 'sidebar-support.php'; ?>
</div>
