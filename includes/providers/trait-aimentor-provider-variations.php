<?php

trait AiMentor_Provider_Variations_Trait {
/**
 * Sanitize the requested variation count and clamp it to a sensible range.
 *
 * @param int $value Requested variation count.
 * @param int $max   Maximum variations supported.
 *
 * @return int
 */
protected function sanitize_variation_count( $value, $max = 4 ) {
$value = absint( $value );

if ( $value < 1 ) {
$value = 1;
}

if ( $max > 0 && $value > $max ) {
$value = $max;
}

return $value;
}

/**
 * Build a localized label for a variation index.
 *
 * @param int $index Zero-based index.
 *
 * @return string
 */
protected function get_variation_label( $index ) {
$number = absint( $index ) + 1;

/* translators: %d: Variation number. */
return sprintf( __( 'Variation %d', 'aimentor' ), $number );
}

/**
 * Summarize Elementor canvas layout structure for quick descriptions.
 *
 * @param array $layout Canvas layout array.
 *
 * @return string
 */
protected function describe_canvas_layout( $layout ) {
$meta = $this->analyze_canvas_layout_counts( $layout );

$parts = array();

if ( $meta['sections'] > 0 ) {
$parts[] = sprintf(
_n( '%d section', '%d sections', $meta['sections'], 'aimentor' ),
number_format_i18n( $meta['sections'] )
);
}

if ( $meta['columns'] > 0 ) {
$parts[] = sprintf(
_n( '%d column', '%d columns', $meta['columns'], 'aimentor' ),
number_format_i18n( $meta['columns'] )
);
}

if ( $meta['widgets'] > 0 ) {
$parts[] = sprintf(
_n( '%d widget', '%d widgets', $meta['widgets'], 'aimentor' ),
number_format_i18n( $meta['widgets'] )
);
}

if ( empty( $parts ) ) {
return __( 'Layout variation', 'aimentor' );
}

return implode( _x( ' • ', 'separator between variation meta details', 'aimentor' ), $parts );
}

/**
 * Analyse the Elementor layout array for section/column/widget counts.
 *
 * @param mixed $layout Layout data.
 *
 * @return array
 */
protected function analyze_canvas_layout_counts( $layout ) {
$counts = array(
'sections' => 0,
'columns'  => 0,
'widgets'  => 0,
);

$walker = function( $elements ) use ( &$walker, &$counts ) {
if ( ! is_array( $elements ) ) {
return;
}

foreach ( $elements as $element ) {
if ( ! is_array( $element ) ) {
continue;
}

$type = isset( $element['elType'] ) ? $element['elType'] : '';

switch ( $type ) {
case 'section':
$counts['sections']++;
break;
case 'column':
$counts['columns']++;
break;
case 'widget':
$counts['widgets']++;
break;
}

if ( isset( $element['elements'] ) ) {
$walker( $element['elements'] );
}
}
};

if ( isset( $layout['elements'] ) && is_array( $layout['elements'] ) ) {
$walker( $layout['elements'] );
} elseif ( is_array( $layout ) ) {
$walker( $layout );
}

return $counts;
}

/**
 * Normalize raw canvas JSON strings into structured variation payloads.
 *
 * @param array $raw_messages Raw message strings from the provider.
 * @param array $rate_limit   Rate limit payload for error context.
 *
 * @return array|WP_Error
 */
protected function build_canvas_variations( array $raw_messages, $rate_limit = array() ) {
$variations = array();

foreach ( $raw_messages as $index => $raw_message ) {
$raw = trim( (string) $raw_message );

if ( '' === $raw ) {
continue;
}

$decoded = json_decode( $raw, true );

if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $decoded ) ) {
return new WP_Error(
'aimentor_invalid_canvas',
__( 'The response was not valid Elementor JSON.', 'aimentor' ),
array(
'content'    => $raw,
'rate_limit' => $rate_limit,
)
);
}

$meta     = $this->analyze_canvas_layout_counts( $decoded );
$label    = $this->get_variation_label( $index );
$summary  = $this->describe_canvas_layout( $decoded );
$variations[] = array(
'id'      => 'canvas-' . ( $index + 1 ),
'label'   => $label,
'summary' => $summary,
'layout'  => $decoded,
'raw'     => $raw,
'meta'    => $meta,
);
}

if ( empty( $variations ) ) {
return new WP_Error(
'aimentor_empty_response',
__( 'The API response did not include generated content.', 'aimentor' ),
array(
'rate_limit' => $rate_limit,
)
);
}

return $variations;
}

/**
 * Normalize raw HTML/text variations.
 *
 * @param array $raw_messages Raw message strings from the provider.
 *
 * @return array|WP_Error
 */
protected function build_content_variations( array $raw_messages ) {
$variations = array();

foreach ( $raw_messages as $index => $raw_message ) {
$content = trim( (string) $raw_message );

if ( '' === $content ) {
continue;
}

$label   = $this->get_variation_label( $index );
$summary = $this->summarize_html_snippet( $content );

$variations[] = array(
'id'      => 'content-' . ( $index + 1 ),
'label'   => $label,
'summary' => $summary,
'html'    => $content,
);
}

if ( empty( $variations ) ) {
return new WP_Error(
'aimentor_empty_response',
__( 'The API response did not include generated content.', 'aimentor' )
);
}

return $variations;
}

/**
 * Generate a concise summary snippet for textual content.
 *
 * @param string $html Raw HTML/text returned by the provider.
 *
 * @return string
 */
protected function summarize_html_snippet( $html ) {
$text = wp_strip_all_tags( (string) $html );
$text = trim( preg_replace( '/\s+/', ' ', $text ) );

if ( '' === $text ) {
return $this->get_variation_label( 0 );
}

if ( mb_strlen( $text ) > 140 ) {
$text = mb_substr( $text, 0, 137 ) . '…';
}

return $text;
}
}

