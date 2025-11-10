<?php
/**
 * AiMentor knowledge base storage and REST helpers.
 *
 * @package AiMentorElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Option key used to persist knowledge pack data.
 *
 * @return string
 */
function aimentor_get_knowledge_packs_option_name() {
    return 'aimentor_knowledge_packs';
}

/**
 * Retrieve raw knowledge pack data from the options table.
 *
 * @return array
 */
function aimentor_get_knowledge_packs_raw() {
    $value = get_option( aimentor_get_knowledge_packs_option_name(), array() );

    if ( ! is_array( $value ) ) {
        return array();
    }

    return $value;
}

/**
 * Persist sanitized knowledge pack data to the options table.
 *
 * @param array $packs Knowledge packs to store.
 *
 * @return void
 */
function aimentor_store_knowledge_packs_raw( $packs ) {
    if ( ! is_array( $packs ) ) {
        $packs = array();
    }

    update_option( aimentor_get_knowledge_packs_option_name(), array_values( $packs ), false );
}

/**
 * Normalize incoming knowledge ID data into a flat array.
 *
 * @param mixed $value Raw value from a request or stored state.
 *
 * @return array
 */
function aimentor_normalize_knowledge_ids_input( $value ) {
    if ( is_array( $value ) ) {
        return $value;
    }

    if ( is_string( $value ) ) {
        $decoded = json_decode( $value, true );

        if ( is_array( $decoded ) ) {
            return $decoded;
        }

        return preg_split( '/[\s,]+/', $value, -1, PREG_SPLIT_NO_EMPTY );
    }

    if ( null === $value || '' === $value ) {
        return array();
    }

    return array( $value );
}

/**
 * Sanitize a list of knowledge IDs.
 *
 * @param mixed $ids Raw IDs to sanitize.
 *
 * @return array
 */
function aimentor_sanitize_knowledge_ids( $ids ) {
    $ids = aimentor_normalize_knowledge_ids_input( $ids );

    if ( empty( $ids ) ) {
        return array();
    }

    $normalized = array();

    foreach ( $ids as $id ) {
        $candidate = sanitize_text_field( (string) $id );

        if ( '' === $candidate ) {
            continue;
        }

        $normalized[ $candidate ] = true;
    }

    return array_keys( $normalized );
}

/**
 * Sanitize knowledge IDs coming from REST requests.
 *
 * @param mixed $value Raw value from the REST request.
 *
 * @return array
 */
function aimentor_rest_sanitize_knowledge_ids( $value ) {
    return aimentor_sanitize_knowledge_ids( $value );
}

/**
 * Normalize a single knowledge pack entry.
 *
 * @param array $entry Raw entry.
 *
 * @return array|null
 */
function aimentor_normalize_knowledge_pack_entry( $entry ) {
    if ( ! is_array( $entry ) ) {
        return null;
    }

    $id        = isset( $entry['id'] ) ? sanitize_text_field( $entry['id'] ) : '';
    $title     = isset( $entry['title'] ) ? sanitize_text_field( $entry['title'] ) : '';
    $summary   = isset( $entry['summary'] ) ? sanitize_textarea_field( $entry['summary'] ) : '';
    $guidance  = isset( $entry['guidance'] ) ? sanitize_textarea_field( $entry['guidance'] ) : '';
    $created   = isset( $entry['created_at'] ) ? absint( $entry['created_at'] ) : 0;
    $updated   = isset( $entry['updated_at'] ) ? absint( $entry['updated_at'] ) : 0;
    $author_id = isset( $entry['author'] ) ? absint( $entry['author'] ) : 0;

    if ( '' === $id ) {
        return null;
    }

    return array(
        'id'         => $id,
        'title'      => $title,
        'summary'    => $summary,
        'guidance'   => $guidance,
        'created_at' => $created,
        'updated_at' => $updated,
        'author'     => $author_id,
    );
}

/**
 * Retrieve sanitized knowledge pack entries.
 *
 * @return array
 */
function aimentor_get_knowledge_packs() {
    $raw   = aimentor_get_knowledge_packs_raw();
    $packs = array();

    foreach ( $raw as $entry ) {
        $normalized = aimentor_normalize_knowledge_pack_entry( $entry );

        if ( $normalized ) {
            $packs[] = $normalized;
        }
    }

    return $packs;
}

/**
 * Retrieve a single knowledge pack by ID.
 *
 * @param string $id Knowledge pack ID.
 *
 * @return array|null
 */
function aimentor_get_knowledge_pack( $id ) {
    $id    = sanitize_text_field( (string) $id );
    $packs = aimentor_get_knowledge_packs();

    foreach ( $packs as $pack ) {
        if ( $pack['id'] === $id ) {
            return $pack;
        }
    }

    return null;
}

