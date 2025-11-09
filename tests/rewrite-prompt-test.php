<?php
require_once __DIR__ . '/wp-stubs.php';
require_once __DIR__ . '/../includes/settings.php';

if ( ! defined( 'ABSPATH' ) ) {
        define( 'ABSPATH', __DIR__ . '/../' );
}

require_once __DIR__ . '/../aimentor-elementor.php';

function aimentor_assert_true( $condition, $message ) {
        if ( ! $condition ) {
                throw new RuntimeException( $message );
        }
}

function aimentor_assert_contains( $needle, $haystack, $message ) {
        if ( false === strpos( $haystack, $needle ) ) {
                throw new RuntimeException( $message . ' Missing substring: ' . $needle );
        }
}

$prompt = aimentor_build_rewrite_prompt( ' Original copy with [shortcode] ', '  playful , bold  ' );

aimentor_assert_contains( 'Match this tone: playful , bold.', $prompt, 'Prompt should include sanitized tone keywords.' );
aimentor_assert_contains( "Source copy:\n\nOriginal copy with [shortcode]", $prompt, 'Prompt should include trimmed source copy.' );
aimentor_assert_contains( "Rewritten copy:", $prompt, 'Prompt should request rewritten copy section.' );

aimentor_assert_true(
        substr_count( $prompt, 'Rewritten copy:' ) === 1,
        'Prompt should only include a single rewritten copy marker.'
);

$prompt_without_tone = aimentor_build_rewrite_prompt( 'Another example', '' );

aimentor_assert_true(
        false === strpos( $prompt_without_tone, 'Match this tone:' ),
        'Prompt without tone should omit tone instruction.'
);

echo "All rewrite prompt tests passed.\n";
