
<?php
/**
 * Plugin Name: AiMentor Elementor
 * Plugin URI: https://jagjourney.com/
 * Update URI: https://github.com/aimentor/aimentor-elementor
 * Description: 🚀 FREE AI Page Builder - Generate full Elementor layouts with AiMentor. One prompt = complete pages!
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

if ( ! function_exists( 'aimentor_get_error_log_directory' ) ) {
        /**
         * Determine the directory where the AiMentor error log should be stored.
         *
         * @return string
         */
        function aimentor_get_error_log_directory() {
                $default_directory = trailingslashit( WP_CONTENT_DIR ) . 'uploads/aimentor';

                /**
                 * Allow third parties to override the AiMentor error log directory.
                 *
                 * @param string $default_directory Default log directory.
                 */
                $directory = apply_filters( 'aimentor_error_log_directory', $default_directory );

                if ( ! is_string( $directory ) || '' === trim( $directory ) ) {
                        $directory = $default_directory;
                }

                if ( ! file_exists( $directory ) ) {
                        if ( function_exists( 'wp_mkdir_p' ) ) {
                                wp_mkdir_p( $directory );
                        } else {
                                @mkdir( $directory, 0755, true );
                        }
                }

                if ( is_dir( $directory ) && is_writable( $directory ) ) {
                        return $directory;
                }

                $fallback_directory = trailingslashit( WP_CONTENT_DIR );

                if ( is_dir( $fallback_directory ) && is_writable( $fallback_directory ) ) {
                        return $fallback_directory;
                }

                return AIMENTOR_PLUGIN_DIR . 'includes';
        }
}

