<?php
// Prevent direct access
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

// Clean up options
delete_option( 'jaggrok_xai_api_key' );
delete_option( 'jaggrok_api_tested' );
delete_option( 'jaggrok_auto_insert' );
delete_option( 'jaggrok_theme_style' );
delete_option( 'jaggrok_max_tokens' );
delete_option( 'jaggrok_model' );

// Delete log file
$log_file = plugin_dir_path( __FILE__ ) . 'includes/jaggrok-errors.log';
if ( file_exists( $log_file ) ) unlink( $log_file );