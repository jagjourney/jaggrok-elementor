<?php
/**
 * Knowledge base settings tab.
 *
 * @package AiMentorElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$packs                  = isset( $knowledge_packs ) && is_array( $knowledge_packs ) ? $knowledge_packs : array();
$rest_nonce_value       = isset( $knowledge_rest_nonce ) ? $knowledge_rest_nonce : wp_create_nonce( 'wp_rest' );
$knowledge_nonce_value  = isset( $knowledge_nonce ) ? $knowledge_nonce : wp_create_nonce( 'aimentor_knowledge_packs' );
$endpoint_url           = esc_url( rest_url( 'aimentor/v1/knowledge-packs' ) );
$initial_packs_attribute = esc_attr( wp_json_encode( array_values( $packs ) ) );

$render_table = static function( $entries ) {
    if ( empty( $entries ) ) {
        ob_start();
        ?>
        <p class="description aimentor-knowledge__empty">
            <?php esc_html_e( 'No knowledge packs have been added yet.', 'aimentor' ); ?>
        </p>
        <?php
        return ob_get_clean();
    }

    ob_start();
    ?>
    <table class="widefat striped aimentor-knowledge__table">
        <thead>
            <tr>
                <th scope="col"><?php esc_html_e( 'Title', 'aimentor' ); ?></th>
                <th scope="col"><?php esc_html_e( 'Summary', 'aimentor' ); ?></th>
                <th scope="col"><?php esc_html_e( 'Updated', 'aimentor' ); ?></th>
                <th scope="col" class="aimentor-knowledge__actions-header"><?php esc_html_e( 'Actions', 'aimentor' ); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $entries as $entry ) :
                $pack_id      = isset( $entry['id'] ) ? (string) $entry['id'] : '';
                $title        = isset( $entry['title'] ) ? (string) $entry['title'] : '';
                $summary      = isset( $entry['summary'] ) ? (string) $entry['summary'] : '';
                $guidance     = isset( $entry['guidance'] ) ? (string) $entry['guidance'] : '';
                $updated_at   = isset( $entry['updated_at_gmt'] ) && $entry['updated_at_gmt'] ? $entry['updated_at_gmt'] : '';
                $updated_human = '';

                if ( $updated_at ) {
                    $timestamp = strtotime( $updated_at );
                    if ( $timestamp ) {
                        $updated_human = sprintf(
                            /* translators: %s: Human readable time difference. */
                            __( '%s ago', 'aimentor' ),
                            human_time_diff( $timestamp, current_time( 'timestamp' ) )
                        );
                    }
                }

                if ( '' === $updated_human && $updated_at ) {
                    $updated_human = $updated_at;
                }
            ?>
            <tr data-id="<?php echo esc_attr( $pack_id ); ?>">
                <td>
                    <strong><?php echo esc_html( $title ); ?></strong>
                </td>
                <td>
                    <div class="aimentor-knowledge__summary">
                        <span><?php echo esc_html( wp_trim_words( $summary ? $summary : $guidance, 30, '…' ) ); ?></span>
                    </div>
                </td>
                <td>
                    <?php echo esc_html( $updated_human ? $updated_human : __( 'Just added', 'aimentor' ) ); ?>
                </td>
                <td class="aimentor-knowledge__actions">
                    <button type="button" class="button aimentor-knowledge__edit" data-id="<?php echo esc_attr( $pack_id ); ?>">
                        <?php esc_html_e( 'Edit', 'aimentor' ); ?>
                    </button>
                    <button type="button" class="button button-link-delete aimentor-knowledge__delete" data-id="<?php echo esc_attr( $pack_id ); ?>" data-label="<?php echo esc_attr( $title ); ?>">
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
        <div class="aimentor-knowledge" data-rest-endpoint="<?php echo esc_url( $endpoint_url ); ?>" data-rest-nonce="<?php echo esc_attr( $rest_nonce_value ); ?>" data-initial-packs="<?php echo $initial_packs_attribute; ?>">
            <h2><?php esc_html_e( 'Knowledge Base', 'aimentor' ); ?></h2>
            <p class="description"><?php esc_html_e( 'Create reusable knowledge packs that keep AiMentor aligned with your brand playbooks, product facts, and compliance notes.', 'aimentor' ); ?></p>

            <div class="aimentor-knowledge__notice" role="status" aria-live="polite" hidden></div>

            <form class="aimentor-knowledge__form" method="post">
                <?php wp_nonce_field( 'wp_rest', 'aimentor_rest_nonce' ); ?>
                <?php wp_nonce_field( 'aimentor_knowledge_packs', 'aimentor_knowledge_nonce' ); ?>
                <input type="hidden" name="id" id="aimentor-knowledge-id" value="" />
                <table class="form-table">
                    <tr>
                        <th scope="row"><label for="aimentor-knowledge-title"><?php esc_html_e( 'Title', 'aimentor' ); ?></label></th>
                        <td>
                            <input type="text" id="aimentor-knowledge-title" name="title" class="regular-text" required placeholder="<?php esc_attr_e( 'e.g., Brand voice and audience cues', 'aimentor' ); ?>" />
                            <p class="description"><?php esc_html_e( 'Name the knowledge pack so editors can identify it quickly.', 'aimentor' ); ?></p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="aimentor-knowledge-summary"><?php esc_html_e( 'Summary', 'aimentor' ); ?></label></th>
                        <td>
                            <textarea id="aimentor-knowledge-summary" name="summary" rows="3" class="large-text" placeholder="<?php esc_attr_e( 'Optional short description that appears in pickers.', 'aimentor' ); ?>"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="aimentor-knowledge-guidance"><?php esc_html_e( 'Guidance', 'aimentor' ); ?></label></th>
                        <td>
                            <textarea id="aimentor-knowledge-guidance" name="guidance" rows="6" class="large-text" required placeholder="<?php esc_attr_e( 'Provide the detailed facts, tone cues, or compliance rules AiMentor should follow.', 'aimentor' ); ?>"></textarea>
                        </td>
                    </tr>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary" data-action="save">
                        <?php esc_html_e( 'Save Knowledge Pack', 'aimentor' ); ?>
                    </button>
                    <button type="button" class="button button-secondary aimentor-knowledge__cancel" data-action="reset" hidden>
                        <?php esc_html_e( 'Cancel Editing', 'aimentor' ); ?>
                    </button>
                </p>
            </form>

            <hr />

            <section class="aimentor-knowledge__section" aria-labelledby="aimentor-knowledge-list">
                <h3 id="aimentor-knowledge-list"><?php esc_html_e( 'Knowledge Packs', 'aimentor' ); ?></h3>
                <p class="description"><?php esc_html_e( 'Edit or remove packs as your brand playbooks evolve. Changes are available immediately inside the Elementor modal.', 'aimentor' ); ?></p>
                <div class="aimentor-knowledge__list">
                    <?php echo $render_table( $packs ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </div>
            </section>
        </div>
    </div>
    <?php include plugin_dir_path( __FILE__ ) . 'sidebar-support.php'; ?>
</div>
