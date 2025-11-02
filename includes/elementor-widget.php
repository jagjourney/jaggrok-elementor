<?php
// ============================================================================
// AiMentor ELEMENTOR WIDGET v1.4.2 (CANVAS INSERT)
// ============================================================================

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class AiMentor_AI_Generator_Widget extends Widget_Base {
    public function get_name() { return 'aimentor-ai-generator'; }
    public function get_title() { return 'AiMentor AI Generator'; }
    public function get_icon() { return 'eicon-robot'; }
    public function get_categories() { return [ 'general' ]; }

    protected function register_controls() {
        $this->start_controls_section( 'prompt_section', [ 'label' => __( 'AiMentor Prompt', 'aimentor' ) ] );
        $provider_meta = function_exists( 'aimentor_get_provider_meta_map' ) ? aimentor_get_provider_meta_map() : [
                'grok'   => [
                        'label'      => __( 'xAI Grok', 'aimentor' ),
                        'icon'       => '🚀',
                        'summary'    => sprintf( __( 'Content generated with %s.', 'aimentor' ), __( 'xAI Grok', 'aimentor' ) ),
                        'badgeText'  => __( 'xAI', 'aimentor' ),
                        'badgeColor' => '#1E1E1E',
                ],
                'openai' => [
                        'label'      => __( 'OpenAI', 'aimentor' ),
                        'icon'       => '🔷',
                        'summary'    => sprintf( __( 'Content generated with %s.', 'aimentor' ), __( 'OpenAI', 'aimentor' ) ),
                        'badgeText'  => __( 'OpenAI', 'aimentor' ),
                        'badgeColor' => '#2B8CFF',
                ],
        ];
        $provider_labels = wp_list_pluck( $provider_meta, 'label' );
        $default_provider = get_option( 'aimentor_provider', 'grok' );
        if ( ! array_key_exists( $default_provider, $provider_labels ) ) {
                $provider_keys    = array_keys( $provider_labels );
                $default_provider = isset( $provider_keys[0] ) ? $provider_keys[0] : 'grok';
        }
        $default_options = function_exists( 'aimentor_get_default_options' ) ? aimentor_get_default_options() : [
                'aimentor_default_generation_type' => 'content',
                'aimentor_default_performance'     => 'fast',
        ];
        $default_task = function_exists( 'aimentor_sanitize_generation_type' ) ? aimentor_sanitize_generation_type( get_option( 'aimentor_default_generation_type', $default_options['aimentor_default_generation_type'] ) ) : 'content';
        $default_tier = function_exists( 'aimentor_sanitize_performance_tier' ) ? aimentor_sanitize_performance_tier( get_option( 'aimentor_default_performance', $default_options['aimentor_default_performance'] ) ) : 'fast';
        $is_pro_active = function_exists( 'aimentor_is_pro_active' ) ? aimentor_is_pro_active() : false;
        $this->add_control( 'provider', [
                'label'   => __( 'Provider', 'aimentor' ),
                'type'    => Controls_Manager::SELECT,
                'options' => $provider_labels,
                'default' => $default_provider,
        ] );
        $generation_options = [ 'content' => __( 'Page Copy', 'aimentor' ) ];
        if ( $is_pro_active ) {
                $generation_options['canvas'] = __( 'Page Layout', 'aimentor' );
        }
        $this->add_control( 'generation_type', [
                'label'       => __( 'Generation Type', 'aimentor' ),
                'type'        => Controls_Manager::SELECT,
                'options'     => $is_pro_active ? [
                        'canvas'  => __( 'Page Layout', 'aimentor' ),
                        'content' => __( 'Page Copy', 'aimentor' ),
                ] : $generation_options,
                'default'     => $is_pro_active ? $default_task : 'content',
                'description' => __( 'Choose whether AiMentor drafts layouts or copy by default.', 'aimentor' ),
        ] );
        $this->add_control( 'performance', [
                'label'       => __( 'Performance', 'aimentor' ),
                'type'        => Controls_Manager::SELECT,
                'options'     => [
                        'fast'    => __( 'Fast', 'aimentor' ),
                        'quality' => __( 'Quality', 'aimentor' ),
                ],
                'default'     => $default_tier,
                'description' => __( 'Pick the balance between speed and fidelity for AiMentor.', 'aimentor' ),
        ] );
        $this->add_control( 'prompt', [
                'label' => __( 'Describe your page', 'aimentor' ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => 'Create a modern donation page with hero call out, and three columns for different products people can donate to.',
                'placeholder' => 'e.g., "Landing page with blue hero and contact form"'
        ]);
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $widget_id = $this->get_id();
        $provider_meta = function_exists( 'aimentor_get_provider_meta_map' ) ? aimentor_get_provider_meta_map() : $provider_meta;
        $provider_labels = wp_list_pluck( $provider_meta, 'label' );
        $provider_setting = isset( $settings['provider'] ) ? $settings['provider'] : get_option( 'aimentor_provider', 'grok' );
        if ( ! array_key_exists( $provider_setting, $provider_meta ) ) {
                $provider_keys    = array_keys( $provider_meta );
                $provider_setting = isset( $provider_keys[0] ) ? $provider_keys[0] : 'grok';
        }
        $default_options   = function_exists( 'aimentor_get_default_options' ) ? aimentor_get_default_options() : [
                'aimentor_default_generation_type' => 'content',
                'aimentor_default_performance'     => 'fast',
        ];
        $is_pro_active     = function_exists( 'aimentor_is_pro_active' ) ? aimentor_is_pro_active() : false;
        $default_task      = function_exists( 'aimentor_sanitize_generation_type' ) ? aimentor_sanitize_generation_type( get_option( 'aimentor_default_generation_type', $default_options['aimentor_default_generation_type'] ) ) : 'content';
        $default_tier      = function_exists( 'aimentor_sanitize_performance_tier' ) ? aimentor_sanitize_performance_tier( get_option( 'aimentor_default_performance', $default_options['aimentor_default_performance'] ) ) : 'fast';
        $generation_type   = isset( $settings['generation_type'] ) ? $settings['generation_type'] : $default_task;
        if ( function_exists( 'aimentor_sanitize_generation_type' ) ) {
                $generation_type = aimentor_sanitize_generation_type( $generation_type );
        }
        if ( ! $is_pro_active && 'canvas' === $generation_type ) {
                $generation_type = 'content';
        }
        $performance_tier = isset( $settings['performance'] ) ? $settings['performance'] : $default_tier;
        if ( function_exists( 'aimentor_sanitize_performance_tier' ) ) {
                $performance_tier = aimentor_sanitize_performance_tier( $performance_tier );
        }
        $active_provider_meta = $provider_meta[ $provider_setting ];
        static $styles_printed = false;
        if ( ! $styles_printed ) {
                $styles_printed = true;
                ?>
                <style>
                        .aimentor-provider-display,
                        .jaggrok-provider-display {
                                display: flex;
                                align-items: center;
                                gap: 8px;
                                margin-top: 8px;
                        }
                        .aimentor-provider-badge,
                        .jaggrok-provider-badge {
                                display: inline-flex;
                                align-items: center;
                                padding: 2px 8px;
                                border-radius: 999px;
                                font-size: 11px;
                                font-weight: 600;
                                color: #ffffff;
                                text-transform: uppercase;
                                letter-spacing: 0.05em;
                        }
                </style>
                <?php
        }
        ?>
        <div class="aimentor-widget jaggrok-widget" id="aimentor-widget-<?php echo esc_attr( $widget_id ); ?>">
            <div class="aimentor-provider-selector jaggrok-provider-selector">
                <label for="aimentor-provider-<?php echo esc_attr( $widget_id ); ?>"><?php esc_html_e( 'Provider', 'aimentor' ); ?></label>
                <select class="aimentor-provider-control jaggrok-provider-control" id="aimentor-provider-<?php echo esc_attr( $widget_id ); ?>">
                    <?php foreach ( $provider_labels as $key => $label ) : ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $provider_setting, $key ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="aimentor-provider-display jaggrok-provider-display">
                    <span class="aimentor-provider-active-icon jaggrok-provider-active-icon" id="aimentor-provider-icon-<?php echo esc_attr( $widget_id ); ?>" aria-hidden="true"><?php echo esc_html( $active_provider_meta['icon'] ); ?></span>
                    <span class="aimentor-provider-active-label jaggrok-provider-active-label" id="aimentor-provider-label-<?php echo esc_attr( $widget_id ); ?>"><?php echo esc_html( $active_provider_meta['label'] ); ?></span>
                    <span class="aimentor-provider-badge jaggrok-provider-badge" id="aimentor-provider-badge-<?php echo esc_attr( $widget_id ); ?>" style="background-color: <?php echo esc_attr( $active_provider_meta['badgeColor'] ); ?>;">
                        <?php echo esc_html( $active_provider_meta['badgeText'] ); ?>
                    </span>
                </div>
                <p class="aimentor-provider-summary jaggrok-provider-summary" id="aimentor-provider-summary-<?php echo esc_attr( $widget_id ); ?>"><?php echo esc_html( $active_provider_meta['summary'] ); ?></p>
            </div>
            <div class="aimentor-generation-controls">
                <label for="aimentor-generation-type-<?php echo esc_attr( $widget_id ); ?>"><?php esc_html_e( 'Generation Type', 'aimentor' ); ?></label>
                <select id="aimentor-generation-type-<?php echo esc_attr( $widget_id ); ?>" class="aimentor-generation-select" aria-label="<?php esc_attr_e( 'AiMentor generation type', 'aimentor' ); ?>" <?php echo $is_pro_active ? '' : 'disabled="disabled"'; ?>>
                    <option value="content" <?php selected( $generation_type, 'content' ); ?>><?php esc_html_e( 'Page Copy', 'aimentor' ); ?></option>
                    <option value="canvas" <?php selected( $generation_type, 'canvas' ); ?> <?php echo $is_pro_active ? '' : 'disabled'; ?>><?php esc_html_e( 'Page Layout', 'aimentor' ); ?></option>
                </select>
                <label for="aimentor-performance-<?php echo esc_attr( $widget_id ); ?>"><?php esc_html_e( 'Performance', 'aimentor' ); ?></label>
                <select id="aimentor-performance-<?php echo esc_attr( $widget_id ); ?>" class="aimentor-performance-select" aria-label="<?php esc_attr_e( 'AiMentor performance preference', 'aimentor' ); ?>">
                    <option value="fast" <?php selected( $performance_tier, 'fast' ); ?>><?php esc_html_e( 'Fast', 'aimentor' ); ?></option>
                    <option value="quality" <?php selected( $performance_tier, 'quality' ); ?>><?php esc_html_e( 'Quality', 'aimentor' ); ?></option>
                </select>
                <p class="aimentor-context-summary" id="aimentor-context-summary-<?php echo esc_attr( $widget_id ); ?>" aria-live="polite"></p>
            </div>
            <textarea class="aimentor-prompt jaggrok-prompt" id="aimentor-prompt-<?php echo $widget_id; ?>" rows="4" style="width:100%;"><?php echo esc_textarea( $settings['prompt'] ); ?></textarea>
            <button class="aimentor-generate-btn jaggrok-generate-btn" id="aimentor-btn-<?php echo esc_attr( $widget_id ); ?>" style="margin:10px 0;" aria-label="<?php esc_attr_e( 'Ask AiMentor to generate content', 'aimentor' ); ?>">
                <?php echo esc_html( sprintf( __( 'Ask AiMentor via %s', 'aimentor' ), $active_provider_meta['label'] ) ); ?>
            </button>
            <div class="aimentor-output jaggrok-output" id="aimentor-output-<?php echo $widget_id; ?>"></div>
        </div>
        <script>
            jQuery(document).ready(function($) {
                window.AiMentorProviders = Object.assign({}, window.AiMentorProviders || {}, <?php echo wp_json_encode( $provider_meta ); ?>);
                window.JagGrokProviders = window.JagGrokProviders || window.AiMentorProviders;
                var init = window.AiMentorElementorUI && typeof window.AiMentorElementorUI.initWidget === 'function'
                    ? window.AiMentorElementorUI.initWidget
                    : null;

                if (init) {
                    init({
                        widgetId: '<?php echo esc_js( $widget_id ); ?>',
                        container: '#aimentor-widget-<?php echo esc_js( $widget_id ); ?>',
                        providerSelector: '#aimentor-provider-<?php echo esc_js( $widget_id ); ?>',
                        promptSelector: '#aimentor-prompt-<?php echo esc_js( $widget_id ); ?>',
                        outputSelector: '#aimentor-output-<?php echo esc_js( $widget_id ); ?>',
                        buttonSelector: '#aimentor-btn-<?php echo esc_js( $widget_id ); ?>',
                        providerIconSelector: '#aimentor-provider-icon-<?php echo esc_js( $widget_id ); ?>',
                        providerLabelSelector: '#aimentor-provider-label-<?php echo esc_js( $widget_id ); ?>',
                        providerSummarySelector: '#aimentor-provider-summary-<?php echo esc_js( $widget_id ); ?>',
                        providerBadgeSelector: '#aimentor-provider-badge-<?php echo esc_js( $widget_id ); ?>',
                        taskSelector: '#aimentor-generation-type-<?php echo esc_js( $widget_id ); ?>',
                        tierSelector: '#aimentor-performance-<?php echo esc_js( $widget_id ); ?>',
                        summarySelector: '#aimentor-context-summary-<?php echo esc_js( $widget_id ); ?>',
                        defaults: {
                            task: '<?php echo esc_js( $generation_type ); ?>',
                            tier: '<?php echo esc_js( $performance_tier ); ?>'
                        },
                        allowCanvas: <?php echo wp_json_encode( $is_pro_active ); ?>
                    });
                }
            });
        </script>
        <?php
    }
}