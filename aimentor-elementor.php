
<?php
/**
 * Plugin Name: AiMentor for Elementor
 * Plugin URI: https://jagjourney.com/
 * Description: 🚀 FREE AI Page Builder - Generate full Elementor layouts with AiMentor. One prompt = complete pages and content!
 * Version: 0.0.001
 * Author: AiMentor
 * Author URI: https://jagjourney.com/
 * License: GPL v2 or later
 * Text Domain: aimentor
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 * Elementor tested up to: 3.18
 * Elementor Pro tested up to: 3.18
 */

if ( ! defined( 'ABSPATH' ) ) {
        exit;
}

if ( ! defined( 'AIMENTOR_PLUGIN_VERSION' ) ) {
        define( 'AIMENTOR_PLUGIN_VERSION', '0.0.001' );
}

if ( ! defined( 'AIMENTOR_PLUGIN_FILE' ) ) {
        define( 'AIMENTOR_PLUGIN_FILE', __FILE__ );
}

if ( ! defined( 'AIMENTOR_PLUGIN_BASENAME' ) ) {
        define( 'AIMENTOR_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'AIMENTOR_PLUGIN_DIR' ) ) {
        define( 'AIMENTOR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'AIMENTOR_PLUGIN_URL' ) ) {
        define( 'AIMENTOR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Load plugin translations.
 */
function aimentor_load_textdomain() {
        load_plugin_textdomain( 'aimentor', false, dirname( AIMENTOR_PLUGIN_BASENAME ) . '/languages/' );
}
add_action( 'plugins_loaded', 'aimentor_load_textdomain' );

/**
 * Determine if an option value should be considered empty for migration.
 *
 * @param mixed $value Option value.
 * @return bool
 */
function aimentor_is_option_value_empty( $value ) {
        if ( false === $value || null === $value ) {
                return true;
        }

        if ( is_array( $value ) ) {
                return empty( $value );
        }

        return '' === $value;
}

/**
 * Copy legacy JagGrok options to their AiMentor equivalents and migrate the error log file.
 */
function aimentor_maybe_run_legacy_migration() {
        static $ran = false;

        if ( $ran ) {
                return;
        }

        $ran = true;

        $option_map = array(
                'jaggrok_provider'               => 'aimentor_provider',
                'jaggrok_xai_api_key'            => 'aimentor_xai_api_key',
                'jaggrok_openai_api_key'         => 'aimentor_openai_api_key',
                'jaggrok_auto_insert'            => 'aimentor_auto_insert',
                'jaggrok_theme_style'            => 'aimentor_theme_style',
                'jaggrok_max_tokens'             => 'aimentor_max_tokens',
                'jaggrok_provider_models'        => 'aimentor_provider_models',
                'jaggrok_model'                  => 'aimentor_model',
                'jaggrok_openai_model'           => 'aimentor_openai_model',
                'jaggrok_api_tested'             => 'aimentor_api_tested',
                'jaggrok_provider_test_statuses' => 'aimentor_provider_test_statuses',
        );

        foreach ( $option_map as $legacy_key => $modern_key ) {
                $legacy_value = get_option( $legacy_key, null );

                if ( null === $legacy_value || false === $legacy_value ) {
                        continue;
                }

                $modern_value = get_option( $modern_key, null );

                if ( aimentor_is_option_value_empty( $modern_value ) && ! aimentor_is_option_value_empty( $legacy_value ) ) {
                        update_option( $modern_key, $legacy_value );
                }
        }

        $legacy_log = AIMENTOR_PLUGIN_DIR . 'includes/jaggrok-errors.log';
        $modern_log = AIMENTOR_PLUGIN_DIR . 'includes/aimentor-errors.log';

        if ( file_exists( $legacy_log ) && ! file_exists( $modern_log ) ) {
                @rename( $legacy_log, $modern_log );
        }
}
add_action( 'plugins_loaded', 'aimentor_maybe_run_legacy_migration', 1 );

/**
 * Register script handles for both AiMentor and legacy JagGrok identifiers.
 */
function aimentor_register_asset_handles() {
        static $registered = false;

        if ( $registered ) {
                return;
        }

        $registered = true;

        $version = AIMENTOR_PLUGIN_VERSION;
        $base    = AIMENTOR_PLUGIN_URL;

        wp_register_script( 'aimentor-admin-settings', $base . 'js/admin-settings.js', array( 'jquery' ), $version, true );
        wp_register_script( 'aimentor-elementor-widget', $base . 'js/elementor-widget.js', array( 'jquery', 'elementor-frontend' ), $version, true );

        // Register legacy handles as lightweight aliases that depend on the AiMentor versions.
        wp_register_script( 'jaggrok-admin-settings', null, array( 'aimentor-admin-settings' ), $version, true );
        wp_register_script( 'jaggrok-elementor-widget', null, array( 'aimentor-elementor-widget' ), $version, true );
}
add_action( 'init', 'aimentor_register_asset_handles' );

/**
 * Prevent legacy alias handles from rendering duplicate script tags while keeping them enqueueable.
 */
function aimentor_block_legacy_script_output() {
        global $wp_scripts;

        if ( ! class_exists( 'WP_Scripts' ) ) {
                return;
        }

        if ( ! $wp_scripts instanceof WP_Scripts ) {
                return;
        }

        foreach ( array( 'jaggrok-admin-settings', 'jaggrok-elementor-widget' ) as $legacy_handle ) {
                if ( $wp_scripts->query( $legacy_handle, 'enqueued' ) ) {
                        wp_dequeue_script( $legacy_handle );

                        if ( ! in_array( $legacy_handle, $wp_scripts->done, true ) ) {
                                $wp_scripts->done[] = $legacy_handle;
                        }
                }
        }
}
add_action( 'wp_print_scripts', 'aimentor_block_legacy_script_output', 0 );
add_action( 'admin_print_scripts', 'aimentor_block_legacy_script_output', 0 );
add_action( 'elementor/editor/after_enqueue_scripts', 'aimentor_block_legacy_script_output', 20 );

/**
 * Ensure Elementor is active before proceeding.
 *
 * @return bool
 */
function aimentor_check_dependencies() {
        if ( ! did_action( 'elementor/loaded' ) ) {
                add_action( 'admin_notices', function() {
                        echo '<div class="notice notice-error"><p><strong>AiMentor Elementor</strong> requires Elementor to be installed and active.</p></div>';
                } );

                return false;
        }

        return true;
}
add_action( 'plugins_loaded', 'aimentor_check_dependencies' );

/**
 * Determine if Elementor Pro is active.
 *
 * @return bool
 */
function aimentor_is_pro_active() {
        return class_exists( '\ElementorPro\Plugin' ) || defined( 'ELEMENTOR_PRO_VERSION' );
}

/**
 * Add a link to the AiMentor settings page from the plugins list.
 *
 * @param array  $actions     Existing action links.
 * @param string $plugin_file Plugin file basename.
 * @return array
 */
function aimentor_settings_link( $actions, $plugin_file ) {
        if ( $plugin_file === plugin_basename( AIMENTOR_PLUGIN_FILE ) ) {
                $settings_link = '<a href="' . esc_url( admin_url( 'options-general.php?page=aimentor-settings' ) ) . '">' . esc_html__( 'Settings', 'aimentor' ) . '</a>';
                array_unshift( $actions, $settings_link );
        }

        return $actions;
}
add_filter( 'plugin_action_links', 'aimentor_settings_link', 10, 2 );

/**
 * Localize shared AJAX payload.
 *
 * @return array
 */
function aimentor_get_ajax_payload() {
        $provider_meta_map  = aimentor_get_provider_meta_map();
        $provider_labels    = wp_list_pluck( $provider_meta_map, 'label' );
        $provider_summaries = wp_list_pluck( $provider_meta_map, 'summary' );

        return array(
                'ajaxurl'           => admin_url( 'admin-ajax.php' ),
                'nonce'             => wp_create_nonce( 'aimentor_test' ),
                'strings'           => array(
                        'testingBadge'       => __( 'Testing', 'aimentor' ),
                        'testingDescription' => __( 'Testing connection…', 'aimentor' ),
                        'missingKey'         => __( 'Enter an API key before testing.', 'aimentor' ),
                        'errorBadge'         => __( 'Error', 'aimentor' ),
                        'unknownError'       => __( 'Unknown error', 'aimentor' ),
                        /* translators: %s: Provider label. */
                        'generateWith'       => __( 'Generate with %s', 'aimentor' ),
                        /* translators: %s: Provider label. */
                        'generatingWith'     => __( 'Generating with %s…', 'aimentor' ),
                        /* translators: %s: Provider label. */
                        'contentGenerated'   => __( 'Content generated with %s.', 'aimentor' ),
                        /* translators: %s: Provider label. */
                        'writeWith'          => __( 'Write with %s', 'aimentor' ),
                        'chooseProvider'     => __( 'Choose provider', 'aimentor' ),
                        'promptRequired'     => __( 'Please enter a prompt!', 'aimentor' ),
                        'generateAgain'      => __( 'Generate Again', 'aimentor' ),
                        'closeModal'         => __( 'Close modal', 'aimentor' ),
                        'successPrefix'      => __( '✅', 'aimentor' ),
                        'errorPrefix'        => __( 'Error:', 'aimentor' ),
                ),
                'provider'          => get_option( 'aimentor_provider', 'grok' ),
                'providerLabels'    => $provider_labels,
                'providerSummaries' => $provider_summaries,
                'providersMeta'     => $provider_meta_map,
        );
}

/**
 * Enqueue admin assets for the settings screen.
 *
 * @param string $hook Current admin hook.
 */
function aimentor_enqueue_assets( $hook ) {
        aimentor_register_asset_handles();

        $screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;

        $is_settings_screen  = ( 'settings_page_aimentor-settings' === $hook );
        $is_elementor_screen = ( $screen && 0 === strpos( $screen->id, 'elementor' ) );

        if ( ! $is_settings_screen && ! $is_elementor_screen ) {
                return;
        }

        if ( $is_elementor_screen && ! $is_settings_screen ) {
                // Elementor assets are handled via elementor/editor/after_enqueue_scripts.
                return;
        }

        wp_enqueue_script( 'aimentor-admin-settings' );
        $payload = aimentor_get_ajax_payload();

        wp_localize_script( 'aimentor-admin-settings', 'aimentorAjax', $payload );
        wp_localize_script( 'aimentor-admin-settings', 'jaggrokAjax', $payload );
        wp_add_inline_script(
                'aimentor-admin-settings',
                "window.aimentorAjax = window.aimentorAjax || {};\n"
                . "window.jaggrokAjax = window.jaggrokAjax || window.aimentorAjax;\n"
                . "window.aimentorAjax.legacy = Object.assign({ prefixes: { id: 'jaggrok_', class: 'jaggrok-' } }, window.aimentorAjax.legacy || {});\n",
                'after'
        );
}
add_action( 'admin_enqueue_scripts', 'aimentor_enqueue_assets' );

/**
 * Enqueue assets for the Elementor editor.
 */
function aimentor_enqueue_elementor_assets() {
        aimentor_register_asset_handles();

        wp_enqueue_script( 'aimentor-elementor-widget' );
        $payload = aimentor_get_ajax_payload();

        wp_localize_script( 'aimentor-elementor-widget', 'aimentorAjax', $payload );
        wp_localize_script( 'aimentor-elementor-widget', 'jaggrokAjax', $payload );
        wp_add_inline_script(
                'aimentor-elementor-widget',
                "window.aimentorAjax = window.aimentorAjax || {};\n"
                . "window.jaggrokAjax = window.jaggrokAjax || window.aimentorAjax;\n",
                'after'
        );
}
add_action( 'elementor/editor/after_enqueue_scripts', 'aimentor_enqueue_elementor_assets' );

/**
 * Build the provider metadata map shared with the editor.
 *
 * @return array
 */
function aimentor_get_provider_meta_map() {
        $provider_labels = function_exists( 'aimentor_get_provider_labels' ) ? aimentor_get_provider_labels() : array(
                'grok'   => __( 'xAI Grok', 'aimentor' ),
                'openai' => __( 'OpenAI', 'aimentor' ),
        );

        $meta = array();

        foreach ( $provider_labels as $key => $label ) {
                $is_openai    = ( 'openai' === $key );
                $meta[ $key ] = array(
                        'label'      => $label,
                        'icon'       => $is_openai ? '🔷' : '🚀',
                        'summary'    => sprintf( __( 'Content generated with %s.', 'aimentor' ), $label ),
                        'badgeText'  => $is_openai ? __( 'OpenAI', 'aimentor' ) : __( 'xAI', 'aimentor' ),
                        'badgeColor' => $is_openai ? '#2B8CFF' : '#1E1E1E',
                );
        }

        $meta = apply_filters( 'aimentor_provider_meta_map', $meta );
        $meta = apply_filters( 'jaggrok_provider_meta_map', $meta );

        return $meta;
}

// Providers.
require_once AIMENTOR_PLUGIN_DIR . 'includes/providers/class-aimentor-provider-interface.php';
require_once AIMENTOR_PLUGIN_DIR . 'includes/providers/class-aimentor-grok-provider.php';
require_once AIMENTOR_PLUGIN_DIR . 'includes/providers/class-aimentor-openai-provider.php';

if ( class_exists( 'AiMentor_Provider_Interface' ) && ! class_exists( 'JagGrok_Provider_Interface' ) ) {
        class_alias( 'AiMentor_Provider_Interface', 'JagGrok_Provider_Interface' );
}

if ( class_exists( 'AiMentor_Grok_Provider' ) && ! class_exists( 'JagGrok_Grok_Provider' ) ) {
        class_alias( 'AiMentor_Grok_Provider', 'JagGrok_Grok_Provider' );
}

if ( class_exists( 'AiMentor_OpenAI_Provider' ) && ! class_exists( 'JagGrok_OpenAI_Provider' ) ) {
        class_alias( 'AiMentor_OpenAI_Provider', 'JagGrok_OpenAI_Provider' );
}

// Settings & helpers.
require_once AIMENTOR_PLUGIN_DIR . 'includes/settings.php';
require_once AIMENTOR_PLUGIN_DIR . 'includes/legacy-shims.php';

register_activation_hook( AIMENTOR_PLUGIN_FILE, 'aimentor_activate_plugin' );
/**
 * Run setup tasks during plugin activation.
 */
function aimentor_activate_plugin() {
        if ( function_exists( 'aimentor_seed_default_options' ) ) {
                aimentor_seed_default_options();
        }
}

if ( function_exists( 'aimentor_seed_default_options' ) ) {
        add_action( 'plugins_loaded', 'aimentor_seed_default_options', 5 );
}

// Elementor widget registration.
add_action( 'elementor/widgets/register', function( $widgets_manager ) {
        if ( ! aimentor_check_dependencies() ) {
                return;
        }

        require_once AIMENTOR_PLUGIN_DIR . 'includes/elementor-widget.php';

        if ( class_exists( 'AiMentor_AI_Generator_Widget' ) ) {
                $widgets_manager->register( new AiMentor_AI_Generator_Widget() );

                if ( class_exists( 'JagGrok_AI_Generator_Widget' ) ) {
                        $widgets_manager->register( new JagGrok_AI_Generator_Widget() );
                }
        }
} );

// Updater.
if ( aimentor_check_dependencies() ) {
        require_once AIMENTOR_PLUGIN_DIR . 'includes/updater.php';

        if ( class_exists( 'AiMentor_Updater' ) && ! class_exists( 'JagGrok_Updater' ) ) {
                class_alias( 'AiMentor_Updater', 'JagGrok_Updater' );
        }
}

/**
 * Resolve the active provider based on configuration.
 *
 * @param string|null $provider_key Provider key.
 * @return AiMentor_Provider_Interface
 */
function aimentor_get_active_provider( $provider_key = null ) {
        if ( null === $provider_key ) {
                $provider_key = get_option( 'aimentor_provider', 'grok' );
        }

        switch ( $provider_key ) {
                case 'openai':
                        return new AiMentor_OpenAI_Provider();
                case 'grok':
                default:
                        return new AiMentor_Grok_Provider();
        }
}

add_action( 'wp_ajax_aimentor_generate_page', 'aimentor_generate_page_ajax' );
add_action( 'wp_ajax_jaggrok_generate_page', 'aimentor_generate_page_ajax' );
/**
 * Handle the AJAX request to generate Elementor content.
 */
function aimentor_generate_page_ajax() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

        if ( ! wp_verify_nonce( $nonce, 'aimentor_test' ) && ! wp_verify_nonce( $nonce, 'jaggrok_test' ) ) {
                wp_send_json_error(
                        array(
                                'message' => __( 'Security check failed.', 'aimentor' ),
                                'code'    => 'aimentor_invalid_nonce',
                        ),
                        403
                );
        }

        if ( ! current_user_can( 'edit_posts' ) ) {
                wp_send_json_error(
                        array(
                                'message' => __( 'Insufficient permissions to generate content.', 'aimentor' ),
                                'code'    => 'aimentor_insufficient_permissions',
                        ),
                        403
                );
        }

        $prompt = isset( $_POST['prompt'] ) ? sanitize_textarea_field( wp_unslash( $_POST['prompt'] ) ) : '';
        $is_pro = aimentor_is_pro_active();

        $provider_labels = aimentor_get_provider_labels();
        $provider_key    = isset( $_POST['provider'] ) ? sanitize_text_field( wp_unslash( $_POST['provider'] ) ) : get_option( 'aimentor_provider', 'grok' );

        if ( ! array_key_exists( $provider_key, $provider_labels ) ) {
                $provider_key = get_option( 'aimentor_provider', 'grok' );
        }

        $models         = aimentor_get_provider_models();
        $model_defaults = aimentor_get_provider_model_defaults();
        $model          = $models[ $provider_key ] ?? ( $model_defaults[ $provider_key ] ?? '' );

        $provider = aimentor_get_active_provider( $provider_key );

        if ( ! $provider instanceof AiMentor_Provider_Interface ) {
                aimentor_log_error(
                        'Invalid provider configuration.',
                        array(
                                'provider' => $provider_key,
                                'model'    => $model,
                                'user_id'  => get_current_user_id(),
                        )
                );

                wp_send_json_error( __( 'Provider configuration error.', 'aimentor' ) );
        }

        switch ( $provider_key ) {
                case 'openai':
                        $api_key = get_option( 'aimentor_openai_api_key' );
                        $model   = $models['openai'] ?? $model_defaults['openai'];
                        break;
                case 'grok':
                default:
                        $api_key = get_option( 'aimentor_xai_api_key' );
                        $model   = $models['grok'] ?? $model_defaults['grok'];
                        break;
        }

        $is_canvas_requested = $is_pro && ! empty( $_POST['pro_features'] );
        $is_canvas           = $is_canvas_requested && $provider->supports_canvas();

        $result = $provider->request( $prompt, array(
                'api_key'    => $api_key,
                'model'      => $model,
                'max_tokens' => get_option( 'aimentor_max_tokens', 2000 ),
                'is_canvas'  => $is_canvas,
        ) );

        if ( is_wp_error( $result ) ) {
                $error_message = $result->get_error_message();

                aimentor_log_error(
                        $error_message . ' | Details: ' . wp_json_encode( $result->get_error_data() ),
                        array(
                                'provider' => $provider_key,
                                'model'    => $model,
                                'user_id'  => get_current_user_id(),
                        )
                );

                wp_send_json_error( $error_message );
        }

        $response_payload = array(
                'provider'       => $provider_key,
                'provider_label' => $provider_labels[ $provider_key ] ?? ucfirst( $provider_key ),
        );

        if ( isset( $result['type'] ) && 'canvas' === $result['type'] ) {
                $response_payload['canvas_json'] = $result['content'];
                wp_send_json_success( $response_payload );
        }

        $response_payload['html'] = $result['content'];
        wp_send_json_success( $response_payload );
}

register_uninstall_hook( AIMENTOR_PLUGIN_FILE, 'aimentor_uninstall' );
/**
 * Invoke the uninstall cleanup routine.
 */
function aimentor_uninstall() {
        $uninstall_file = AIMENTOR_PLUGIN_DIR . 'uninstall.php';

        if ( file_exists( $uninstall_file ) ) {
                include $uninstall_file;
        }
}

// Emergency widget registration fallbacks.
$emergency_register = function() {
        if ( ! did_action( 'elementor/loaded' ) || did_action( 'elementor/widgets/register' ) ) {
                return;
        }

        add_action( 'elementor/widgets/register', function( $widgets_manager ) {
                require_once AIMENTOR_PLUGIN_DIR . 'includes/elementor-widget.php';

                if ( class_exists( 'AiMentor_AI_Generator_Widget' ) ) {
                        $widgets_manager->register( new AiMentor_AI_Generator_Widget() );

                        if ( class_exists( 'JagGrok_AI_Generator_Widget' ) ) {
                                $widgets_manager->register( new JagGrok_AI_Generator_Widget() );
                        }
                }
        }, 999 );
};

add_action( 'init', $emergency_register );
add_action( 'plugins_loaded', $emergency_register );
add_action( 'wp_loaded', $emergency_register );