/**
 * Prepare a knowledge pack for JSON responses.
 *
 * @param array $pack Knowledge pack data.
 *
 * @return array
 */
function aimentor_prepare_knowledge_pack_for_response( $pack ) {
    $pack = aimentor_normalize_knowledge_pack_entry( $pack );

    if ( ! $pack ) {
        return array();
    }

    $timezone = new \DateTimeZone( 'UTC' );

    $pack['created_at_gmt'] = $pack['created_at'] ? wp_date( 'c', $pack['created_at'], $timezone ) : '';
    $pack['updated_at_gmt'] = $pack['updated_at'] ? wp_date( 'c', $pack['updated_at'], $timezone ) : '';

    return apply_filters( 'aimentor_prepare_knowledge_pack_for_response', $pack );
}

/**
 * Save a knowledge pack entry.
 *
 * @param array $data Knowledge pack fields.
 *
 * @return array|WP_Error
 */
function aimentor_save_knowledge_pack( $data ) {
    if ( ! is_array( $data ) ) {
        $data = array();
    }

    $title    = isset( $data['title'] ) ? sanitize_text_field( $data['title'] ) : '';
    $guidance = isset( $data['guidance'] ) ? sanitize_textarea_field( $data['guidance'] ) : '';
    $summary  = isset( $data['summary'] ) ? sanitize_textarea_field( $data['summary'] ) : '';
    $id       = isset( $data['id'] ) ? sanitize_text_field( $data['id'] ) : '';

    if ( '' === $title ) {
        return new WP_Error( 'aimentor_missing_title', __( 'Knowledge pack title is required.', 'aimentor' ), array( 'status' => 400 ) );
    }

    if ( '' === $guidance ) {
        return new WP_Error( 'aimentor_missing_guidance', __( 'Add guidance so the knowledge pack can influence generations.', 'aimentor' ), array( 'status' => 400 ) );
    }

    $packs   = aimentor_get_knowledge_packs_raw();
    $now     = current_time( 'timestamp' );
    $user_id = get_current_user_id();
    $updated = false;

    if ( $id ) {
        foreach ( $packs as $index => $pack ) {
            if ( isset( $pack['id'] ) && $pack['id'] === $id ) {
                $created_at = isset( $pack['created_at'] ) ? absint( $pack['created_at'] ) : $now;

                $packs[ $index ] = array(
                    'id'         => $id,
                    'title'      => $title,
                    'summary'    => $summary,
                    'guidance'   => $guidance,
                    'created_at' => $created_at,
                    'updated_at' => $now,
                    'author'     => isset( $pack['author'] ) ? absint( $pack['author'] ) : $user_id,
                );

                $updated = true;
                break;
            }
        }
    }

    if ( ! $updated ) {
        $id    = wp_generate_uuid4();
        $packs[] = array(
            'id'         => $id,
            'title'      => $title,
            'summary'    => $summary,
            'guidance'   => $guidance,
            'created_at' => $now,
            'updated_at' => $now,
            'author'     => $user_id,
        );
    }

    aimentor_store_knowledge_packs_raw( $packs );

    $saved = aimentor_get_knowledge_pack( $id );

    return $saved ? $saved : new WP_Error( 'aimentor_knowledge_pack_not_saved', __( 'Unable to save the knowledge pack.', 'aimentor' ), array( 'status' => 500 ) );
}

/**
 * Delete a knowledge pack by ID.
 *
 * @param string $id Knowledge pack ID.
 *
 * @return array|WP_Error
 */
function aimentor_delete_knowledge_pack( $id ) {
    $id    = sanitize_text_field( (string) $id );
    $packs = aimentor_get_knowledge_packs_raw();
    $found = null;

    $remaining = array();

    foreach ( $packs as $pack ) {
        if ( isset( $pack['id'] ) && $pack['id'] === $id ) {
            $found = aimentor_normalize_knowledge_pack_entry( $pack );
            continue;
        }

        $remaining[] = $pack;
    }

    if ( null === $found ) {
        return new WP_Error( 'aimentor_knowledge_pack_not_found', __( 'Knowledge pack not found.', 'aimentor' ), array( 'status' => 404 ) );
    }

    aimentor_store_knowledge_packs_raw( $remaining );

    return $found;
}

/**
 * Compile knowledge directives for provider context consumption.
 *
 * @param array $ids Requested knowledge pack IDs.
 *
 * @return array
 */
