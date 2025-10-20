<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class JagGrok_AI_Generator_Widget extends Widget_Base {
    public function get_name() { return 'jaggrok-ai-generator'; }
    public function get_title() { return 'JagGrok AI Generator'; }
    public function get_icon() { return 'eicon-robot'; }
    public function get_categories() { return [ 'general' ]; }

    protected function register_controls() {
        $this->start_controls_section( 'content', [ 'label' => 'Generate Content' ] );
        $this->add_control( 'prompt', [
                'label' => 'Describe your page',
                'type' => Controls_Manager::TEXTAREA,
                'default' => 'Create a modern homepage with hero section'
        ]);
        $this->add_control( 'generate', [
                'label' => 'Generate',
                'type' => Controls_Manager::BUTTON,
                'text' => 'Generate with Grok',
                'event' => 'generate'
        ]);
        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        $id = $this->get_id();
        ?>
        <div class="jaggrok-widget">
            <textarea id="prompt-<?php echo $id; ?>" style="width:100%;height:100px;"><?php echo esc_textarea( $settings['prompt'] ); ?></textarea>
            <button id="btn-<?php echo $id; ?>" class="button button-primary" style="width:100%;margin:10px 0;padding:10px;">Generate with Grok</button>
            <div id="output-<?php echo $id; ?>" style="padding:10px;background:#f1f3f5;border-radius:3px;"></div>
        </div>
        <script>
            jQuery('#btn-<?php echo $id; ?>').click(function() {
                var prompt = jQuery('#prompt-<?php echo $id; ?>').val();
                jQuery('#output-<?php echo $id; ?>').html('Generating...');
                jQuery.post(ajaxurl, {
                    action: 'jaggrok_generate_page',
                    prompt: prompt,
                    nonce: '<?php echo wp_create_nonce( 'jaggrok_generate' ); ?>'
                }, function(r) {
                    jQuery('#output-<?php echo $id; ?>').html(r.data.html);
                });
            });
        </script>
        <?php
    }
}