<?php
/**
 * Saved prompts settings tab.
 *
 * @package AiMentorElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$payload                 = isset( $saved_prompts_payload ) && is_array( $saved_prompts_payload ) ? $saved_prompts_payload : [];
$personal_prompts        = isset( $payload['user'] ) && is_array( $payload['user'] ) ? $payload['user'] : [];
$shared_prompts          = isset( $payload['global'] ) && is_array( $payload['global'] ) ? $payload['global'] : [];
$rest_nonce_value        = isset( $saved_prompts_rest_nonce ) ? $saved_prompts_rest_nonce : wp_create_nonce( 'wp_rest' );
$saved_prompts_nonce_val = isset( $saved_prompts_nonce ) ? $saved_prompts_nonce : wp_create_nonce( 'aimentor_saved_prompts' );
$endpoint_url            = esc_url( rest_url( 'aimentor/v1/prompts' ) );
$can_manage_shared       = current_user_can( 'manage_options' );
$initial_prompts_attr    = wp_json_encode(
    [
        'global' => array_values( $shared_prompts ),
        'user'   => array_values( $personal_prompts ),
    ]
);

$render_table = static function( $entries, $scope ) {
    if ( empty( $entries ) ) {
        ob_start();
        ?>
        <p class="description aimentor-saved-prompts__empty" data-scope="<?php echo esc_attr( $scope ); ?>">
            <?php esc_html_e( 'No prompts saved yet.', 'aimentor' ); ?>
        </p>
        <?php
        return ob_get_clean();
    }

    ob_start();
    ?>
    <table class="widefat striped aimentor-saved-prompts__table" data-scope="<?php echo esc_attr( $scope ); ?>">
        <thead>
            <tr>
                <th scope="col"><?php esc_html_e( 'Label', 'aimentor' ); ?></th>
                <th scope="col"><?php esc_html_e( 'Prompt', 'aimentor' ); ?></th>
                <th scope="col" class="aimentor-saved-prompts__actions-header"><?php esc_html_e( 'Actions', 'aimentor' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $entries as $entry ) :
                $prompt_id    = isset( $entry['id'] ) ? (string) $entry['id'] : '';
                $prompt_label = isset( $entry['label'] ) ? (string) $entry['label'] : '';
                $prompt_body  = isset( $entry['prompt'] ) ? (string) $entry['prompt'] : '';
                $excerpt      = wp_trim_words( wp_strip_all_tags( $prompt_body ), 30, '…' );
            ?>
            <tr data-id="<?php echo esc_attr( $prompt_id ); ?>" data-scope="<?php echo esc_attr( $scope ); ?>">
                <td>
                    <strong><?php echo esc_html( $prompt_label ); ?></strong>
                </td>
                <td>
                    <div class="aimentor-saved-prompts__excerpt">
                        <span><?php echo esc_html( $excerpt ); ?></span>
                    </div>
                </td>
                <td class="aimentor-saved-prompts__actions">
                    <button type="button" class="button button-link-delete aimentor-saved-prompts__delete" data-id="<?php echo esc_attr( $prompt_id ); ?>" data-scope="<?php echo esc_attr( $scope ); ?>" data-label="<?php echo esc_attr( $prompt_label ); ?>">
                        <?php esc_html_e( 'Delete', 'aimentor' ); ?>
                    </button>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php
    return ob_get_clean();
};
?>

<div class="aimentor-settings-layout">
    <div class="aimentor-settings-main">
        <div class="aimentor-saved-prompts" data-rest-endpoint="<?php echo esc_url( $endpoint_url ); ?>" data-rest-nonce="<?php echo esc_attr( $rest_nonce_value ); ?>" data-saved-prompt-nonce="<?php echo esc_attr( $saved_prompts_nonce_val ); ?>" data-initial-prompts="<?php echo esc_attr( $initial_prompts_attr ); ?>">
            <h2><?php esc_html_e( 'Saved Prompts', 'aimentor' ); ?></h2>
            <p class="description"><?php esc_html_e( 'Create reusable prompts for yourself or share them with your team. Updates appear instantly inside the Elementor widget.', 'aimentor' ); ?></p>

            <div class="aimentor-saved-prompts__notice" role="status" aria-live="polite" hidden></div>

            <form class="aimentor-saved-prompts__form" method="post">
                <?php wp_nonce_field( 'wp_rest', 'aimentor_rest_nonce' ); ?>
                <?php wp_nonce_field( 'aimentor_saved_prompts', 'aimentor_saved_prompts_nonce' ); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="aimentor-saved-prompt-label"><?php esc_html_e( 'Label', 'aimentor' ); ?></label></th>
                        <td>
                            <input type="text" id="aimentor-saved-prompt-label" name="label" class="regular-text" placeholder="<?php esc_attr_e( 'Optional name for quick selection', 'aimentor' ); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="aimentor-saved-prompt-scope"><?php esc_html_e( 'Scope', 'aimentor' ); ?></label></th>
                        <td>
                            <?php if ( $can_manage_shared ) : ?>
                            <select id="aimentor-saved-prompt-scope" name="scope">
                                <option value="user" selected><?php esc_html_e( 'My Prompt', 'aimentor' ); ?></option>
                                <option value="global"><?php esc_html_e( 'Shared Prompt', 'aimentor' ); ?></option>
                            </select>
                            <p class="description"><?php esc_html_e( 'Shared prompts are visible to every editor who can access AiMentor.', 'aimentor' ); ?></p>
                            <?php else : ?>
                            <input type="hidden" name="scope" value="user" />
                            <p class="description"><?php esc_html_e( 'Prompts you save here are only visible to you.', 'aimentor' ); ?></p>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="aimentor-saved-prompt-body"><?php esc_html_e( 'Prompt', 'aimentor' ); ?></label></th>
                        <td>
                            <textarea id="aimentor-saved-prompt-body" name="prompt" rows="6" class="large-text" required placeholder="<?php esc_attr_e( 'Describe the layout or copy you need…', 'aimentor' ); ?>"></textarea>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary" data-action="create">
                        <?php esc_html_e( 'Save Prompt', 'aimentor' ); ?>
                    </button>
                </p>
            </form>

            <hr />

            <section class="aimentor-saved-prompts__section" aria-labelledby="aimentor-saved-prompts-user">
                <h3 id="aimentor-saved-prompts-user"><?php esc_html_e( 'My Prompts', 'aimentor' ); ?></h3>
                <div class="aimentor-saved-prompts__list" data-scope="user">
                    <?php echo $render_table( $personal_prompts, 'user' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
            </section>

            <section class="aimentor-saved-prompts__section" aria-labelledby="aimentor-saved-prompts-global">
                <h3 id="aimentor-saved-prompts-global"><?php esc_html_e( 'Shared Prompts', 'aimentor' ); ?></h3>
                <?php if ( ! $can_manage_shared ) : ?>
                <p class="description"><?php esc_html_e( 'Only administrators can add or remove shared prompts.', 'aimentor' ); ?></p>
                <?php endif; ?>
                <div class="aimentor-saved-prompts__list" data-scope="global">
                    <?php echo $render_table( $shared_prompts, 'global' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
            </section>
        </div>
    </div>
    <?php include plugin_dir_path( __FILE__ ) . 'sidebar-support.php'; ?>
</div>