if ( ! function_exists( 'aimentor_get_error_log_path' ) ) {
        /**
         * Retrieve the fully-qualified path to the AiMentor error log file.
         *
         * @return string
         */
        function aimentor_get_error_log_path() {
                $directory = aimentor_get_error_log_directory();

                return trailingslashit( $directory ) . 'aimentor-errors.log';
        }
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
                'jaggrok_onboarding_dismissed'   => 'aimentor_onboarding_dismissed',
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

        $modern_log = aimentor_get_error_log_path();
        $legacy_logs = array(
                AIMENTOR_PLUGIN_DIR . 'includes/jaggrok-errors.log',
                AIMENTOR_PLUGIN_DIR . 'includes/aimentor-errors.log',
        );

        foreach ( $legacy_logs as $legacy_log ) {
                if ( ! file_exists( $legacy_log ) ) {
                        continue;
                }

                if ( $legacy_log === $modern_log ) {
                        continue;
                }

                $log_directory = dirname( $modern_log );

                if ( ! file_exists( $log_directory ) ) {
                        if ( function_exists( 'wp_mkdir_p' ) ) {
                                wp_mkdir_p( $log_directory );
                        } else {
                                @mkdir( $log_directory, 0755, true );
                        }
                }

                if ( file_exists( $modern_log ) ) {
                        $modern_log_dir = dirname( $modern_log );
                        $can_write      = is_writable( $modern_log ) || ( is_dir( $modern_log_dir ) && is_writable( $modern_log_dir ) );

                        if ( $can_write ) {
                                $legacy_contents = file_get_contents( $legacy_log );

                                if ( false !== $legacy_contents ) {
                                        file_put_contents( $modern_log, $legacy_contents, FILE_APPEND | LOCK_EX );
                                        @unlink( $legacy_log );
                                }

                                continue;
                        }
                }

                if ( ! @rename( $legacy_log, $modern_log ) ) {
                        $legacy_contents = file_get_contents( $legacy_log );

                        if ( false !== $legacy_contents ) {
                                file_put_contents( $modern_log, $legacy_contents, FILE_APPEND | LOCK_EX );
                        }

                        @unlink( $legacy_log );
                }
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

        wp_register_script( 'aimentor-admin-settings', $base . 'js/admin-settings.js', array( 'jquery', 'wp-util' ), $version, true );
        wp_register_script( 'aimentor-elementor-widget', $base . 'js/elementor-widget.js', array( 'jquery', 'elementor-frontend' ), $version, true );
}
add_action( 'init', 'aimentor_register_asset_handles' );

/**
 * Output the Elementor dependency notice.
 */
function aimentor_render_missing_elementor_notice() {
        if ( ! current_user_can( 'activate_plugins' ) ) {
                return;
        }

        echo '<div class="notice notice-error"><p><strong>' . esc_html__( 'AiMentor Elementor', 'aimentor' ) . '</strong> ' . esc_html__( 'requires Elementor to be installed and active.', 'aimentor' ) . '</p></div>';
}

/**
 * Ensure Elementor is active before proceeding.
 *
 * @return bool
 */
function aimentor_check_dependencies() {
        $transient_key = 'aimentor_missing_elementor_notice';

        $elementor_active = did_action( 'elementor/loaded' ) || class_exists( '\Elementor\Plugin' ) || defined( 'ELEMENTOR_VERSION' );

        if ( $elementor_active ) {
                delete_transient( $transient_key );

                return true;
        }

        if ( false === get_transient( $transient_key ) ) {
                set_transient( $transient_key, 1 );
                add_action( 'admin_notices', 'aimentor_render_missing_elementor_notice' );
        }

        return false;
}
add_action( 'plugins_loaded', 'aimentor_check_dependencies', 20 );

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
        $provider_meta_map     = aimentor_get_provider_meta_map();
        $provider_labels       = wp_list_pluck( $provider_meta_map, 'label' );
        $provider_summaries    = wp_list_pluck( $provider_meta_map, 'summary' );
        $defaults              = aimentor_get_default_options();
        $default_task_fallback = aimentor_sanitize_generation_type( $defaults['aimentor_default_generation_type'] ?? 'content' );
        $default_tier_fallback = aimentor_sanitize_performance_tier( $defaults['aimentor_default_performance'] ?? 'fast' );
        $default_task          = aimentor_sanitize_generation_type( get_option( 'aimentor_default_generation_type', $default_task_fallback ) );
        $default_tier          = aimentor_sanitize_performance_tier( get_option( 'aimentor_default_performance', $default_tier_fallback ) );

        return array(
                'ajaxurl'           => admin_url( 'admin-ajax.php' ),
                'nonce'             => wp_create_nonce( 'aimentor_test' ),
                'dismissNonce'      => wp_create_nonce( 'aimentor_onboarding' ),
                'usageNonce'        => wp_create_nonce( 'aimentor_usage_metrics' ),
                'logNonce'          => wp_create_nonce( 'aimentor_error_log' ),
                'restNonce'         => wp_create_nonce( 'wp_rest' ),
                'historyEndpoint'   => esc_url_raw( rest_url( 'aimentor/v1/history' ) ),
                'usageRefreshInterval' => apply_filters( 'aimentor_usage_refresh_interval', MINUTE_IN_SECONDS ),
                'strings'           => array(
                        'testingBadge'       => __( 'Testing', 'aimentor' ),
                        'testingDescription' => __( 'Testing connection…', 'aimentor' ),
                        'missingKey'         => __( 'Enter an API key before testing.', 'aimentor' ),
                        'errorBadge'         => __( 'Error', 'aimentor' ),
                        'unknownError'       => __( 'Unknown error', 'aimentor' ),
                        'onboardingDismissError' => __( 'Unable to dismiss the onboarding card. Please try again.', 'aimentor' ),
                        'usageUpdated'        => __( 'Updated %s', 'aimentor' ),
                        'usageNoActivity'     => __( 'No activity yet', 'aimentor' ),
                        'usageNoContext'      => __( 'Most recent context unavailable.', 'aimentor' ),
                        'usageJustNow'        => __( 'Just now', 'aimentor' ),
                        'logFetchError'       => __( 'Unable to load error logs. Please try again.', 'aimentor' ),
                        'logDownloadError'    => __( 'Unable to download the error log. Please try again.', 'aimentor' ),
                        'logDownloadReady'    => __( 'Log download will begin shortly.', 'aimentor' ),
                        'logClearConfirm'     => __( 'Are you sure you want to clear the error log? This cannot be undone.', 'aimentor' ),
                        'logClearSuccess'     => __( 'Error log cleared.', 'aimentor' ),
                        'logClearError'       => __( 'Unable to clear the error log. Please try again.', 'aimentor' ),
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
                        'askAiMentor'        => __( 'Ask AiMentor', 'aimentor' ),
                        /* translators: %s: Provider label. */
                        'askAiMentorWith'    => __( 'Ask AiMentor via %s', 'aimentor' ),
                        'generationType'     => __( 'Generation Type', 'aimentor' ),
                        'pageLayout'         => __( 'Page Layout', 'aimentor' ),
                        'pageCopy'           => __( 'Page Copy', 'aimentor' ),
                        'performanceLabel'   => __( 'Performance', 'aimentor' ),
                        'fastLabel'          => __( 'Fast', 'aimentor' ),
                        'qualityLabel'       => __( 'Quality', 'aimentor' ),
                        'layoutLabel'        => __( 'Layout', 'aimentor' ),
                        'copyLabel'          => __( 'Copy', 'aimentor' ),
                        /* translators: %s: Model label. */
                        'summaryPoweredBy'   => __( '– powered by %s', 'aimentor' ),
                        'summarySeparator'   => _x( ' • ', 'separator between task and tier', 'aimentor' ),
                        'promptLabel'        => __( 'Prompt', 'aimentor' ),
                        'promptPlaceholder'  => __( 'Describe your page (e.g., hero with CTA)', 'aimentor' ),
                        'missingConfig'      => __( 'AiMentor AJAX configuration is missing. Please ensure the plugin assets are enqueued properly.', 'aimentor' ),
                ),
                'provider'          => get_option( 'aimentor_provider', 'grok' ),
                'providerLabels'    => $provider_labels,
                'providerSummaries' => $provider_summaries,
                'providersMeta'     => $provider_meta_map,
                'defaults'          => array(
                        'task' => $default_task,
                        'tier' => $default_tier,
                ),
                'modelPresets'      => aimentor_get_model_presets(),
                'modelLabels'       => aimentor_get_model_labels(),
                'isProActive'       => aimentor_is_pro_active(),
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
        wp_localize_script( 'aimentor-admin-settings', 'aimentorAjax', aimentor_get_ajax_payload() );
        wp_add_inline_script(
                'aimentor-admin-settings',
                "window.aimentorAjax = window.aimentorAjax || {};\n"
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
        wp_localize_script( 'aimentor-elementor-widget', 'aimentorAjax', aimentor_get_ajax_payload() );
        wp_add_inline_script(
                'aimentor-elementor-widget',
                "window.aimentorAjax = window.aimentorAjax || {};\n",
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

        if ( class_exists( 'AiMentor_AI_Generator_Widget' ) && ! class_exists( 'JagGrok_AI_Generator_Widget' ) ) {
                class_alias( 'AiMentor_AI_Generator_Widget', 'JagGrok_AI_Generator_Widget' );
        }

        $widgets_manager->register( new AiMentor_AI_Generator_Widget() );
} );

// Updater.
if ( aimentor_check_dependencies() ) {
        require_once AIMENTOR_PLUGIN_DIR . 'includes/updater.php';

        if ( class_exists( 'AiMentor_Updater' ) ) {
                new AiMentor_Updater();
        }
}

/**
 * Resolve the active provider based on configuration.
 *
 * @param string|null $provider_key Provider key.
 * @return AiMentor_Provider_Interface
 */
function jaggrok_get_active_provider( $provider_key = null ) {
        if ( null === $provider_key ) {
                $provider_key = get_option( 'aimentor_provider', 'grok' );
        }

        switch ( $provider_key ) {
                case 'openai':
                        return new JagGrok_OpenAI_Provider();
                case 'grok':
                default:
                        return new JagGrok_Grok_Provider();
        }
}

if ( ! function_exists( 'aimentor_get_active_provider' ) ) {
        function aimentor_get_active_provider( $provider_key = null ) {
                return jaggrok_get_active_provider( $provider_key );
        }
}

function jaggrok_normalize_generation_task( $task ) {
        $task = sanitize_key( $task );

        return in_array( $task, [ 'canvas', 'content' ], true ) ? $task : 'content';
}

function jaggrok_normalize_performance_tier( $tier ) {
        $tier = sanitize_key( $tier );

        return in_array( $tier, [ 'fast', 'quality' ], true ) ? $tier : 'fast';
}

function jaggrok_resolve_generation_preset( $provider_key, $requested_task, $requested_tier, $supports_canvas, $is_pro ) {
        $defaults     = aimentor_get_default_options();
        $model_presets  = aimentor_get_model_presets();
        $model_defaults = aimentor_get_provider_model_defaults();

        $default_task = jaggrok_normalize_generation_task( $defaults['aimentor_default_generation_type'] ?? 'content' );
        $default_tier = jaggrok_normalize_performance_tier( $defaults['aimentor_default_performance'] ?? 'fast' );

        $requested_task = $requested_task ? jaggrok_normalize_generation_task( $requested_task ) : '';
        $requested_tier = $requested_tier ? jaggrok_normalize_performance_tier( $requested_tier ) : '';

        $task = $requested_task ? $requested_task : $default_task;
        $tier = $requested_tier ? $requested_tier : $default_tier;

        if ( 'canvas' === $task && ( ! $supports_canvas || ! $is_pro ) ) {
                $task = 'content';
        }

        if ( ! isset( $model_presets[ $provider_key ] ) ) {
                $provider_key = array_key_exists( $provider_key, $model_defaults ) ? $provider_key : 'grok';
        }

        if ( ! isset( $model_presets[ $provider_key ][ $task ] ) ) {
                $task = 'content';
        }

        if ( ! isset( $model_presets[ $provider_key ][ $task ][ $tier ] ) || '' === $model_presets[ $provider_key ][ $task ][ $tier ] ) {
                $tier_candidates = array_keys( $model_presets[ $provider_key ][ $task ] );
                if ( ! empty( $tier_candidates ) ) {
                        $tier = in_array( $default_tier, $tier_candidates, true ) ? $default_tier : reset( $tier_candidates );
                }
        }

        $model = $model_presets[ $provider_key ][ $task ][ $tier ] ?? '';

        if ( '' === $model ) {
                $model = $model_defaults[ $provider_key ][ $task ][ $tier ] ?? '';
        }

        if ( '' === $model ) {
                $fallbacks = aimentor_map_presets_to_legacy_defaults( $model_defaults );
                $model     = $fallbacks[ $provider_key ] ?? '';
        }

        return [
                'task'  => $task,
                'tier'  => $tier,
                'model' => $model,
        ];
}

if ( ! function_exists( 'aimentor_resolve_generation_preset' ) ) {
        function aimentor_resolve_generation_preset( $provider_key, $requested_task, $requested_tier, $supports_canvas, $is_pro ) {
                return jaggrok_resolve_generation_preset( $provider_key, $requested_task, $requested_tier, $supports_canvas, $is_pro );
        }
}

add_action( 'wp_ajax_aimentor_generate_page', 'jaggrok_generate_page_ajax' );
add_action( 'wp_ajax_jaggrok_generate_page', 'jaggrok_generate_page_ajax' );
/**
 * Handle the AJAX request to generate Elementor content.
 */
function jaggrok_generate_page_ajax() {
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

        $provider = jaggrok_get_active_provider( $provider_key );

        if ( ! $provider instanceof AiMentor_Provider_Interface ) {
                aimentor_log_error(
                        'Invalid provider configuration.',
                        array(
                                'provider' => $provider_key,
                                'model'    => '',
                                'task'     => '',
                                'tier'     => '',
                                'user_id'  => get_current_user_id(),
                        )
                );

                wp_send_json_error( __( 'Provider configuration error.', 'aimentor' ) );
        }

        $requested_task_raw = '';
        foreach ( array( 'task', 'task_type' ) as $task_field ) {
                if ( ! isset( $_POST[ $task_field ] ) ) {
                        continue;
                }

                $candidate = sanitize_text_field( wp_unslash( $_POST[ $task_field ] ) );

                if ( '' === $candidate ) {
                        continue;
                }

                $requested_task_raw = $candidate;
                break;
        }

        $requested_tier_raw = '';
        foreach ( array( 'tier', 'performance_tier' ) as $tier_field ) {
                if ( ! isset( $_POST[ $tier_field ] ) ) {
                        continue;
                }

                $candidate = sanitize_text_field( wp_unslash( $_POST[ $tier_field ] ) );

                if ( '' === $candidate ) {
                        continue;
                }

                $requested_tier_raw = $candidate;
                break;
        }

        $requested_task = $requested_task_raw;
        $requested_tier = $requested_tier_raw;

        if ( function_exists( 'aimentor_sanitize_generation_type' ) ) {
                $requested_task = aimentor_sanitize_generation_type( $requested_task );
        } else {
                $requested_task = $requested_task ? jaggrok_normalize_generation_task( $requested_task ) : '';
        }

        if ( function_exists( 'aimentor_sanitize_performance_tier' ) ) {
                $requested_tier = aimentor_sanitize_performance_tier( $requested_tier );
        } else {
                $requested_tier = $requested_tier ? jaggrok_normalize_performance_tier( $requested_tier ) : '';
        }

        $supports_canvas = $provider->supports_canvas();
        $resolution = jaggrok_resolve_generation_preset( $provider_key, $requested_task, $requested_tier, $supports_canvas, $is_pro );
        $task       = $resolution['task'];
        $tier       = $resolution['tier'];
        $model      = $resolution['model'];

        switch ( $provider_key ) {
                case 'openai':
                        $api_key = get_option( 'aimentor_openai_api_key' );
                        break;
                case 'grok':
                default:
                        $api_key = get_option( 'aimentor_xai_api_key' );
                        break;
        }

        $result = $provider->request( $prompt, array(
                'api_key'    => $api_key,
                'model'      => $model,
                'max_tokens' => get_option( 'aimentor_max_tokens', 2000 ),
                'context'    => array(
                        'task' => $task,
                        'tier' => $tier,
                ),
        ) );

        if ( is_wp_error( $result ) ) {
                $error_message = $result->get_error_message();

                if ( function_exists( 'aimentor_record_provider_usage' ) ) {
                        aimentor_record_provider_usage(
                                $provider_key,
                                'error',
                                array(
                                        'model'  => $model,
                                        'task'   => $task,
                                        'tier'   => $tier,
                                        'origin' => 'generation',
                                )
                        );
                }

                aimentor_log_error(
                        $error_message . ' | Details: ' . wp_json_encode( $result->get_error_data() ),
                        array(
                                'provider' => $provider_key,
                                'model'    => $model,
                                'task'     => $task,
                                'tier'     => $tier,
                                'user_id'  => get_current_user_id(),
                        )
                );

                wp_send_json_error( $error_message );
        }

        $response_payload = array(
                'provider'       => $provider_key,
                'provider_label' => $provider_labels[ $provider_key ] ?? ucfirst( $provider_key ),
                'model'          => $model,
                'task'           => $task,
                'tier'           => $tier,
        );

        if ( isset( $result['type'] ) && 'canvas' === $result['type'] ) {
                if ( function_exists( 'aimentor_record_provider_usage' ) ) {
                        aimentor_record_provider_usage(
                                $provider_key,
                                'success',
                                array(
                                        'model'  => $model,
                                        'task'   => $task,
                                        'tier'   => $tier,
                                        'origin' => 'generation',
                                )
                        );
                }

                $response_payload['canvas_json'] = $result['content'];
                wp_send_json_success( $response_payload );
        }

        if ( function_exists( 'aimentor_record_provider_usage' ) ) {
                aimentor_record_provider_usage(
                        $provider_key,
                        'success',
                        array(
                                'model'  => $model,
                                'task'   => $task,
                                'tier'   => $tier,
                                'origin' => 'generation',
                        )
                );
        }

        $response_payload['html'] = $result['content'];
        wp_send_json_success( $response_payload );
}

if ( ! function_exists( 'aimentor_generate_page_ajax' ) ) {
        function aimentor_generate_page_ajax() {
                jaggrok_generate_page_ajax();
        }
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

                if ( class_exists( 'AiMentor_AI_Generator_Widget' ) && ! class_exists( 'JagGrok_AI_Generator_Widget' ) ) {
                        class_alias( 'AiMentor_AI_Generator_Widget', 'JagGrok_AI_Generator_Widget' );
                }

                $widgets_manager->register( new AiMentor_AI_Generator_Widget() );
        }, 999 );
};

add_action( 'init', $emergency_register );
add_action( 'plugins_loaded', $emergency_register );
add_action( 'wp_loaded', $emergency_register );
