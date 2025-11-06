<?php
require_once __DIR__ . '/wp-stubs.php';
require_once __DIR__ . '/../includes/settings.php';

if ( ! function_exists( 'aimentor_assert_same' ) ) {
        function aimentor_assert_same( $expected, $actual, $message ) {
                if ( $expected !== $actual ) {
                        throw new RuntimeException(
                                $message . ' Expected ' . var_export( $expected, true ) . ' but received ' . var_export( $actual, true )
                        );
                }
        }
}

aimentor_test_reset_options();
aimentor_test_reset_capabilities();
aimentor_test_reset_nonce_verification();

function aimentor_expect_ajax_error( callable $callback, $status, $message ) {
        $caught = false;

        try {
                $callback();
        } catch ( RuntimeException $exception ) {
                $caught = true;
                aimentor_assert_same( 'wp_send_json_error: ' . $status, $exception->getMessage(), $message );
        }

        if ( ! $caught ) {
                throw new RuntimeException( $message . ' Expected wp_send_json_error to be triggered.' );
        }
}

// Unauthorized users should be rejected.
aimentor_test_set_capability( 'manage_options', false );
aimentor_test_set_nonce_verification( 'aimentor_settings_tab', true );
$_POST = [
        'nonce' => 'nonce',
        'tab'   => 'overview',
];
aimentor_expect_ajax_error( 'aimentor_load_settings_tab_ajax', 403, 'Unauthorized requests should be rejected.' );

// Invalid nonces should be rejected even for authorized users.
aimentor_test_reset_capabilities();
aimentor_test_set_capability( 'manage_options', true );
aimentor_test_set_nonce_verification( 'aimentor_settings_tab', false );
$_POST = [
        'nonce' => 'invalid',
        'tab'   => 'overview',
];
aimentor_expect_ajax_error( 'aimentor_load_settings_tab_ajax', 403, 'Invalid nonces should be rejected.' );

// Unknown tabs should trigger a 400 error.
aimentor_test_set_nonce_verification( 'aimentor_settings_tab', true );
$_POST = [
        'nonce' => 'nonce',
        'tab'   => 'missing',
];
aimentor_expect_ajax_error( 'aimentor_load_settings_tab_ajax', 400, 'Unknown tabs should return a bad request error.' );

$_POST = [];
aimentor_test_reset_capabilities();
aimentor_test_reset_nonce_verification();

echo "All settings tab loader tests passed.\n";
