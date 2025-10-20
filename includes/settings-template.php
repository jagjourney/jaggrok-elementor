<div class="wrap">
	<h1><img src="<?php echo plugin_dir_url( __DIR__ ); ?>assets/icon-128x128.png" style="height:40px; vertical-align:middle;">
		JagGrok Elementor Settings</h1>

	<form method="post" action="options.php">
		<?php settings_fields( 'jaggrok_settings' ); ?>
		<table class="form-table">
			<tr><th><label for="jaggrok_xai_api_key">xAI API Key <span class="required">*</span></label></th>
				<td><input type="password" id="jaggrok_xai_api_key" name="jaggrok_xai_api_key"
				           value="<?php echo esc_attr( get_option( 'jaggrok_xai_api_key' ) ); ?>" class="regular-text" />
					<p class="description"><a href="https://x.ai/api" target="_blank">Get key</a> |
						<button type="button" class="button" id="jaggrok-test-api">Test Connection</button>
						<span id="jaggrok-api-status"></span></p></td></tr>

			<tr><th>Auto-Insert</th><td>
					<?php $auto = get_option( 'jaggrok_auto_insert', 'yes' ); ?>
					<label><input type="radio" name="jaggrok_auto_insert" value="yes" <?php checked( $auto, 'yes' ); ?>> Yes</label>
					<label><input type="radio" name="jaggrok_auto_insert" value="no" <?php checked( $auto, 'no' ); ?>> No</label></td></tr>

			<tr><th>Default Style</th><td>
					<?php $style = get_option( 'jaggrok_theme_style', 'modern' ); ?>
					<select name="jaggrok_theme_style"><option value="modern" <?php selected( $style, 'modern' ); ?>>Modern</option>
						<option value="bold" <?php selected( $style, 'bold' ); ?>>Bold</option><option value="minimal" <?php selected( $style, 'minimal' ); ?>>Minimal</option></select></td></tr>

			<tr><th>Max Tokens</th><td>
					<input type="number" name="jaggrok_max_tokens" value="<?php echo get_option( 'jaggrok_max_tokens', 2000 ); ?>" min="500" max="8000" class="small-text" /> tokens</td></tr>
		</table>
		<?php submit_button(); ?>
	</form>
	<?php if ( get_option( 'jaggrok_api_tested' ) ): ?><div class="notice notice-success"><p>✅ API Connected!</p></div><?php endif; ?>
</div>

<style>.required { color: #d63638; }</style>