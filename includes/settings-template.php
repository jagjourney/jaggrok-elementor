
<div class="wrap">
    <h1><img src="<?php echo plugin_dir_url( __DIR__ ); ?>assets/icon-128x128.png" style="height:40px; vertical-align:middle;">
        JagGrok Elementor Settings</h1>

    <p class="description">Connect to your preferred AI provider and unlock AI-powered page building! <a href="https://jagjourney.com/" target="_blank">By Jag Journey, LLC</a></p>

    <form method="post" action="options.php">
        <?php settings_fields( 'jaggrok_settings' ); ?>
        <?php
        $provider       = get_option( 'jaggrok_provider', 'grok' );
        $api_keys       = [
                'grok'   => get_option( 'jaggrok_xai_api_key' ),
                'openai' => get_option( 'jaggrok_openai_api_key' ),
        ];
        $models         = jaggrok_get_provider_models();
        $allowed_models = jaggrok_get_allowed_provider_models();
        $grok_model_labels = [
                'grok-3-mini' => __( 'Grok 3 Mini (Fast)', 'jaggrok-elementor' ),
                'grok-3-beta' => __( 'Grok 3 Beta (Balanced) ★', 'jaggrok-elementor' ),
                'grok-3'      => __( 'Grok 3 (Standard)', 'jaggrok-elementor' ),
                'grok-4-mini' => __( 'Grok 4 Mini (Premium)', 'jaggrok-elementor' ),
                'grok-4'      => __( 'Grok 4 (Flagship)', 'jaggrok-elementor' ),
                'grok-4-code' => __( 'Grok 4 Code', 'jaggrok-elementor' ),
        ];
        $openai_model_labels = [
                'gpt-4o-mini'  => __( 'GPT-4o mini (Balanced) ★', 'jaggrok-elementor' ),
                'gpt-4o'       => __( 'GPT-4o (Flagship)', 'jaggrok-elementor' ),
                'gpt-4.1'      => __( 'GPT-4.1 (Reasoning)', 'jaggrok-elementor' ),
                'gpt-4.1-mini' => __( 'GPT-4.1 mini (Fast)', 'jaggrok-elementor' ),
                'gpt-4.1-nano' => __( 'GPT-4.1 nano (Edge)', 'jaggrok-elementor' ),
                'o4-mini'      => __( 'o4-mini (Preview)', 'jaggrok-elementor' ),
                'o4'           => __( 'o4 (Preview)', 'jaggrok-elementor' ),
        ];
        ?>
        <table class="form-table">
            <tr>
                <th scope="row"><label><?php esc_html_e( 'Provider', 'jaggrok-elementor' ); ?></label></th>
                <td>
                    <fieldset id="jaggrok-provider-selector" class="jaggrok-provider-fieldset">
                        <legend class="screen-reader-text"><?php esc_html_e( 'AI provider', 'jaggrok-elementor' ); ?></legend>
                        <label class="jaggrok-provider-option">
                            <input type="radio" name="jaggrok_provider" value="grok" <?php checked( $provider, 'grok' ); ?> />
                            <span class="jaggrok-provider-name"><?php esc_html_e( 'xAI Grok', 'jaggrok-elementor' ); ?></span>
                            <span class="description jaggrok-provider-summary"><?php esc_html_e( "Creator tier includes roughly 30 requests per minute and bundled usage. Confirm current allowances on xAI's pricing page.", 'jaggrok-elementor' ); ?></span>
                        </label>
                        <label class="jaggrok-provider-option">
                            <input type="radio" name="jaggrok_provider" value="openai" <?php checked( $provider, 'openai' ); ?> />
                            <span class="jaggrok-provider-name"><?php esc_html_e( 'OpenAI', 'jaggrok-elementor' ); ?></span>
                            <span class="description jaggrok-provider-summary"><?php esc_html_e( 'Pay-as-you-go billing with token-based rates. Review OpenAI pricing for the latest per-model costs.', 'jaggrok-elementor' ); ?></span>
                        </label>
                    </fieldset>
                    <div class="jaggrok-provider-help" data-provider="grok" aria-live="polite">
                        <p class="description"><?php esc_html_e( 'Grok API access is part of the Creator subscription. Typical soft limits hover around 30 requests/minute; usage beyond that may queue. See the xAI pricing page for up-to-date information.', 'jaggrok-elementor' ); ?></p>
                        <p class="description"><a href="<?php echo esc_url( 'https://x.ai/pricing' ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'View xAI pricing & limits', 'jaggrok-elementor' ); ?></a></p>
                    </div>
                    <div class="jaggrok-provider-help" data-provider="openai" aria-live="polite">
                        <p class="description"><?php esc_html_e( 'OpenAI accounts charge only for tokens used. Most teams start with generous rate limits that scale automatically after billing verification.', 'jaggrok-elementor' ); ?></p>
                        <p class="description"><a href="<?php echo esc_url( 'https://openai.com/pricing' ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Review OpenAI pricing & limits', 'jaggrok-elementor' ); ?></a></p>
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row"><label for="jaggrok_xai_api_key"><?php esc_html_e( 'API Credentials', 'jaggrok-elementor' ); ?></label></th>
                <td>
                    <div class="jaggrok-provider-group" data-provider="grok">
                        <label for="jaggrok_xai_api_key" class="jaggrok-provider-group__label"><?php esc_html_e( 'xAI API Key', 'jaggrok-elementor' ); ?> <span class="required">*</span></label>
                        <div class="jaggrok-api-key-container">
                            <input type="password" id="jaggrok_xai_api_key" name="jaggrok_xai_api_key" value="<?php echo esc_attr( $api_keys['grok'] ); ?>" class="regular-text jaggrok-api-input" autocomplete="off" />
                            <button type="button" class="button button-secondary jaggrok-toggle-visibility" data-target="jaggrok_xai_api_key" data-show-label="<?php esc_attr_e( 'Show', 'jaggrok-elementor' ); ?>" data-hide-label="<?php esc_attr_e( 'Hide', 'jaggrok-elementor' ); ?>" aria-label="<?php esc_attr_e( 'Toggle xAI API key visibility', 'jaggrok-elementor' ); ?>" aria-pressed="false"><?php esc_html_e( 'Show', 'jaggrok-elementor' ); ?></button>
                        </div>
                        <p class="description"><a href="<?php echo esc_url( 'https://x.ai/api' ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Generate an API key', 'jaggrok-elementor' ); ?></a></p>
                        <p class="description">
                            <button type="button" class="button" id="jaggrok-test-api"><?php esc_html_e( 'Test Connection', 'jaggrok-elementor' ); ?></button>
                            <span id="jaggrok-api-status" class="jaggrok-api-status" aria-live="polite"></span>
                        </p>
                    </div>
                    <div class="jaggrok-provider-group" data-provider="openai">
                        <label for="jaggrok_openai_api_key" class="jaggrok-provider-group__label"><?php esc_html_e( 'OpenAI API Key', 'jaggrok-elementor' ); ?></label>
                        <div class="jaggrok-api-key-container">
                            <input type="password" id="jaggrok_openai_api_key" name="jaggrok_openai_api_key" value="<?php echo esc_attr( $api_keys['openai'] ); ?>" class="regular-text jaggrok-api-input" autocomplete="off" />
                            <button type="button" class="button button-secondary jaggrok-toggle-visibility" data-target="jaggrok_openai_api_key" data-show-label="<?php esc_attr_e( 'Show', 'jaggrok-elementor' ); ?>" data-hide-label="<?php esc_attr_e( 'Hide', 'jaggrok-elementor' ); ?>" aria-label="<?php esc_attr_e( 'Toggle OpenAI API key visibility', 'jaggrok-elementor' ); ?>" aria-pressed="false"><?php esc_html_e( 'Show', 'jaggrok-elementor' ); ?></button>
                        </div>
                        <p class="description"><a href="<?php echo esc_url( 'https://platform.openai.com/account/api-keys' ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Create an API key', 'jaggrok-elementor' ); ?></a></p>
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Default Model', 'jaggrok-elementor' ); ?></th>
                <td>
                    <div class="jaggrok-provider-group" data-provider="grok">
                        <label class="screen-reader-text" for="jaggrok_provider_models_grok"><?php esc_html_e( 'xAI Grok default model', 'jaggrok-elementor' ); ?></label>
                        <select name="jaggrok_provider_models[grok]" id="jaggrok_provider_models_grok" class="regular-text">
                            <?php foreach ( $allowed_models['grok'] as $model_key ) : ?>
                                <option value="<?php echo esc_attr( $model_key ); ?>" <?php selected( $models['grok'], $model_key ); ?>><?php echo esc_html( $grok_model_labels[ $model_key ] ?? strtoupper( $model_key ) ); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php esc_html_e( 'Grok 3 Beta is a reliable balance of quality and speed for most Elementor flows.', 'jaggrok-elementor' ); ?></p>
                    </div>
                    <div class="jaggrok-provider-group" data-provider="openai">
                        <label class="screen-reader-text" for="jaggrok_provider_models_openai"><?php esc_html_e( 'OpenAI default model', 'jaggrok-elementor' ); ?></label>
                        <select name="jaggrok_provider_models[openai]" id="jaggrok_provider_models_openai" class="regular-text">
                            <?php foreach ( $allowed_models['openai'] as $model_key ) : ?>
                                <option value="<?php echo esc_attr( $model_key ); ?>" <?php selected( $models['openai'], $model_key ); ?>><?php echo esc_html( $openai_model_labels[ $model_key ] ?? strtoupper( $model_key ) ); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php esc_html_e( 'GPT-4o mini delivers strong reasoning with lower cost; upgrade as your budget allows.', 'jaggrok-elementor' ); ?></p>
                    </div>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Auto-Insert', 'jaggrok-elementor' ); ?></th>
                <td>
                    <?php $auto = get_option( 'jaggrok_auto_insert', 'yes' ); ?>
                    <label><input type="radio" name="jaggrok_auto_insert" value="yes" <?php checked( $auto, 'yes' ); ?> /> <?php esc_html_e( 'Yes', 'jaggrok-elementor' ); ?></label>
                    <label><input type="radio" name="jaggrok_auto_insert" value="no" <?php checked( $auto, 'no' ); ?> /> <?php esc_html_e( 'No', 'jaggrok-elementor' ); ?></label>
                    <p class="description"><?php esc_html_e( 'Automatically add generated sections to the Elementor canvas.', 'jaggrok-elementor' ); ?></p>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Default Style', 'jaggrok-elementor' ); ?></th>
                <td>
                    <?php $style = get_option( 'jaggrok_theme_style', 'modern' ); ?>
                    <select name="jaggrok_theme_style">
                        <option value="modern" <?php selected( $style, 'modern' ); ?>><?php esc_html_e( 'Modern', 'jaggrok-elementor' ); ?></option>
                        <option value="bold" <?php selected( $style, 'bold' ); ?>><?php esc_html_e( 'Bold', 'jaggrok-elementor' ); ?></option>
                        <option value="minimal" <?php selected( $style, 'minimal' ); ?>><?php esc_html_e( 'Minimal', 'jaggrok-elementor' ); ?></option>
                    </select>
                </td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e( 'Max Tokens', 'jaggrok-elementor' ); ?></th>
                <td>
                    <input type="number" name="jaggrok_max_tokens" value="<?php echo esc_attr( get_option( 'jaggrok_max_tokens', 2000 ) ); ?>" min="500" max="8000" class="small-text" /> <?php esc_html_e( 'tokens', 'jaggrok-elementor' ); ?>
                    <p class="description"><?php esc_html_e( 'Higher values allow for more detailed layouts. Stay within your provider limits.', 'jaggrok-elementor' ); ?></p>
                </td>
            </tr>
        </table>
        <input type="hidden" name="jaggrok_model" id="jaggrok_model_legacy" value="<?php echo esc_attr( $models['grok'] ); ?>" />
        <input type="hidden" name="jaggrok_openai_model" id="jaggrok_openai_model_legacy" value="<?php echo esc_attr( $models['openai'] ); ?>" />
        <?php submit_button(); ?>
    </form>
    <?php if ( get_option( 'jaggrok_api_tested' ) ) : ?>
        <div class="notice notice-success"><p>✅ API Connected!</p></div>
    <?php endif; ?>

    <!-- ERROR LOG TABLE -->
    <h2>Error Log</h2>
    <table class="widefat striped">
        <thead><tr><th>Timestamp</th><th>Error Message</th></tr></thead>
        <tbody>
        <?php
        $log_file = plugin_dir_path( __FILE__ ) . 'jaggrok-errors.log';
        if ( file_exists( $log_file ) ) {
            $logs = array_reverse( file( $log_file ) );
            $logs = array_slice( $logs, 0, 10 );
            foreach ( $logs as $log ) {
                // FIXED v1.3.10: Safe array access (NO MORE WARNINGS!)
                $parts = explode( ' - ', trim( $log ), 2 );
                $timestamp = isset( $parts[0] ) ? trim( $parts[0] ) : 'Unknown';
                $message = isset( $parts[1] ) ? trim( $parts[1] ) : $log;
                echo '<tr><td>' . esc_html( $timestamp ) . '</td><td>' . esc_html( $message ) . '</td></tr>';
            }
        } else {
            echo '<tr><td colspan="2">No errors logged yet.</td></tr>';
        }
        ?>
        </tbody>
    </table>
