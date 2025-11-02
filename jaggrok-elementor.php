<?php
/**
 * Plugin Name: JagGrok Elementor
 * Plugin URI: https://jagjourney.com/
 * Description: 🚀 FREE AI Page Builder - Generate full Elementor layouts with Grok by xAI. One prompt = complete pages! By Jag Journey, LLC.
 * Version: 1.4.3
 * Author: Jag Journey, LLC
 * Author URI: https://jagjourney.com/
 * License: GPL v2 or later
 * Text Domain: jaggrok-elementor
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Elementor tested up to: 3.18
 * Elementor Pro tested up to: 3.18
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) exit;

// ============================================================================
// JAGJourney v1.4.3 - CORE PLUGIN (TRIPLE EMERGENCY WIDGET - NO MORE MISSING!)
// ============================================================================

// Check Elementor
function jaggrok_check_dependencies() {
	if ( ! did_action( 'elementor/loaded' ) ) {
		add_action( 'admin_notices', function() {
			echo '<div class="notice notice-error"><p><strong>JagGrok Elementor</strong> requires Elementor to be installed and active.</p></div>';
		});
		return false;
	}
	return true;
}
add_action( 'plugins_loaded', 'jaggrok_check_dependencies' );

// PRO DETECTION
function jaggrok_is_pro_active() {
	return class_exists( '\ElementorPro\Plugin' ) || defined( 'ELEMENTOR_PRO_VERSION' );
}

// SETTINGS LINK under plugin name
function jaggrok_settings_link( $actions, $plugin_file ) {
	if ( $plugin_file === plugin_basename( __FILE__ ) ) {
		$settings_link = '<a href="' . admin_url( 'options-general.php?page=jaggrok-settings' ) . '">Settings</a>';
		array_unshift( $actions, $settings_link );
	}
	return $actions;
}
add_filter( 'plugin_action_links', 'jaggrok_settings_link', 10, 2 );

// Enqueue admin JS files (v1.4.3)
function jaggrok_enqueue_assets( $hook ) {
        $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

        $is_settings_screen = ( 'settings_page_jaggrok-settings' === $hook );
        $is_elementor_screen = ( $screen && 0 === strpos( $screen->id, 'elementor' ) );

        if ( ! $is_settings_screen && ! $is_elementor_screen ) {
                return;
        }

        if ( $is_elementor_screen && ! $is_settings_screen ) {
                // Elementor assets are handled via elementor/editor/after_enqueue_scripts.
                return;
        }

        wp_enqueue_script( 'jaggrok-admin-settings', plugin_dir_url( __FILE__ ) . 'js/admin-settings.js', array( 'jquery' ), '1.4.3', true );
        wp_localize_script( 'jaggrok-admin-settings', 'jaggrokAjax', jaggrok_get_ajax_payload() );
}
add_action( 'admin_enqueue_scripts', 'jaggrok_enqueue_assets' );

function jaggrok_enqueue_elementor_assets() {
        wp_enqueue_script( 'jaggrok-elementor-widget', plugin_dir_url( __FILE__ ) . 'js/elementor-widget.js', array( 'jquery', 'elementor-frontend' ), '1.4.3', true );
        wp_localize_script( 'jaggrok-elementor-widget', 'jaggrokAjax', jaggrok_get_ajax_payload() );
}
add_action( 'elementor/editor/after_enqueue_scripts', 'jaggrok_enqueue_elementor_assets' );

function jaggrok_get_ajax_payload() {
        $provider_labels = function_exists( 'jaggrok_get_provider_labels' ) ? jaggrok_get_provider_labels() : array(
                'grok'   => __( 'xAI Grok', 'jaggrok-elementor' ),
                'openai' => __( 'OpenAI', 'jaggrok-elementor' ),
        );
        $provider_summaries = array();
        foreach ( $provider_labels as $key => $label ) {
                /* translators: %s: Provider label. */
                $provider_summaries[ $key ] = sprintf( __( 'Content generated with %s.', 'jaggrok-elementor' ), $label );
        }

        return array(
                'ajaxurl'            => admin_url( 'admin-ajax.php' ),
                'nonce'              => wp_create_nonce( 'jaggrok_test' ),
                'strings'            => array(
                        'testingBadge'       => __( 'Testing', 'jaggrok-elementor' ),
                        'testingDescription' => __( 'Testing connection…', 'jaggrok-elementor' ),
                        'missingKey'         => __( 'Enter an API key before testing.', 'jaggrok-elementor' ),
                        'errorBadge'         => __( 'Error', 'jaggrok-elementor' ),
                        'unknownError'       => __( 'Unknown error', 'jaggrok-elementor' ),
                ),
                'provider'           => get_option( 'jaggrok_provider', 'grok' ),
                'providerLabels'     => $provider_labels,
                'providerSummaries'  => $provider_summaries,
        );
}

// Providers
require_once plugin_dir_path( __FILE__ ) . 'includes/providers/class-jaggrok-provider-interface.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/providers/class-jaggrok-grok-provider.php';
require_once plugin_dir_path( __FILE__ ) . 'includes/providers/class-jaggrok-openai-provider.php';

// Include settings page (v1.4.3)
require_once plugin_dir_path( __FILE__ ) . 'includes/settings.php';

register_activation_hook( __FILE__, 'jaggrok_activate_plugin' );
function jaggrok_activate_plugin() {
        if ( function_exists( 'jaggrok_seed_default_options' ) ) {
                jaggrok_seed_default_options();
        }
}

if ( function_exists( 'jaggrok_seed_default_options' ) ) {
        add_action( 'plugins_loaded', 'jaggrok_seed_default_options', 5 );
}

