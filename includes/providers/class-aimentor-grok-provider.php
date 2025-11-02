<?php

class AiMentor_Grok_Provider implements AiMentor_Provider_Interface {
    const API_URL = 'https://api.x.ai/v1/chat/completions';

    public function supports_canvas() {
        return true;
    }

    public function build_request( $prompt, $args = [] ) {
        $api_key = isset( $args['api_key'] ) ? trim( $args['api_key'] ) : '';

        if ( empty( $api_key ) ) {
            return new WP_Error( 'aimentor_missing_api_key', __( 'xAI API key not configured.', 'aimentor' ) );
        }

        /**
         * Build the HTTP request for the Grok API.
         *
         * @param string $prompt Prompt supplied by the editor.
         * @param array  $args   Additional request arguments.
         *
         * @return array|WP_Error
         */
        public function build_request( $prompt, $args = [] ) {
                $api_key = isset( $args['api_key'] ) ? trim( $args['api_key'] ) : '';

                if ( empty( $api_key ) ) {
                        return new WP_Error( 'aimentor_missing_api_key', __( 'xAI API key not configured.', 'aimentor' ) );
                }

                $context   = $this->normalize_context( $args['context'] ?? [] );
                $model     = ! empty( $args['model'] ) ? sanitize_text_field( $args['model'] ) : $this->get_default_model( $context );
                $max_tokens = isset( $args['max_tokens'] ) ? absint( $args['max_tokens'] ) : 2000;
                $timeout    = isset( $args['timeout'] ) ? absint( $args['timeout'] ) : $this->get_default_timeout( $context );
                $prompt    .= $this->get_prompt_suffix( $context );

                $body = [
                        'model'       => $model,
                        'messages'    => [ [ 'role' => 'user', 'content' => $prompt ] ],
                        'max_tokens'  => $max_tokens,
                        'temperature' => $this->get_temperature( $context ),
                ];

                if ( 'canvas' === $context['task'] ) {
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
                                'timeout' => $timeout,
                        ],
                ];
        }

        /**
         * Execute the API request.
         *
         * @param string $prompt Prompt text.
         * @param array  $args   Request arguments.
         *
         * @return array|WP_Error
         */
        public function request( $prompt, $args = [] ) {
                $request = $this->build_request( $prompt, $args );

        if ( is_wp_error( $response ) ) {
            return new WP_Error(
                'aimentor_http_request_failed',
                sprintf( __( 'API request failed: %s', 'aimentor' ), $response->get_error_message() ),
                [ 'original' => $response ]
            );
        }

        /**
         * Parse the API response into a normalized payload.
         *
         * @param array|WP_Error $response Response object.
         * @param array          $args     Request arguments.
         *
         * @return array|WP_Error
         */
        public function parse_response( $response, $args = [] ) {
                $context     = $this->normalize_context( $args['context'] ?? [] );
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

                        return new WP_Error( 'aimentor_http_error', $message, [ 'status_code' => $status_code, 'raw_body' => $raw_body ] );
                }

            return new WP_Error( 'aimentor_http_error', $message, [ 'status_code' => $status_code, 'raw_body' => $raw_body ] );
        }

        if ( JSON_ERROR_NONE !== $json_error || ! is_array( $body ) ) {
            return new WP_Error( 'aimentor_invalid_response', __( 'Unexpected response from the API.', 'aimentor' ), [ 'raw_body' => $raw_body ] );
        }

        /**
         * Resolve a sensible default model for the given context.
         *
         * @param array $context Normalized context.
         *
         * @return string
         */
        protected function get_default_model( $context ) {
                $defaults = [
                        'canvas'  => [
                                'fast'    => 'grok-3-mini',
                                'quality' => 'grok-4-code',
                        ],
                        'content' => [
                                'fast'    => 'grok-3-beta',
                                'quality' => 'grok-4',
                        ],
                ];

        if ( empty( $generated ) ) {
            return new WP_Error( 'aimentor_empty_response', __( 'The API response did not include generated content.', 'aimentor' ), [ 'body' => $body ] );
        }

        /**
         * Provide a prompt suffix tailored for the requested context.
         *
         * @param array $context Normalized context.
         *
         * @return string
         */
        protected function get_prompt_suffix( $context ) {
                if ( 'canvas' === $context['task'] ) {
                        return ' Output as structured Elementor JSON with dynamic content, forms, and responsive columns.';
                }

        if ( $is_canvas ) {
            $elementor_json = json_decode( $generated, true );
            if ( json_last_error() !== JSON_ERROR_NONE || ! is_array( $elementor_json ) ) {
                return new WP_Error( 'aimentor_invalid_canvas', __( 'The response was not valid Elementor JSON.', 'aimentor' ), [ 'content' => $generated ] );
            }

                return ' Output as clean Elementor HTML sections optimized for fast insertion.';
        }

        /**
         * Determine a temperature tuned for the context.
         *
         * @param array $context Normalized context.
         *
         * @return float
         */
        protected function get_temperature( $context ) {
                if ( 'canvas' === $context['task'] ) {
                        return 'quality' === $context['tier'] ? 0.4 : 0.35;
                }

                return 'quality' === $context['tier'] ? 0.7 : 0.5;
        }

        /**
         * Determine a sensible timeout for the context.
         *
         * @param array $context Normalized context.
         *
         * @return int
         */
        protected function get_default_timeout( $context ) {
                if ( 'canvas' === $context['task'] ) {
                        return 'quality' === $context['tier'] ? 75 : 55;
                }

                return 'quality' === $context['tier'] ? 45 : 30;
        }
}

if ( ! class_exists( 'AiMentor_Grok_Provider', false ) ) {
        class_alias( JagGrok_Grok_Provider::class, 'AiMentor_Grok_Provider' );
}
