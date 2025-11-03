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

$preloaded = [
        'aimentor_provider'      => 'openai',
        'aimentor_auto_insert'   => 'no',
        'aimentor_primary_color' => '#111827',
];

foreach ( $preloaded as $option => $value ) {
        aimentor_test_preload_option( $option, $value );
}

aimentor_seed_default_options();

foreach ( $preloaded as $option => $value ) {
        aimentor_assert_same(
                $value,
                get_option( $option ),
                sprintf( 'Preloaded option %s should retain its seeded value.', $option )
        );

        aimentor_assert_same(
                'preloaded',
                aimentor_test_get_option_source( $option ),
                sprintf( 'Preloaded option %s should not be overwritten by defaults.', $option )
        );
}

$expected_defaults = [
        'aimentor_theme_style' => 'modern',
];

foreach ( $expected_defaults as $option => $expected_value ) {
        $actual = get_option( $option, false );

        if ( array_key_exists( $option, $preloaded ) ) {
                continue;
        }

        aimentor_assert_same(
                $expected_value,
                $actual,
                sprintf( 'Option %s should be initialized with its default.', $option )
        );

        aimentor_assert_same(
                'default',
                aimentor_test_get_option_source( $option ),
                sprintf( 'Option %s should be marked as default seeded.', $option )
        );
}

echo "Option seeding tests passed.\n";
