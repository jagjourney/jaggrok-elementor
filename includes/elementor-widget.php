<?php
// ============================================================================
// JAGJourney ELEMENTOR WIDGET v1.0.0
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
			'default' => 'Create a modern homepage with hero, features, and contact form'
		]);
		$this->end_controls_section();
	}

	protected function render() {
		echo '<div class="jaggrok-placeholder">Drag me! AI generation coming in v1.1.0</div>';
	}
}

add_action( 'elementor/widgets/register', function( $widgets_manager ) {
	$widgets_manager->register( new JagGrok_AI_Generator_Widget() );
});