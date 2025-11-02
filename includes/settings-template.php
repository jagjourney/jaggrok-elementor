
<div class="wrap">
    <h1 class="aimentor-settings-title">
        <img src="<?php echo esc_url( AIMENTOR_PLUGIN_URL . 'assets/icon-128x128.png' ); ?>" class="aimentor-settings-logo" alt="<?php esc_attr_e( 'AiMentor logo', 'aimentor' ); ?>">
        <span class="aimentor-settings-heading"><?php esc_html_e( 'AiMentor Control Center', 'aimentor' ); ?></span>
        <span class="aimentor-settings-badge" aria-hidden="true"><?php esc_html_e( 'Multi-provider', 'aimentor' ); ?></span>
    </h1>

    <p class="description">
        <?php
        printf(
                wp_kses(
                        /* translators: 1: AiMentor marketing URL */
                        __( 'Connect Elementor to the AI partner that fits your team&mdash;xAI Grok, OpenAI, or any future integration. <a href="%1$s" target="_blank" rel="noopener noreferrer">Powered by AiMentor</a>', 'aimentor' ),
                        [
                                'a' => [
                                        'href'   => [],
                                        'target' => [],
                                        'rel'    => [],
                                ],
                        ]
                ),
                esc_url( 'https://aimentor.ai/' )
        );
        ?>
    </p>

    <form method="post" action="options.php">
        <?php settings_fields( 'aimentor_settings' ); ?>
        <?php
        $defaults       = aimentor_get_default_options();
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
        ?>
        <p class="description aimentor-defaults-notice">
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
                    <fieldset id="aimentor-provider-selector" class="aimentor-provider-fieldset">
                        <legend class="screen-reader-text"><?php esc_html_e( 'AI provider', 'aimentor' ); ?></legend>
                        <label class="aimentor-provider-option">
                            <input type="radio" name="aimentor_provider" value="grok" <?php checked( $provider, 'grok' ); ?> />
                            <span class="aimentor-provider-name"><?php esc_html_e( 'xAI Grok', 'aimentor' ); ?></span>
                            <span class="aimentor-provider-badge" style="background-color:#1E1E1E;" aria-hidden="true">
                                <?php esc_html_e( 'xAI', 'aimentor' ); ?>
                            </span>
                            <span class="description aimentor-provider-summary"><?php esc_html_e( "Creator tier includes roughly 30 requests per minute and bundled usage. Confirm current allowances on xAI's pricing page.", 'aimentor' ); ?></span>
                        </label>
                        <label class="aimentor-provider-option">
                            <input type="radio" name="aimentor_provider" value="openai" <?php checked( $provider, 'openai' ); ?> />
                            <span class="aimentor-provider-name"><?php esc_html_e( 'OpenAI', 'aimentor' ); ?></span>
                            <span class="aimentor-provider-badge" style="background-color:#2B8CFF;" aria-hidden="true">
                                <?php esc_html_e( 'OpenAI', 'aimentor' ); ?>
                            </span>
                            <span class="description aimentor-provider-summary"><?php esc_html_e( 'Pay-as-you-go billing with token-based rates. Review OpenAI pricing for the latest per-model costs.', 'aimentor' ); ?></span>
                        </label>
                    </fieldset>
                    <div class="aimentor-provider-help" data-provider="grok" aria-live="polite">
                        <p class="description"><?php esc_html_e( 'Grok API access is part of the Creator subscription. Typical soft limits hover around 30 requests/minute; usage beyond that may queue. See the xAI pricing page for up-to-date information.', 'aimentor' ); ?></p>
                        <p class="description"><a href="<?php echo esc_url( 'https://x.ai/pricing' ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View xAI pricing & limits', 'aimentor' ); ?></a></p>
                    </div>
                    <div class="aimentor-provider-help" data-provider="openai" aria-live="polite">
                        <p class="description"><?php esc_html_e( 'OpenAI accounts charge only for tokens used. Most teams start with generous rate limits that scale automatically after billing verification.', 'aimentor' ); ?></p>
                        <p class="description"><a href="<?php echo esc_url( 'https://openai.com/pricing' ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Review OpenAI pricing & limits', 'aimentor' ); ?></a></p>
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="aimentor_xai_api_key"><?php esc_html_e( 'API Credentials', 'aimentor' ); ?></label></th>
                <td>
                    <div class="aimentor-provider-group" data-provider="grok">
                        <label for="aimentor_xai_api_key" class="aimentor-provider-group__label"><?php esc_html_e( 'xAI API Key', 'aimentor' ); ?> <span class="required">*</span></label>
                        <div class="aimentor-api-key-container">
                            <input type="password" id="aimentor_xai_api_key" name="aimentor_xai_api_key" value="<?php echo esc_attr( $api_keys['grok'] ); ?>" class="regular-text aimentor-api-input" autocomplete="off" />
                            <button type="button" class="button button-secondary aimentor-toggle-visibility" data-target="aimentor_xai_api_key" data-show-label="<?php esc_attr_e( 'Show', 'aimentor' ); ?>" data-hide-label="<?php esc_attr_e( 'Hide', 'aimentor' ); ?>" aria-label="<?php esc_attr_e( 'Toggle xAI API key visibility', 'aimentor' ); ?>" aria-pressed="false"><?php esc_html_e( 'Show', 'aimentor' ); ?></button>
                        </div>
                        <p class="description"><a href="<?php echo esc_url( 'https://x.ai/api' ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Generate an API key', 'aimentor' ); ?></a></p>
                        <p class="description">
                            <button type="button" class="button aimentor-test-provider" data-provider="grok"><?php esc_html_e( 'Test Connection', 'aimentor' ); ?></button>
                        </p>
                        <?php $grok_status = $provider_status_views['grok']; ?>
                        <div class="aimentor-provider-status" data-provider="grok" data-timestamp="<?php echo esc_attr( $grok_status['timestamp'] ); ?>" aria-live="polite">
                            <span class="aimentor-status-badge aimentor-status-badge--<?php echo esc_attr( $grok_status['badge_state'] ); ?>" data-provider="grok"><?php echo esc_html( $grok_status['badge_label'] ); ?></span>
                            <span class="aimentor-status-description" data-provider="grok"><?php echo esc_html( $grok_status['description'] ); ?></span>
                        </div>
                    </div>
                    <div class="aimentor-provider-group" data-provider="openai">
                        <label for="aimentor_openai_api_key" class="aimentor-provider-group__label"><?php esc_html_e( 'OpenAI API Key', 'aimentor' ); ?></label>
                        <div class="aimentor-api-key-container">
                            <input type="password" id="aimentor_openai_api_key" name="aimentor_openai_api_key" value="<?php echo esc_attr( $api_keys['openai'] ); ?>" class="regular-text aimentor-api-input" autocomplete="off" placeholder="<?php esc_attr_e( 'sk-...', 'aimentor' ); ?>" />
                            <button type="button" class="button button-secondary aimentor-toggle-visibility" data-target="aimentor_openai_api_key" data-show-label="<?php esc_attr_e( 'Show', 'aimentor' ); ?>" data-hide-label="<?php esc_attr_e( 'Hide', 'aimentor' ); ?>" aria-label="<?php esc_attr_e( 'Toggle OpenAI API key visibility', 'aimentor' ); ?>" aria-pressed="false"><?php esc_html_e( 'Show', 'aimentor' ); ?></button>
                        </div>
                        <p class="description"><a href="<?php echo esc_url( 'https://platform.openai.com/account/api-keys' ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Create an API key', 'aimentor' ); ?></a></p>
                        <p class="description">
                            <button type="button" class="button aimentor-test-provider" data-provider="openai"><?php esc_html_e( 'Test Connection', 'aimentor' ); ?></button>
                        </p>
                        <?php $openai_status = $provider_status_views['openai']; ?>
                        <div class="aimentor-provider-status" data-provider="openai" data-timestamp="<?php echo esc_attr( $openai_status['timestamp'] ); ?>" aria-live="polite">
                            <span class="aimentor-status-badge aimentor-status-badge--<?php echo esc_attr( $openai_status['badge_state'] ); ?>" data-provider="openai"><?php echo esc_html( $openai_status['badge_label'] ); ?></span>
                            <span class="aimentor-status-description" data-provider="openai"><?php echo esc_html( $openai_status['description'] ); ?></span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Default Model', 'aimentor' ); ?></th>
                <td>
                    <div class="aimentor-provider-group" data-provider="grok">
                        <label class="screen-reader-text" for="aimentor_provider_models_grok"><?php esc_html_e( 'xAI Grok default model', 'aimentor' ); ?></label>
                        <select name="aimentor_provider_models[grok]" id="aimentor_provider_models_grok" class="regular-text">
                            <?php foreach ( $allowed_models['grok'] as $model_key ) : ?>
                                <option value="<?php echo esc_attr( $model_key ); ?>" <?php selected( $models['grok'], $model_key ); ?>><?php echo esc_html( $grok_model_labels[ $model_key ] ?? strtoupper( $model_key ) ); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php esc_html_e( 'Grok 3 Beta is a reliable balance of quality and speed for most Elementor flows.', 'aimentor' ); ?></p>
                    </div>
                    <div class="aimentor-provider-group" data-provider="openai">
                        <label class="screen-reader-text" for="aimentor_provider_models_openai"><?php esc_html_e( 'OpenAI default model', 'aimentor' ); ?></label>
                        <select name="aimentor_provider_models[openai]" id="aimentor_provider_models_openai" class="regular-text">
                            <?php foreach ( $allowed_models['openai'] as $model_key ) : ?>
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
    <h2><?php esc_html_e( 'Error Log', 'aimentor' ); ?></h2>
    <table class="widefat striped">
        <thead>
            <tr>
                <th><?php esc_html_e( 'Timestamp', 'aimentor' ); ?></th>
                <th><?php esc_html_e( 'Provider', 'aimentor' ); ?></th>
                <th><?php esc_html_e( 'Error Message', 'aimentor' ); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php
        $log_file = plugin_dir_path( __FILE__ ) . 'aimentor-errors.log';
        if ( file_exists( $log_file ) ) {
            $logs = array_reverse( file( $log_file ) );
            $logs = array_slice( $logs, 0, 10 );
            foreach ( $logs as $log ) {
                // FIXED v1.3.10: Safe array access (NO MORE WARNINGS!)
                $parts     = explode( ' - ', trim( $log ), 2 );
                $timestamp = isset( $parts[0] ) ? trim( $parts[0] ) : esc_html__( 'Unknown', 'aimentor' );
                $raw_entry = isset( $parts[1] ) ? trim( $parts[1] ) : '';
                $message   = $raw_entry ?: $log;
                $provider  = '';

                if ( '' !== $raw_entry ) {
                    $decoded = json_decode( $raw_entry, true );

                    if ( is_array( $decoded ) && isset( $decoded['message'] ) ) {
                        $message  = $decoded['message'];
                        $provider = isset( $decoded['context']['provider'] ) ? $decoded['context']['provider'] : '';
                    }
                }

                echo '<tr><td>' . esc_html( $timestamp ) . '</td><td>' . ( '' !== $provider ? esc_html( $provider ) : '&mdash;' ) . '</td><td>' . esc_html( $message ) . '</td></tr>';
            }
        } else {
            echo '<tr><td colspan="3">' . esc_html__( 'No errors logged yet.', 'aimentor' ) . '</td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>

<style>
.aimentor-settings-title { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
.aimentor-settings-logo { height: 48px; width: 48px; border-radius: 12px; box-shadow: 0 4px 18px rgba(64, 84, 178, 0.25); }
.aimentor-settings-heading { font-size: 26px; font-weight: 600; color: #1f2937; }
.aimentor-settings-badge { display: inline-flex; align-items: center; padding: 4px 12px; border-radius: 999px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; background: linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%); color: #ffffff; }
.aimentor-provider-badge { display: inline-flex; align-items: center; padding: 2px 8px; margin-left: 6px; border-radius: 999px; font-size: 11px; font-weight: 600; color: #ffffff; text-transform: uppercase; letter-spacing: 0.05em; }
.required { color: #d63638; }
.aimentor-provider-fieldset { border: 1px solid #ccd0d4; padding: 12px; max-width: 640px; background: #fff; border-radius: 6px; }
.aimentor-provider-option { display: block; margin-bottom: 12px; }
.aimentor-provider-option:last-of-type { margin-bottom: 0; }
.aimentor-provider-name { font-weight: 600; display: inline-block; margin-right: 6px; }
.aimentor-provider-summary { display: block; margin-left: 26px; }
.aimentor-provider-group { margin-bottom: 16px; }
.js .aimentor-provider-group { display: none; }
.aimentor-provider-group__label { font-weight: 600; display: block; margin-bottom: 4px; }
.aimentor-provider-help { margin-top: 8px; max-width: 640px; }
.js .aimentor-provider-help { display: none; }
.aimentor-api-key-container { display: flex; align-items: center; gap: 8px; max-width: 420px; }
.aimentor-api-input { width: 100%; }
.aimentor-provider-status { display: flex; align-items: center; gap: 8px; margin-top: 6px; max-width: 520px; }
.aimentor-status-badge { display: inline-flex; align-items: center; padding: 2px 10px; border-radius: 999px; font-weight: 600; font-size: 12px; letter-spacing: .01em; }
.aimentor-status-badge--success { background-color: #dff4e2; color: #116329; }
.aimentor-status-badge--error { background-color: #fce1e1; color: #b32d2e; }
.aimentor-status-badge--idle { background-color: #e7ecf3; color: #2c3e50; }
.aimentor-status-badge--pending { background-color: #fef3c7; color: #8a6110; }
.aimentor-status-description { display: inline-block; font-size: 13px; line-height: 1.5; }
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
