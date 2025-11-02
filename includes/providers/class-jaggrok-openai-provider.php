<?php

class JagGrok_OpenAI_Provider implements JagGrok_Provider_Interface {
    const API_URL = 'https://api.openai.com/v1/chat/completions';

    public function supports_canvas() {
        return true;
    }

    public function build_request( $prompt, $args = [] ) {
        $api_key = isset( $args['api_key'] ) ? trim( $args['api_key'] ) : '';

        if ( empty( $api_key ) ) {
            return new WP_Error( 'jaggrok_missing_api_key', __( 'OpenAI API key not configured.', 'jaggrok-elementor' ) );
        }

        $is_canvas = ! empty( $args['is_canvas'] );
        $max_tokens = isset( $args['max_tokens'] ) ? absint( $args['max_tokens'] ) : 2000;
        $model      = ! empty( $args['model'] ) ? sanitize_text_field( $args['model'] ) : 'gpt-4o-mini';

        $prompt_suffix = $is_canvas
            ? ' Output as structured Elementor JSON with dynamic content and forms.'
            : ' Output as clean HTML sections for Elementor.';

        $body = [
            'model' => $model,
            'messages' => [ [ 'role' => 'user', 'content' => $prompt . $prompt_suffix ] ],
            'max_tokens' => $max_tokens,
        ];

        if ( $is_canvas ) {
            $body['response_format'] = [ 'type' => 'json_object' ];
        }

        return [
            'url'  => self::API_URL,
            'args' => [
                'headers' => [
                    'Authorization' => 'Bearer ' . $api_key,
                    'Content-Type'  => 'application/json',
                ],
                'body'    => wp_json_encode( $body ),
                'timeout' => isset( $args['timeout'] ) ? absint( $args['timeout'] ) : 30,
            ],
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
                'jaggrok_http_request_failed',
                sprintf( __( 'API request failed: %s', 'jaggrok-elementor' ), $response->get_error_message() ),
                [ 'original' => $response ]
            );
        }

        return $this->parse_response( $response, $args );
    }

    public function parse_response( $response, $args = [] ) {
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

            return new WP_Error( 'jaggrok_http_error', $message, [ 'status_code' => $status_code, 'raw_body' => $raw_body ] );
        }

        if ( JSON_ERROR_NONE !== $json_error || ! is_array( $body ) ) {
            return new WP_Error( 'jaggrok_invalid_response', __( 'Unexpected response from the API.', 'jaggrok-elementor' ), [ 'raw_body' => $raw_body ] );
        }

        $message = $body['choices'][0]['message'] ?? [];
        $content = '';

        if ( is_string( $message['content'] ?? null ) ) {
            $content = $message['content'];
        } elseif ( ! empty( $message['content'] ) && is_array( $message['content'] ) ) {
            // Handle the new content array format.
            foreach ( $message['content'] as $part ) {
                if ( isset( $part['text'] ) ) {
                    $content .= $part['text'];
                }
            }
        }

        if ( empty( $content ) ) {
            return new WP_Error( 'jaggrok_empty_response', __( 'The API response did not include generated content.', 'jaggrok-elementor' ), [ 'body' => $body ] );
        }

        $is_canvas = ! empty( $args['is_canvas'] );

        if ( $is_canvas ) {
            $elementor_json = json_decode( $content, true );
            if ( json_last_error() !== JSON_ERROR_NONE || ! is_array( $elementor_json ) ) {
                return new WP_Error( 'jaggrok_invalid_canvas', __( 'The response was not valid Elementor JSON.', 'jaggrok-elementor' ), [ 'content' => $content ] );
            }

            return [
                'type'    => 'canvas',
                'content' => $elementor_json,
            ];
        }

        return [
            'type'    => 'html',
            'content' => $content,
        ];
    }
}