</div>

<style>
.required { color: #d63638; }
.jaggrok-provider-fieldset { border: 1px solid #ccd0d4; padding: 12px; max-width: 640px; background: #fff; border-radius: 6px; }
.jaggrok-provider-option { display: block; margin-bottom: 12px; }
.jaggrok-provider-option:last-of-type { margin-bottom: 0; }
.jaggrok-provider-name { font-weight: 600; display: inline-block; margin-right: 6px; }
.jaggrok-provider-summary { display: block; margin-left: 26px; }
.jaggrok-provider-group { margin-bottom: 16px; }
.js .jaggrok-provider-group { display: none; }
.jaggrok-provider-group__label { font-weight: 600; display: block; margin-bottom: 4px; }
.jaggrok-provider-help { margin-top: 8px; max-width: 640px; }
.js .jaggrok-provider-help { display: none; }
.jaggrok-api-key-container { display: flex; align-items: center; gap: 8px; max-width: 420px; }
.jaggrok-api-input { width: 100%; }
.jaggrok-api-status { margin-left: 8px; font-weight: 600; }
.jaggrok-status-success { color: #007017; }
.jaggrok-status-error { color: #d63638; }
</style>

<script>
    jQuery(document).ready(function($) {
        var ajaxNonce = '<?php echo wp_create_nonce( 'jaggrok_test' ); ?>';
        var $providerInputs = $('input[name="jaggrok_provider"]');

        function toggleProvider(provider) {
            $('.jaggrok-provider-group').hide().attr('aria-hidden', 'true');
            $('.jaggrok-provider-group[data-provider="' + provider + '"]').show().attr('aria-hidden', 'false');
            $('.jaggrok-provider-help').hide().attr('aria-hidden', 'true');
            $('.jaggrok-provider-help[data-provider="' + provider + '"]').show().attr('aria-hidden', 'false');
        }

        var initialProvider = $providerInputs.filter(':checked').val() || 'grok';
        toggleProvider(initialProvider);

        $providerInputs.on('change', function() {
            toggleProvider($(this).val());
        });

        $('.jaggrok-toggle-visibility').on('click', function() {
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

        $('#jaggrok_provider_models_grok').on('change', function() {
            $('#jaggrok_model_legacy').val($(this).val());
        });

        $('#jaggrok_provider_models_openai').on('change', function() {
            $('#jaggrok_openai_model_legacy').val($(this).val());
        });

        $('#jaggrok-test-api').on('click', function() {
            var $button = $(this);
            var $status = $('#jaggrok-api-status');
            var apiKey = $('#jaggrok_xai_api_key').val();

            if ( ! apiKey ) {
                $status.html('<span class="jaggrok-status-error"><?php echo esc_js( __( 'Enter API key!', 'jaggrok-elementor' ) ); ?></span>');
                return;
            }

            $status.text('<?php echo esc_js( __( 'Testing…', 'jaggrok-elementor' ) ); ?>');
            $button.prop('disabled', true);

            $.post(ajaxurl, {
                action: 'jaggrok_test_api',
                api_key: apiKey,
                nonce: ajaxNonce
            }).done(function(response) {
                if ( response && response.success ) {
                    $status.html('<span class="jaggrok-status-success">✅ <?php echo esc_js( __( 'Connected!', 'jaggrok-elementor' ) ); ?></span>');
                    window.setTimeout(function() { window.location.reload(); }, 600);
                } else if ( response && response.data ) {
                    $status.html('<span class="jaggrok-status-error">❌ ' + response.data + '</span>');
                } else {
                    $status.html('<span class="jaggrok-status-error">❌ <?php echo esc_js( __( 'Connection failed.', 'jaggrok-elementor' ) ); ?></span>');
                }
            }).fail(function() {
                $status.html('<span class="jaggrok-status-error">❌ <?php echo esc_js( __( 'Request timed out.', 'jaggrok-elementor' ) ); ?></span>');
            }).always(function() {
                $button.prop('disabled', false);
            });
        });
    });
</script>
