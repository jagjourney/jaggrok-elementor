<?php
// ============================================================================
// AiMentor ELEMENTOR WIDGET v1.4.2 (CANVAS INSERT)
// ============================================================================

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

class AiMentor_AI_Generator_Widget extends Widget_Base {
    const LEGACY_WIDGET_SLUG = 'jaggrok-ai-generator';

    public function get_name() {
        return 'aimentor-ai-generator';
    }

    public function get_title() {
        return __( 'AiMentor AI Generator', 'aimentor' );
    }

    public function get_legacy_title() {
        return __( 'JagGrok AI Generator', 'aimentor' );
    }

    public function get_icon() { return 'eicon-robot'; }
    public function get_categories() { return [ 'general' ]; }

    protected function get_saved_prompt_options() {
        if ( ! function_exists( 'aimentor_get_saved_prompts_payload' ) ) {
            return [];
        }

        $payload = aimentor_get_saved_prompts_payload();
        $entries = [];

        if ( isset( $payload['user'] ) && is_array( $payload['user'] ) ) {
            $entries = array_merge( $entries, $payload['user'] );
        }

        if ( isset( $payload['global'] ) && is_array( $payload['global'] ) ) {
            $entries = array_merge( $entries, $payload['global'] );
        }

        $options = [];

        foreach ( $entries as $entry ) {
            if ( ! is_array( $entry ) ) {
                continue;
            }

            $id    = isset( $entry['id'] ) ? (string) $entry['id'] : '';
            $label = isset( $entry['label'] ) ? (string) $entry['label'] : '';
            $scope = isset( $entry['scope'] ) ? (string) $entry['scope'] : '';

            if ( '' === $id ) {
                continue;
            }

            if ( '' === $label ) {
                $label = __( 'Saved prompt', 'aimentor' );
            }

            if ( 'global' === $scope ) {
                /* translators: %s: Saved prompt label. */
                $label = sprintf( __( '%s (Shared)', 'aimentor' ), $label );
            }

            $options[ $id ] = $label;
        }

        return $options;
    }

    protected function register_controls() {
        $this->start_controls_section(
            'aimentor_ai_controls',
            [
                'label' => __( 'AiMentor Generator', 'aimentor' ),
            ]
        );

        $this->add_control(
            'write_with_aimentor',
            [
                'type'        => Controls_Manager::BUTTON,
                'label'       => __( 'Generate', 'aimentor' ),
                'text'        => __( 'Write with AiMentor', 'aimentor' ),
                'event'       => 'panel/widgets/aimentor-ai-generator/controls/write_with_aimentor/event',
                'description' => __( 'Use this button to open the AiMentor generator modal.', 'aimentor' ),
            ]
        );

        $saved_prompt_options = $this->get_saved_prompt_options();
        $placeholder_label    = __( 'Select a saved prompt…', 'aimentor' );
        $select_options       = array( '' => $placeholder_label ) + $saved_prompt_options;

        $this->add_control(
            'aimentor_saved_prompt',
            [
                'label'       => __( 'Saved Prompts', 'aimentor' ),
                'type'        => Controls_Manager::SELECT,
                'options'     => $select_options,
                'default'     => '',
                'label_block' => true,
                'separator'   => 'before',
                'description' => __( 'Choose a saved prompt to quickly prefill the AiMentor modal.', 'aimentor' ),
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        if ( ! \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            return;
        }

        echo '<div class="aimentor-editor-placeholder"><p>' . esc_html__( 'Use the “Write with AiMentor” sidebar button to generate content for this widget.', 'aimentor' ) . '</p></div>';
    }
}

if ( ! class_exists( 'JagGrok_AI_Generator_Widget' ) ) {
    class JagGrok_AI_Generator_Widget extends AiMentor_AI_Generator_Widget {
        public function get_name() {
            return self::LEGACY_WIDGET_SLUG;
        }

        public function get_title() {
            return $this->get_legacy_title();
        }

        public function show_in_panel() {
            return false;
        }
    }
}
