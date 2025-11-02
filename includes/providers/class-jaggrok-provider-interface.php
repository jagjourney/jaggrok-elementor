<?php

interface JagGrok_Provider_Interface {
    /**
     * Determine if the provider can output Elementor canvas JSON when requested.
     *
     * @return bool
     */
    public function supports_canvas();

    /**
     * Build the HTTP request configuration for the provider.
     *
     * @param string $prompt Prompt provided by the user.
     * @param array  $args   Additional arguments (e.g. api_key, model, is_canvas).
     *
     * @return array|WP_Error Request data containing `url` and `args` keys or WP_Error on failure.
     */
    public function build_request( $prompt, $args = [] );

    /**
     * Execute the remote request using the built configuration and return the provider response.
     *
     * @param string $prompt Prompt provided by the user.
     * @param array  $args   Additional arguments.
     *
     * @return array|WP_Error Normalized response payload or WP_Error on failure.
     */
    public function request( $prompt, $args = [] );

    /**
     * Parse the raw HTTP response from the provider into a normalized structure.
     *
     * @param array|WP_Error $response Raw response from wp_remote_request.
     * @param array          $args     Additional arguments.
     *
     * @return array|WP_Error Normalized response payload or WP_Error on failure.
     */
    public function parse_response( $response, $args = [] );
}
