<?php if ( ! empty( $support_sections ) ) : ?>
<aside class="aimentor-settings-sidebar" aria-label="<?php esc_attr_e( 'AiMentor help resources', 'aimentor' ); ?>">
    <div class="aimentor-settings-sidebar__card">
        <?php foreach ( $support_sections as $section ) :
                $section_title = isset( $section['title'] ) ? $section['title'] : '';
                $links         = isset( $section['links'] ) && is_array( $section['links'] ) ? $section['links'] : [];
                if ( empty( $links ) ) {
                        continue;
                }
        ?>
        <section class="aimentor-support-section">
            <?php if ( '' !== $section_title ) : ?>
            <h2 class="aimentor-support-section__title"><?php echo esc_html( $section_title ); ?></h2>
            <?php endif; ?>
            <ul class="aimentor-support-list">
                <?php foreach ( $links as $link ) :
                        $label       = isset( $link['label'] ) ? $link['label'] : '';
                        $url         = isset( $link['url'] ) ? $link['url'] : '';
                        $description = isset( $link['description'] ) ? $link['description'] : '';
                        $is_external = 0 === strpos( $url, 'http' );
                ?>
                <li class="aimentor-support-list__item">
                    <a class="aimentor-support-link" href="<?php echo esc_url( $url ); ?>"<?php echo $is_external ? ' target="_blank" rel="noopener noreferrer"' : ''; ?>>
                        <?php echo esc_html( $label ); ?>
                    </a>
                    <?php if ( '' !== $description ) : ?>
                    <p class="aimentor-support-description"><?php echo esc_html( $description ); ?></p>
                    <?php endif; ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </section>
        <?php endforeach; ?>
    </div>
</aside>
<?php endif; ?>
