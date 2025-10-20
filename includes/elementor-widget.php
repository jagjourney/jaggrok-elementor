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
        ?>
        <div class="jaggrok-widget">
            <textarea class="jaggrok-prompt" id="jaggrok-prompt-<?php echo $widget_id; ?>" rows="4" style="width:100%;"><?php echo esc_textarea( $settings['prompt'] ); ?></textarea>
            <button class="jaggrok-generate-btn" id="jaggrok-btn-<?php echo $widget_id; ?>" style="margin:10px 0;">Generate with Grok</button>
            <div class="jaggrok-output" id="jaggrok-output-<?php echo $widget_id; ?>"></div>
        </div>
        <script>
            jQuery(document).ready(function($) {
                $('#jaggrok-btn-<?php echo $widget_id; ?>').click(function() {
                    var prompt = $('#jaggrok-prompt-<?php echo $widget_id; ?>').val();
                    var $output = $('#jaggrok-output-<?php echo $widget_id; ?>');

                    $output.html('<p>Generating...</p>');
                    $.post(ajaxurl, {
                        action: 'jaggrok_generate_page',
                        prompt: prompt,
                        nonce: '<?php echo wp_create_nonce( 'jaggrok_generate' ); ?>'
                    }, function(response) {
                        if (response.success) {
                            if (response.data.canvas_json) {
                                // INSERT TO CANVAS (v1.4.2 FIX)
                                elementorFrontend.elementsHandler.addElements( response.data.canvas_json );
                            } else {
                                $output.html(response.data.html);
                            }
                        } else {
                            $output.html('<p style="color:red">Error: ' + response.data + '</p>');
                        }
                    });
                });
            });
        </script>
        <?php
    }
}