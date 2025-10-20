<?php
// ============================================================================
// JAGJourney ELEMENTOR WIDGET v1.2.5 ( "Write with AI" POPUP LIKE ELEMENTOR)
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

		// "Write with AI" LINK (v1.2.5 - Like Elementor)
		$this->add_control( 'write_with_ai', [
			'type' => Controls_Manager::RAW_HTML,
			'raw' => '<a href="#" class="elementor-button elementor-button-success jaggrok-write-ai" style="font-size: 12px; padding: 5px 10px; margin-top: 10px; display: inline-block;"><i class="eicon-brain"></i> Write with AI</a>',
			'content_classes' => 'jaggrok-ai-link'
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
			<div class="jaggrok-output" id="jaggrok-output-<?php echo $widget_id; ?>"></div>
		</div>
		<?php
	}
}