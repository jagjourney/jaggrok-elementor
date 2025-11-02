<?php
// Prevent direct access
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
        exit;
}

// Clean up options
foreach ( [
        'aimentor_xai_api_key',
        'aimentor_openai_api_key',
        'aimentor_api_tested',
        'aimentor_auto_insert',
        'aimentor_theme_style',
        'aimentor_max_tokens',
        'aimentor_model_presets',
        'aimentor_model',
        'aimentor_openai_model',
        'aimentor_provider',
        'aimentor_provider_models',
        'aimentor_provider_test_statuses',
        'jaggrok_xai_api_key',
        'jaggrok_openai_api_key',
        'jaggrok_api_tested',
        'jaggrok_auto_insert',
        'jaggrok_theme_style',
        'jaggrok_max_tokens',
        'jaggrok_model',
        'jaggrok_openai_model',
        'jaggrok_provider',
        'jaggrok_provider_models',
        'jaggrok_provider_test_statuses',
] as $option ) {
        delete_option( $option );
}

// Delete log file
$log_file = plugin_dir_path( __FILE__ ) . 'includes/aimentor-errors.log';
if ( file_exists( $log_file ) ) {
        unlink( $log_file );
}

$legacy_log = plugin_dir_path( __FILE__ ) . 'includes/jaggrok-errors.log';
if ( file_exists( $legacy_log ) ) {
        unlink( $legacy_log );
}