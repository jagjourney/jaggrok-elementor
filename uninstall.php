<?php
// ============================================================================
// JAGJourney UNINSTALL v1.0.0
// ============================================================================

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit;

delete_option( 'jaggrok_xai_api_key' );
delete_option( 'jaggrok_api_tested' );
delete_option( 'jaggrok_auto_insert' );
delete_option( 'jaggrok_theme_style' );
delete_option( 'jaggrok_max_tokens' );