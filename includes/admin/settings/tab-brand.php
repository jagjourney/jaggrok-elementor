<?php
$quick_actions_payload = isset( $quick_actions_payload ) && is_array( $quick_actions_payload )
    ? $quick_actions_payload
    : aimentor_get_quick_actions_payload();
$quick_actions_rest_nonce = wp_create_nonce( 'wp_rest' );
$quick_actions_endpoint   = esc_url( rest_url( 'aimentor/v1/quick-actions' ) );
$quick_actions_json       = wp_json_encode( $quick_actions_payload );

if ( false === $quick_actions_json ) {
    $quick_actions_json = wp_json_encode(
        [
            'registry' => [],
            'settings' => [],
        ]
    );
}
?>

<div class="aimentor-settings-layout">
    <div class="aimentor-settings-main">
        <form method="post" action="options.php" class="aimentor-settings-form aimentor-settings-form--brand">
            <?php settings_fields( 'aimentor_settings' ); ?>
            <table class="form-table">
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
                    <th scope="row"><?php esc_html_e( 'Archive Layouts', 'aimentor' ); ?></th>
                    <td>
                        <?php
                        $archive_layouts    = get_option( 'aimentor_archive_layouts', 'no' );
                        $archive_show_ui    = get_option( 'aimentor_archive_layouts_show_ui', 'no' );
                        $archive_list_table = admin_url( 'edit.php?post_type=ai_layout' );
                        ?>
                        <input type="hidden" name="aimentor_archive_layouts" value="no" />
                        <label>
                            <input type="checkbox" name="aimentor_archive_layouts" value="yes" <?php checked( $archive_layouts, 'yes' ); ?> />
                            <?php esc_html_e( 'Save each generated canvas or content response for later review.', 'aimentor' ); ?>
                        </label>
                        <p class="description"><?php esc_html_e( 'Stores the raw payload as a private AI Layout post so you can audit or reuse results.', 'aimentor' ); ?></p>
                        <input type="hidden" name="aimentor_archive_layouts_show_ui" value="no" />
                        <?php if ( 'yes' === $archive_layouts ) : ?>
                            <label style="margin-top:8px; display:block;">
                                <input type="checkbox" name="aimentor_archive_layouts_show_ui" value="yes" <?php checked( $archive_show_ui, 'yes' ); ?> />
                                <?php esc_html_e( 'Expose the AI Layout archive screens in the WordPress admin.', 'aimentor' ); ?>
                            </label>
                            <?php if ( current_user_can( 'edit_posts' ) ) : ?>
                                <p class="description">
                                    <?php
                                    printf(
                                            wp_kses_post( __( 'Browse saved layouts in the <a href="%s">AI Layouts list table</a>.', 'aimentor' ) ),
                                            esc_url( $archive_list_table )
                                    );
                                    ?>
                                </p>
                            <?php endif; ?>
                        <?php else : ?>
                            <p class="description"><?php esc_html_e( 'Enable archival to keep a running history of generated layouts.', 'aimentor' ); ?></p>
                        <?php endif; ?>
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
            </table>
            <?php submit_button(); ?>
        </form>

        <section class="aimentor-quick-actions" data-rest-endpoint="<?php echo esc_url( $quick_actions_endpoint ); ?>" data-rest-nonce="<?php echo esc_attr( $quick_actions_rest_nonce ); ?>" data-initial-actions="<?php echo esc_attr( $quick_actions_json ); ?>">
            <h2><?php esc_html_e( 'Quick Actions', 'aimentor' ); ?></h2>
            <p class="description"><?php esc_html_e( 'Choose which quick actions appear in the Elementor modal and fine-tune the prompt and system guidance each action sends.', 'aimentor' ); ?></p>
            <div class="aimentor-quick-actions__notice" role="status" aria-live="polite" hidden></div>
            <?php wp_nonce_field( 'wp_rest', 'aimentor_rest_nonce' ); ?>
            <div class="aimentor-quick-actions__list" data-role="quick-actions-list"></div>
            <p class="submit">
                <button type="button" class="button button-primary aimentor-quick-actions__save"><?php esc_html_e( 'Save Quick Actions', 'aimentor' ); ?></button>
            </p>
        </section>
    </div>
    <?php include plugin_dir_path( __FILE__ ) . 'sidebar-support.php'; ?>
</div>
