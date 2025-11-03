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
        update_option( $option, $value );
}

update_option( 'aimentor_model_presets', aimentor_get_provider_model_defaults() );

aimentor_assert_same(
        'grok-3-beta',
        aimentor_sanitize_model( 'not-a-real-model' ),
        'Invalid Grok models should fall back to the fast default.'
);

aimentor_assert_same(
        'gpt-4o',
        aimentor_sanitize_openai_model( 'gpt-4o' ),
        'Known OpenAI models should pass straight through.'
);

$submitted = [
        'grok'   => 'grok-4',
        'openai' => 'not-a-real-model',
];

$sanitized = aimentor_sync_legacy_model_options( $submitted, [] );
update_option( 'aimentor_provider_models', $sanitized );

aimentor_assert_same( 'grok-4', $sanitized['grok'], 'Grok selection should persist after sanitization.' );

$openai_fallback = aimentor_sanitize_openai_model( 'not-a-real-model' );
aimentor_assert_same( $openai_fallback, $sanitized['openai'], 'OpenAI selection should fall back to the configured default.' );

aimentor_assert_same( 'grok-4', get_option( 'aimentor_model' ), 'Legacy Grok option should receive the sanitized model.' );

aimentor_assert_same(
        $openai_fallback,
        get_option( 'aimentor_openai_model' ),
        'Legacy OpenAI option should receive the sanitized model.'
);

$presets = get_option( 'aimentor_model_presets' );
$provider_defaults = aimentor_get_provider_model_defaults();

aimentor_assert_same(
        'grok-4',
        $presets['grok']['content']['fast'],
        'Grok content fast preset should mirror the stored provider model.'
);

aimentor_assert_same(
        'grok-4',
        $presets['grok']['canvas']['fast'],
        'Grok canvas fast preset should mirror the stored provider model.'
);

aimentor_assert_same(
        $provider_defaults['grok']['content']['quality'],
        $presets['grok']['content']['quality'],
        'Grok quality preset should remain at its default selection.'
);

aimentor_assert_same(
        $openai_fallback,
        $presets['openai']['content']['fast'],
        'OpenAI fast preset should honor the sanitized fallback.'
);

aimentor_assert_same(
        $sanitized,
        aimentor_get_provider_models(),
        'Provider model getter should return the sanitized selections.'
);

aimentor_assert_same(
        $presets,
        aimentor_get_model_presets(),
        'Model presets getter should return the sanitized preset matrix.'
);

echo "All model option tests passed.\n";
