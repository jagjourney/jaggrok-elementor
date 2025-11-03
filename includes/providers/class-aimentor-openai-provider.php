<?php

class AiMentor_OpenAI_Provider implements AiMentor_Provider_Interface {
    const API_URL = 'https://api.openai.com/v1/chat/completions';

    public function supports_canvas() {
        return true;
    }

    public function build_request( $prompt, $args = [] ) {
        $api_key = isset( $args['api_key'] ) ? trim( $args['api_key'] ) : '';

        if ( empty( $api_key ) ) {
            return new WP_Error( 'aimentor_missing_api_key', __( 'OpenAI API key not configured.', 'aimentor' ) );
        }

        $context = isset( $args['context'] ) && is_array( $args['context'] )
            ? $args['context']
            : [];

        if ( ! isset( $context['task'], $context['tier'] ) ) {
            $context = $this->normalize_context( $context );
        }

        $model      = ! empty( $args['model'] ) ? sanitize_text_field( $args['model'] ) : $this->get_default_model( $context );
        $max_tokens = isset( $args['max_tokens'] ) ? absint( $args['max_tokens'] ) : 2000;
        $timeout    = isset( $args['timeout'] ) ? absint( $args['timeout'] ) : $this->get_default_timeout( $context );
        $prompt     = trim( (string) $prompt ) . $this->get_prompt_suffix( $context );

        $body = [
            'model'      => $model,
            'messages'   => [ [ 'role' => 'user', 'content' => $prompt ] ],
            'max_tokens' => $max_tokens,
            'temperature' => $this->get_temperature( $context ),
        ];

        if ( 'canvas' === $context['task'] ) {
            $body['response_format'] = [ 'type' => 'json_object' ];
        }

        return [
            'url'      => self::API_URL,
            'args'     => [
                'headers' => [
                    'Authorization' => 'Bearer ' . $api_key,
                    'Content-Type'  => 'application/json',
                ],
                'body'    => wp_json_encode( $body ),
                'timeout' => $timeout,
            ],
            'context' => $context,
        ];
    }

    public function request( $prompt, $args = [] ) {
        $request = $this->build_request( $prompt, $args );

        if ( is_wp_error( $request ) ) {
            return $request;
        }

        $response = wp_remote_post( $request['url'], $request['args'] );

        if ( is_wp_error( $response ) ) {
            return new WP_Error(
                'aimentor_http_request_failed',
                sprintf( __( 'API request failed: %s', 'aimentor' ), $response->get_error_message() ),
                [ 'original' => $response ]
            );
        }

        return $this->parse_response(
            $response,
            [
                'context' => $request['context'],
            ]
        );
    }

    public function parse_response( $response, $args = [] ) {
        $context = isset( $args['context']['task'], $args['context']['tier'] )
            ? $args['context']
            : $this->normalize_context( $args['context'] ?? [] );

        $status_code = wp_remote_retrieve_response_code( $response );
        $raw_body    = wp_remote_retrieve_body( $response );
        $body        = json_decode( $raw_body, true );
        $json_error  = json_last_error();

        if ( 200 !== $status_code ) {
            $error_detail = '';

            if ( is_array( $body ) && isset( $body['error']['message'] ) ) {
                $error_detail = $body['error']['message'];
            } elseif ( ! empty( $raw_body ) ) {
                $error_detail = $raw_body;
            }

            $message = sprintf( 'API request failed with HTTP %d', $status_code );

            if ( ! empty( $error_detail ) ) {
                $message .= ': ' . $error_detail;
            }

            return new WP_Error(
                'aimentor_http_error',
                $message,
                [
                    'status_code' => $status_code,
                    'raw_body'    => $raw_body,
                ]
            );
        }

        if ( JSON_ERROR_NONE !== $json_error || ! is_array( $body ) ) {
            return new WP_Error(
                'aimentor_invalid_response',
                __( 'Unexpected response from the API.', 'aimentor' ),
                [ 'raw_body' => $raw_body ]
            );
        }

        $choices = isset( $body['choices'] ) && is_array( $body['choices'] ) ? $body['choices'] : [];
        $choice  = $choices[0] ?? [];
        $message = isset( $choice['message'] ) && is_array( $choice['message'] ) ? $choice['message'] : [];
        $content = isset( $message['content'] ) ? trim( (string) $message['content'] ) : '';

        if ( '' === $content ) {
            return new WP_Error(
                'aimentor_empty_response',
                __( 'The API response did not include generated content.', 'aimentor' ),
                [ 'body' => $body ]
            );
        }

        $type    = 'canvas' === $context['task'] ? 'canvas' : 'content';
        $payload = $content;

        if ( 'canvas' === $type ) {
            $decoded = json_decode( $content, true );

            if ( JSON_ERROR_NONE !== json_last_error() || ! is_array( $decoded ) ) {
                return new WP_Error(
                    'aimentor_invalid_canvas',
                    __( 'The response was not valid Elementor JSON.', 'aimentor' ),
                    [ 'content' => $content ]
                );
            }

            $payload = $decoded;
        }

        return [
            'type'    => $type,
            'content' => $payload,
            'raw'     => $body,
        ];
    }

