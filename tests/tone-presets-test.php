<?php
require_once __DIR__ . '/wp-stubs.php';
require_once __DIR__ . '/../includes/settings.php';

function aimentor_assert_same( $expected, $actual, $message ) {
        if ( $expected !== $actual ) {
                throw new RuntimeException(
                        $message . ' Expected ' . var_export( $expected, true ) . ' but received ' . var_export( $actual, true )
                );
        }
}

aimentor_test_reset_options();
$defaults = aimentor_get_default_options();
foreach ( $defaults as $option => $value ) {
        add_option( $option, $value );
}

update_option( 'aimentor_tone_keywords', ' Friendly,  confident ' );

$presets = aimentor_get_tone_presets();

if ( count( $presets ) < 4 ) {
        throw new RuntimeException( 'Expected at least four tone presets (brand + three defaults).' );
}

aimentor_assert_same( 'brand', $presets[0]['id'], 'Brand preset should appear first.' );
aimentor_assert_same( 'Brand tone — Friendly, confident', $presets[0]['label'], 'Brand preset label should include sanitized tone.' );
aimentor_assert_same( 'Friendly, confident', $presets[0]['keywords'], 'Brand preset keywords should be sanitized.' );
aimentor_assert_same( true, $presets[0]['is_brand'], 'Brand preset should be flagged as is_brand.' );

aimentor_test_reset_options();
foreach ( $defaults as $option => $value ) {
        add_option( $option, $value );
}

update_option( 'aimentor_tone_keywords', '' );

$presets = aimentor_get_tone_presets();

aimentor_assert_same( 'Brand tone', $presets[0]['label'], 'Empty brand tone should fall back to generic label.' );
aimentor_assert_same( '', $presets[0]['keywords'], 'Empty brand tone should not expose keywords.' );

echo "All tone preset tests passed.\n";
