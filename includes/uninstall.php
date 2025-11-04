<?php
// ============================================================================
// AiMentor UNINSTALL v1.3.1
// ============================================================================

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

foreach ( [
        'aimentor_xai_api_key',
        'aimentor_anthropic_api_key',
        'aimentor_openai_api_key',
        'aimentor_api_tested',
        'aimentor_auto_insert',
        'aimentor_theme_style',
        'aimentor_max_tokens',
        'aimentor_model',
        'aimentor_model_presets',
        'aimentor_document_provider_defaults',
        'aimentor_anthropic_model',
        'aimentor_openai_model',
        'aimentor_provider',
        'aimentor_provider_models',
        'aimentor_provider_test_statuses',
        'aimentor_onboarding_dismissed',
        'jaggrok_xai_api_key',
        'jaggrok_anthropic_api_key',
        'jaggrok_openai_api_key',
        'jaggrok_api_tested',
        'jaggrok_auto_insert',
        'jaggrok_theme_style',
        'jaggrok_max_tokens',
        'jaggrok_model',
        'jaggrok_model_presets',
        'jaggrok_anthropic_model',
        'jaggrok_openai_model',
        'jaggrok_provider',
        'jaggrok_provider_models',
        'jaggrok_provider_test_statuses',
        'jaggrok_onboarding_dismissed',
] as $option ) {
        delete_option( $option );
}

// Delete log file
$log_file = function_exists( 'aimentor_get_error_log_path' )
        ? aimentor_get_error_log_path()
        : plugin_dir_path( __FILE__ ) . 'aimentor-errors.log';

if ( file_exists( $log_file ) ) {
        unlink( $log_file );
}

$legacy_log = plugin_dir_path( __FILE__ ) . 'jaggrok-errors.log';
if ( file_exists( $legacy_log ) ) {
        unlink( $legacy_log );
}