    protected function normalize_context( $context ) {
        if ( ! is_array( $context ) ) {
            $context = [];
        }

        $task = isset( $context['task'] ) ? sanitize_key( $context['task'] ) : 'content';
        $tier = isset( $context['tier'] ) ? sanitize_key( $context['tier'] ) : 'fast';

        if ( ! in_array( $task, [ 'canvas', 'content' ], true ) ) {
            $task = 'content';
        }

        if ( ! in_array( $tier, [ 'fast', 'quality' ], true ) ) {
            $tier = 'fast';
        }

        return [
            'task' => $task,
            'tier' => $tier,
        ];
    }

    protected function get_default_model( $context ) {
        $defaults = [
            'canvas'  => [
                'fast'    => 'gpt-4.1-nano',
                'quality' => 'o4-mini',
            ],
            'content' => [
                'fast'    => 'gpt-4o-mini',
                'quality' => 'gpt-4o',
            ],
        ];

        $task_defaults = $defaults[ $context['task'] ] ?? $defaults['content'];

        return $task_defaults[ $context['tier'] ] ?? $defaults['content']['fast'];
    }

    protected function get_prompt_suffix( $context ) {
        $suffix = 'canvas' === $context['task']
            ? ' Respond using Elementor JSON schema with widgets, containers, and layout metadata.'
            : ' Respond with concise Elementor HTML blocks optimized for fast editing.';

        return $suffix . $this->get_brand_prompt_guidance();
    }

    protected function get_temperature( $context ) {
        if ( 'canvas' === $context['task'] ) {
            return 'quality' === $context['tier'] ? 0.25 : 0.2;
        }

        return 'quality' === $context['tier'] ? 0.65 : 0.55;
    }

    protected function get_default_timeout( $context ) {
        if ( 'canvas' === $context['task'] ) {
            return 'quality' === $context['tier'] ? 90 : 60;
        }

        return 'quality' === $context['tier'] ? 50 : 35;
    }

    protected function get_brand_prompt_guidance() {
        $color = sanitize_hex_color( get_option( 'aimentor_primary_color', '#3B82F6' ) );
        $tone  = sanitize_textarea_field( get_option( 'aimentor_tone_keywords', '' ) );
        $tone  = trim( preg_replace( '/\s+/', ' ', $tone ) );

        $parts = [];

        if ( ! empty( $color ) ) {
            $parts[] = sprintf( 'Anchor styling around the brand primary color %s.', strtoupper( $color ) );
        }

        if ( '' !== $tone ) {
            $parts[] = sprintf( 'Keep the writing voice aligned with these tone keywords: %s.', $tone );
        }

        if ( empty( $parts ) ) {
            return '';
        }

        return ' ' . implode( ' ', $parts );
    }
}
