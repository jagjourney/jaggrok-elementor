<div class="aimentor-settings-layout">
    <div class="aimentor-settings-main">
        <form method="post" action="options.php" class="aimentor-settings-form aimentor-settings-form--provider">
            <?php settings_fields( 'aimentor_settings' ); ?>
            <table class="form-table">
                <?php if ( ! empty( $is_multisite_instance ) && ! empty( $is_network_admin ) ) : ?>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Network enforcement', 'aimentor' ); ?></th>
                    <td>
                        <input type="hidden" name="aimentor_network_lock_provider_models" value="no" />
                        <label>
                            <input type="checkbox" name="aimentor_network_lock_provider_models" value="yes" <?php checked( $network_lock_enabled ); ?> />
                            <?php esc_html_e( 'Apply provider and model defaults to every site in this network.', 'aimentor' ); ?>
                        </label>
                        <p class="description"><?php esc_html_e( 'When enabled, local site administrators cannot change provider or model defaults and the values saved here are shared everywhere.', 'aimentor' ); ?></p>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <th scope="row"><label><?php esc_html_e( 'Provider', 'aimentor' ); ?></label></th>
                    <td>
                        <?php if ( $provider_controls_locked ) : ?>
                            <p class="description"><?php esc_html_e( 'Provider selection is locked by a network administrator.', 'aimentor' ); ?></p>
                        <?php endif; ?>
                        <fieldset class="aimentor-provider-fieldset jaggrok-provider-fieldset" role="radiogroup" aria-label="<?php esc_attr_e( 'Preferred AI provider', 'aimentor' ); ?>">
                            <label class="aimentor-provider-option jaggrok-provider-option">
                                <input type="radio" name="aimentor_provider" value="grok" <?php checked( $provider, 'grok' ); ?> <?php disabled( $provider_controls_locked ); ?> />
                                <span class="aimentor-provider-name jaggrok-provider-name"><?php esc_html_e( 'xAI Grok', 'aimentor' ); ?></span>
                                <span class="aimentor-provider-badge jaggrok-provider-badge" style="background-color:#1E1E1E;" aria-hidden="true">xAI</span>
                                <span class="description aimentor-provider-summary jaggrok-provider-summary"><?php esc_html_e( 'Great for fast experimentation with flexible monthly usage tiers.', 'aimentor' ); ?></span>
                            </label>
                            <label class="aimentor-provider-option jaggrok-provider-option">
                                <input type="radio" name="aimentor_provider" value="anthropic" <?php checked( $provider, 'anthropic' ); ?> <?php disabled( $provider_controls_locked ); ?> />
                                <span class="aimentor-provider-name jaggrok-provider-name"><?php esc_html_e( 'Anthropic Claude', 'aimentor' ); ?></span>
                                <span class="aimentor-provider-badge jaggrok-provider-badge" style="background-color:#FF5C35;" aria-hidden="true"><?php esc_html_e( 'Claude', 'aimentor' ); ?></span>
                                <span class="description aimentor-provider-summary jaggrok-provider-summary"><?php esc_html_e( 'Enterprise-grade safeguards with generous team allowances. Review Anthropic pricing for seat and usage tiers.', 'aimentor' ); ?></span>
                            </label>
                            <label class="aimentor-provider-option jaggrok-provider-option">
                                <input type="radio" name="aimentor_provider" value="openai" <?php checked( $provider, 'openai' ); ?> <?php disabled( $provider_controls_locked ); ?> />
                                <span class="aimentor-provider-name jaggrok-provider-name"><?php esc_html_e( 'OpenAI', 'aimentor' ); ?></span>
                                <span class="aimentor-provider-badge jaggrok-provider-badge" style="background-color:#2B8CFF;" aria-hidden="true"><?php esc_html_e( 'OpenAI', 'aimentor' ); ?></span>
                                <span class="description aimentor-provider-summary jaggrok-provider-summary"><?php esc_html_e( 'Pay-as-you-go billing with token-based rates. Review OpenAI pricing for the latest per-model costs.', 'aimentor' ); ?></span>
                            </label>
                        </fieldset>
                        <?php if ( $provider_controls_locked ) : ?>
                            <input type="hidden" name="aimentor_provider" value="<?php echo esc_attr( $provider ); ?>" />
                        <?php endif; ?>
                        <div class="aimentor-provider-help jaggrok-provider-help" data-provider="grok" aria-live="polite">
                            <p class="description"><?php esc_html_e( 'Grok API access is part of the Creator subscription. Typical soft limits hover around 30 requests/minute; usage beyond that may queue. See the xAI pricing page for up-to-date information.', 'aimentor' ); ?></p>
                            <p class="description"><a href="<?php echo esc_url( 'https://x.ai/pricing' ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View xAI pricing & limits', 'aimentor' ); ?></a></p>
                        </div>
                        <div class="aimentor-provider-help jaggrok-provider-help" data-provider="anthropic" aria-live="polite">
                            <p class="description"><?php esc_html_e( 'Claude plans combine usage-based billing with workspace seats. Workspace admins can assign API keys per project for granular control.', 'aimentor' ); ?></p>
                            <p class="description"><a href="<?php echo esc_url( 'https://www.anthropic.com/pricing' ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Review Anthropic pricing & limits', 'aimentor' ); ?></a></p>
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
                            <?php
                            $grok_status         = isset( $provider_status_views['grok'] ) ? $provider_status_views['grok'] : aimentor_format_provider_status_for_display( 'grok', [] );
                            $grok_history_json   = isset( $grok_status['history'] ) ? wp_json_encode( $grok_status['history'] ) : '[]';
                            $grok_success_count  = isset( $grok_status['success_count'] ) ? absint( $grok_status['success_count'] ) : 0;
                            $grok_failure_count  = isset( $grok_status['failure_count'] ) ? absint( $grok_status['failure_count'] ) : 0;
                            $grok_total_count    = isset( $grok_status['total_count'] ) ? absint( $grok_status['total_count'] ) : ( $grok_success_count + $grok_failure_count );
                            $grok_success_rate   = isset( $grok_status['success_rate'] ) && is_numeric( $grok_status['success_rate'] ) ? (string) $grok_status['success_rate'] : '';
                            $grok_metrics_label  = isset( $grok_status['metrics_label'] ) && $grok_status['metrics_label'] ? $grok_status['metrics_label'] : __( 'No connection tests recorded yet.', 'aimentor' );
                            $grok_summary_label  = isset( $grok_status['summary_label'] ) && $grok_status['summary_label'] ? $grok_status['summary_label'] : __( 'No tests yet', 'aimentor' );
                            ?>
                            <div class="aimentor-provider-status jaggrok-provider-status" data-provider="grok" data-timestamp="<?php echo esc_attr( $grok_status['timestamp'] ); ?>" data-success-count="<?php echo esc_attr( $grok_success_count ); ?>" data-failure-count="<?php echo esc_attr( $grok_failure_count ); ?>" data-total-count="<?php echo esc_attr( $grok_total_count ); ?>" data-success-rate="<?php echo esc_attr( $grok_success_rate ); ?>" data-history="<?php echo esc_attr( $grok_history_json ); ?>" data-provider-label="<?php echo esc_attr( $provider_labels_map['grok'] ?? 'Grok' ); ?>" data-metrics-label="<?php echo esc_attr( $grok_metrics_label ); ?>" data-summary-label="<?php echo esc_attr( $grok_summary_label ); ?>" aria-live="polite">
                                <span class="aimentor-status-badge aimentor-status-badge--<?php echo esc_attr( $grok_status['badge_state'] ); ?>" data-provider="grok"><?php echo esc_html( $grok_status['badge_label'] ); ?></span>
                                <span class="aimentor-status-trend jaggrok-status-trend" data-provider="grok" aria-hidden="true"></span>
                                <span class="aimentor-status-metrics-summary" data-provider="grok" aria-hidden="true"><?php echo esc_html( $grok_summary_label ); ?></span>
                                <span class="aimentor-status-description jaggrok-status-description" data-provider="grok"><?php echo esc_html( $grok_status['description'] ); ?></span>
                                <span class="screen-reader-text aimentor-status-metrics" data-provider="grok"><?php echo esc_html( $grok_metrics_label ); ?></span>
                            </div>
                        </div>
                        <div class="aimentor-provider-group jaggrok-provider-group" data-provider="anthropic">
                            <label for="aimentor_anthropic_api_key" class="aimentor-provider-group__label jaggrok-provider-group__label"><?php esc_html_e( 'Anthropic API Key', 'aimentor' ); ?></label>
                            <div class="aimentor-api-key-container jaggrok-api-key-container">
                                <input type="password" id="aimentor_anthropic_api_key" name="aimentor_anthropic_api_key" value="<?php echo esc_attr( $api_keys['anthropic'] ); ?>" class="regular-text aimentor-api-input jaggrok-api-input" autocomplete="off" placeholder="<?php esc_attr_e( 'sk-ant-...', 'aimentor' ); ?>" />
                                <button type="button" class="button button-secondary aimentor-toggle-visibility jaggrok-toggle-visibility" data-target="aimentor_anthropic_api_key" data-show-label="<?php esc_attr_e( 'Show', 'aimentor' ); ?>" data-hide-label="<?php esc_attr_e( 'Hide', 'aimentor' ); ?>" aria-label="<?php esc_attr_e( 'Toggle Anthropic API key visibility', 'aimentor' ); ?>" aria-pressed="false"><?php esc_html_e( 'Show', 'aimentor' ); ?></button>
                            </div>
                            <p class="description"><a href="<?php echo esc_url( 'https://console.anthropic.com/settings/keys' ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Create an API key', 'aimentor' ); ?></a></p>
                            <p class="description">
                                <button type="button" class="button aimentor-test-provider jaggrok-test-provider" data-provider="anthropic"><?php esc_html_e( 'Test Connection', 'aimentor' ); ?></button>
                            </p>
                            <?php
                            $anthropic_status        = isset( $provider_status_views['anthropic'] ) ? $provider_status_views['anthropic'] : aimentor_format_provider_status_for_display( 'anthropic', [] );
                            $anthropic_history_json  = isset( $anthropic_status['history'] ) ? wp_json_encode( $anthropic_status['history'] ) : '[]';
                            $anthropic_success_count = isset( $anthropic_status['success_count'] ) ? absint( $anthropic_status['success_count'] ) : 0;
                            $anthropic_failure_count = isset( $anthropic_status['failure_count'] ) ? absint( $anthropic_status['failure_count'] ) : 0;
                            $anthropic_total_count   = isset( $anthropic_status['total_count'] ) ? absint( $anthropic_status['total_count'] ) : ( $anthropic_success_count + $anthropic_failure_count );
                            $anthropic_success_rate  = isset( $anthropic_status['success_rate'] ) && is_numeric( $anthropic_status['success_rate'] ) ? (string) $anthropic_status['success_rate'] : '';
                            $anthropic_metrics_label = isset( $anthropic_status['metrics_label'] ) && $anthropic_status['metrics_label'] ? $anthropic_status['metrics_label'] : __( 'No connection tests recorded yet.', 'aimentor' );
                            $anthropic_summary_label = isset( $anthropic_status['summary_label'] ) && $anthropic_status['summary_label'] ? $anthropic_status['summary_label'] : __( 'No tests yet', 'aimentor' );
                            ?>
                            <div class="aimentor-provider-status jaggrok-provider-status" data-provider="anthropic" data-timestamp="<?php echo esc_attr( $anthropic_status['timestamp'] ); ?>" data-success-count="<?php echo esc_attr( $anthropic_success_count ); ?>" data-failure-count="<?php echo esc_attr( $anthropic_failure_count ); ?>" data-total-count="<?php echo esc_attr( $anthropic_total_count ); ?>" data-success-rate="<?php echo esc_attr( $anthropic_success_rate ); ?>" data-history="<?php echo esc_attr( $anthropic_history_json ); ?>" data-provider-label="<?php echo esc_attr( $provider_labels_map['anthropic'] ?? 'Anthropic' ); ?>" data-metrics-label="<?php echo esc_attr( $anthropic_metrics_label ); ?>" data-summary-label="<?php echo esc_attr( $anthropic_summary_label ); ?>" aria-live="polite">
                                <span class="aimentor-status-badge aimentor-status-badge--<?php echo esc_attr( $anthropic_status['badge_state'] ); ?>" data-provider="anthropic"><?php echo esc_html( $anthropic_status['badge_label'] ); ?></span>
                                <span class="aimentor-status-trend jaggrok-status-trend" data-provider="anthropic" aria-hidden="true"></span>
                                <span class="aimentor-status-metrics-summary" data-provider="anthropic" aria-hidden="true"><?php echo esc_html( $anthropic_summary_label ); ?></span>
                                <span class="aimentor-status-description jaggrok-status-description" data-provider="anthropic"><?php echo esc_html( $anthropic_status['description'] ); ?></span>
                                <span class="screen-reader-text aimentor-status-metrics" data-provider="anthropic"><?php echo esc_html( $anthropic_metrics_label ); ?></span>
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
                            <?php
                            $openai_status        = isset( $provider_status_views['openai'] ) ? $provider_status_views['openai'] : aimentor_format_provider_status_for_display( 'openai', [] );
                            $openai_history_json  = isset( $openai_status['history'] ) ? wp_json_encode( $openai_status['history'] ) : '[]';
                            $openai_success_count = isset( $openai_status['success_count'] ) ? absint( $openai_status['success_count'] ) : 0;
                            $openai_failure_count = isset( $openai_status['failure_count'] ) ? absint( $openai_status['failure_count'] ) : 0;
                            $openai_total_count   = isset( $openai_status['total_count'] ) ? absint( $openai_status['total_count'] ) : ( $openai_success_count + $openai_failure_count );
                            $openai_success_rate  = isset( $openai_status['success_rate'] ) && is_numeric( $openai_status['success_rate'] ) ? (string) $openai_status['success_rate'] : '';
                            $openai_metrics_label = isset( $openai_status['metrics_label'] ) && $openai_status['metrics_label'] ? $openai_status['metrics_label'] : __( 'No connection tests recorded yet.', 'aimentor' );
                            $openai_summary_label = isset( $openai_status['summary_label'] ) && $openai_status['summary_label'] ? $openai_status['summary_label'] : __( 'No tests yet', 'aimentor' );
                            ?>
                            <div class="aimentor-provider-status jaggrok-provider-status" data-provider="openai" data-timestamp="<?php echo esc_attr( $openai_status['timestamp'] ); ?>" data-success-count="<?php echo esc_attr( $openai_success_count ); ?>" data-failure-count="<?php echo esc_attr( $openai_failure_count ); ?>" data-total-count="<?php echo esc_attr( $openai_total_count ); ?>" data-success-rate="<?php echo esc_attr( $openai_success_rate ); ?>" data-history="<?php echo esc_attr( $openai_history_json ); ?>" data-provider-label="<?php echo esc_attr( $provider_labels_map['openai'] ?? 'OpenAI' ); ?>" data-metrics-label="<?php echo esc_attr( $openai_metrics_label ); ?>" data-summary-label="<?php echo esc_attr( $openai_summary_label ); ?>" aria-live="polite">
                                <span class="aimentor-status-badge aimentor-status-badge--<?php echo esc_attr( $openai_status['badge_state'] ); ?>" data-provider="openai"><?php echo esc_html( $openai_status['badge_label'] ); ?></span>
                                <span class="aimentor-status-trend jaggrok-status-trend" data-provider="openai" aria-hidden="true"></span>
                                <span class="aimentor-status-metrics-summary" data-provider="openai" aria-hidden="true"><?php echo esc_html( $openai_summary_label ); ?></span>
                                <span class="aimentor-status-description jaggrok-status-description" data-provider="openai"><?php echo esc_html( $openai_status['description'] ); ?></span>
                                <span class="screen-reader-text aimentor-status-metrics" data-provider="openai"><?php echo esc_html( $openai_metrics_label ); ?></span>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Automatic Updates', 'aimentor' ); ?></th>
                    <td>
                        <fieldset>
                            <legend class="screen-reader-text"><?php esc_html_e( 'Automatic update preference', 'aimentor' ); ?></legend>
                            <input type="hidden" name="aimentor_enable_auto_updates" value="no" />
                            <label>
                                <input type="checkbox" name="aimentor_enable_auto_updates" value="yes" <?php checked( $auto_updates_setting_enabled ); ?> />
                                <?php esc_html_e( 'Allow WordPress to automatically install AiMentor Elementor updates', 'aimentor' ); ?>
                            </label>
                            <p class="description"><?php esc_html_e( 'When enabled, new AiMentor Elementor releases install automatically during the built-in WordPress update checks.', 'aimentor' ); ?></p>
                            <?php if ( $auto_updates_active ) : ?>
                                <p class="description">
                                    <span class="dashicons dashicons-yes" aria-hidden="true"></span>
                                    <span class="screen-reader-text"><?php esc_html_e( 'Status:', 'aimentor' ); ?></span>
                                    <?php esc_html_e( 'Automatic updates are currently active for AiMentor Elementor.', 'aimentor' ); ?>
                                </p>
                            <?php elseif ( $auto_updates_setting_enabled ) : ?>
                                <p class="description">
                                    <span class="dashicons dashicons-warning" aria-hidden="true"></span>
                                    <span class="screen-reader-text"><?php esc_html_e( 'Status:', 'aimentor' ); ?></span>
                                    <?php esc_html_e( 'Automatic updates are enabled here, but WordPress automatic plugin updates are disabled elsewhere.', 'aimentor' ); ?>
                                </p>
                            <?php else : ?>
                                <p class="description">
                                    <span class="dashicons dashicons-no-alt" aria-hidden="true"></span>
                                    <span class="screen-reader-text"><?php esc_html_e( 'Status:', 'aimentor' ); ?></span>
                                    <?php esc_html_e( 'Automatic updates are currently disabled for AiMentor Elementor.', 'aimentor' ); ?>
                                </p>
                            <?php endif; ?>
                        </fieldset>
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
                            <br />
                            <input type="hidden" name="aimentor_enable_health_check_alerts" value="no" />
                            <label>
                                <input type="checkbox" name="aimentor_enable_health_check_alerts" value="yes" <?php checked( $health_check_alerts_enabled ); ?> />
                                <?php esc_html_e( 'Email administrators when repeated failures occur', 'aimentor' ); ?>
                            </label>
                            <p class="description"><?php echo esc_html( sprintf( __( 'AiMentor will re-test stored API keys daily and alert you after %d consecutive failures.', 'aimentor' ), $health_check_threshold ) ); ?></p>
                        </fieldset>
                        <label class="screen-reader-text" for="aimentor_health_check_recipients"><?php esc_html_e( 'Health check alert recipients', 'aimentor' ); ?></label>
                        <input type="text" id="aimentor_health_check_recipients" name="aimentor_health_check_recipients" value="<?php echo esc_attr( $health_check_recipients ); ?>" class="regular-text" placeholder="<?php esc_attr_e( 'name@example.com, team@example.com', 'aimentor' ); ?>" />
                        <p class="description"><?php esc_html_e( 'Leave blank to notify every site administrator using the built-in WordPress email system.', 'aimentor' ); ?></p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php include plugin_dir_path( __FILE__ ) . 'sidebar-support.php'; ?>
</div>
