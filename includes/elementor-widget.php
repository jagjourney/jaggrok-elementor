<?php
// ============================================================================
// JAGJourney ELEMENTOR WIDGET v1.4.2 (CANVAS INSERT)
// ============================================================================

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class JagGrok_AI_Generator_Widget extends Widget_Base {
    public function get_name() { return 'jaggrok-ai-generator'; }
    public function get_title() { return 'JagGrok AI Generator'; }
    public function get_icon() { return 'eicon-robot'; }
    public function get_categories() { return [ 'general' ]; }

    protected function register_controls() {
        $this->start_controls_section( 'prompt_section', [ 'label' => 'AI Prompt' ] );
        $provider_meta = function_exists( 'jaggrok_get_provider_meta_map' ) ? jaggrok_get_provider_meta_map() : [
                'grok'   => [
                        'label'      => __( 'xAI Grok', 'jaggrok-elementor' ),
                        'icon'       => '🚀',
                        'summary'    => sprintf( __( 'Content generated with %s.', 'jaggrok-elementor' ), __( 'xAI Grok', 'jaggrok-elementor' ) ),
                        'badgeText'  => __( 'xAI', 'jaggrok-elementor' ),
                        'badgeColor' => '#1E1E1E',
                ],
                'openai' => [
                        'label'      => __( 'OpenAI', 'jaggrok-elementor' ),
                        'icon'       => '🔷',
                        'summary'    => sprintf( __( 'Content generated with %s.', 'jaggrok-elementor' ), __( 'OpenAI', 'jaggrok-elementor' ) ),
                        'badgeText'  => __( 'OpenAI', 'jaggrok-elementor' ),
                        'badgeColor' => '#2B8CFF',
                ],
        ];
        $provider_labels = wp_list_pluck( $provider_meta, 'label' );
        $default_provider = get_option( 'jaggrok_provider', 'grok' );
        if ( ! array_key_exists( $default_provider, $provider_labels ) ) {
                $provider_keys    = array_keys( $provider_labels );
                $default_provider = isset( $provider_keys[0] ) ? $provider_keys[0] : 'grok';
        }
        $this->add_control( 'provider', [
                'label'   => 'Provider',
                'type'    => Controls_Manager::SELECT,
                'options' => $provider_labels,
                'default' => $default_provider,
        ] );
        $this->add_control( 'prompt', [
                'label' => 'Describe your page',
                'type' => Controls_Manager::TEXTAREA,
                'default' => 'Create a modern donation page with hero call out, and three columns for different products people can donate to.',
                'placeholder' => 'e.g., "Landing page with blue hero and contact form"'
        ]);
        $this->end_controls_section();

        $this->start_controls_section( 'advanced_section', [ 'label' => 'Advanced' ] );
        if ( function_exists( 'jaggrok_is_pro_active' ) && jaggrok_is_pro_active() ) {
            $this->add_control( 'pro_features', [
                    'label' => 'Pro Features',
                    'type' => Controls_Manager::SWITCHER,
                    'default' => 'yes'
            ]);
        }
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $widget_id = $this->get_id();
        $provider_meta = function_exists( 'jaggrok_get_provider_meta_map' ) ? jaggrok_get_provider_meta_map() : $provider_meta;
        $provider_labels = wp_list_pluck( $provider_meta, 'label' );
        $provider_setting = isset( $settings['provider'] ) ? $settings['provider'] : get_option( 'jaggrok_provider', 'grok' );
        if ( ! array_key_exists( $provider_setting, $provider_meta ) ) {
                $provider_keys    = array_keys( $provider_meta );
                $provider_setting = isset( $provider_keys[0] ) ? $provider_keys[0] : 'grok';
        }
        $active_provider_meta = $provider_meta[ $provider_setting ];
        static $styles_printed = false;
        if ( ! $styles_printed ) {
                $styles_printed = true;
                ?>
                <style>
                        .jaggrok-provider-display {
                                display: flex;
                                align-items: center;
                                gap: 8px;
                                margin-top: 8px;
                        }
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
        <div class="jaggrok-widget">
            <div class="jaggrok-provider-selector">
                <label for="jaggrok-provider-<?php echo esc_attr( $widget_id ); ?>"><?php esc_html_e( 'Provider', 'jaggrok-elementor' ); ?></label>
                <select class="jaggrok-provider-control" id="jaggrok-provider-<?php echo esc_attr( $widget_id ); ?>">
                    <?php foreach ( $provider_labels as $key => $label ) : ?>
                        <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $provider_setting, $key ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="jaggrok-provider-display">
                    <span class="jaggrok-provider-active-icon" id="jaggrok-provider-icon-<?php echo esc_attr( $widget_id ); ?>" aria-hidden="true"><?php echo esc_html( $active_provider_meta['icon'] ); ?></span>
                    <span class="jaggrok-provider-active-label" id="jaggrok-provider-label-<?php echo esc_attr( $widget_id ); ?>"><?php echo esc_html( $active_provider_meta['label'] ); ?></span>
                    <span class="jaggrok-provider-badge" id="jaggrok-provider-badge-<?php echo esc_attr( $widget_id ); ?>" style="background-color: <?php echo esc_attr( $active_provider_meta['badgeColor'] ); ?>;">
                        <?php echo esc_html( $active_provider_meta['badgeText'] ); ?>
                    </span>
                </div>
                <p class="jaggrok-provider-summary" id="jaggrok-provider-summary-<?php echo esc_attr( $widget_id ); ?>"><?php echo esc_html( $active_provider_meta['summary'] ); ?></p>
            </div>
            <textarea class="jaggrok-prompt" id="jaggrok-prompt-<?php echo $widget_id; ?>" rows="4" style="width:100%;"><?php echo esc_textarea( $settings['prompt'] ); ?></textarea>
            <button class="jaggrok-generate-btn" id="jaggrok-btn-<?php echo esc_attr( $widget_id ); ?>" style="margin:10px 0;">
                <?php echo esc_html( sprintf( __( 'Generate with %s', 'jaggrok-elementor' ), $active_provider_meta['label'] ) ); ?>
            </button>
            <div class="jaggrok-output" id="jaggrok-output-<?php echo $widget_id; ?>"></div>
        </div>
        <script>
            jQuery(document).ready(function($) {
                var $button = $('#jaggrok-btn-<?php echo esc_js( $widget_id ); ?>');
                var $output = $('#jaggrok-output-<?php echo esc_js( $widget_id ); ?>');
                var $providerSelect = $('#jaggrok-provider-<?php echo esc_js( $widget_id ); ?>');
                var $providerIcon = $('#jaggrok-provider-icon-<?php echo esc_js( $widget_id ); ?>');
                var $providerLabel = $('#jaggrok-provider-label-<?php echo esc_js( $widget_id ); ?>');
                var $providerSummary = $('#jaggrok-provider-summary-<?php echo esc_js( $widget_id ); ?>');
                var $providerBadge = $('#jaggrok-provider-badge-<?php echo esc_js( $widget_id ); ?>');
                var jaggrokData = window.jaggrokAjax;
                window.JagGrokProviders = Object.assign({}, window.JagGrokProviders || {}, <?php echo wp_json_encode( $provider_meta ); ?>);

                function getProviderMeta(providerKey) {
                    var meta = window.JagGrokProviders || {};
                    return meta[providerKey] || {
                        label: providerKey,
                        icon: '🤖',
                        summary: (jaggrokData && jaggrokData.strings && jaggrokData.strings.contentGenerated)
                            ? jaggrokData.strings.contentGenerated.replace('%s', providerKey)
                            : 'Content generated with ' + providerKey + '.',
                        badgeText: providerKey,
                        badgeColor: '#444444'
                    };
                }

                function updateProviderUI(providerKey) {
                    var meta = getProviderMeta(providerKey);
                    $providerIcon.text(meta.icon || '🤖');
                    $providerLabel.text(meta.label || providerKey);
                    $providerSummary.text(meta.summary || '');
                    if (meta.badgeColor && $providerBadge.length) {
                        $providerBadge.css('background-color', meta.badgeColor);
                    }
                    if ($providerBadge.length) {
                        $providerBadge.text(meta.badgeText || providerKey);
                    }
                    if (jaggrokData && jaggrokData.strings && jaggrokData.strings.generateWith) {
                        $button.text(jaggrokData.strings.generateWith.replace('%s', meta.label || providerKey));
                    } else {
                        $button.text('Generate with ' + (meta.label || providerKey));
                    }
                }

                $providerSelect.on('change', function() {
                    updateProviderUI($(this).val());
                });

                if (!jaggrokData || !jaggrokData.ajaxurl || !jaggrokData.nonce) {
                    var noticeHtml = '<div class="notice notice-error jaggrok-missing-config"><p>' +
                        '<?php echo esc_js( __( 'JagGrok AJAX configuration is missing. Please ensure the plugin assets are enqueued properly.', 'jaggrok-elementor' ) ); ?>' +
                        '</p></div>';

                    var $widget = $button.closest('.jaggrok-widget');
                    if ($widget.length) {
                        $widget.prepend(noticeHtml);
                    } else {
                        $('body').prepend(noticeHtml);
                    }

                    $button.prop('disabled', true);
                    console.error('JagGrok AJAX configuration missing: expected window.jaggrokAjax.');
                    return;
                }

                $button.on('click', function() {
                    var prompt = $('#jaggrok-prompt-<?php echo esc_js( $widget_id ); ?>').val();
                    var provider = $providerSelect.val();
                    var providerMeta = getProviderMeta(provider);

                    if (jaggrokData && jaggrokData.strings && jaggrokData.strings.generatingWith) {
                        $output.html('<p>' + jaggrokData.strings.generatingWith.replace('%s', providerMeta.label || provider) + '</p>');
                    } else {
                        $output.html('<p>Generating with ' + (providerMeta.label || provider) + '...</p>');
                    }
                    $.post(jaggrokData.ajaxurl, {
                        action: 'jaggrok_generate_page',
                        prompt: prompt,
                        provider: provider,
                        nonce: jaggrokData.nonce
                    }, function(response) {
                        var responseProvider = response && response.data && response.data.provider ? response.data.provider : provider;
                        var responseMeta = getProviderMeta(responseProvider);
                        var summaryText;
                        if (response && response.data && response.data.provider_label) {
                            if (jaggrokData && jaggrokData.strings && jaggrokData.strings.contentGenerated) {
                                summaryText = jaggrokData.strings.contentGenerated.replace('%s', response.data.provider_label);
                            } else {
                                summaryText = 'Content generated with ' + response.data.provider_label + '.';
                            }
                        } else {
                            summaryText = responseMeta.summary;
                        }
                        if (response.success) {
                            if (response.data.canvas_json) {
                                // INSERT TO CANVAS (v1.4.2 FIX)
                                elementorFrontend.elementsHandler.addElements( response.data.canvas_json );
                                $output.html('<p class="jaggrok-provider-message">' + summaryText + '</p>');
                            } else {
                                var html = response.data.html || '';
                                $output.html('<p class="jaggrok-provider-message">' + summaryText + '</p>' + html);
                            }
                        } else {
                            var errorPrefix = (jaggrokData && jaggrokData.strings && jaggrokData.strings.errorPrefix) ? jaggrokData.strings.errorPrefix : 'Error:';
                            $output.html('<p style="color:red">' + errorPrefix + ' ' + response.data + '</p>');
                        }
                    });
                });

                updateProviderUI($providerSelect.val());
            });
        </script>
        <?php
    }
}