// Include Elementor widget (v1.4.3)
add_action( 'elementor/widgets/register', function() {
        if ( jaggrok_check_dependencies() ) {
                require_once plugin_dir_path( __FILE__ ) . 'includes/elementor-widget.php';
	}
});

// Include updater (v1.4.3)
if ( jaggrok_check_dependencies() ) {
	require_once plugin_dir_path( __FILE__ ) . 'includes/updater.php';
}

function jaggrok_get_active_provider( $provider_key = null ) {
        if ( null === $provider_key ) {
                $provider_key = get_option( 'jaggrok_provider', 'grok' );
        }

        switch ( $provider_key ) {
                case 'openai':
                        return new JagGrok_OpenAI_Provider();
                case 'grok':
                default:
                        return new JagGrok_Grok_Provider();
        }
}

// AJAX: Generate Page with Grok (v1.4.3)
add_action( 'wp_ajax_jaggrok_generate_page', 'jaggrok_generate_page_ajax' );
function jaggrok_generate_page_ajax() {
        check_ajax_referer( 'jaggrok_test', 'nonce' );

        if ( ! current_user_can( 'edit_posts' ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'Insufficient permissions to generate content.', 'jaggrok-elementor' ),
                                'code'    => 'jaggrok_insufficient_permissions',
                        ],
                        403
                );
        }

        $prompt        = isset( $_POST['prompt'] ) ? sanitize_textarea_field( wp_unslash( $_POST['prompt'] ) ) : '';
        $is_pro = jaggrok_is_pro_active();

	$provider_labels = jaggrok_get_provider_labels();
	$provider_key    = isset( $_POST['provider'] ) ? sanitize_text_field( wp_unslash( $_POST['provider'] ) ) : get_option( 'jaggrok_provider', 'grok' );
	if ( ! array_key_exists( $provider_key, $provider_labels ) ) {
		$provider_key = get_option( 'jaggrok_provider', 'grok' );
	}

	$models         = jaggrok_get_provider_models();
	$model_defaults = jaggrok_get_provider_model_defaults();
	$model          = $models[ $provider_key ] ?? ( $model_defaults[ $provider_key ] ?? '' );

	$provider = jaggrok_get_active_provider( $provider_key );

	if ( ! $provider instanceof JagGrok_Provider_Interface ) {
		jaggrok_log_error(
			'Invalid provider configuration.',
			[
				'provider' => $provider_key,
				'model'    => $model,
				'user_id'  => get_current_user_id(),
			]
		);
		wp_send_json_error( __( 'Provider configuration error.', 'jaggrok-elementor' ) );
	}

	switch ( $provider_key ) {
		case 'openai':
			$api_key = get_option( 'jaggrok_openai_api_key' );
			$model   = $models['openai'] ?? $model_defaults['openai'];
			break;
		case 'grok':
		default:
			$api_key = get_option( 'jaggrok_xai_api_key' );
			$model   = $models['grok'] ?? $model_defaults['grok'];
			break;
	}

        $is_canvas_requested = $is_pro && ! empty( $_POST['pro_features'] );
        $is_canvas = $is_canvas_requested && $provider->supports_canvas();

	$result = $provider->request( $prompt, [
		'api_key'   => $api_key,
		'model'     => $model,
		'max_tokens'=> get_option( 'jaggrok_max_tokens', 2000 ),
		'is_canvas' => $is_canvas,
	] );

	if ( is_wp_error( $result ) ) {
		$error_message = $result->get_error_message();
		jaggrok_log_error(
			$error_message . ' | Details: ' . wp_json_encode( $result->get_error_data() ),
			[
				'provider' => $provider_key,
				'model'    => $model,
				'user_id'  => get_current_user_id(),
			]
		);
		wp_send_json_error( $error_message );
	}

        $response_payload = [
                'provider'        => $provider_key,
                'provider_label'  => $provider_labels[ $provider_key ] ?? ucfirst( $provider_key ),
        ];

        if ( 'canvas' === $result['type'] ) {
                $response_payload['canvas_json'] = $result['content'];
                wp_send_json_success( $response_payload );
        }

        $response_payload['html'] = $result['content'];
        wp_send_json_success( $response_payload );
}

// Include uninstall
register_uninstall_hook( __FILE__, 'jaggrok_uninstall' );

// EMERGENCY WIDGET REGISTRATION (if hook fails) - v1.4.3 GUARANTEED
add_action( 'init', function() {
	if ( did_action( 'elementor/loaded' ) && ! did_action( 'elementor/widgets/register' ) ) {
		add_action( 'elementor/widgets/register', function( $widgets_manager ) {
			require_once __DIR__ . '/includes/elementor-widget.php';
			$widgets_manager->register( new JagGrok_AI_Generator_Widget() );
		}, 999 );
	}
});

// DOUBLE EMERGENCY - plugins_loaded fallback
add_action( 'plugins_loaded', function() {
	if ( did_action( 'elementor/loaded' ) && ! did_action( 'elementor/widgets/register' ) ) {
		add_action( 'elementor/widgets/register', function( $widgets_manager ) {
			require_once __DIR__ . '/includes/elementor-widget.php';
			$widgets_manager->register( new JagGrok_AI_Generator_Widget() );
		}, 999 );
	}
});

// TRIPLE EMERGENCY - wp_loaded fallback
add_action( 'wp_loaded', function() {
	if ( did_action( 'elementor/loaded' ) && ! did_action( 'elementor/widgets/register' ) ) {
		add_action( 'elementor/widgets/register', function( $widgets_manager ) {
			require_once __DIR__ . '/includes/elementor-widget.php';
			$widgets_manager->register( new JagGrok_AI_Generator_Widget() );
		}, 999 );
	}
});
