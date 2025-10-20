<div class="wrap">
    <h1><img src="<?php echo plugin_dir_url( __DIR__ ); ?>assets/icon-128x128.png" style="height:40px; vertical-align:middle;">
        JagGrok Elementor Settings</h1>

    <p class="description">Connect to xAI's Grok API and unlock AI-powered page building! <a href="https://jagjourney.com/" target="_blank">By Jag Journey, LLC</a></p>

    <form method="post" action="options.php">
        <?php settings_fields( 'jaggrok_settings' ); ?>
        <table class="form-table">
            <tr><th><label for="jaggrok_xai_api_key">xAI API Key <span class="required">*</span></label></th>
                <td>
                    <div class="jaggrok-api-key-container" style="display: flex; align-items: center; gap: 8px;">
                        <input type="password" id="jaggrok_xai_api_key" name="jaggrok_xai_api_key"
                               value="<?php echo esc_attr( get_option( 'jaggrok_xai_api_key' ) ); ?>"
                               class="regular-text jaggrok-api-input"
                               style="width: auto; flex: 1; min-width: 200px;" />
                        <button type="button" id="toggle-api-key" class="jaggrok-eye-btn" style="margin-left: 8px;">
                            👁️
                        </button>
                    </div>
                    <p class="description"><a href="https://x.ai/api" target="_blank">Get key</a> |
                        <button type="button" class="button" id="jaggrok-test-api">Test Connection</button>
                        <span id="jaggrok-api-status"></span></p>
                </td></tr>

            <tr><th>Auto-Insert</th><td>
                    <?php $auto = get_option( 'jaggrok_auto_insert', 'yes' ); ?>
                    <label><input type="radio" name="jaggrok_auto_insert" value="yes" <?php checked( $auto, 'yes' ); ?>> Yes</label>
                    <label><input type="radio" name="jaggrok_auto_insert" value="no" <?php checked( $auto, 'no' ); ?>> No</label>
                    <p class="description">Auto-add generated sections to canvas</p></th></td></tr>

            <tr><th>Default Style</th><td>
                    <?php $style = get_option( 'jaggrok_theme_style', 'modern' ); ?>
                    <select name="jaggrok_theme_style">
                        <option value="modern" <?php selected( $style, 'modern' ); ?>>Modern</option>
                        <option value="bold" <?php selected( $style, 'bold' ); ?>>Bold</option>
                        <option value="minimal" <?php selected( $style, 'minimal' ); ?>>Minimal</option>
                    </select></td></tr>

            <tr><th>Max Tokens</th><td>
                    <input type="number" name="jaggrok_max_tokens" value="<?php echo get_option( 'jaggrok_max_tokens', 2000 ); ?>" min="500" max="8000" class="small-text" /> tokens
                    <p class="description">Higher = more detailed pages</p></td></tr>

            <tr><th>Grok Model</th><td>
                    <?php $model = get_option( 'jaggrok_model', 'grok-3-beta' ); ?>
                    <select name="jaggrok_model" class="regular-text">
                        <option value="grok-3-beta" <?php selected( $model, 'grok-3-beta' ); ?>>Grok 3 Beta (Current Standard)</option>
                        <option value="grok-3-fast-beta" <?php selected( $model, 'grok-3-fast-beta' ); ?>>Grok 3 Fast Beta (Faster)</option>
                    </select>
                    <p class="description">Grok-beta is deprecated - use Grok 3 models</p></td></tr>
        </table>
        <?php submit_button(); ?>
    </form>
    <?php if ( get_option( 'jaggrok_api_tested' ) ): ?><div class="notice notice-success"><p>✅ API Connected!</p></div><?php endif; ?>

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

<style>.required { color: #d63638; }</style>

<script>
    jQuery(document).ready(function($) {
        // EYE TOGGLE FOR API KEY
        $('#toggle-api-key').click(function() {
            var $input = $('#jaggrok_xai_api_key');
            if ($input.attr('type') === 'password') {
                $input.attr('type', 'text');
                $(this).html('🙈');
            } else {
                $input.attr('type', 'password');
                $(this).html('👁️');
            }
        });

        // TEST API BUTTON
        $('#jaggrok-test-api').click(function() {
            var apiKey = $('#jaggrok_xai_api_key').val();
            var status = $('#jaggrok-api-status');
            if (!apiKey) return status.html('<span style="color:red">Enter API key!</span>');
            status.html('Testing...');
            $.post(ajaxurl, {
                action: 'jaggrok_test_api',
                api_key: apiKey,
                nonce: '<?php echo wp_create_nonce( 'jaggrok_test' ); ?>'
            }, function(response) {
                if (response.success) {
                    status.html('<span style="color:green">✅ Connected!</span>');
                    location.reload();
                } else {
                    status.html('<span style="color:red">❌ ' + response.data + '</span>');
                }
            });
        });
    });
</script>