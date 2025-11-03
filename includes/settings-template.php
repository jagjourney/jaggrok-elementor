
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
