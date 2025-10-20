<?php
// ============================================================================
// JAGJourney ELEMENTOR WIDGET v1.2.3 (WORKING BUTTON + TEXTAREA)
// ============================================================================

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class JagGrok_AI_Generator_Widget extends Widget_Base {
	public function get_name() { return 'jaggrok-ai-generator'; }
	public function get_title() { return 'JagGrok AI Generator'; }
	public function get_icon() { return 'eicon-robot'; }
	public function get_categories() { return [ 'general' ]; }

	protected function register_controls() {
		// PROMPT SECTION
		$this->start_controls_section( 'prompt_section', [ 'label' => 'AI Prompt' ] );
		$this->add_control( 'prompt', [
			'label' => 'Describe your page',
			'type' => Controls_Manager::TEXTAREA,
			'default' => 'Create a modern homepage with hero, features, and contact form',
			'placeholder' => 'e.g., "Landing page with blue hero and contact form"'
		]);
		$this->end_controls_section();

		// ADVANCED OPTIONS
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
			<div class="jaggrok-prompt-area">
                <textarea class="elementor-control-textarea jaggrok-prompt"
                          id="jaggrok-prompt-<?php echo $widget_id; ?>"
                          rows="3"><?php echo esc_textarea( $settings['prompt'] ); ?></textarea>
			</div>
			<button class="jaggrok-generate-btn elementor-button elementor-button-success"
			        id="jaggrok-btn-<?php echo $widget_id; ?>">
				<i class="eicon-brain"></i> Generate with Grok
			</button>
			<div class="jaggrok-output" id="jaggrok-output-<?php echo $widget_id; ?>"></div>
		</div>
		<?php
	}
}

// REGISTER WIDGET (v1.2.3 - CORRECT HOOK)
add_action( 'elementor/widgets/widgets_registered', function( $widgets_manager ) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/elementor-widget.php';
	$widgets_manager->register( new JagGrok_AI_Generator_Widget() );
}, 10, 1 );