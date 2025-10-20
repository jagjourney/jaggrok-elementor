<?php
// ============================================================================
// JAGJourney ELEMENTOR WIDGET v1.2.6 ("Write with JagGrok")
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
			'default' => 'Create a modern homepage with hero, features, and contact form',
			'placeholder' => 'e.g., "Landing page with blue hero and contact form"'
		]);

		// "Write with JagGrok" LINK
		$this->add_control( 'write_with_jaggrok', [
			'type' => Controls_Manager::RAW_HTML,
			'raw' => '<a href="#" class="elementor-control-raw-html jaggrok-write-ai-btn" style="color: #93003c; font-weight: bold; text-decoration: underline; margin-top: 10px; display: inline-block; cursor: pointer;"><i class="eicon-brain"></i> Write with JagGrok</a>',
			'content_classes' => 'jaggrok-ai-trigger'
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
		$widget_id = $this->get_id();
		?>
		<div class="jaggrok-widget-output" id="jaggrok-output-<?php echo $widget_id; ?>"></div>
		<?php
	}
}