function aimentor_compile_knowledge_context( $ids ) {
    $normalized_ids = aimentor_sanitize_knowledge_ids( $ids );

    if ( empty( $normalized_ids ) ) {
        return array();
    }

    $packs        = array();
    $guidance_set = array();
    $summary_set  = array();

    foreach ( $normalized_ids as $id ) {
        $pack = aimentor_get_knowledge_pack( $id );

        if ( ! $pack ) {
            continue;
        }

        $packs[] = array(
            'id'       => $pack['id'],
            'title'    => $pack['title'],
            'summary'  => $pack['summary'],
            'guidance' => $pack['guidance'],
        );

        $title = $pack['title'] ? $pack['title'] : $pack['id'];

        if ( '' !== $pack['guidance'] ) {
            $guidance_set[] = trim( $title . ': ' . $pack['guidance'] );
        }

        if ( '' !== $pack['summary'] ) {
            $summary_set[] = trim( $title . ': ' . $pack['summary'] );
        }
    }

    if ( empty( $packs ) ) {
        return array();
    }

    $compiled = array(
        'packs'    => $packs,
        'summary'  => implode( ' | ', $summary_set ),
        'guidance' => implode( "\n\n", $guidance_set ),
        'ids'      => $normalized_ids,
    );

    /**
     * Allow third parties to modify the compiled knowledge payload before it is passed to providers.
     *
     * @param array $compiled Compiled knowledge data.
     * @param array $packs    Individual pack data.
     * @param array $ids      Requested knowledge pack IDs.
     */
    $compiled = apply_filters( 'aimentor_compiled_knowledge_payload', $compiled, $packs, $normalized_ids );

    return $compiled;
}

/**
 * Prepare provider-ready knowledge context with filters applied.
 *
 * @param mixed  $ids          Raw knowledge IDs.
 * @param string $provider_key Provider key receiving the context.
 * @param array  $args         Additional metadata.
 *
 * @return array
 */
function aimentor_prepare_provider_knowledge_context( $ids, $provider_key = '', $args = array() ) {
    $normalized_ids = aimentor_sanitize_knowledge_ids( $ids );

    if ( empty( $normalized_ids ) ) {
        return array();
    }

    $compiled = aimentor_compile_knowledge_context( $normalized_ids );

    if ( empty( $compiled ) ) {
        return array();
    }

    if ( ! isset( $compiled['ids'] ) || ! is_array( $compiled['ids'] ) ) {
        $compiled['ids'] = $normalized_ids;
    } else {
        $compiled['ids'] = aimentor_sanitize_knowledge_ids( $compiled['ids'] );
    }

    /**
     * Allow third parties to modify the provider knowledge context payload.
     *
     * @param array  $compiled      Compiled knowledge payload.
     * @param array  $normalized_ids Sanitized knowledge IDs.
     * @param string $provider_key  Provider key.
     * @param array  $args          Additional context arguments.
     */
    $filtered = apply_filters( 'aimentor_provider_knowledge_context', $compiled, $compiled['ids'], $provider_key, $args );

    if ( empty( $filtered ) || ! is_array( $filtered ) ) {
        return array();
    }

    if ( ! isset( $filtered['ids'] ) || ! is_array( $filtered['ids'] ) ) {
        $filtered['ids'] = $compiled['ids'];
    } else {
        $filtered['ids'] = aimentor_sanitize_knowledge_ids( $filtered['ids'] );
    }

    return $filtered;
}

/**
 * Determine if the current user can manage knowledge packs.
 *
 * @return bool
 */
function aimentor_can_manage_knowledge_packs() {
    return current_user_can( 'manage_options' );
}

/**
 * Permission callback for reading knowledge packs.
 *
 * @param WP_REST_Request $request REST request.
 *
 * @return bool
 */
function aimentor_knowledge_packs_read_permissions_check( WP_REST_Request $request ) {
    return current_user_can( 'edit_posts' );
}

/**
 * Permission callback for mutating knowledge packs.
 *
 * @param WP_REST_Request $request REST request.
 *
 * @return bool
 */
function aimentor_knowledge_packs_manage_permissions_check( WP_REST_Request $request ) {
    return aimentor_can_manage_knowledge_packs();
}

/**
 * REST callback for listing knowledge packs.
 *
 * @param WP_REST_Request $request REST request.
 *
 * @return WP_REST_Response
 */
function aimentor_rest_get_knowledge_packs( WP_REST_Request $request ) {
    $packs = array_map( 'aimentor_prepare_knowledge_pack_for_response', aimentor_get_knowledge_packs() );

    return new WP_REST_Response(
        array(
            'success' => true,
            'packs'   => $packs,
        ),
        200
    );
}

/**
 * REST callback for creating a knowledge pack.
 *
 * @param WP_REST_Request $request REST request.
 *
 * @return WP_REST_Response|WP_Error
 */
function aimentor_rest_create_knowledge_pack( WP_REST_Request $request ) {
    $data = array(
        'title'    => $request->get_param( 'title' ),
        'summary'  => $request->get_param( 'summary' ),
        'guidance' => $request->get_param( 'guidance' ),
    );

    $result = aimentor_save_knowledge_pack( $data );

    if ( is_wp_error( $result ) ) {
        return $result;
    }

    return new WP_REST_Response(
        array(
            'success' => true,
            'pack'    => aimentor_prepare_knowledge_pack_for_response( $result ),
            'packs'   => array_map( 'aimentor_prepare_knowledge_pack_for_response', aimentor_get_knowledge_packs() ),
        ),
        201
    );
}

