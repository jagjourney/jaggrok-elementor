<?php
// Legacy compatibility shims.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'jaggrok_activate_plugin' ) ) {
	function jaggrok_activate_plugin( ...$args ) {
		return aimentor_activate_plugin( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_add_settings_page' ) ) {
	function jaggrok_add_settings_page( ...$args ) {
		return aimentor_add_settings_page( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_check_dependencies' ) ) {
	function jaggrok_check_dependencies( ...$args ) {
		return aimentor_check_dependencies( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_enqueue_assets' ) ) {
	function jaggrok_enqueue_assets( ...$args ) {
		return aimentor_enqueue_assets( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_enqueue_elementor_assets' ) ) {
	function jaggrok_enqueue_elementor_assets( ...$args ) {
		return aimentor_enqueue_elementor_assets( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_format_provider_status_for_display' ) ) {
	function jaggrok_format_provider_status_for_display( ...$args ) {
		return aimentor_format_provider_status_for_display( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_generate_page_ajax' ) ) {
	function jaggrok_generate_page_ajax( ...$args ) {
		return aimentor_generate_page_ajax( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_get_active_provider' ) ) {
	function jaggrok_get_active_provider( ...$args ) {
		return aimentor_get_active_provider( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_get_ajax_payload' ) ) {
	function jaggrok_get_ajax_payload( ...$args ) {
		return aimentor_get_ajax_payload( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_get_allowed_provider_models' ) ) {
	function jaggrok_get_allowed_provider_models( ...$args ) {
		return aimentor_get_allowed_provider_models( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_get_default_options' ) ) {
	function jaggrok_get_default_options( ...$args ) {
		return aimentor_get_default_options( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_get_provider_labels' ) ) {
	function jaggrok_get_provider_labels( ...$args ) {
		return aimentor_get_provider_labels( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_get_provider_meta_map' ) ) {
	function jaggrok_get_provider_meta_map( ...$args ) {
		return aimentor_get_provider_meta_map( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_get_provider_model_defaults' ) ) {
        function jaggrok_get_provider_model_defaults( ...$args ) {
                return aimentor_get_provider_model_defaults( ...$args );
        }
}

if ( ! function_exists( 'jaggrok_get_model_presets' ) ) {
        function jaggrok_get_model_presets( ...$args ) {
                return aimentor_get_model_presets( ...$args );
        }
}

if ( ! function_exists( 'jaggrok_get_model_labels' ) ) {
        function jaggrok_get_model_labels( ...$args ) {
                return aimentor_get_model_labels( ...$args );
        }
}

if ( ! function_exists( 'jaggrok_get_provider_models' ) ) {
        function jaggrok_get_provider_models( ...$args ) {
                return aimentor_get_provider_models( ...$args );
        }
}

if ( ! function_exists( 'jaggrok_get_provider_test_statuses' ) ) {
	function jaggrok_get_provider_test_statuses( ...$args ) {
		return aimentor_get_provider_test_statuses( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_is_option_value_empty' ) ) {
	function jaggrok_is_option_value_empty( ...$args ) {
		return aimentor_is_option_value_empty( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_is_pro_active' ) ) {
	function jaggrok_is_pro_active( ...$args ) {
		return aimentor_is_pro_active( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_load_textdomain' ) ) {
	function jaggrok_load_textdomain( ...$args ) {
		return aimentor_load_textdomain( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_log_error' ) ) {
	function jaggrok_log_error( ...$args ) {
		return aimentor_log_error( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_maybe_run_legacy_migration' ) ) {
	function jaggrok_maybe_run_legacy_migration( ...$args ) {
		return aimentor_maybe_run_legacy_migration( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_register_asset_handles' ) ) {
	function jaggrok_register_asset_handles( ...$args ) {
		return aimentor_register_asset_handles( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_register_settings' ) ) {
	function jaggrok_register_settings( ...$args ) {
		return aimentor_register_settings( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_sanitize_api_key' ) ) {
	function jaggrok_sanitize_api_key( ...$args ) {
		return aimentor_sanitize_api_key( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_sanitize_auto_insert' ) ) {
	function jaggrok_sanitize_auto_insert( ...$args ) {
		return aimentor_sanitize_auto_insert( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_sanitize_max_tokens' ) ) {
        function jaggrok_sanitize_max_tokens( ...$args ) {
                return aimentor_sanitize_max_tokens( ...$args );
        }
}

if ( ! function_exists( 'jaggrok_sanitize_primary_color' ) ) {
        function jaggrok_sanitize_primary_color( ...$args ) {
                return aimentor_sanitize_primary_color( ...$args );
        }
}

if ( ! function_exists( 'jaggrok_sanitize_tone_keywords' ) ) {
        function jaggrok_sanitize_tone_keywords( ...$args ) {
                return aimentor_sanitize_tone_keywords( ...$args );
        }
}

if ( ! function_exists( 'jaggrok_sanitize_model' ) ) {
        function jaggrok_sanitize_model( ...$args ) {
                return aimentor_sanitize_model( ...$args );
        }
}

if ( ! function_exists( 'jaggrok_sanitize_openai_model' ) ) {
	function jaggrok_sanitize_openai_model( ...$args ) {
		return aimentor_sanitize_openai_model( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_sanitize_provider' ) ) {
	function jaggrok_sanitize_provider( ...$args ) {
		return aimentor_sanitize_provider( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_sanitize_provider_models' ) ) {
	function jaggrok_sanitize_provider_models( ...$args ) {
		return aimentor_sanitize_provider_models( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_sanitize_theme_style' ) ) {
	function jaggrok_sanitize_theme_style( ...$args ) {
		return aimentor_sanitize_theme_style( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_seed_default_options' ) ) {
	function jaggrok_seed_default_options( ...$args ) {
		return aimentor_seed_default_options( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_settings_link' ) ) {
	function jaggrok_settings_link( ...$args ) {
		return aimentor_settings_link( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_settings_page_callback' ) ) {
	function jaggrok_settings_page_callback( ...$args ) {
		return aimentor_settings_page_callback( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_sync_legacy_model_options' ) ) {
	function jaggrok_sync_legacy_model_options( ...$args ) {
		return aimentor_sync_legacy_model_options( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_test_api_connection' ) ) {
	function jaggrok_test_api_connection( ...$args ) {
		return aimentor_test_api_connection( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_uninstall' ) ) {
	function jaggrok_uninstall( ...$args ) {
		return aimentor_uninstall( ...$args );
	}
}

if ( ! function_exists( 'jaggrok_update_provider_test_status' ) ) {
	function jaggrok_update_provider_test_status( ...$args ) {
		return aimentor_update_provider_test_status( ...$args );
	}
}
