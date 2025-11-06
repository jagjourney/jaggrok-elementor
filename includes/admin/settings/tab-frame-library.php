<?php
/**
 * Frame Library settings tab.
 *
 * @package AiMentorElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$items           = isset( $items ) && is_array( $items ) ? $items : [];
$status_type     = isset( $status_type ) ? $status_type : '';
$status_msg      = isset( $status_msg ) ? $status_msg : '';
$archive_enabled = isset( $archive_enabled ) ? (bool) $archive_enabled : false;
$frame_library_endpoint = isset( $frame_library_endpoint ) ? $frame_library_endpoint : '';
$rest_nonce      = isset( $rest_nonce ) ? $rest_nonce : wp_create_nonce( 'wp_rest' );
$frame_presets   = isset( $frame_presets ) && is_array( $frame_presets ) ? $frame_presets : [];
$provider_labels = isset( $provider_labels ) && is_array( $provider_labels ) ? $provider_labels : [];
$archive_manage_url = admin_url( 'edit.php?post_type=ai_layout' );
$has_items       = ! empty( $items );
$layout_archive_enabled = $archive_enabled;

?>
<div class="aimentor-settings-layout">
    <div class="aimentor-settings-main">
        <h2><?php esc_html_e( 'Frame Library', 'aimentor' ); ?></h2>
        <p class="description"><?php esc_html_e( 'Promote archived AI layouts into reusable frames that appear in the Elementor editor. Frames stay synced with their metadata, summaries, and preview imagery.', 'aimentor' ); ?></p>

        <?php if ( $status_msg ) : ?>
        <div class="notice <?php echo 'error' === $status_type ? 'notice-error' : 'notice-success'; ?> is-dismissible">
            <p><?php echo esc_html( $status_msg ); ?></p>
        </div>
        <?php endif; ?>

        <?php if ( ! $layout_archive_enabled ) : ?>
        <div class="notice notice-warning">
            <p>
                <?php
                printf(
                    '%s <a href="%s">%s</a>',
                    esc_html__( 'Layout archival is currently disabled. Enable archival in Brand & Automation to start promoting frames.', 'aimentor' ),
                    esc_url( add_query_arg( 'tab', 'brand-automation', admin_url( 'options-general.php?page=aimentor-settings' ) ) ),
                    esc_html__( 'Open Brand & Automation settings.', 'aimentor' )
                );
                ?>
            </p>
        </div>
        <?php endif; ?>

        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="aimentor-frame-library__form" data-rest-endpoint="<?php echo esc_url( $frame_library_endpoint ); ?>" data-rest-nonce="<?php echo esc_attr( $rest_nonce ); ?>">
            <?php wp_nonce_field( 'aimentor_save_frame_library', 'aimentor_frame_library_nonce' ); ?>
            <input type="hidden" name="action" value="aimentor_save_frame_library" />

            <?php if ( ! $has_items ) : ?>
            <p class="description">
                <?php esc_html_e( 'No archived layouts found yet. Generate layouts in Elementor, archive them, then return here to curate frames.', 'aimentor' ); ?>
            </p>
            <?php else : ?>
            <div class="aimentor-frame-library__grid">
                <?php foreach ( $items as $item ) :
                    $sections_placeholder = __( "Hero\nBenefits\nTestimonials", 'aimentor' );
                    $preview_url = $item['preview_url'] ? $item['preview_url'] : '';
                    $provider_key = $item['provider'] ? $item['provider'] : '';
                    $provider_name = $item['provider_label'] ? $item['provider_label'] : ( $provider_key ? strtoupper( $provider_key ) : __( 'Unknown', 'aimentor' ) );
                    $modified_caption = '';
                    if ( ! empty( $item['modified_human'] ) ) {
                        /* translators: %s: Human readable time difference. */
                        $modified_caption = sprintf( esc_html__( 'Updated %s ago', 'aimentor' ), esc_html( $item['modified_human'] ) );
                    }
                ?>
                <article class="aimentor-frame-library__item" data-frame-id="<?php echo esc_attr( $item['id'] ); ?>">
                    <header class="aimentor-frame-library__header">
                        <div>
                            <h3><?php echo esc_html( $item['title'] ); ?></h3>
                            <?php if ( $modified_caption ) : ?>
                            <p class="aimentor-frame-library__meta"><?php echo esc_html( $modified_caption ); ?></p>
                            <?php endif; ?>
                        </div>
                        <label class="aimentor-frame-library__toggle">
                            <input type="checkbox" name="layouts[<?php echo esc_attr( $item['id'] ); ?>][enabled]" value="yes" <?php checked( $item['enabled'], true ); ?> />
                            <span><?php esc_html_e( 'Promote as frame', 'aimentor' ); ?></span>
                        </label>
                    </header>

                    <div class="aimentor-frame-library__body">
                        <div class="aimentor-frame-library__preview" data-placeholder="<?php esc_attr_e( 'Add preview', 'aimentor' ); ?>">
                            <?php if ( $preview_url ) : ?>
                            <img src="<?php echo esc_url( $preview_url ); ?>" alt="" />
                            <?php else : ?>
                            <span class="aimentor-frame-library__preview-placeholder"><?php esc_html_e( 'Preview pending', 'aimentor' ); ?></span>
                            <?php endif; ?>
                            <div class="aimentor-frame-library__preview-actions">
                                <button type="button" class="button button-secondary aimentor-frame-library__select-preview" data-target="layouts[<?php echo esc_attr( $item['id'] ); ?>][preview_id]">
                                    <?php esc_html_e( 'Select preview', 'aimentor' ); ?>
                                </button>
                                <button type="button" class="button button-link-delete aimentor-frame-library__clear-preview" data-target="layouts[<?php echo esc_attr( $item['id'] ); ?>][preview_id]">
                                    <?php esc_html_e( 'Remove', 'aimentor' ); ?>
                                </button>
                            </div>
                            <input type="hidden" class="aimentor-frame-library__preview-input" name="layouts[<?php echo esc_attr( $item['id'] ); ?>][preview_id]" value="<?php echo esc_attr( $item['preview_id'] ); ?>" />
                        </div>

                        <div class="aimentor-frame-library__fields">
                            <label>
                                <span><?php esc_html_e( 'Internal summary', 'aimentor' ); ?></span>
                                <textarea name="layouts[<?php echo esc_attr( $item['id'] ); ?>][summary]" rows="3" class="widefat" placeholder="<?php esc_attr_e( 'Short description displayed in the Elementor frame panel.', 'aimentor' ); ?>"><?php echo esc_textarea( $item['summary'] ); ?></textarea>
                            </label>
                            <label>
                                <span><?php esc_html_e( 'Suggested sections', 'aimentor' ); ?></span>
                                <textarea name="layouts[<?php echo esc_attr( $item['id'] ); ?>][sections]" rows="3" class="widefat" placeholder="<?php echo esc_attr( $sections_placeholder ); ?>"><?php echo esc_textarea( $item['sections_text'] ); ?></textarea>
                                <p class="description"><?php esc_html_e( 'One section per line. Displayed as quick cues in the Elementor frame panel.', 'aimentor' ); ?></p>
                            </label>
                            <label>
                                <span><?php esc_html_e( 'Provider', 'aimentor' ); ?></span>
                                <select name="layouts[<?php echo esc_attr( $item['id'] ); ?>][provider]" class="widefat">
                                    <option value="">—</option>
                                    <?php foreach ( $provider_labels as $provider_key_option => $label ) : ?>
                                    <option value="<?php echo esc_attr( $provider_key_option ); ?>" <?php selected( $item['provider'], $provider_key_option ); ?>><?php echo esc_html( $label ); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </label>
                            <label>
                                <span><?php esc_html_e( 'Prompt seed', 'aimentor' ); ?></span>
                                <textarea name="layouts[<?php echo esc_attr( $item['id'] ); ?>][prompt]" rows="4" class="widefat" placeholder="<?php esc_attr_e( 'Optional: update the prompt editors will see when seeding this frame.', 'aimentor' ); ?>"><?php echo esc_textarea( $item['prompt'] ); ?></textarea>
                            </label>
                            <div class="aimentor-frame-library__meta-columns">
                                <label>
                                    <span><?php esc_html_e( 'Model', 'aimentor' ); ?></span>
                                    <input type="text" name="layouts[<?php echo esc_attr( $item['id'] ); ?>][model]" value="<?php echo esc_attr( $item['model'] ); ?>" class="regular-text" />
                                </label>
                                <label>
                                    <span><?php esc_html_e( 'Task', 'aimentor' ); ?></span>
                                    <input type="text" name="layouts[<?php echo esc_attr( $item['id'] ); ?>][task]" value="<?php echo esc_attr( $item['task'] ); ?>" class="regular-text" />
                                </label>
                                <label>
                                    <span><?php esc_html_e( 'Tier', 'aimentor' ); ?></span>
                                    <input type="text" name="layouts[<?php echo esc_attr( $item['id'] ); ?>][tier]" value="<?php echo esc_attr( $item['tier'] ); ?>" class="regular-text" />
                                </label>
                            </div>
                            <?php if ( ! empty( $frame_presets ) ) :
                                $matched_presets = array_filter( $frame_presets, static function ( $preset ) use ( $provider_key ) {
                                    return isset( $preset['provider'] ) && $preset['provider'] === $provider_key;
                                } );
                            ?>
                            <details class="aimentor-frame-library__presets">
                                <summary><?php esc_html_e( 'Reference frame presets', 'aimentor' ); ?></summary>
                                <ul>
                                    <?php foreach ( $matched_presets as $preset_id => $preset_meta ) : ?>
                                    <li>
                                        <strong><?php echo esc_html( $preset_meta['label'] ?? $preset_id ); ?></strong>
                                        <?php if ( ! empty( $preset_meta['description'] ) ) : ?>
                                        <p><?php echo esc_html( $preset_meta['description'] ); ?></p>
                                        <?php endif; ?>
                                    </li>
                                    <?php endforeach; ?>
                                    <?php if ( empty( $matched_presets ) ) : ?>
                                    <li><?php esc_html_e( 'No presets mapped to this provider yet.', 'aimentor' ); ?></li>
                                    <?php endif; ?>
                                </ul>
                            </details>
                            <?php endif; ?>
                            <?php if ( ! empty( $item['edit_link'] ) ) : ?>
                            <p class="aimentor-frame-library__edit-link"><a href="<?php echo esc_url( $item['edit_link'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Edit layout', 'aimentor' ); ?></a></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <p class="submit">
                <button type="submit" class="button button-primary" <?php disabled( ! $has_items ); ?>>
                    <?php esc_html_e( 'Save frame library', 'aimentor' ); ?>
                </button>
                <a class="button" href="<?php echo esc_url( $archive_manage_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Manage AI Layouts', 'aimentor' ); ?></a>
            </p>
        </form>
    </div>
    <?php include plugin_dir_path( __FILE__ ) . 'sidebar-support.php'; ?>
</div>
