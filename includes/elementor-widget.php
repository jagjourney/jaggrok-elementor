<?php
// ============================================================================
// JAGJourney ELEMENTOR WIDGET v1.1.0 (PRO + FREE COMPATIBILITY)
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

		// ADVANCED OPTIONS (v1.1.0 - PRO/FREE CONDITIONAL)
		$this->start_controls_section(
			'advanced_section',
			[ 'label' => 'Advanced Options' ]
		);

		if ( jaggrok_is_pro_active() ) {
			$this->add_control(
				'pro_features',
				[
					'label' => 'Pro Features',
					'type' => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'description' => 'Enable dynamic content, forms, etc. (Pro only)'
				]
			);
			$this->add_control(
				'add_form',
				[
					'label' => 'Add Contact Form',
					'type' => Controls_Manager::SWITCHER,
					'default' => 'yes',
					'description' => 'Auto-insert Pro form widget'
				]
			);
			$this->add_control(
				'dynamic_content',
				[
					'label' => 'Dynamic Fields',
					'type' => Controls_Manager::SELECT,
					'options' => [
						'none' => 'None',
						'acf' => 'ACF Fields',
						'posts' => 'Dynamic Posts'
					]
				]
			);
		} else {
			$this->add_control(
				'pro_upgrade',
				[
					'type' => Controls_Manager::RAW_HTML,
					'raw' => '<p><a href="https://elementor.com/pro/" target="_blank">Upgrade to Pro</a> for forms & dynamic content!</p>',
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info'
				]
			);
		}

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings_for_display();
		$prompt = $settings['prompt'];

		// If Pro, enhance prompt with Pro features (v1.1.0)
		if ( jaggrok_is_pro_active() && $settings['pro_features'] === 'yes' ) {
			$prompt .= ' Include dynamic fields and contact form if possible.';
		}

		// Generate via Grok API (placeholder - replace with actual call)
		$generated = $this->generate_with_grok( $prompt ); // TODO: Implement API call

		// For Pro: Add form widget if enabled (v1.1.0)
		if ( jaggrok_is_pro_active() && $settings['add_form'] === 'yes' ) {
			echo do_shortcode( '[elementor-template id="your-pro-form-template-id"]' ); // Or dynamic Pro form insert
		}

		echo '<div class="jaggrok-generated-content">' . $generated . '</div>';
	}

	// Placeholder for Grok API generation (implement in v1.2.0)
	private function generate_with_grok( $prompt ) {
		// Actual API call code goes here
		return 'Generated content placeholder: ' . esc_html( $prompt );
	}
}

add_action( 'elementor/widgets/register', function( $widgets_manager ) {
	$widgets_manager->register( new JagGrok_AI_Generator_Widget() );
});