/**
 * REST callback for updating a knowledge pack.
 *
 * @param WP_REST_Request $request REST request.
 *
 * @return WP_REST_Response|WP_Error
 */
function aimentor_rest_update_knowledge_pack( WP_REST_Request $request ) {
    $id = $request->get_param( 'id' );

    if ( ! $id ) {
        return new WP_Error( 'aimentor_missing_id', __( 'Knowledge pack ID is required.', 'aimentor' ), array( 'status' => 400 ) );
    }

    $data = array(
        'id'       => $id,
        'title'    => $request->get_param( 'title' ),
        'summary'  => $request->get_param( 'summary' ),
        'guidance' => $request->get_param( 'guidance' ),
    );

    $result = aimentor_save_knowledge_pack( $data );

    if ( is_wp_error( $result ) ) {
        return $result;
    }

    return new WP_REST_Response(
        array(
            'success' => true,
            'pack'    => aimentor_prepare_knowledge_pack_for_response( $result ),
            'packs'   => array_map( 'aimentor_prepare_knowledge_pack_for_response', aimentor_get_knowledge_packs() ),
        ),
        200
    );
}

/**
 * REST callback for deleting a knowledge pack.
 *
 * @param WP_REST_Request $request REST request.
 *
 * @return WP_REST_Response|WP_Error
 */
function aimentor_rest_delete_knowledge_pack( WP_REST_Request $request ) {
    $id = $request->get_param( 'id' );

    if ( ! $id ) {
        return new WP_Error( 'aimentor_missing_id', __( 'Knowledge pack ID is required.', 'aimentor' ), array( 'status' => 400 ) );
    }

    $result = aimentor_delete_knowledge_pack( $id );

    if ( is_wp_error( $result ) ) {
        return $result;
    }

    return new WP_REST_Response(
        array(
            'success' => true,
            'pack'    => aimentor_prepare_knowledge_pack_for_response( $result ),
            'packs'   => array_map( 'aimentor_prepare_knowledge_pack_for_response', aimentor_get_knowledge_packs() ),
        ),
        200
    );
}

/**
 * Register REST API routes for managing knowledge packs.
 */
function aimentor_register_knowledge_routes() {
    register_rest_route(
        'aimentor/v1',
        '/knowledge-packs',
        array(
            array(
                'methods'             => WP_REST_Server::READABLE,
                'callback'            => 'aimentor_rest_get_knowledge_packs',
                'permission_callback' => 'aimentor_knowledge_packs_read_permissions_check',
            ),
            array(
                'methods'             => WP_REST_Server::CREATABLE,
                'callback'            => 'aimentor_rest_create_knowledge_pack',
                'permission_callback' => 'aimentor_knowledge_packs_manage_permissions_check',
                'args'                => array(
                    'title' => array(
                        'type'              => 'string',
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                    'summary' => array(
                        'type'              => 'string',
                        'required'          => false,
                        'sanitize_callback' => 'sanitize_textarea_field',
                    ),
                    'guidance' => array(
                        'type'              => 'string',
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_textarea_field',
                    ),
                ),
            ),
        )
    );

    register_rest_route(
        'aimentor/v1',
        '/knowledge-packs/(?P<id>[A-Za-z0-9\-]+)',
        array(
            array(
                'methods'             => WP_REST_Server::EDITABLE,
                'callback'            => 'aimentor_rest_update_knowledge_pack',
                'permission_callback' => 'aimentor_knowledge_packs_manage_permissions_check',
                'args'                => array(
                    'id' => array(
                        'type'     => 'string',
                        'required' => true,
                    ),
                    'title' => array(
                        'type'              => 'string',
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_text_field',
                    ),
                    'summary' => array(
                        'type'              => 'string',
                        'required'          => false,
                        'sanitize_callback' => 'sanitize_textarea_field',
                    ),
                    'guidance' => array(
                        'type'              => 'string',
                        'required'          => true,
                        'sanitize_callback' => 'sanitize_textarea_field',
                    ),
                ),
            ),
            array(
                'methods'             => WP_REST_Server::DELETABLE,
                'callback'            => 'aimentor_rest_delete_knowledge_pack',
                'permission_callback' => 'aimentor_knowledge_packs_manage_permissions_check',
                'args'                => array(
                    'id' => array(
                        'type'     => 'string',
                        'required' => true,
                    ),
                ),
            ),
        )
    );
}
add_action( 'rest_api_init', 'aimentor_register_knowledge_routes' );
