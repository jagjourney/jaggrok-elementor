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

        if ( $options ) {
            natcasesort( $options );
        }

        /**
         * Filter the saved prompt options exposed to the Elementor widget control.
         *
         * @since 1.0.00
         *
         * @param array $options Saved prompt select options.
         * @param array $payload Raw saved prompt payload grouped by scope.
         */
        return apply_filters( 'aimentor_widget_saved_prompt_options', $options, $payload );
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
                'type'        => Controls_Manager::SELECT2,
                'options'     => $select_options,
                'default'     => '',
                'label_block' => true,
                'separator'   => 'before',
                'description' => __( 'Choose a saved prompt to quickly prefill the AiMentor modal.', 'aimentor' ),
            ]
        );

        $preset_catalog   = function_exists( 'aimentor_get_prompt_preset_catalog' ) ? aimentor_get_prompt_preset_catalog() : [];
        $provider_labels  = function_exists( 'aimentor_get_provider_labels' ) ? aimentor_get_provider_labels() : [];
        $category_options = [ '' => __( 'Select a preset category…', 'aimentor' ) ];
        $preset_options   = [ '' => __( 'Custom prompt', 'aimentor' ) ];

        foreach ( $preset_catalog as $provider_key => $categories ) {
            if ( ! is_array( $categories ) ) {
                continue;
            }

            $provider_label = isset( $provider_labels[ $provider_key ] ) ? $provider_labels[ $provider_key ] : ucfirst( (string) $provider_key );

            foreach ( $categories as $category_key => $category_meta ) {
                if ( ! is_array( $category_meta ) ) {
                    continue;
                }

                $category_label = isset( $category_meta['label'] ) ? (string) $category_meta['label'] : ucfirst( str_replace( '_', ' ', (string) $category_key ) );
                $category_value = $provider_key . '::' . $category_key;
                /* translators: 1: Provider label. 2: Preset category label. */
                $category_options[ $category_value ] = sprintf( __( '%1$s — %2$s', 'aimentor' ), $provider_label, $category_label );

                if ( ! isset( $category_meta['presets'] ) || ! is_array( $category_meta['presets'] ) ) {
                    continue;
                }

                foreach ( $category_meta['presets'] as $preset_key => $preset_meta ) {
                    if ( ! is_array( $preset_meta ) ) {
                        continue;
                    }

                    $preset_label = isset( $preset_meta['label'] ) ? (string) $preset_meta['label'] : ucfirst( str_replace( '_', ' ', (string) $preset_key ) );
                    $option_key   = $provider_key . '::' . $category_key . '::' . $preset_key;
                    /* translators: 1: Provider label. 2: Preset label. */
                    $preset_options[ $option_key ] = sprintf( __( '%1$s — %2$s', 'aimentor' ), $provider_label, $preset_label );
                }
            }
        }

        $this->add_control(
            'aimentor_prompt_category',
            [
                'label'       => __( 'Preset Category', 'aimentor' ),
                'type'        => Controls_Manager::SELECT,
                'options'     => $category_options,
                'default'     => '',
                'label_block' => true,
                'separator'   => 'before',
                'description' => __( 'Filter curated prompts by provider and use-case before selecting a preset.', 'aimentor' ),
            ]
        );

        $this->add_control(
            'aimentor_prompt_preset',
            [
                'label'       => __( 'Prompt Preset', 'aimentor' ),
                'type'        => Controls_Manager::SELECT,
                'options'     => $preset_options,
                'default'     => '',
                'label_block' => true,
                'description' => __( 'Select a preset to merge curated guidance with the prompt field.', 'aimentor' ),
            ]
        );

        $this->add_control(
            'aimentor_prompt_text',
            [
                'label'       => __( 'Prompt', 'aimentor' ),
                'type'        => Controls_Manager::TEXTAREA,
                'rows'        => 6,
                'default'     => '',
                'placeholder' => __( 'Describe what you would like AiMentor to create.', 'aimentor' ),
                'label_block' => true,
                'description' => __( 'Use this field to customize the request that will be sent to AiMentor. Preset guidance is merged here so you can continue refining it.', 'aimentor' ),
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        if ( ! \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
            return;
        }

        echo '<div class="aimentor-editor-placeholder">';
        echo '<p>' . esc_html__( 'Use the “Write with AiMentor” sidebar button to generate content for this widget.', 'aimentor' ) . '</p>';
        echo '<p class="aimentor-editor-cooldown" aria-live="polite" style="display:none;margin-top:8px;font-size:12px;color:#b45309;"></p>';
        echo '<div class="aimentor-layout-history" aria-live="polite" data-empty-text="' . esc_attr__( 'Generate a layout to see it here after your next run.', 'aimentor' ) . '">';
        echo '<div class="aimentor-layout-history__header">';
        echo '<strong class="aimentor-layout-history__title">' . esc_html__( 'Recent layouts', 'aimentor' ) . '</strong>';
        echo '<div class="aimentor-layout-history__nav" role="group" aria-label="' . esc_attr__( 'Browse recent layouts', 'aimentor' ) . '">';
        echo '<button type="button" class="aimentor-layout-history__nav-button aimentor-layout-history__nav-button--prev" aria-label="' . esc_attr__( 'Show previous layout', 'aimentor' ) . '" disabled>&lsaquo;</button>';
        echo '<button type="button" class="aimentor-layout-history__nav-button aimentor-layout-history__nav-button--next" aria-label="' . esc_attr__( 'Show next layout', 'aimentor' ) . '" disabled>&rsaquo;</button>';
        echo '</div>';
        echo '</div>';
        echo '<div class="aimentor-layout-history__viewport" role="listbox" aria-label="' . esc_attr__( 'Recent AiMentor layouts', 'aimentor' ) . '"></div>';
        echo '<p class="aimentor-layout-history__empty">' . esc_html__( 'Generate a layout to see it here after your next run.', 'aimentor' ) . '</p>';
        echo '</div>';
        echo '</div>';
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
