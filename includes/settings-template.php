
<div class="wrap">
    <h1>AiMentor Elementor Settings</h1>
    <style>
        .aimentor-provider-badge {
            display: inline-flex;
            align-items: center;
            padding: 2px 8px;
            margin-left: 6px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 600;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .aimentor-onboarding-card {
            position: relative;
            padding-right: 40px;
            margin: 20px 0;
        }

        .aimentor-onboarding-card.is-dismissing {
            opacity: 0.6;
        }

        .aimentor-onboarding-card__lead {
            margin-top: 0;
        }

        .aimentor-onboarding-card__steps {
            margin: 0;
            padding-left: 0;
            list-style: none;
        }

        .aimentor-onboarding-card__step {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 10px;
        }

        .aimentor-onboarding-card__step:last-child {
            margin-bottom: 0;
        }

        .aimentor-onboarding-card__step .dashicons {
            margin-top: 2px;
            font-size: 18px;
        }

        .aimentor-onboarding-card__step.is-complete .dashicons {
            color: #1DA866;
        }

        .aimentor-onboarding-card__step.is-pending .dashicons {
            color: #2271b1;
        }

        .aimentor-onboarding-card__step p {
            margin: 2px 0 0;
        }
    </style>

    <p class="description">Connect to your preferred AI provider and unlock AI-powered page building! <a href="https://jagjourney.com/" target="_blank">By AiMentor</a></p>

    <?php
    $defaults       = aimentor_get_default_options();
    $usage_metrics  = isset( $aimentor_usage_metrics ) && is_array( $aimentor_usage_metrics ) ? $aimentor_usage_metrics : aimentor_get_provider_usage_summary();
    $usage_providers = isset( $usage_metrics['providers'] ) && is_array( $usage_metrics['providers'] ) ? $usage_metrics['providers'] : [];
    $provider       = get_option( 'aimentor_provider', $defaults['aimentor_provider'] );
    $api_keys       = [
            'grok'   => get_option( 'aimentor_xai_api_key' ),
            'openai' => get_option( 'aimentor_openai_api_key' ),
    ];
    $models         = aimentor_get_provider_models();
    $allowed_models = aimentor_get_allowed_provider_models();
    $document_context_blueprint = aimentor_get_document_context_blueprint();
    $document_provider_defaults = aimentor_get_document_provider_defaults();
    $provider_labels_map = aimentor_get_provider_labels();
    $page_type_defaults = isset( $document_provider_defaults['page_types'] ) && is_array( $document_provider_defaults['page_types'] )
            ? $document_provider_defaults['page_types']
            : [];
    $page_type_blueprint = isset( $document_context_blueprint['page_types'] ) && is_array( $document_context_blueprint['page_types'] )
            ? $document_context_blueprint['page_types']
            : [];
    $combined_page_types = $page_type_blueprint;

    foreach ( $page_type_defaults as $post_type => $defaults_entry ) {
            if ( isset( $combined_page_types[ $post_type ] ) ) {
                    continue;
            }

            $template_map = [];

            if ( isset( $defaults_entry['templates'] ) && is_array( $defaults_entry['templates'] ) ) {
                    foreach ( $defaults_entry['templates'] as $template_file => $template_entry ) {
                            $template_map[ $template_file ] = [
                                    'key'   => 'template:' . $template_file,
                                    'label' => $template_file,
                            ];
                    }
            }

            $combined_page_types[ $post_type ] = [
                    'key'       => 'post_type:' . $post_type,
                    'label'     => ucfirst( trim( str_replace( [ '_', '-' ], ' ', (string) $post_type ) ) ),
                    'templates' => $template_map,
            ];
    }
    $brand_preferences = aimentor_get_brand_preferences();
    $grok_model_labels = [
            'grok-3-mini' => __( 'Grok 3 Mini (Fast)', 'aimentor' ),
            'grok-3-beta' => __( 'Grok 3 Beta (Balanced) ★', 'aimentor' ),
            'grok-3'      => __( 'Grok 3 (Standard)', 'aimentor' ),
            'grok-4-mini' => __( 'Grok 4 Mini (Premium)', 'aimentor' ),
            'grok-4'      => __( 'Grok 4 (Flagship)', 'aimentor' ),
            'grok-4-code' => __( 'Grok 4 Code', 'aimentor' ),
    ];
    $openai_model_labels = [
            'gpt-4o-mini'  => __( 'GPT-4o mini (Balanced) ★', 'aimentor' ),
            'gpt-4o'       => __( 'GPT-4o (Flagship)', 'aimentor' ),
            'gpt-4.1'      => __( 'GPT-4.1 (Reasoning)', 'aimentor' ),
            'gpt-4.1-mini' => __( 'GPT-4.1 mini (Fast)', 'aimentor' ),
            'gpt-4.1-nano' => __( 'GPT-4.1 nano (Edge)', 'aimentor' ),
            'o4-mini'      => __( 'o4-mini (Preview)', 'aimentor' ),
            'o4'           => __( 'o4 (Preview)', 'aimentor' ),
    ];
    $provider_statuses = aimentor_get_provider_test_statuses();
    $provider_status_views = [];

    foreach ( aimentor_get_provider_labels() as $provider_key => $provider_label ) {
            $current_status = $provider_statuses[ $provider_key ] ?? [ 'status' => '', 'message' => '', 'timestamp' => 0 ];
            $provider_status_views[ $provider_key ] = aimentor_format_provider_status_for_display( $provider_key, $current_status );
    }

    $health_checks_enabled   = aimentor_health_checks_enabled();
    $health_check_recipients = aimentor_sanitize_health_check_recipients( get_option( 'aimentor_health_check_recipients', $defaults['aimentor_health_check_recipients'] ) );
    $health_check_threshold  = aimentor_get_health_check_failure_threshold();

    $has_api_key           = ! empty( $api_keys['grok'] ) || ! empty( $api_keys['openai'] );
    $provider_tested       = (bool) get_option( 'aimentor_api_tested', $defaults['aimentor_api_tested'] );
    $onboarding_dismissed  = 'yes' === get_option( 'aimentor_onboarding_dismissed', $defaults['aimentor_onboarding_dismissed'] );
    $should_show_onboarding = ! $onboarding_dismissed && ( ! $has_api_key || ! $provider_tested );

    if ( $should_show_onboarding ) :
            $onboarding_steps = [
                    [
                            'label'       => __( 'Add your API key', 'aimentor' ),
                            'description' => __( 'Paste your xAI or OpenAI key into the fields below.', 'aimentor' ),
                            'completed'   => $has_api_key,
                    ],
                    [
                            'label'       => __( 'Test your provider', 'aimentor' ),
                            'description' => __( 'Use “Test Connection” to confirm the integration is ready.', 'aimentor' ),
                            'completed'   => $provider_tested,
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

    <section id="aimentor-usage-metrics" class="aimentor-usage-card jaggrok-usage-card" data-generated-at="<?php echo esc_attr( $usage_metrics['generated_at'] ?? '' ); ?>" aria-live="polite">
        <header class="aimentor-usage-card__header jaggrok-usage-card__header">
            <h2 class="aimentor-usage-card__title jaggrok-usage-card__title"><?php esc_html_e( 'Usage snapshot', 'aimentor' ); ?></h2>
            <?php if ( ! empty( $usage_metrics['generated_at_human'] ) ) : ?>
            <span class="aimentor-usage-card__timestamp jaggrok-usage-card__timestamp" data-metric="generated_at">
                <?php echo esc_html( sprintf( __( 'Updated %s', 'aimentor' ), $usage_metrics['generated_at_human'] ) ); ?>
            </span>
            <?php endif; ?>
        </header>
        <p class="description aimentor-usage-card__description jaggrok-usage-card__description"><?php esc_html_e( 'Counts reset automatically after the transient expires (roughly every 24 hours). Data never leaves your site.', 'aimentor' ); ?></p>
        <div class="aimentor-usage-grid jaggrok-usage-grid">
            <?php foreach ( $usage_providers as $usage_provider => $usage_data ) :
                $usage_label       = isset( $usage_data['label'] ) ? $usage_data['label'] : ucfirst( $usage_provider );
                $total_requests    = isset( $usage_data['total_requests'] ) ? absint( $usage_data['total_requests'] ) : 0;
                $success_total     = isset( $usage_data['success_total'] ) ? absint( $usage_data['success_total'] ) : 0;
                $error_total       = isset( $usage_data['error_total'] ) ? absint( $usage_data['error_total'] ) : 0;
                $last_event_label  = isset( $usage_data['last_event_human'] ) ? $usage_data['last_event_human'] : '';
                $origin_label      = isset( $usage_data['origin_label'] ) ? $usage_data['origin_label'] : '';
                $context_summary   = isset( $usage_data['context_summary'] ) ? $usage_data['context_summary'] : '';
                $event_display     = $last_event_label ? $last_event_label : __( 'No activity yet', 'aimentor' );
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

    <form method="post" action="options.php">
        <?php settings_fields( 'aimentor_settings' ); ?>
        <p class="description aimentor-defaults-notice jaggrok-defaults-notice">
                <?php
                printf(
                        /* translators: 1: Grok model, 2: OpenAI model, 3: max tokens */
                        esc_html__( 'Defaults: Grok starts on %1$s, OpenAI uses %2$s, and requests are capped at %3$s tokens until you change them.', 'aimentor' ),
                        esc_html( strtoupper( $defaults['aimentor_model'] ) ),
                        esc_html( strtoupper( $defaults['aimentor_openai_model'] ) ),
                        esc_html( number_format_i18n( $defaults['aimentor_max_tokens'] ) )
                );
                ?>
        </p>
        <table class="form-table">
            <tr>
                <th scope="row"><label><?php esc_html_e( 'Provider', 'aimentor' ); ?></label></th>
                <td>
                    <fieldset id="aimentor-provider-selector" class="aimentor-provider-fieldset jaggrok-provider-fieldset">
                        <legend class="screen-reader-text"><?php esc_html_e( 'AI provider', 'aimentor' ); ?></legend>
                        <label class="aimentor-provider-option jaggrok-provider-option">
                            <input type="radio" name="aimentor_provider" value="grok" <?php checked( $provider, 'grok' ); ?> />
                            <span class="aimentor-provider-name jaggrok-provider-name"><?php esc_html_e( 'xAI Grok', 'aimentor' ); ?></span>
                            <span class="aimentor-provider-badge jaggrok-provider-badge" style="background-color:#1E1E1E;" aria-hidden="true">
                                <?php esc_html_e( 'xAI', 'aimentor' ); ?>
                            </span>
                            <span class="description aimentor-provider-summary jaggrok-provider-summary"><?php esc_html_e( "Creator tier includes roughly 30 requests per minute and bundled usage. Confirm current allowances on xAI's pricing page.", 'aimentor' ); ?></span>
                        </label>
                        <label class="aimentor-provider-option jaggrok-provider-option">
                            <input type="radio" name="aimentor_provider" value="openai" <?php checked( $provider, 'openai' ); ?> />
                            <span class="aimentor-provider-name jaggrok-provider-name"><?php esc_html_e( 'OpenAI', 'aimentor' ); ?></span>
                            <span class="aimentor-provider-badge jaggrok-provider-badge" style="background-color:#2B8CFF;" aria-hidden="true">
                                <?php esc_html_e( 'OpenAI', 'aimentor' ); ?>
                            </span>
                            <span class="description aimentor-provider-summary jaggrok-provider-summary"><?php esc_html_e( 'Pay-as-you-go billing with token-based rates. Review OpenAI pricing for the latest per-model costs.', 'aimentor' ); ?></span>
                        </label>
                    </fieldset>
                    <div class="aimentor-provider-help jaggrok-provider-help" data-provider="grok" aria-live="polite">
                        <p class="description"><?php esc_html_e( 'Grok API access is part of the Creator subscription. Typical soft limits hover around 30 requests/minute; usage beyond that may queue. See the xAI pricing page for up-to-date information.', 'aimentor' ); ?></p>
                        <p class="description"><a href="<?php echo esc_url( 'https://x.ai/pricing' ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View xAI pricing & limits', 'aimentor' ); ?></a></p>
                    </div>
                    <div class="aimentor-provider-help jaggrok-provider-help" data-provider="openai" aria-live="polite">
                        <p class="description"><?php esc_html_e( 'OpenAI accounts charge only for tokens used. Most teams start with generous rate limits that scale automatically after billing verification.', 'aimentor' ); ?></p>
                        <p class="description"><a href="<?php echo esc_url( 'https://openai.com/pricing' ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Review OpenAI pricing & limits', 'aimentor' ); ?></a></p>
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="aimentor_xai_api_key"><?php esc_html_e( 'API Credentials', 'aimentor' ); ?></label></th>
                <td>
                    <div class="aimentor-provider-group jaggrok-provider-group" data-provider="grok">
                        <label for="aimentor_xai_api_key" class="aimentor-provider-group__label jaggrok-provider-group__label"><?php esc_html_e( 'xAI API Key', 'aimentor' ); ?> <span class="required">*</span></label>
                        <div class="aimentor-api-key-container jaggrok-api-key-container">
                            <input type="password" id="aimentor_xai_api_key" name="aimentor_xai_api_key" value="<?php echo esc_attr( $api_keys['grok'] ); ?>" class="regular-text aimentor-api-input jaggrok-api-input" autocomplete="off" />
                            <button type="button" class="button button-secondary aimentor-toggle-visibility jaggrok-toggle-visibility" data-target="aimentor_xai_api_key" data-show-label="<?php esc_attr_e( 'Show', 'aimentor' ); ?>" data-hide-label="<?php esc_attr_e( 'Hide', 'aimentor' ); ?>" aria-label="<?php esc_attr_e( 'Toggle xAI API key visibility', 'aimentor' ); ?>" aria-pressed="false"><?php esc_html_e( 'Show', 'aimentor' ); ?></button>
                        </div>
                        <p class="description"><a href="<?php echo esc_url( 'https://x.ai/api' ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Generate an API key', 'aimentor' ); ?></a></p>
                        <p class="description">
                            <button type="button" class="button aimentor-test-provider jaggrok-test-provider" data-provider="grok"><?php esc_html_e( 'Test Connection', 'aimentor' ); ?></button>
                        </p>
                        <?php $grok_status = $provider_status_views['grok']; ?>
                        <div class="aimentor-provider-status jaggrok-provider-status" data-provider="grok" data-timestamp="<?php echo esc_attr( $grok_status['timestamp'] ); ?>" aria-live="polite">
                            <span class="aimentor-status-badge aimentor-status-badge--<?php echo esc_attr( $grok_status['badge_state'] ); ?>" data-provider="grok"><?php echo esc_html( $grok_status['badge_label'] ); ?></span>
                            <span class="aimentor-status-description jaggrok-status-description" data-provider="grok"><?php echo esc_html( $grok_status['description'] ); ?></span>
                        </div>
                    </div>
                    <div class="aimentor-provider-group jaggrok-provider-group" data-provider="openai">
                        <label for="aimentor_openai_api_key" class="aimentor-provider-group__label jaggrok-provider-group__label"><?php esc_html_e( 'OpenAI API Key', 'aimentor' ); ?></label>
                        <div class="aimentor-api-key-container jaggrok-api-key-container">
                            <input type="password" id="aimentor_openai_api_key" name="aimentor_openai_api_key" value="<?php echo esc_attr( $api_keys['openai'] ); ?>" class="regular-text aimentor-api-input jaggrok-api-input" autocomplete="off" placeholder="<?php esc_attr_e( 'sk-...', 'aimentor' ); ?>" />
                            <button type="button" class="button button-secondary aimentor-toggle-visibility jaggrok-toggle-visibility" data-target="aimentor_openai_api_key" data-show-label="<?php esc_attr_e( 'Show', 'aimentor' ); ?>" data-hide-label="<?php esc_attr_e( 'Hide', 'aimentor' ); ?>" aria-label="<?php esc_attr_e( 'Toggle OpenAI API key visibility', 'aimentor' ); ?>" aria-pressed="false"><?php esc_html_e( 'Show', 'aimentor' ); ?></button>
                        </div>
                        <p class="description"><a href="<?php echo esc_url( 'https://platform.openai.com/account/api-keys' ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Create an API key', 'aimentor' ); ?></a></p>
                        <p class="description">
                            <button type="button" class="button aimentor-test-provider jaggrok-test-provider" data-provider="openai"><?php esc_html_e( 'Test Connection', 'aimentor' ); ?></button>
                        </p>
                        <?php $openai_status = $provider_status_views['openai']; ?>
                        <div class="aimentor-provider-status jaggrok-provider-status" data-provider="openai" data-timestamp="<?php echo esc_attr( $openai_status['timestamp'] ); ?>" aria-live="polite">
                            <span class="aimentor-status-badge aimentor-status-badge--<?php echo esc_attr( $openai_status['badge_state'] ); ?>" data-provider="openai"><?php echo esc_html( $openai_status['badge_label'] ); ?></span>
                            <span class="aimentor-status-description jaggrok-status-description" data-provider="openai"><?php echo esc_html( $openai_status['description'] ); ?></span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Connection Monitoring', 'aimentor' ); ?></th>
                <td>
                    <fieldset>
                        <legend class="screen-reader-text"><?php esc_html_e( 'Connection monitoring preferences', 'aimentor' ); ?></legend>
                        <input type="hidden" name="aimentor_enable_health_checks" value="no" />
                        <label>
                            <input type="checkbox" name="aimentor_enable_health_checks" value="yes" <?php checked( $health_checks_enabled ); ?> />
                            <?php esc_html_e( 'Run daily provider connection checks', 'aimentor' ); ?>
                        </label>
                        <p class="description"><?php echo esc_html( sprintf( __( 'AiMentor will re-test stored API keys daily and alert you after %d consecutive failures.', 'aimentor' ), $health_check_threshold ) ); ?></p>
                    </fieldset>
                    <label class="screen-reader-text" for="aimentor_health_check_recipients"><?php esc_html_e( 'Health check alert recipients', 'aimentor' ); ?></label>
                    <input type="text" id="aimentor_health_check_recipients" name="aimentor_health_check_recipients" value="<?php echo esc_attr( $health_check_recipients ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'name@example.com, team@example.com', 'aimentor' ); ?>" />
                    <p class="description"><?php esc_html_e( 'Leave blank to notify every site administrator using the built-in WordPress email system.', 'aimentor' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Default Model', 'aimentor' ); ?></th>
                <td>
                    <div class="aimentor-provider-group jaggrok-provider-group" data-provider="grok">
                        <label class="screen-reader-text" for="aimentor_provider_models_grok"><?php esc_html_e( 'xAI Grok default model', 'aimentor' ); ?></label>
                        <select name="aimentor_provider_models[grok]" id="aimentor_provider_models_grok" class="regular-text">
                            <?php foreach ( array_keys( $allowed_models['grok'] ) as $model_key ) : ?>
                                <option value="<?php echo esc_attr( $model_key ); ?>" <?php selected( $models['grok'], $model_key ); ?>><?php echo esc_html( $grok_model_labels[ $model_key ] ?? strtoupper( $model_key ) ); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php esc_html_e( 'Grok 3 Beta is a reliable balance of quality and speed for most Elementor flows.', 'aimentor' ); ?></p>
                    </div>
                    <div class="aimentor-provider-group jaggrok-provider-group" data-provider="openai">
                        <label class="screen-reader-text" for="aimentor_provider_models_openai"><?php esc_html_e( 'OpenAI default model', 'aimentor' ); ?></label>
                        <select name="aimentor_provider_models[openai]" id="aimentor_provider_models_openai" class="regular-text">
                            <?php foreach ( array_keys( $allowed_models['openai'] ) as $model_key ) : ?>
                                <option value="<?php echo esc_attr( $model_key ); ?>" <?php selected( $models['openai'], $model_key ); ?>><?php echo esc_html( $openai_model_labels[ $model_key ] ?? strtoupper( $model_key ) ); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php esc_html_e( 'GPT-4o mini delivers strong reasoning with lower cost; upgrade as your budget allows.', 'aimentor' ); ?></p>
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Context Defaults', 'aimentor' ); ?></th>
                <td>
                    <p class="description"><?php esc_html_e( 'Choose which provider and model should load automatically for each Elementor document type.', 'aimentor' ); ?></p>
                    <?php
                    $global_defaults      = isset( $document_provider_defaults['default'] ) ? $document_provider_defaults['default'] : [];
                    $global_provider      = isset( $global_defaults['provider'] ) ? $global_defaults['provider'] : 'grok';
                    $global_model         = isset( $global_defaults['model'] ) ? $global_defaults['model'] : '';
                    $has_page_type_groups = ! empty( $combined_page_types );
                    ?>
                    <table class="widefat striped aimentor-context-defaults-table" style="max-width:680px;">
                        <thead>
                            <tr>
                                <th scope="col"><?php esc_html_e( 'Context', 'aimentor' ); ?></th>
                                <th scope="col"><?php esc_html_e( 'Provider', 'aimentor' ); ?></th>
                                <th scope="col"><?php esc_html_e( 'Model', 'aimentor' ); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <strong><?php esc_html_e( 'Global Default', 'aimentor' ); ?></strong>
                                    <p class="description" style="margin:4px 0 0;">
                                        <?php esc_html_e( 'Used when no specific mapping is found.', 'aimentor' ); ?>
                                    </p>
                                </td>
                                <td>
                                    <label class="screen-reader-text" for="aimentor-context-provider-default"><?php esc_html_e( 'Preferred provider', 'aimentor' ); ?></label>
                                    <select name="aimentor_document_provider_defaults[default][provider]" id="aimentor-context-provider-default" class="aimentor-context-provider" style="min-width:160px;">
                                        <?php foreach ( $provider_labels_map as $provider_key => $provider_label ) : ?>
                                            <option value="<?php echo esc_attr( $provider_key ); ?>" <?php selected( $global_provider, $provider_key ); ?>><?php echo esc_html( $provider_label ); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td>
                                    <label class="screen-reader-text" for="aimentor-context-model-default"><?php esc_html_e( 'Preferred model', 'aimentor' ); ?></label>
                                    <select name="aimentor_document_provider_defaults[default][model]" id="aimentor-context-model-default" class="aimentor-context-model" style="min-width:200px;">
                                        <?php foreach ( $allowed_models as $provider_key => $model_group ) :
                                            $group_label = isset( $provider_labels_map[ $provider_key ] ) ? $provider_labels_map[ $provider_key ] : strtoupper( $provider_key );
                                        ?>
                                        <optgroup label="<?php echo esc_attr( $group_label ); ?>">
                                            <?php foreach ( $model_group as $model_key => $model_label ) : ?>
                                                <option value="<?php echo esc_attr( $model_key ); ?>" data-provider="<?php echo esc_attr( $provider_key ); ?>" <?php selected( $global_model, $model_key ); ?>><?php echo esc_html( $model_label ); ?></option>
                                            <?php endforeach; ?>
                                        </optgroup>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <?php if ( $has_page_type_groups ) : ?>
                        <?php foreach ( $combined_page_types as $post_type => $meta ) :
                            $post_type_label    = isset( $meta['label'] ) && '' !== $meta['label'] ? $meta['label'] : ucfirst( (string) $post_type );
                            $post_type_defaults = isset( $page_type_defaults[ $post_type ] ) && is_array( $page_type_defaults[ $post_type ] ) ? $page_type_defaults[ $post_type ] : [];
                            $post_type_default  = isset( $post_type_defaults['default'] ) && is_array( $post_type_defaults['default'] ) ? $post_type_defaults['default'] : $global_defaults;
                            $post_type_provider = isset( $post_type_default['provider'] ) ? $post_type_default['provider'] : $global_provider;
                            $post_type_model    = isset( $post_type_default['model'] ) ? $post_type_default['model'] : $global_model;
                            $template_blueprint = isset( $meta['templates'] ) && is_array( $meta['templates'] ) ? $meta['templates'] : [];
                            $template_default_map = isset( $post_type_defaults['templates'] ) && is_array( $post_type_defaults['templates'] ) ? $post_type_defaults['templates'] : [];
                        ?>
                        <div class="aimentor-context-group" style="margin-top:24px;">
                            <h3 style="margin:0 0 8px;"><?php echo esc_html( sprintf( __( 'Post Type: %s', 'aimentor' ), $post_type_label ) ); ?></h3>
                            <table class="widefat striped aimentor-context-defaults-table" style="max-width:680px;">
                                <thead>
                                    <tr>
                                        <th scope="col"><?php esc_html_e( 'Context', 'aimentor' ); ?></th>
                                        <th scope="col"><?php esc_html_e( 'Provider', 'aimentor' ); ?></th>
                                        <th scope="col"><?php esc_html_e( 'Model', 'aimentor' ); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <strong><?php esc_html_e( 'Post Type Default', 'aimentor' ); ?></strong>
                                            <p class="description" style="margin:4px 0 0;">
                                                <?php esc_html_e( 'Applies to Elementor documents for this type when no specific template match is found.', 'aimentor' ); ?>
                                            </p>
                                        </td>
                                        <td>
                                            <?php $provider_id = 'aimentor-context-provider-' . md5( 'post_type:' . $post_type ); ?>
                                            <label class="screen-reader-text" for="<?php echo esc_attr( $provider_id ); ?>"><?php esc_html_e( 'Preferred provider', 'aimentor' ); ?></label>
                                            <select name="aimentor_document_provider_defaults[page_types][<?php echo esc_attr( $post_type ); ?>][default][provider]" id="<?php echo esc_attr( $provider_id ); ?>" class="aimentor-context-provider" style="min-width:160px;">
                                                <?php foreach ( $provider_labels_map as $provider_key => $provider_label ) : ?>
                                                    <option value="<?php echo esc_attr( $provider_key ); ?>" <?php selected( $post_type_provider, $provider_key ); ?>><?php echo esc_html( $provider_label ); ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <?php $model_id = 'aimentor-context-model-' . md5( 'post_type:' . $post_type ); ?>
                                            <label class="screen-reader-text" for="<?php echo esc_attr( $model_id ); ?>"><?php esc_html_e( 'Preferred model', 'aimentor' ); ?></label>
                                            <select name="aimentor_document_provider_defaults[page_types][<?php echo esc_attr( $post_type ); ?>][default][model]" id="<?php echo esc_attr( $model_id ); ?>" class="aimentor-context-model" style="min-width:200px;">
                                                <?php foreach ( $allowed_models as $provider_key => $model_group ) :
                                                    $group_label = isset( $provider_labels_map[ $provider_key ] ) ? $provider_labels_map[ $provider_key ] : strtoupper( $provider_key );
                                                ?>
                                                <optgroup label="<?php echo esc_attr( $group_label ); ?>">
                                                    <?php foreach ( $model_group as $model_key => $model_label ) : ?>
                                                        <option value="<?php echo esc_attr( $model_key ); ?>" data-provider="<?php echo esc_attr( $provider_key ); ?>" <?php selected( $post_type_model, $model_key ); ?>><?php echo esc_html( $model_label ); ?></option>
                                                    <?php endforeach; ?>
                                                </optgroup>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                    </tr>
                                    <?php if ( ! empty( $template_blueprint ) ) : ?>
                                        <?php foreach ( $template_blueprint as $template_file => $template_meta ) :
                                            $template_label = is_array( $template_meta ) && isset( $template_meta['label'] ) ? $template_meta['label'] : ( is_string( $template_meta ) ? $template_meta : $template_file );
                                            $template_entry    = isset( $template_default_map[ $template_file ] ) && is_array( $template_default_map[ $template_file ] ) ? $template_default_map[ $template_file ] : $post_type_default;
                                            $template_provider = isset( $template_entry['provider'] ) ? $template_entry['provider'] : $post_type_provider;
                                            $template_model    = isset( $template_entry['model'] ) ? $template_entry['model'] : $post_type_model;
                                            $template_provider_id = 'aimentor-context-provider-' . md5( $post_type . '|' . $template_file );
                                            $template_model_id    = 'aimentor-context-model-' . md5( $post_type . '|' . $template_file );
                                        ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo esc_html( sprintf( __( 'Template: %s', 'aimentor' ), $template_label ) ); ?></strong>
                                                <p class="description" style="margin:4px 0 0;">
                                                    <?php esc_html_e( 'Overrides the post type default when the Elementor document uses this template.', 'aimentor' ); ?>
                                                </p>
                                            </td>
                                            <td>
                                                <label class="screen-reader-text" for="<?php echo esc_attr( $template_provider_id ); ?>"><?php esc_html_e( 'Preferred provider', 'aimentor' ); ?></label>
                                                <select name="aimentor_document_provider_defaults[page_types][<?php echo esc_attr( $post_type ); ?>][templates][<?php echo esc_attr( $template_file ); ?>][provider]" id="<?php echo esc_attr( $template_provider_id ); ?>" class="aimentor-context-provider" style="min-width:160px;">
                                                    <?php foreach ( $provider_labels_map as $provider_key => $provider_label ) : ?>
                                                        <option value="<?php echo esc_attr( $provider_key ); ?>" <?php selected( $template_provider, $provider_key ); ?>><?php echo esc_html( $provider_label ); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                            <td>
                                                <label class="screen-reader-text" for="<?php echo esc_attr( $template_model_id ); ?>"><?php esc_html_e( 'Preferred model', 'aimentor' ); ?></label>
                                                <select name="aimentor_document_provider_defaults[page_types][<?php echo esc_attr( $post_type ); ?>][templates][<?php echo esc_attr( $template_file ); ?>][model]" id="<?php echo esc_attr( $template_model_id ); ?>" class="aimentor-context-model" style="min-width:200px;">
                                                    <?php foreach ( $allowed_models as $provider_key => $model_group ) :
                                                        $group_label = isset( $provider_labels_map[ $provider_key ] ) ? $provider_labels_map[ $provider_key ] : strtoupper( $provider_key );
                                                    ?>
                                                    <optgroup label="<?php echo esc_attr( $group_label ); ?>">
                                                        <?php foreach ( $model_group as $model_key => $model_label ) : ?>
                                                            <option value="<?php echo esc_attr( $model_key ); ?>" data-provider="<?php echo esc_attr( $provider_key ); ?>" <?php selected( $template_model, $model_key ); ?>><?php echo esc_html( $model_label ); ?></option>
                                                        <?php endforeach; ?>
                                                    </optgroup>
                                                    <?php endforeach; ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="3">
                                                <p class="description" style="margin:8px 0;">
                                                    <?php esc_html_e( 'No templates detected for this post type. The post type default will apply to all Elementor documents.', 'aimentor' ); ?>
                                                </p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <p class="description" style="margin-top:8px;">
                        <?php esc_html_e( 'Editors inherit the global default when no page type or template mapping is defined.', 'aimentor' ); ?>
                    </p>
                    <script>
                    jQuery(function($) {
                        function updateContextRow($row) {
                            if (!$row || !$row.length) {
                                return;
                            }
                            var $provider = $row.find('.aimentor-context-provider');
                            var $model = $row.find('.aimentor-context-model');
                            if (!$provider.length || !$model.length) {
                                return;
                            }
                            var providerValue = $provider.val();
                            var $options = $model.find('option[data-provider]');
                            var hasEnabled = false;
                            $options.each(function() {
                                var $option = $(this);
                                var optionProvider = $option.data('provider');
                                var isMatch = !providerValue || optionProvider === providerValue;
                                $option.prop('disabled', !isMatch);
                                if (isMatch) {
                                    hasEnabled = true;
                                }
                            });
                            var $selected = $model.find('option:selected');
                            if (!$selected.length || $selected.prop('disabled')) {
                                var $replacement = $options.filter(function() {
                                    return $(this).data('provider') === providerValue;
                                }).first();
                                if ($replacement.length) {
                                    $model.val($replacement.val());
                                } else if (hasEnabled) {
                                    var $fallback = $options.filter(function() {
                                        return !$(this).prop('disabled');
                                    }).first();
                                    if ($fallback.length) {
                                        $model.val($fallback.val());
                                    }
                                } else {
                                    $model.val('');
                                }
                            }
                        }

                        $('.aimentor-context-provider').on('change.aimentorContext', function() {
                            updateContextRow($(this).closest('tr'));
                        }).each(function() {
                            updateContextRow($(this).closest('tr'));
                        });
                    });
                    </script>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Auto-Insert', 'aimentor' ); ?></th>
                <td>
                    <?php $auto = get_option( 'aimentor_auto_insert', 'yes' ); ?>
                    <label><input type="radio" name="aimentor_auto_insert" value="yes" <?php checked( $auto, 'yes' ); ?> /> <?php esc_html_e( 'Yes', 'aimentor' ); ?></label>
                    <label><input type="radio" name="aimentor_auto_insert" value="no" <?php checked( $auto, 'no' ); ?> /> <?php esc_html_e( 'No', 'aimentor' ); ?></label>
                    <p class="description"><?php esc_html_e( 'Automatically add generated sections to the Elementor canvas.', 'aimentor' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Default Style', 'aimentor' ); ?></th>
                <td>
                    <?php $style = get_option( 'aimentor_theme_style', 'modern' ); ?>
                    <select name="aimentor_theme_style">
                        <option value="modern" <?php selected( $style, 'modern' ); ?>><?php esc_html_e( 'Modern', 'aimentor' ); ?></option>
                        <option value="bold" <?php selected( $style, 'bold' ); ?>><?php esc_html_e( 'Bold', 'aimentor' ); ?></option>
                        <option value="minimal" <?php selected( $style, 'minimal' ); ?>><?php esc_html_e( 'Minimal', 'aimentor' ); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="aimentor_primary_color"><?php esc_html_e( 'Brand Primary Color', 'aimentor' ); ?></label></th>
                <td>
                    <?php $primary_color = isset( $brand_preferences['primary_color'] ) ? $brand_preferences['primary_color'] : $defaults['aimentor_primary_color']; ?>
                    <input type="color" id="aimentor_primary_color" name="aimentor_primary_color" value="<?php echo esc_attr( $primary_color ); ?>" />
                    <p class="description"><?php esc_html_e( 'Hints the generator toward your main brand accent color.', 'aimentor' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="aimentor_tone_keywords"><?php esc_html_e( 'Tone Keywords', 'aimentor' ); ?></label></th>
                <td>
                    <?php $tone_keywords = isset( $brand_preferences['tone_keywords'] ) ? $brand_preferences['tone_keywords'] : $defaults['aimentor_tone_keywords']; ?>
                    <textarea id="aimentor_tone_keywords" name="aimentor_tone_keywords" rows="3" class="large-text"><?php echo esc_textarea( $tone_keywords ); ?></textarea>
                    <p class="description"><?php esc_html_e( 'Provide comma-separated adjectives or short phrases to guide the writing voice.', 'aimentor' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Max Tokens', 'aimentor' ); ?></th>
                <td>
                    <input type="number" name="aimentor_max_tokens" value="<?php echo esc_attr( get_option( 'aimentor_max_tokens', 2000 ) ); ?>" min="500" max="8000" class="small-text" /> <?php esc_html_e( 'tokens', 'aimentor' ); ?>
                    <p class="description"><?php esc_html_e( 'Higher values allow for more detailed layouts. Stay within your provider limits.', 'aimentor' ); ?></p>
                </td>
            </tr>
        </table>
        <input type="hidden" name="aimentor_model" id="aimentor_model_legacy" value="<?php echo esc_attr( $models['grok'] ); ?>" />
        <input type="hidden" name="aimentor_openai_model" id="aimentor_openai_model_legacy" value="<?php echo esc_attr( $models['openai'] ); ?>" />
        <?php submit_button(); ?>
    </form>
    <?php
    $history_entries = aimentor_get_generation_history();
    $provider_meta   = aimentor_get_provider_meta_map();
    ?>
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
    <!-- ERROR LOG TABLE -->
    <div class="aimentor-error-log-header">
        <h2><?php esc_html_e( 'Error Log', 'aimentor' ); ?></h2>
        <div class="aimentor-error-log-actions">
            <button type="button" class="button button-secondary" id="aimentor-download-log">
                <?php esc_html_e( 'Download Log', 'aimentor' ); ?>
            </button>
            <button type="button" class="button button-link-delete" id="aimentor-clear-log">
                <?php esc_html_e( 'Clear Log', 'aimentor' ); ?>
            </button>
        </div>
    </div>
    <p id="aimentor-error-log-feedback" class="aimentor-error-log-feedback" role="status" aria-live="polite" hidden></p>
    <?php
    $log_filter_provider = isset( $_GET['provider'] ) ? sanitize_key( wp_unslash( $_GET['provider'] ) ) : '';
    $log_filter_keyword  = isset( $_GET['keyword'] ) ? sanitize_text_field( wp_unslash( $_GET['keyword'] ) ) : '';

    if ( 'all' === $log_filter_provider ) {
        $log_filter_provider = '';
    }

    $log_entries = aimentor_get_error_log_entries(
        [
            'provider' => $log_filter_provider,
            'keyword'  => $log_filter_keyword,
        ]
    );

    $log_rows = aimentor_build_error_log_rows_html(
        $log_entries['entries'],
        [
            'readable'      => $log_entries['readable'],
            'had_filters'   => ( '' !== $log_filter_provider || '' !== $log_filter_keyword ),
            'total_entries' => $log_entries['total_entries'],
        ]
    );
    ?>
    <form id="aimentor-error-log-form" class="aimentor-error-log-form" method="get" action="" data-nonce="<?php echo esc_attr( wp_create_nonce( 'aimentor_error_log' ) ); ?>">
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
    <?php
    $log_file = $log_entries['log_file'];

    if ( ! empty( $log_file ) ) {
        $display_path = wp_normalize_path( $log_file );

        if ( defined( 'ABSPATH' ) ) {
            $display_path = str_replace( wp_normalize_path( ABSPATH ), '', $display_path );
        }

        echo '<p class="description">' . sprintf(
            /* translators: %s: path to the AiMentor error log file */
            esc_html__( 'Error logs are stored at %s.', 'aimentor' ),
            '<code>' . esc_html( $display_path ) . '</code>'
        ) . '</p>';
    }
    ?>
</div>

<style>
.aimentor-settings-title { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
.aimentor-settings-logo { height: 48px; width: 48px; border-radius: 12px; box-shadow: 0 4px 18px rgba(64, 84, 178, 0.25); }
.aimentor-settings-heading { font-size: 26px; font-weight: 600; color: #1f2937; }
.aimentor-settings-badge { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 999px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%); color: #ffffff; }
.aimentor-provider-badge { display: inline-flex; align-items: center; padding: 2px 8px; margin-left: 6px; border-radius: 999px; font-size: 11px; font-weight: 600; color: #ffffff; text-transform: uppercase; letter-spacing: 0.05em; }
.aimentor-error-log-header { margin-top: 32px; display: flex; flex-wrap: wrap; align-items: center; gap: 12px; justify-content: space-between; }
.aimentor-error-log-actions { display: flex; gap: 8px; align-items: center; }
.aimentor-error-log-actions.is-busy button { pointer-events: none; }
.aimentor-error-log-feedback { margin: 8px 0 0; font-size: 13px; }
.aimentor-error-log-feedback.is-success { color: #116329; }
.aimentor-error-log-feedback.is-error { color: #b32d2e; }
.aimentor-error-log-form { margin-top: 12px; }
.aimentor-history-card { margin: 32px 0; padding: 20px; border: 1px solid #dcdcdc; border-radius: 8px; background: #fff; }
.aimentor-history-card__title { margin-top: 0; margin-bottom: 12px; font-size: 20px; font-weight: 600; }
.aimentor-history-table td span[title] { cursor: help; }
.aimentor-history-provider-label { font-weight: 600; margin-right: 6px; }
.aimentor-error-log-form.is-loading { opacity: 0.7; pointer-events: none; }
.aimentor-error-log-filters { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; margin-bottom: 12px; }
.aimentor-error-log-filters label { font-weight: 600; }
.aimentor-error-log-filters input[type="search"] { min-width: 220px; }
.required { color: #d63638; }
.aimentor-provider-fieldset, .jaggrok-provider-fieldset { border: 1px solid #ccd0d4; padding: 12px; max-width: 640px; background: #fff; border-radius: 6px; }
.aimentor-provider-option, .jaggrok-provider-option { display: block; margin-bottom: 12px; }
.aimentor-provider-option:last-of-type, .jaggrok-provider-option:last-of-type { margin-bottom: 0; }
.aimentor-provider-name, .jaggrok-provider-name { font-weight: 600; display: inline-block; margin-right: 6px; }
.aimentor-provider-summary, .jaggrok-provider-summary { display: block; margin-left: 26px; }
.aimentor-provider-group, .jaggrok-provider-group { margin-bottom: 16px; }
.js .aimentor-provider-group, .js .jaggrok-provider-group { display: none; }
.aimentor-provider-group__label, .jaggrok-provider-group__label { font-weight: 600; display: block; margin-bottom: 4px; }
.aimentor-provider-help, .jaggrok-provider-help { margin-top: 8px; max-width: 640px; }
.js .aimentor-provider-help, .js .jaggrok-provider-help { display: none; }
.aimentor-api-key-container, .jaggrok-api-key-container { display: flex; align-items: center; gap: 8px; max-width: 420px; }
.aimentor-api-input, .jaggrok-api-input { width: 100%; }
.aimentor-provider-status, .jaggrok-provider-status { display: flex; align-items: center; gap: 8px; margin-top: 6px; max-width: 520px; }
.aimentor-status-badge, .jaggrok-status-badge { display: inline-flex; align-items: center; padding: 2px 10px; border-radius: 999px; font-weight: 600; font-size: 12px; letter-spacing: .01em; }
.aimentor-status-badge--success, .jaggrok-status-badge--success { background-color: #dff4e2; color: #116329; }
.aimentor-status-badge--error, .jaggrok-status-badge--error { background-color: #fce1e1; color: #b32d2e; }
.aimentor-status-badge--idle, .jaggrok-status-badge--idle { background-color: #e7ecf3; color: #2c3e50; }
.aimentor-status-badge--pending, .jaggrok-status-badge--pending { background-color: #fef3c7; color: #8a6110; }
.aimentor-status-description, .jaggrok-status-description { display: inline-block; font-size: 13px; line-height: 1.5; }
.aimentor-usage-card, .jaggrok-usage-card { margin: 32px 0; padding: 20px; border: 1px solid #dcdcdc; border-radius: 8px; background: #fff; }
.aimentor-usage-card__header, .jaggrok-usage-card__header { display: flex; justify-content: space-between; align-items: baseline; gap: 12px; }
.aimentor-usage-card__title, .jaggrok-usage-card__title { margin: 0; font-size: 20px; font-weight: 600; }
.aimentor-usage-card__timestamp, .jaggrok-usage-card__timestamp { font-size: 12px; color: #6b7280; font-style: italic; }
.aimentor-usage-card__description, .jaggrok-usage-card__description { margin-top: 8px; max-width: 640px; }
.aimentor-usage-grid, .jaggrok-usage-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-top: 16px; }
.aimentor-usage-provider, .jaggrok-usage-provider { border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px; background: #f9fafb; display: flex; flex-direction: column; gap: 10px; }
.aimentor-usage-provider__title, .jaggrok-usage-provider__title { margin: 0; font-size: 16px; font-weight: 600; color: #1f2937; }
.aimentor-usage-provider__stats, .jaggrok-usage-provider__stats { display: flex; gap: 12px; }
.aimentor-usage-stat, .jaggrok-usage-stat { flex: 1 1 auto; background: #fff; border-radius: 6px; padding: 10px; border: 1px solid #e5e7eb; text-align: center; }
.aimentor-usage-stat__label, .jaggrok-usage-stat__label { display: block; font-size: 12px; text-transform: uppercase; letter-spacing: 0.04em; color: #6b7280; margin-bottom: 4px; }
.aimentor-usage-stat__value, .jaggrok-usage-stat__value { font-size: 18px; font-weight: 600; color: #111827; }
.aimentor-usage-provider__meta, .jaggrok-usage-provider__meta { margin: 0; font-size: 13px; color: #374151; }
.aimentor-usage-provider__context, .jaggrok-usage-provider__context { margin: 0; font-size: 12px; color: #6b7280; }
</style>

<script>
    jQuery(document).ready(function($) {
        var $providerInputs = $('input[name="aimentor_provider"]');

        function toggleProvider(provider) {
            $('.aimentor-provider-group').hide().attr('aria-hidden', 'true');
            $('.aimentor-provider-group[data-provider="' + provider + '"]').show().attr('aria-hidden', 'false');
            $('.aimentor-provider-help').hide().attr('aria-hidden', 'true');
            $('.aimentor-provider-help[data-provider="' + provider + '"]').show().attr('aria-hidden', 'false');
        }

        var initialProvider = $providerInputs.filter(':checked').val() || 'grok';
        toggleProvider(initialProvider);

        $providerInputs.on('change', function() {
            toggleProvider($(this).val());
        });

        $('.aimentor-toggle-visibility').on('click', function() {
            var $button = $(this);
            var targetId = $button.data('target');
            var $input = $('#' + targetId);

            if ( ! $input.length ) {
                return;
            }

            var isPassword = $input.attr('type') === 'password';
            $input.attr('type', isPassword ? 'text' : 'password');

            var showLabel = $button.data('show-label');
            var hideLabel = $button.data('hide-label');
            $button.attr('aria-pressed', isPassword ? 'true' : 'false');
            $button.text(isPassword ? hideLabel : showLabel);
        });

        $('#aimentor_provider_models_grok').on('change', function() {
            $('#aimentor_model_legacy').val($(this).val());
        });

        $('#aimentor_provider_models_openai').on('change', function() {
            $('#aimentor_openai_model_legacy').val($(this).val());
        });
    });
</script>
