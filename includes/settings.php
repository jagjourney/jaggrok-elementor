<?php
// ============================================================================
// AiMentor SETTINGS PAGE v1.3.18 (PROVIDER TEST METRICS)
// ============================================================================

function aimentor_get_provider_model_defaults() {
        return [
                'grok'   => [
                        'canvas'  => [
                                'fast'    => 'grok-3-mini',
                                'quality' => 'grok-4-code',
                        ],
                        'content' => [
                                'fast'    => 'grok-3-beta',
                                'quality' => 'grok-4',
                        ],
                ],
                'anthropic' => [
                        'canvas'  => [
                                'fast'    => 'claude-3-5-haiku',
                                'quality' => 'claude-3-5-sonnet',
                        ],
                        'content' => [
                                'fast'    => 'claude-3-5-haiku',
                                'quality' => 'claude-3-5-sonnet',
                        ],
                ],
                'openai' => [
                        'canvas'  => [
                                'fast'    => 'gpt-4.1-nano',
                                'quality' => 'o4-mini',
                        ],
                        'content' => [
                                'fast'    => 'gpt-4o-mini',
                                'quality' => 'gpt-4o',
                        ],
                ],
        ];
}

function aimentor_get_allowed_provider_models() {
        return [
                'grok'   => [
                        'grok-3-mini' => __( 'Grok 3 Mini (Fast)', 'aimentor' ),
                        'grok-3-beta' => __( 'Grok 3 Beta (Balanced) ★', 'aimentor' ),
                        'grok-3'      => __( 'Grok 3 (Standard)', 'aimentor' ),
                        'grok-4-mini' => __( 'Grok 4 Mini (Premium)', 'aimentor' ),
                        'grok-4'      => __( 'Grok 4 (Flagship)', 'aimentor' ),
                        'grok-4-code' => __( 'Grok 4 Code', 'aimentor' ),
                ],
                'anthropic' => [
                        'claude-3-5-haiku'  => __( 'Claude 3.5 Haiku (Fast)', 'aimentor' ),
                        'claude-3-5-sonnet' => __( 'Claude 3.5 Sonnet (Balanced) ★', 'aimentor' ),
                        'claude-3-5-opus'   => __( 'Claude 3.5 Opus (Flagship)', 'aimentor' ),
                        'claude-3-opus'     => __( 'Claude 3 Opus (Legacy)', 'aimentor' ),
                ],
                'openai' => [
                        'gpt-4o-mini'  => __( 'GPT-4o mini (Balanced) ★', 'aimentor' ),
                        'gpt-4o'       => __( 'GPT-4o (Flagship)', 'aimentor' ),
                        'gpt-4.1'      => __( 'GPT-4.1 (Reasoning)', 'aimentor' ),
                        'gpt-4.1-mini' => __( 'GPT-4.1 mini (Fast)', 'aimentor' ),
                        'gpt-4.1-nano' => __( 'GPT-4.1 nano (Edge)', 'aimentor' ),
                        'o4-mini'      => __( 'o4-mini (Preview)', 'aimentor' ),
                        'o4'           => __( 'o4 (Preview)', 'aimentor' ),
                ],
        ];
}

function aimentor_get_settings_support_resources() {
        $resources = [
                'support'   => [
                        'title' => __( 'Support', 'aimentor' ),
                        'links' => [
                                [
                                        'label'       => __( 'AiMentor Support Center', 'aimentor' ),
                                        'url'         => 'https://jagjourney.com/support/',
                                        'description' => __( 'Browse troubleshooting guides or open a help request.', 'aimentor' ),
                                ],
                                [
                                        'label'       => __( 'System Status', 'aimentor' ),
                                        'url'         => 'https://status.jagjourney.com/',
                                        'description' => __( 'Check live availability for JagJourney services.', 'aimentor' ),
                                ],
                        ],
                ],
                'tutorials' => [
                        'title' => __( 'Tutorials & Learning', 'aimentor' ),
                        'links' => [
                                [
                                        'label'       => __( 'Getting Started with AiMentor', 'aimentor' ),
                                        'url'         => 'https://jagjourney.com/tutorials/aimentor-getting-started/',
                                        'description' => __( 'Step-by-step setup guidance for new sites.', 'aimentor' ),
                                ],
                                [
                                        'label'       => __( 'Build Workflows with Elementor', 'aimentor' ),
                                        'url'         => 'https://jagjourney.com/tutorials/aimentor-workflows/',
                                        'description' => __( 'See examples of prompt engineering and layout generation.', 'aimentor' ),
                                ],
                        ],
                ],
                'contact'   => [
                        'title' => __( 'Contact JagJourney', 'aimentor' ),
                        'links' => [
                                [
                                        'label'       => __( 'Email support@jagjourney.com', 'aimentor' ),
                                        'url'         => 'mailto:support@jagjourney.com',
                                        'description' => __( 'Reach the AiMentor success team directly.', 'aimentor' ),
                                ],
                                [
                                        'label'       => __( 'Schedule a Strategy Call', 'aimentor' ),
                                        'url'         => 'https://jagjourney.com/contact/',
                                        'description' => __( 'Book onboarding or co-building sessions.', 'aimentor' ),
                                ],
                        ],
                ],
        ];

        return apply_filters( 'aimentor_settings_support_resources', $resources );
}

function aimentor_get_request_override_defaults() {
        $tasks = [ 'canvas', 'content' ];
        $fields = [ 'temperature', 'timeout' ];
        $defaults = [];
        foreach ( array_keys( aimentor_get_provider_labels() ) as $provider ) {
                foreach ( $tasks as $task ) {
                        $defaults[ $provider ][ $task ] = array_fill_keys( $fields, '' );
                }
        }

        return $defaults;
}

function aimentor_get_network_managed_options() {
        return [
                'aimentor_network_lock_provider_models',
                'aimentor_provider',
                'aimentor_xai_api_key',
                'aimentor_anthropic_api_key',
                'aimentor_openai_api_key',
                'aimentor_provider_models',
                'aimentor_model_presets',
                'aimentor_document_provider_defaults',
                'aimentor_model',
                'aimentor_anthropic_model',
                'aimentor_openai_model',
                'aimentor_default_generation_type',
                'aimentor_default_performance',
                'aimentor_provider_test_statuses',
                'aimentor_api_tested',
                'aimentor_request_overrides',
        ];
}

function aimentor_is_network_provider_lock_enabled() {
        if ( ! is_multisite() ) {
                return false;
        }

        $raw_value = get_site_option( 'aimentor_network_lock_provider_models', 'no' );
        $raw_value = aimentor_sanitize_toggle( $raw_value );

        return 'yes' === $raw_value;
}

function aimentor_provider_controls_locked_for_request() {
        if ( ! is_multisite() ) {
                return false;
        }

        if ( function_exists( 'is_network_admin' ) && is_network_admin() ) {
                return false;
        }

        return aimentor_is_network_provider_lock_enabled();
}

function aimentor_should_option_use_network_storage( $option ) {
        if ( ! is_multisite() ) {
                return false;
        }

        $managed_options = aimentor_get_network_managed_options();

        if ( ! in_array( $option, $managed_options, true ) ) {
                return false;
        }

        if ( 'aimentor_network_lock_provider_models' === $option ) {
                return true;
        }

        return aimentor_is_network_provider_lock_enabled();
}

function aimentor_maybe_override_option_with_network( $value ) {
        $filter = current_filter();

        if ( 0 !== strpos( $filter, 'pre_option_' ) ) {
                return $value;
        }

        $option = substr( $filter, strlen( 'pre_option_' ) );

        if ( ! aimentor_should_option_use_network_storage( $option ) ) {
                return $value;
        }

        $sentinel     = '__aimentor_network_option_missing__';
        $network_value = get_site_option( $option, $sentinel );

        if ( $sentinel === $network_value ) {
                return $value;
        }

        return $network_value;
}

function aimentor_maybe_sync_network_option( $value, $old_value ) {
        $filter = current_filter();

        if ( 0 !== strpos( $filter, 'pre_update_option_' ) ) {
                return $value;
        }

        $option = substr( $filter, strlen( 'pre_update_option_' ) );

        if ( ! aimentor_should_option_use_network_storage( $option ) ) {
                return $value;
        }

        update_site_option( $option, $value );

        return $value;
}

function aimentor_register_network_option_overrides() {
        if ( ! is_multisite() ) {
                return;
        }

        foreach ( aimentor_get_network_managed_options() as $option ) {
                add_filter( "pre_option_{$option}", 'aimentor_maybe_override_option_with_network' );
                add_filter( "pre_update_option_{$option}", 'aimentor_maybe_sync_network_option', 10, 2 );
        }
}
add_action( 'plugins_loaded', 'aimentor_register_network_option_overrides' );

function aimentor_flatten_allowed_models_for_provider( $provider_key ) {
        $allowed = aimentor_get_allowed_provider_models();

        if ( ! isset( $allowed[ $provider_key ] ) || ! is_array( $allowed[ $provider_key ] ) ) {
                return [];
        }

        $flattened = [];

        $walker = static function( $items ) use ( &$walker, &$flattened ) {
                foreach ( $items as $key => $value ) {
                        if ( is_array( $value ) ) {
                                $walker( $value );
                                continue;
                        }

                        if ( is_string( $key ) && '' !== $key ) {
                                $flattened[] = $key;
                                continue;
                        }

                        if ( is_string( $value ) && '' !== $value ) {
                                $flattened[] = $value;
                        }
                }
        };

        $walker( $allowed[ $provider_key ] );

        return array_values( array_unique( $flattened ) );
}

function aimentor_map_presets_to_legacy_defaults( $presets ) {
        $mapped = [];

        if ( ! is_array( $presets ) ) {
                return $mapped;
        }

        foreach ( $presets as $provider => $tasks ) {
                $fallback = '';

                if ( isset( $tasks['content']['fast'] ) && is_string( $tasks['content']['fast'] ) ) {
                        $fallback = $tasks['content']['fast'];
                } else {
                        foreach ( $tasks as $task ) {
                                if ( ! is_array( $task ) ) {
                                        continue;
                                }

                                foreach ( $task as $model ) {
                                        if ( is_string( $model ) && '' !== $model ) {
                                                $fallback = $model;
                                                break 2;
                                        }
                                }
                        }
                }

                $mapped[ $provider ] = $fallback;
        }

        return $mapped;
}

function aimentor_get_model_labels() {
        return aimentor_get_allowed_provider_models();
}

function aimentor_get_provider_labels() {
        return [
                'grok'   => __( 'xAI Grok', 'aimentor' ),
                'anthropic' => __( 'Anthropic Claude', 'aimentor' ),
                'openai' => __( 'OpenAI', 'aimentor' ),
        ];
}

function aimentor_get_document_context_blueprint() {
        $blueprint = [
                'default'    => [
                        'key'   => 'default',
                        'label' => __( 'Default (all Elementor documents)', 'aimentor' ),
                ],
                'page_types' => [],
        ];

        if ( function_exists( 'get_post_types' ) ) {
                $post_types = get_post_types( [ 'show_ui' => true ], 'objects' );

                foreach ( $post_types as $post_type => $object ) {
                        if ( 'attachment' === $post_type ) {
                                continue;
                        }

                        $label = '';

                        if ( isset( $object->labels->singular_name ) && '' !== $object->labels->singular_name ) {
                                $label = (string) $object->labels->singular_name;
                        } elseif ( isset( $object->label ) && '' !== $object->label ) {
                                $label = (string) $object->label;
                        } else {
                                $label = ucfirst( (string) $post_type );
                        }

                        $blueprint['page_types'][ $post_type ] = [
                                'key'       => 'post_type:' . $post_type,
                                'label'     => $label,
                                'templates' => [],
                        ];
                }
        }

        if ( function_exists( 'wp_get_theme' ) ) {
                $theme = wp_get_theme();

                foreach ( array_keys( $blueprint['page_types'] ) as $post_type_key ) {
                        $templates = $theme->get_page_templates( null, $post_type_key );

                        if ( ! is_array( $templates ) ) {
                                continue;
                        }

                        foreach ( $templates as $template_file => $template_name ) {
                                if ( '' === $template_file ) {
                                        continue;
                                }

                                $label = '' !== $template_name ? $template_name : $template_file;

                                $blueprint['page_types'][ $post_type_key ]['templates'][ $template_file ] = [
                                        'key'   => 'template:' . $template_file,
                                        'label' => $label,
                                ];
                        }
                }
        }

        /**
         * Filter the document context blueprint used for defaults and UI rendering.
         *
         * @param array $blueprint Associative array describing defaults, post types, and templates.
         */
        $blueprint = apply_filters( 'aimentor_document_context_blueprint', $blueprint );

        if ( ! isset( $blueprint['page_types'] ) || ! is_array( $blueprint['page_types'] ) ) {
                $blueprint['page_types'] = [];
        }

        return $blueprint;
}

function aimentor_get_document_context_choices() {
        $blueprint = aimentor_get_document_context_blueprint();
        $choices   = [
                'default' => [
                        'label' => $blueprint['default']['label'] ?? __( 'Default (all Elementor documents)', 'aimentor' ),
                        'type'  => 'default',
                ],
        ];

        foreach ( $blueprint['page_types'] as $post_type => $meta ) {
                $label = isset( $meta['label'] ) ? $meta['label'] : ucfirst( (string) $post_type );

                $choices[ 'post_type:' . $post_type ] = [
                        /* translators: %s: Post type label. */
                        'label'     => sprintf( __( 'Post Type: %s', 'aimentor' ), $label ),
                        'type'      => 'post_type',
                        'post_type' => $post_type,
                ];

                if ( empty( $meta['templates'] ) || ! is_array( $meta['templates'] ) ) {
                        continue;
                }

                foreach ( $meta['templates'] as $template_file => $template_meta ) {
                        $template_label = is_array( $template_meta ) && isset( $template_meta['label'] )
                                ? $template_meta['label']
                                : ( is_string( $template_meta ) ? $template_meta : $template_file );

                        $choices[ 'template:' . $template_file ] = [
                                /* translators: 1: Template label, 2: Post type label. */
                                'label'    => sprintf( __( 'Template: %1$s (%2$s)', 'aimentor' ), $template_label, $label ),
                                'type'     => 'template',
                                'template' => $template_file,
                                'post_type' => $post_type,
                        ];
                }
        }

        /**
         * Filter the list of document contexts available for provider defaults.
         *
         * @param array $choices Associative array of context keys and metadata.
         * @param array $blueprint Document context blueprint used to generate the choices.
         */
        return apply_filters( 'aimentor_document_context_choices', $choices, $blueprint );
}

function aimentor_get_document_provider_default_map() {
        $legacy_defaults  = aimentor_map_presets_to_legacy_defaults( aimentor_get_provider_model_defaults() );
        $default_provider = 'grok';
        $default_model    = $legacy_defaults['grok'] ?? '';
        $blueprint        = aimentor_get_document_context_blueprint();

        $defaults = [
                'default'    => [
                        'provider' => $default_provider,
                        'model'    => $default_model,
                ],
                'page_types' => [],
        ];

        foreach ( $blueprint['page_types'] as $post_type => $meta ) {
                $defaults['page_types'][ $post_type ] = [
                        'provider'  => $default_provider,
                        'model'     => $default_model,
                        'templates' => [],
                ];

                if ( empty( $meta['templates'] ) || ! is_array( $meta['templates'] ) ) {
                        continue;
                }

                foreach ( $meta['templates'] as $template_file => $template_meta ) {
                        $defaults['page_types'][ $post_type ]['templates'][ $template_file ] = [
                                'provider' => $default_provider,
                                'model'    => $default_model,
                        ];
                }
        }

        if ( ! isset( $defaults['page_types']['__global__'] ) ) {
                $defaults['page_types']['__global__'] = [
                        'provider'  => $default_provider,
                        'model'     => $default_model,
                        'templates' => [],
                ];
        }

        /**
         * Filter the default provider/model map for document contexts.
         *
         * @param array $defaults Default mapping of contexts to provider/model pairs.
         * @param array $blueprint Document context blueprint describing post types and templates.
         */
        return apply_filters( 'aimentor_document_provider_default_map', $defaults, $blueprint );
}

function aimentor_get_prompt_preset_catalog() {
        return [
                'grok'   => [
                        'website_copy'     => [
                                'label'       => __( 'Website Copy', 'aimentor' ),
                                'description' => __( 'Persuasive positioning frameworks for high-converting site sections.', 'aimentor' ),
                                'presets'     => [
                                        'landing_page'      => [
                                                'label'       => __( 'Landing Page', 'aimentor' ),
                                                'description' => __( 'Craft conversion-focused hero, benefits, proof, and CTA copy for a marketing landing page.', 'aimentor' ),
                                                'prompt'      => __( 'You are an expert marketing copywriter. Produce detailed landing page copy with a headline, subheadline, value proposition bullets, social proof, and a compelling primary call to action. Highlight the unique benefits and key differentiators provided by the business.', 'aimentor' ),
                                                'task'        => 'content',
                                        ],
                                        'services_overview' => [
                                                'label'       => __( 'Services Overview', 'aimentor' ),
                                                'description' => __( 'Summarize core services with positioning, differentiators, and quick callouts for each offer.', 'aimentor' ),
                                                'prompt'      => __( 'You are a brand strategist. Write persuasive website copy that introduces the business and outlines 3-4 core services. For each service include a short positioning statement, who it helps, and one standout proof point. Conclude with a confident invitation to start a conversation.', 'aimentor' ),
                                                'task'        => 'content',
                                        ],
                                ],
                        ],
                        'content_strategy' => [
                                'label'       => __( 'Content Strategy', 'aimentor' ),
                                'description' => __( 'Editorial planning prompts that outline angles, talking points, and CTAs.', 'aimentor' ),
                                'presets'     => [
                                        'blog_brief' => [
                                                'label'       => __( 'Blog Post Brief', 'aimentor' ),
                                                'description' => __( 'Outline audience, search intent, talking points, and structure for a long-form article.', 'aimentor' ),
                                                'prompt'      => __( 'Act as an editorial strategist. Produce a blog brief that includes: target reader, search intent, working title options, a detailed outline with sections and bullet talking points, suggested keywords, and a closing CTA that drives the next step with the brand.', 'aimentor' ),
                                                'task'        => 'content',
                                        ],
                                ],
                        ],
                        'layout_frames'    => [
                                'label'       => __( 'Canvas Frames', 'aimentor' ),
                                'description' => __( 'Reusable layout prompts curated for Elementor canvas generation.', 'aimentor' ),
                                'presets'     => [
                                        'conversion_landing_frame' => [
                                                'label'       => __( 'Conversion Landing Page Frame', 'aimentor' ),
                                                'description' => __( 'High-impact hero, benefits, proof, and CTA scaffolding tuned for multi-provider reuse.', 'aimentor' ),
                                                'prompt'      => __( 'You are an award-winning UX copy architect. Produce Elementor canvas JSON for a conversion landing page with the following sections: sticky navigation, hero with bold headline and supporting media slot, credibility bar, benefits grid, solution explainer with side-by-side imagery, social proof carousel, pricing/plan comparison, FAQ accordion, and closing CTA banner. Emphasize flexible container spacing and responsive column groupings so editors can quickly swap copy and imagery without breaking the frame.', 'aimentor' ),
                                                'task'        => 'canvas',
                                                'type'        => 'frame',
                                                'sections'    => [
                                                        __( 'Hero & Credibility', 'aimentor' ),
                                                        __( 'Benefits Grid', 'aimentor' ),
                                                        __( 'Solution Explainer', 'aimentor' ),
                                                        __( 'Testimonials Carousel', 'aimentor' ),
                                                        __( 'Pricing Table', 'aimentor' ),
                                                        __( 'FAQ', 'aimentor' ),
                                                        __( 'Closing CTA', 'aimentor' ),
                                                ],
                                        ],
                                        'services_showcase_frame' => [
                                                'label'       => __( 'Services Showcase Frame', 'aimentor' ),
                                                'description' => __( 'Modular services overview designed for fast duplication across industries.', 'aimentor' ),
                                                'prompt'      => __( 'Act as a productized services strategist. Output Elementor canvas JSON that scaffolds a services overview page with: intro hero featuring optional background media, quick-hit differentiators strip, alternating service feature rows (image + copy) for three services, testimonial + proof row, process timeline with 4 steps, resource download/lead capture, and final CTA block. Use nested containers sparingly so editors can swap widgets without rebuilding structure.', 'aimentor' ),
                                                'task'        => 'canvas',
                                                'type'        => 'frame',
                                                'sections'    => [
                                                        __( 'Hero Intro', 'aimentor' ),
                                                        __( 'Differentiators Strip', 'aimentor' ),
                                                        __( 'Service Feature Rows', 'aimentor' ),
                                                        __( 'Testimonial Highlight', 'aimentor' ),
                                                        __( 'Process Timeline', 'aimentor' ),
                                                        __( 'Lead Capture', 'aimentor' ),
                                                        __( 'CTA Banner', 'aimentor' ),
                                                ],
                                        ],
                                ],
                        ],
                ],
                'openai' => [
                        'website_copy'        => [
                                'label'       => __( 'Website Copy', 'aimentor' ),
                                'description' => __( 'Empathetic messaging systems for product and service pages.', 'aimentor' ),
                                'presets'     => [
                                        'landing_page' => [
                                                'label'       => __( 'Landing Page', 'aimentor' ),
                                                'description' => __( 'Generate empathetic, benefits-first copy for a product or service landing page.', 'aimentor' ),
                                                'prompt'      => __( 'You are a conversion copywriter. Deliver landing page copy that opens with an emotional hook, expands on key benefits, addresses objections with reassurance, and ends with a strong CTA. Include short headings for each section.', 'aimentor' ),
                                                'task'        => 'content',
                                        ],
                                ],
                        ],
                        'lifecycle_marketing' => [
                                'label'       => __( 'Lifecycle Marketing', 'aimentor' ),
                                'description' => __( 'Sequenced messaging that nurtures leads toward conversion.', 'aimentor' ),
                                'presets'     => [
                                        'email_sequence' => [
                                                'label'       => __( 'Email Sequence', 'aimentor' ),
                                                'description' => __( 'Plan a three-part nurture sequence with subject lines, preview text, and CTA ideas.', 'aimentor' ),
                                                'prompt'      => __( 'You are an email strategist. Draft a three-message nurture sequence. For each email include a subject line, preview text, main talking points, and a clear CTA that moves the reader toward a discovery call or purchase.', 'aimentor' ),
                                                'task'        => 'content',
                                        ],
                                ],
                        ],
                        'content_strategy'    => [
                                'label'       => __( 'Content Strategy', 'aimentor' ),
                                'description' => __( 'Research-backed outlines and keyword guidance for long-form content.', 'aimentor' ),
                                'presets'     => [
                                        'blog_brief' => [
                                                'label'       => __( 'Blog Post Brief', 'aimentor' ),
                                                'description' => __( 'Provide research-backed talking points and outline guidance for an in-depth blog article.', 'aimentor' ),
                                                'prompt'      => __( 'You are an SEO and content strategist. Create a blog post brief with primary keyword focus, search intent, target reader persona, a comprehensive outline with headings and bullet talking points, and recommended resources or statistics to cite.', 'aimentor' ),
                                                'task'        => 'content',
                                        ],
                                ],
                        ],
                        'layout_frames'       => [
                                'label'       => __( 'AI Frames', 'aimentor' ),
                                'description' => __( 'Curated layout scaffolds optimized for OpenAI canvas output.', 'aimentor' ),
                                'presets'     => [
                                        'thought_leadership_frame' => [
                                                'label'       => __( 'Thought Leadership Frame', 'aimentor' ),
                                                'description' => __( 'Editorial hub layout ideal for webinars, articles, and expert positioning.', 'aimentor' ),
                                                'prompt'      => __( 'You are an editorial experience designer. Return Elementor canvas JSON for a thought leadership hub with: marquee hero featuring video slot and key takeaways, dual-column featured content area (article plus podcast/webinar), resource library grid with category filters, quote spotlight, upcoming events timeline, newsletter signup, and final CTA linking to consultation. Keep column widths balanced for desktop while stacking elegantly on mobile.', 'aimentor' ),
                                                'task'        => 'canvas',
                                                'type'        => 'frame',
                                                'sections'    => [
                                                        __( 'Hero with Media', 'aimentor' ),
                                                        __( 'Featured Insights', 'aimentor' ),
                                                        __( 'Resource Library', 'aimentor' ),
                                                        __( 'Quote Spotlight', 'aimentor' ),
                                                        __( 'Events Timeline', 'aimentor' ),
                                                        __( 'Newsletter Signup', 'aimentor' ),
                                                        __( 'Consultation CTA', 'aimentor' ),
                                                ],
                                        ],
                                        'course_sales_frame' => [
                                                'label'       => __( 'Course Sales Frame', 'aimentor' ),
                                                'description' => __( 'High-converting education layout with curriculum and instructor storytelling.', 'aimentor' ),
                                                'prompt'      => __( 'Act as a course marketing producer. Build Elementor canvas JSON for a digital course sales page with: hero featuring enrollment CTA, credibility strip, course outcomes grid, instructor story block with image, curriculum module accordion, bonus and inclusion highlights, testimonial slider, pricing stack with guarantee, FAQ, and final CTA. Prioritize reusable containers that accept Elementor native widgets.', 'aimentor' ),
                                                'task'        => 'canvas',
                                                'type'        => 'frame',
                                                'sections'    => [
                                                        __( 'Hero & Enrollment CTA', 'aimentor' ),
                                                        __( 'Credibility Strip', 'aimentor' ),
                                                        __( 'Outcomes Grid', 'aimentor' ),
                                                        __( 'Instructor Story', 'aimentor' ),
                                                        __( 'Curriculum Accordion', 'aimentor' ),
                                                        __( 'Bonuses Highlight', 'aimentor' ),
                                                        __( 'Testimonials', 'aimentor' ),
                                                        __( 'Pricing & Guarantee', 'aimentor' ),
                                                        __( 'FAQ', 'aimentor' ),
                                                        __( 'Final CTA', 'aimentor' ),
                                                ],
                                        ],
                                ],
                        ],
                ],
        ];
}

function aimentor_get_frame_prompt_presets() {
        $catalog = aimentor_get_prompt_preset_catalog();
        $frames  = [];

        foreach ( $catalog as $provider_key => $categories ) {
                if ( ! is_array( $categories ) ) {
                        continue;
                }

                foreach ( $categories as $category_key => $category_meta ) {
                        if ( ! isset( $category_meta['presets'] ) || ! is_array( $category_meta['presets'] ) ) {
                                continue;
                        }

                        foreach ( $category_meta['presets'] as $preset_key => $preset_meta ) {
                                if ( ! is_array( $preset_meta ) ) {
                                        continue;
                                }

                                if ( isset( $preset_meta['type'] ) && 'frame' === $preset_meta['type'] ) {
                                        $frames[ $provider_key . '::' . $category_key . '::' . $preset_key ] = array_merge(
                                                $preset_meta,
                                                [
                                                        'provider' => $provider_key,
                                                        'category' => $category_key,
                                                        'key'      => $preset_key,
                                                ]
                                        );
                                }
                        }
                }
        }

        return $frames;
}

function aimentor_get_usage_transient_key() {
        return 'aimentor_provider_usage_snapshot';
}

function aimentor_get_usage_defaults() {
        $providers = array_keys( aimentor_get_provider_labels() );
        $defaults  = [
                'providers'   => [],
                'generated_at' => current_time( 'timestamp' ),
        ];

        foreach ( $providers as $provider ) {
                $defaults['providers'][ $provider ] = [
                        'success_count' => 0,
                        'error_count'   => 0,
                        'last_success'  => 0,
                        'last_error'    => 0,
                        'last_event'    => 0,
                        'last_model'    => '',
                        'last_task'     => '',
                        'last_tier'     => '',
                        'last_origin'   => '',
                ];
        }

        return $defaults;
}

function aimentor_get_provider_usage_data() {
        $transient_key = aimentor_get_usage_transient_key();
        $stored        = get_transient( $transient_key );

        if ( ! is_array( $stored ) ) {
                $stored = aimentor_get_usage_defaults();
        }

        $defaults = aimentor_get_usage_defaults();

        foreach ( $defaults['providers'] as $provider => $template ) {
                if ( ! isset( $stored['providers'][ $provider ] ) || ! is_array( $stored['providers'][ $provider ] ) ) {
                        $stored['providers'][ $provider ] = $template;
                        continue;
                }

                $stored['providers'][ $provider ] = array_merge( $template, $stored['providers'][ $provider ] );
        }

        if ( ! isset( $stored['generated_at'] ) ) {
                $stored['generated_at'] = current_time( 'timestamp' );
        }

        set_transient( $transient_key, $stored, DAY_IN_SECONDS );

        return $stored;
}

function aimentor_record_provider_usage( $provider_key, $status, $context = [] ) {
$provider_key = sanitize_key( $provider_key );
$status       = in_array( $status, [ 'success', 'error' ], true ) ? $status : 'success';
$providers    = aimentor_get_provider_labels();

        if ( ! array_key_exists( $provider_key, $providers ) ) {
                return;
        }

        $data      = aimentor_get_provider_usage_data();
        $timestamp = current_time( 'timestamp' );

        if ( ! isset( $data['providers'][ $provider_key ] ) ) {
                $data['providers'][ $provider_key ] = aimentor_get_usage_defaults()['providers'][ $provider_key ];
        }

        if ( 'success' === $status ) {
                $data['providers'][ $provider_key ]['success_count'] = absint( $data['providers'][ $provider_key ]['success_count'] ) + 1;
                $data['providers'][ $provider_key ]['last_success']  = $timestamp;
        } else {
                $data['providers'][ $provider_key ]['error_count'] = absint( $data['providers'][ $provider_key ]['error_count'] ) + 1;
                $data['providers'][ $provider_key ]['last_error']  = $timestamp;
        }

        $context = is_array( $context ) ? $context : [];

        if ( isset( $context['model'] ) ) {
                $data['providers'][ $provider_key ]['last_model'] = sanitize_text_field( $context['model'] );
        }

        if ( isset( $context['task'] ) ) {
                $data['providers'][ $provider_key ]['last_task'] = sanitize_key( $context['task'] );
        }

        if ( isset( $context['tier'] ) ) {
                $data['providers'][ $provider_key ]['last_tier'] = sanitize_key( $context['tier'] );
        }

        if ( isset( $context['origin'] ) ) {
                $allowed_origins = [ 'generation', 'test' ];
                $origin          = sanitize_key( $context['origin'] );
                $data['providers'][ $provider_key ]['last_origin'] = in_array( $origin, $allowed_origins, true ) ? $origin : '';
        }

        $data['providers'][ $provider_key ]['last_event'] = $timestamp;
        $data['generated_at']                              = $timestamp;

set_transient( aimentor_get_usage_transient_key(), $data, DAY_IN_SECONDS );
}


function aimentor_get_saved_prompts_option_name() {
        return 'aimentor_saved_prompts';
}

function aimentor_get_saved_prompts_user_meta_key() {
        return 'aimentor_saved_prompts';
}

function aimentor_normalize_saved_prompt_scope( $scope ) {
        $normalized = sanitize_key( (string) $scope );

        return 'global' === $normalized ? 'global' : 'user';
}

function aimentor_generate_saved_prompt_label( $label, $prompt ) {
	$label  = sanitize_text_field( (string) $label );
	$prompt = sanitize_textarea_field( (string) $prompt );

	if ( '' !== $label ) {
		return $label;
	}

	$excerpt = trim( wp_html_excerpt( $prompt, 60, '…' ) );

	if ( '' === $excerpt ) {
		$excerpt = __( 'Untitled prompt', 'aimentor' );
	}

	return sanitize_text_field( $excerpt );
}

function aimentor_normalize_saved_prompt_entry( $entry, $scope = 'user' ) {
	if ( ! is_array( $entry ) ) {
		return null;
	}

	$scope  = 'global' === $scope ? 'global' : 'user';
	$id     = isset( $entry['id'] ) ? sanitize_text_field( wp_unslash( $entry['id'] ) ) : '';
	$prompt = isset( $entry['prompt'] ) ? sanitize_textarea_field( wp_unslash( $entry['prompt'] ) ) : '';
	$label  = isset( $entry['label'] ) ? sanitize_text_field( wp_unslash( $entry['label'] ) ) : '';

	$prompt = trim( $prompt );

	if ( '' === $prompt ) {
		return null;
	}

	if ( '' === $id ) {
		return null;
	}

	$label = aimentor_generate_saved_prompt_label( $label, $prompt );

	return [
		'id'     => $id,
		'label'  => $label,
		'prompt' => $prompt,
		'scope'  => $scope,
	];
}

function aimentor_get_saved_prompts_raw( $scope = 'global', $user_id = 0 ) {
        $scope = aimentor_normalize_saved_prompt_scope( $scope );

	if ( 'global' === $scope ) {
		$prompts = get_option( aimentor_get_saved_prompts_option_name(), [] );
	} else {
		$user_id = $user_id ? absint( $user_id ) : get_current_user_id();

		if ( ! $user_id ) {
			return [];
		}

		$prompts = get_user_meta( $user_id, aimentor_get_saved_prompts_user_meta_key(), true );
	}

	return is_array( $prompts ) ? $prompts : [];
}

function aimentor_store_saved_prompts_raw( $scope, $prompts, $user_id = 0 ) {
        $scope   = aimentor_normalize_saved_prompt_scope( $scope );
	$prompts = is_array( $prompts ) ? array_values( $prompts ) : [];

	if ( 'global' === $scope ) {
		update_option( aimentor_get_saved_prompts_option_name(), $prompts, false );
		return;
	}

	$user_id = $user_id ? absint( $user_id ) : get_current_user_id();

	if ( ! $user_id ) {
		return;
	}

	update_user_meta( $user_id, aimentor_get_saved_prompts_user_meta_key(), $prompts );
}

function aimentor_get_saved_prompts_by_scope( $scope = 'global', $user_id = 0 ) {
        $scope    = aimentor_normalize_saved_prompt_scope( $scope );
	$prompts  = aimentor_get_saved_prompts_raw( $scope, $user_id );
	$prepared = [];

	foreach ( $prompts as $entry ) {
		$normalized = aimentor_normalize_saved_prompt_entry( $entry, $scope );

		if ( ! $normalized ) {
			continue;
		}

		$prepared[] = $normalized;
	}

	return $prepared;
}

function aimentor_get_saved_prompts_payload( $user_id = 0 ) {
	return [
		'global' => aimentor_get_saved_prompts_by_scope( 'global' ),
		'user'   => aimentor_get_saved_prompts_by_scope( 'user', $user_id ),
	];
}

function aimentor_add_saved_prompt( $label, $prompt, $scope = 'user', $user_id = 0 ) {
        if ( ! current_user_can( 'edit_posts' ) ) {
		return new WP_Error(
		'aimentor_saved_prompts_forbidden',
		__( 'Sorry, you are not allowed to manage saved prompts.', 'aimentor' ),
		[ 'status' => 403 ]
		);
	}

        $scope  = aimentor_normalize_saved_prompt_scope( $scope );
	$prompt = sanitize_textarea_field( (string) $prompt );

	if ( '' === trim( $prompt ) ) {
		return new WP_Error(
		'aimentor_saved_prompts_empty',
		__( 'Prompt content cannot be empty.', 'aimentor' ),
		[ 'status' => 400 ]
		);
	}

	if ( 'global' === $scope && ! current_user_can( 'manage_options' ) ) {
		return new WP_Error(
		'aimentor_saved_prompts_global_forbidden',
		__( 'Sorry, you are not allowed to manage global prompts.', 'aimentor' ),
		[ 'status' => 403 ]
		);
	}

	$label = aimentor_generate_saved_prompt_label( $label, $prompt );

	$entry = [
		'id'     => wp_generate_uuid4(),
		'label'  => $label,
		'prompt' => $prompt,
	];

	if ( 'global' === $scope ) {
		$prompts   = aimentor_get_saved_prompts_raw( 'global' );
		$prompts[] = $entry;
		aimentor_store_saved_prompts_raw( 'global', $prompts );
	} else {
		$user_id = $user_id ? absint( $user_id ) : get_current_user_id();

		if ( ! $user_id ) {
		return new WP_Error(
		'aimentor_saved_prompts_invalid_user',
		__( 'Unable to determine the current user.', 'aimentor' ),
		[ 'status' => 400 ]
		);
		}

		$prompts   = aimentor_get_saved_prompts_raw( 'user', $user_id );
		$prompts[] = $entry;
		aimentor_store_saved_prompts_raw( 'user', $prompts, $user_id );
	}

	$entry['scope'] = $scope;

	return $entry;
}

function aimentor_delete_saved_prompt( $id, $scope = 'user', $user_id = 0 ) {
        if ( ! current_user_can( 'edit_posts' ) ) {
		return new WP_Error(
		'aimentor_saved_prompts_forbidden',
		__( 'Sorry, you are not allowed to manage saved prompts.', 'aimentor' ),
		[ 'status' => 403 ]
		);
	}

        $scope = aimentor_normalize_saved_prompt_scope( $scope );
	$id    = sanitize_text_field( (string) $id );

	if ( '' === $id ) {
		return new WP_Error(
		'aimentor_saved_prompts_invalid_id',
		__( 'Prompt ID is required.', 'aimentor' ),
		[ 'status' => 400 ]
		);
	}

	if ( 'global' === $scope && ! current_user_can( 'manage_options' ) ) {
		return new WP_Error(
		'aimentor_saved_prompts_global_forbidden',
		__( 'Sorry, you are not allowed to manage global prompts.', 'aimentor' ),
		[ 'status' => 403 ]
		);
	}

	$prompts   = aimentor_get_saved_prompts_raw( $scope, $user_id );
	$remaining = [];
	$removed   = null;

	foreach ( $prompts as $entry ) {
		$normalized = aimentor_normalize_saved_prompt_entry( $entry, $scope );

		if ( ! $normalized ) {
			continue;
		}

		if ( $normalized['id'] === $id ) {
		$removed = $normalized;
		continue;
		}

		$remaining[] = [
		'id'     => $normalized['id'],
		'label'  => $normalized['label'],
		'prompt' => $normalized['prompt'],
		];
	}

	if ( ! $removed ) {
		return new WP_Error(
		'aimentor_saved_prompts_not_found',
		__( 'Saved prompt not found.', 'aimentor' ),
		[ 'status' => 404 ]
		);
	}

	aimentor_store_saved_prompts_raw( $scope, $remaining, $user_id );

	return $removed;
}

function aimentor_saved_prompts_permissions_check( WP_REST_Request $request ) {
	return current_user_can( 'edit_posts' );
}

function aimentor_rest_get_saved_prompts( WP_REST_Request $request ) {
	$prompts = aimentor_get_saved_prompts_payload();

        return new WP_REST_Response(
                [
                        'success' => true,
                        'prompts' => $prompts,
                ],
                200
        );
}

function aimentor_rest_create_saved_prompt( WP_REST_Request $request ) {
        $label  = $request->get_param( 'label' );
        $scope  = aimentor_normalize_saved_prompt_scope( $request->get_param( 'scope' ) );
        $prompt = $request->get_param( 'prompt' );

        $result = aimentor_add_saved_prompt( $label, $prompt, $scope );

	if ( is_wp_error( $result ) ) {
		return $result;
	}

        return new WP_REST_Response(
                [
                        'success' => true,
                        'prompt'  => $result,
                        'prompts' => aimentor_get_saved_prompts_payload(),
                ],
                201
        );
}

function aimentor_rest_delete_saved_prompt( WP_REST_Request $request ) {
        $id    = $request->get_param( 'id' );
        $scope = aimentor_normalize_saved_prompt_scope( $request->get_param( 'scope' ) );

        $result = aimentor_delete_saved_prompt( $id, $scope );

	if ( is_wp_error( $result ) ) {
		return $result;
	}

        return new WP_REST_Response(
                [
                        'success' => true,
                        'prompt'  => $result,
                        'prompts' => aimentor_get_saved_prompts_payload(),
                ],
                200
        );
}


function aimentor_register_saved_prompts_routes() {
	register_rest_route(
		'aimentor/v1',
		'/prompts',
		[
			[
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => 'aimentor_rest_get_saved_prompts',
				'permission_callback' => 'aimentor_saved_prompts_permissions_check',
			],
			[
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => 'aimentor_rest_create_saved_prompt',
				'permission_callback' => 'aimentor_saved_prompts_permissions_check',
				'args'                => [
					'label'  => [
						'type'              => 'string',
						'required'          => false,
						'sanitize_callback' => 'sanitize_text_field',
					],
					'prompt' => [
						'type'              => 'string',
						'required'          => true,
						'sanitize_callback' => 'sanitize_textarea_field',
					],
                                        'scope'  => [
                                                'type'              => 'string',
                                                'required'          => false,
                                                'sanitize_callback' => 'sanitize_key',
                                                'default'           => 'user',
                                                'enum'              => [ 'user', 'global' ],
                                        ],
                                ],
			],
		]
	);

	register_rest_route(
		'aimentor/v1',
		'/prompts/(?P<id>[A-Za-z0-9\-]+)',
		[
			[
				'methods'             => WP_REST_Server::DELETABLE,
				'callback'            => 'aimentor_rest_delete_saved_prompt',
				'permission_callback' => 'aimentor_saved_prompts_permissions_check',
				'args'                => [
					'id'    => [
						'type'     => 'string',
						'required' => true,
					],
                                        'scope' => [
                                                'type'              => 'string',
                                                'required'          => false,
                                                'sanitize_callback' => 'sanitize_key',
                                                'default'           => 'user',
                                                'enum'              => [ 'user', 'global' ],
                                        ],
                                ],
			],
		]
	);
}
add_action( 'rest_api_init', 'aimentor_register_saved_prompts_routes' );

function aimentor_ajax_add_saved_prompt() {
        check_ajax_referer( 'aimentor_saved_prompts', 'nonce' );

        $label  = isset( $_POST['label'] ) ? wp_unslash( $_POST['label'] ) : '';
        $scope  = isset( $_POST['scope'] ) ? wp_unslash( $_POST['scope'] ) : 'user';
        $scope  = aimentor_normalize_saved_prompt_scope( $scope );
        $prompt = isset( $_POST['prompt'] ) ? wp_unslash( $_POST['prompt'] ) : '';

        $result = aimentor_add_saved_prompt( $label, $prompt, $scope );

	if ( is_wp_error( $result ) ) {
		$error_data = $result->get_error_data();
		$status     = isset( $error_data['status'] ) ? absint( $error_data['status'] ) : 400;

                wp_send_json_error(
                        [
                                'code'    => $result->get_error_code(),
                                'message' => $result->get_error_message(),
                        ],
                        $status
                );
	}

        wp_send_json_success(
                [
                        'prompt'  => $result,
                        'prompts' => aimentor_get_saved_prompts_payload(),
                ]
        );
}
add_action( 'wp_ajax_aimentor_add_saved_prompt', 'aimentor_ajax_add_saved_prompt' );

function aimentor_ajax_delete_saved_prompt() {
        check_ajax_referer( 'aimentor_saved_prompts', 'nonce' );

        $scope = isset( $_POST['scope'] ) ? wp_unslash( $_POST['scope'] ) : 'user';
        $scope = aimentor_normalize_saved_prompt_scope( $scope );
        $id    = isset( $_POST['id'] ) ? wp_unslash( $_POST['id'] ) : '';

        $result = aimentor_delete_saved_prompt( $id, $scope );

	if ( is_wp_error( $result ) ) {
		$error_data = $result->get_error_data();
		$status     = isset( $error_data['status'] ) ? absint( $error_data['status'] ) : 400;

                wp_send_json_error(
                        [
                                'code'    => $result->get_error_code(),
                                'message' => $result->get_error_message(),
                        ],
                        $status
                );
	}

        wp_send_json_success(
                [
                        'prompt'  => $result,
                        'prompts' => aimentor_get_saved_prompts_payload(),
                ]
        );
}
add_action( 'wp_ajax_aimentor_delete_saved_prompt', 'aimentor_ajax_delete_saved_prompt' );


function aimentor_get_generation_history_option_name() {
        return 'aimentor_generation_history';
}

function aimentor_get_generation_history_max_items() {
        $max_items = apply_filters( 'aimentor_generation_history_max_items', 10 );

        return max( 1, absint( $max_items ) );
}

function aimentor_get_generation_history() {
        $option_name = aimentor_get_generation_history_option_name();
        $history     = get_option( $option_name, [] );

        if ( ! is_array( $history ) ) {
                return [];
        }

        $normalized = [];

        foreach ( $history as $entry ) {
                if ( ! is_array( $entry ) ) {
                        continue;
                }

                $prompt    = isset( $entry['prompt'] ) ? (string) $entry['prompt'] : '';
                $provider  = isset( $entry['provider'] ) ? sanitize_key( $entry['provider'] ) : '';
                $timestamp = isset( $entry['timestamp'] ) ? absint( $entry['timestamp'] ) : 0;

                if ( '' === $prompt || '' === $provider || 0 === $timestamp ) {
                        continue;
                }

                $normalized[] = [
                        'prompt'    => $prompt,
                        'provider'  => $provider,
                        'timestamp' => $timestamp,
                ];
        }

        return $normalized;
}

function aimentor_store_generation_history_entry( $prompt, $provider ) {
        $prompt   = sanitize_textarea_field( (string) $prompt );
        $provider = sanitize_key( $provider );

        if ( '' === $prompt ) {
                return new WP_Error(
                        'aimentor_history_invalid_prompt',
                        __( 'Prompt cannot be empty.', 'aimentor' ),
                        [ 'status' => 400 ]
                );
        }

        $provider_meta = aimentor_get_provider_meta_map();

        if ( ! array_key_exists( $provider, $provider_meta ) ) {
                return new WP_Error(
                        'aimentor_history_invalid_provider',
                        __( 'Invalid provider.', 'aimentor' ),
                        [ 'status' => 400 ]
                );
        }

        $entry = [
                'prompt'    => $prompt,
                'provider'  => $provider,
                'timestamp' => current_time( 'timestamp' ),
        ];

        $history   = aimentor_get_generation_history();
        array_unshift( $history, $entry );

        $max_items = aimentor_get_generation_history_max_items();

        if ( count( $history ) > $max_items ) {
                $history = array_slice( $history, 0, $max_items );
        }

        update_option( aimentor_get_generation_history_option_name(), $history, false );

        return $entry;
}

function aimentor_get_canvas_history_option_name() {
        return 'aimentor_canvas_history';
}

function aimentor_get_canvas_history_max_items() {
        $max_items = apply_filters( 'aimentor_canvas_history_max_items', 6 );

        return max( 1, absint( $max_items ) );
}

function aimentor_normalize_canvas_history_entry( $entry ) {
        if ( ! is_array( $entry ) ) {
                return null;
        }

        $id        = isset( $entry['id'] ) ? sanitize_text_field( $entry['id'] ) : '';
        $summary   = isset( $entry['summary'] ) ? sanitize_text_field( $entry['summary'] ) : '';
        $provider  = isset( $entry['provider'] ) ? sanitize_key( $entry['provider'] ) : '';
        $model     = isset( $entry['model'] ) ? sanitize_text_field( $entry['model'] ) : '';
        $task      = isset( $entry['task'] ) ? sanitize_key( $entry['task'] ) : 'canvas';
        $tier      = isset( $entry['tier'] ) ? sanitize_key( $entry['tier'] ) : '';
        $timestamp = isset( $entry['timestamp'] ) ? absint( $entry['timestamp'] ) : 0;
        $layout    = $entry['layout'] ?? '';

        if ( '' === $id ) {
                return null;
        }

        if ( '' === $provider ) {
                $provider = 'grok';
        }

        if ( ! in_array( $task, [ 'canvas', 'content' ], true ) ) {
                $task = 'canvas';
        }

        if ( '' !== $tier && ! in_array( $tier, [ 'fast', 'quality' ], true ) ) {
                $tier = '';
        }

        if ( is_array( $layout ) ) {
                $layout = wp_json_encode( $layout );
        }

        if ( ! is_string( $layout ) || '' === $layout ) {
                return null;
        }

        $layout = wp_check_invalid_utf8( $layout );

        $decoded_layout = json_decode( $layout, true );

        if ( ! is_array( $decoded_layout ) ) {
                return null;
        }

        $max_summary_length = 180;

        if ( strlen( $summary ) > $max_summary_length ) {
                $summary = rtrim( wp_html_excerpt( $summary, $max_summary_length - 1, '' ) ) . '…';
        }

        if ( ! $timestamp ) {
                $timestamp = current_time( 'timestamp' );
        }

        return [
                'id'        => $id,
                'summary'   => $summary,
                'provider'  => $provider,
                'model'     => $model,
                'task'      => $task,
                'tier'      => $tier,
                'timestamp' => $timestamp,
                'layout'    => wp_json_encode( $decoded_layout ),
        ];
}

function aimentor_get_canvas_history() {
        $history = get_option( aimentor_get_canvas_history_option_name(), [] );

        if ( ! is_array( $history ) ) {
                return [];
        }

        $normalized = [];

        foreach ( $history as $entry ) {
                $normalized_entry = aimentor_normalize_canvas_history_entry( $entry );

                if ( ! $normalized_entry ) {
                        continue;
                }

                $normalized[] = $normalized_entry;
        }

        return $normalized;
}

function aimentor_get_frame_sections( $post_id ) {
        $raw = get_post_meta( $post_id, '_aimentor_frame_sections', true );

        if ( is_string( $raw ) && '' !== $raw ) {
                $decoded = json_decode( $raw, true );

                if ( is_array( $decoded ) ) {
                        return array_values( array_filter( array_map( 'sanitize_text_field', $decoded ) ) );
                }
        }

        if ( is_array( $raw ) ) {
                return array_values( array_filter( array_map( 'sanitize_text_field', $raw ) ) );
        }

        if ( is_string( $raw ) && '' === $raw ) {
                return [];
        }

        return [];
}

function aimentor_prepare_frame_library_item( $post ) {
        $post = get_post( $post );

        if ( ! $post || 'ai_layout' !== $post->post_type ) {
                return null;
        }

        $is_enabled = get_post_meta( $post->ID, '_aimentor_frame_enabled', true );

        if ( 'yes' !== $is_enabled ) {
                return null;
        }

        $layout_raw = $post->post_content;
        $layout     = [];

        if ( is_string( $layout_raw ) && '' !== $layout_raw ) {
                $decoded = json_decode( $layout_raw, true );

                if ( is_array( $decoded ) ) {
                        $layout = $decoded;
                }
        }

        $layout_json = $layout ? wp_json_encode( $layout ) : '';

        $summary_meta = get_post_meta( $post->ID, '_aimentor_frame_summary', true );
        $summary      = $summary_meta ? sanitize_textarea_field( $summary_meta ) : sanitize_text_field( $post->post_excerpt );
        $provider     = sanitize_key( get_post_meta( $post->ID, '_aimentor_provider', true ) );
        $model        = sanitize_text_field( get_post_meta( $post->ID, '_aimentor_model', true ) );
        $prompt       = sanitize_textarea_field( get_post_meta( $post->ID, '_aimentor_prompt', true ) );
        $task         = sanitize_key( get_post_meta( $post->ID, '_aimentor_task', true ) );
        $tier         = sanitize_key( get_post_meta( $post->ID, '_aimentor_tier', true ) );
        $sections     = aimentor_get_frame_sections( $post->ID );
        $preview_id   = absint( get_post_meta( $post->ID, '_aimentor_frame_preview_id', true ) );

        if ( ! $preview_id ) {
                $preview_id = absint( get_post_thumbnail_id( $post->ID ) );
        }

        $preview_url = $preview_id ? wp_get_attachment_image_url( $preview_id, 'medium' ) : '';

        return [
                'id'        => $post->ID,
                'title'     => get_the_title( $post ),
                'summary'   => $summary,
                'provider'  => $provider,
                'model'     => $model,
                'prompt'    => $prompt,
                'task'      => $task,
                'tier'      => $tier,
                'layout'    => $layout_json,
                'sections'  => $sections,
                'preview'   => $preview_url ? [
                        'id'  => $preview_id,
                        'url' => esc_url_raw( $preview_url ),
                ] : null,
                'modified'  => mysql2date( 'c', $post->post_modified_gmt, false ),
        ];
}

function aimentor_get_frame_library_items( $args = [] ) {
        if ( ! post_type_exists( 'ai_layout' ) ) {
                return [];
        }

        $defaults = [
                'post_type'      => 'ai_layout',
                'post_status'    => [ 'publish', 'draft', 'pending', 'private' ],
                'posts_per_page' => 50,
                'orderby'        => 'modified',
                'order'          => 'DESC',
                'meta_key'       => '_aimentor_frame_enabled',
                'meta_value'     => 'yes',
        ];

        $query_args = wp_parse_args( $args, $defaults );
        $query_args['no_found_rows'] = true;

        $posts = get_posts( $query_args );

        if ( empty( $posts ) ) {
                return [];
        }

        $items = [];

        foreach ( $posts as $post ) {
                $prepared = aimentor_prepare_frame_library_item( $post );

                if ( ! $prepared ) {
                        continue;
                }

                $items[] = $prepared;
        }

        return $items;
}

function aimentor_get_frame_library_candidates( $args = [] ) {
        if ( ! post_type_exists( 'ai_layout' ) ) {
                return [];
        }

        $defaults = [
                'post_type'      => 'ai_layout',
                'post_status'    => [ 'publish', 'draft', 'pending', 'private' ],
                'posts_per_page' => 20,
                'orderby'        => 'modified',
                'order'          => 'DESC',
        ];

        $query_args = wp_parse_args( $args, $defaults );
        $query_args['no_found_rows'] = true;

        return get_posts( $query_args );
}

function aimentor_frame_library_permissions_check( WP_REST_Request $request ) {
        return current_user_can( 'edit_posts' );
}

function aimentor_rest_get_frames( WP_REST_Request $request ) {
        $limit = absint( $request->get_param( 'per_page' ) );

        if ( $limit <= 0 || $limit > 100 ) {
                $limit = 50;
        }

        $items = aimentor_get_frame_library_items(
                [
                        'posts_per_page' => $limit,
                ]
        );

        return new WP_REST_Response(
                [
                        'items' => $items,
                ]
        );
}

function aimentor_register_frame_library_route() {
        register_rest_route(
                'aimentor/v1',
                '/frames',
                [
                        [
                                'methods'             => WP_REST_Server::READABLE,
                                'callback'            => 'aimentor_rest_get_frames',
                                'permission_callback' => 'aimentor_frame_library_permissions_check',
                                'args'                => [
                                        'per_page' => [
                                                'type'              => 'integer',
                                                'required'          => false,
                                                'sanitize_callback' => 'absint',
                                                'default'           => 50,
                                        ],
                                ],
                        ],
                ]
        );
}
add_action( 'rest_api_init', 'aimentor_register_frame_library_route' );

function aimentor_store_canvas_history_entry( $layout, $meta ) {
        if ( is_string( $layout ) ) {
                $layout = wp_unslash( $layout );
        }

        if ( '' === $layout ) {
                return new WP_Error(
                        'aimentor_canvas_history_invalid_layout',
                        __( 'Canvas layout payload cannot be empty.', 'aimentor' ),
                        [ 'status' => 400 ]
                );
        }

        if ( is_string( $layout ) ) {
                $decoded = json_decode( $layout, true );
        } else {
                $decoded = $layout;
        }

        if ( ! is_array( $decoded ) ) {
                return new WP_Error(
                        'aimentor_canvas_history_invalid_json',
                        __( 'Canvas layout payload must be valid JSON.', 'aimentor' ),
                        [ 'status' => 400 ]
                );
        }

        $summary  = isset( $meta['summary'] ) ? sanitize_text_field( $meta['summary'] ) : '';
        $provider = isset( $meta['provider'] ) ? sanitize_key( $meta['provider'] ) : '';
        $model    = isset( $meta['model'] ) ? sanitize_text_field( $meta['model'] ) : '';
        $task     = isset( $meta['task'] ) ? sanitize_key( $meta['task'] ) : 'canvas';
        $tier     = isset( $meta['tier'] ) ? sanitize_key( $meta['tier'] ) : '';

        if ( '' === $provider ) {
                $provider = 'grok';
        }

        if ( ! in_array( $task, [ 'canvas', 'content' ], true ) ) {
                $task = 'canvas';
        }

        if ( '' !== $tier && ! in_array( $tier, [ 'fast', 'quality' ], true ) ) {
                $tier = '';
        }

        $summary = trim( $summary );

        $max_summary_length = 180;

        if ( strlen( $summary ) > $max_summary_length ) {
                $summary = rtrim( wp_html_excerpt( $summary, $max_summary_length - 1, '' ) ) . '…';
        }

        $entry = [
                'id'        => wp_generate_uuid4(),
                'summary'   => $summary,
                'provider'  => $provider,
                'model'     => $model,
                'task'      => $task,
                'tier'      => $tier,
                'timestamp' => current_time( 'timestamp' ),
                'layout'    => $decoded,
        ];

        $normalized_entry = aimentor_normalize_canvas_history_entry( $entry );

        if ( ! $normalized_entry ) {
                return new WP_Error(
                        'aimentor_canvas_history_normalization_failed',
                        __( 'Unable to normalize the canvas entry.', 'aimentor' ),
                        [ 'status' => 400 ]
                );
        }

        $history = aimentor_get_canvas_history();

        $hash = md5( $normalized_entry['layout'] );

        $history = array_filter(
                $history,
                static function( $existing ) use ( $hash ) {
                        $existing_hash = md5( $existing['layout'] ?? '' );

                        return $existing_hash !== $hash;
                }
        );

        array_unshift( $history, $normalized_entry );

        $max_items = aimentor_get_canvas_history_max_items();

        if ( count( $history ) > $max_items ) {
                $history = array_slice( $history, 0, $max_items );
        }

        update_option( aimentor_get_canvas_history_option_name(), array_values( $history ), false );

        return $normalized_entry;
}

function aimentor_is_layout_archival_enabled() {
        return 'yes' === get_option( 'aimentor_archive_layouts', 'no' );
}

function aimentor_maybe_archive_generation_payload( $payload, $context = [] ) {
        if ( ! aimentor_is_layout_archival_enabled() ) {
                return;
        }

        if ( ! post_type_exists( 'ai_layout' ) ) {
                return;
        }

        $type = isset( $context['type'] ) ? sanitize_key( $context['type'] ) : 'content';

        if ( ! in_array( $type, [ 'canvas', 'content' ], true ) ) {
                $type = 'content';
        }

        if ( is_array( $payload ) || is_object( $payload ) ) {
                $serialized_payload = wp_json_encode( $payload );
        } else {
                $serialized_payload = (string) $payload;
        }

        if ( false === $serialized_payload ) {
                return;
        }

        $serialized_payload = wp_check_invalid_utf8( $serialized_payload );
        $serialized_payload = trim( (string) $serialized_payload );

        if ( '' === $serialized_payload ) {
                return;
        }

        $should_archive = apply_filters( 'aimentor_should_archive_generation', true, $payload, $context );

        if ( ! $should_archive ) {
                return;
        }

        $prompt   = isset( $context['prompt'] ) ? sanitize_textarea_field( $context['prompt'] ) : '';
        $provider = isset( $context['provider'] ) ? sanitize_key( $context['provider'] ) : '';
        $model    = isset( $context['model'] ) ? sanitize_text_field( $context['model'] ) : '';
        $tier     = isset( $context['tier'] ) ? sanitize_key( $context['tier'] ) : '';
        $task     = isset( $context['task'] ) ? sanitize_key( $context['task'] ) : ( 'canvas' === $type ? 'canvas' : 'content' );

        $title = '';

        if ( '' !== $prompt ) {
                $title = trim( wp_html_excerpt( $prompt, 80, '…' ) );
        }

        if ( '' === $title ) {
                $type_label      = 'canvas' === $type ? __( 'Canvas layout', 'aimentor' ) : __( 'Content layout', 'aimentor' );
                $datetime_format = get_option( 'date_format' ) . ' ' . get_option( 'time_format' );
                $timestamp       = current_time( 'timestamp' );
                $formatted_time  = function_exists( 'wp_date' )
                        ? wp_date( $datetime_format, $timestamp )
                        : date_i18n( $datetime_format, $timestamp );

                $title = sprintf(
                        __( '%1$s generated on %2$s', 'aimentor' ),
                        $type_label,
                        $formatted_time
                );
        }

        $excerpt = '';

        if ( '' !== $prompt ) {
                $excerpt = sanitize_textarea_field( wp_trim_words( $prompt, 55, '…' ) );
        }

        $meta_input = [
                '_aimentor_prompt'          => $prompt,
                '_aimentor_provider'        => $provider,
                '_aimentor_generation_type' => $type,
                '_aimentor_payload_format'  => ( 'canvas' === $type ) ? 'json' : 'html',
        ];

        if ( '' !== $model ) {
                $meta_input['_aimentor_model'] = $model;
        }

        if ( '' !== $tier ) {
                $meta_input['_aimentor_tier'] = $tier;
        }

        if ( '' !== $task ) {
                $meta_input['_aimentor_task'] = $task;
        }

        $meta_input = array_filter(
                $meta_input,
                static function( $value ) {
                        return '' !== $value && null !== $value;
                }
        );

        $post_data = [
                'post_type'    => 'ai_layout',
                'post_status'  => 'private',
                'post_author'  => get_current_user_id(),
                'post_title'   => $title,
                'post_content' => $serialized_payload,
                'post_excerpt' => $excerpt,
                'meta_input'   => $meta_input,
        ];

        $post_data = apply_filters( 'aimentor_layout_archive_post_data', $post_data, $context, $payload );

        $post_id = wp_insert_post( $post_data, true );

        if ( is_wp_error( $post_id ) ) {
                return;
        }

        do_action( 'aimentor_layout_archived', $post_id, $post_data, $context, $payload );
}

function aimentor_ajax_store_canvas_history() {
        check_ajax_referer( 'aimentor_canvas_history', 'nonce' );

        if ( ! current_user_can( 'edit_posts' ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'Insufficient permissions to store layouts.', 'aimentor' ),
                                'code'    => 'aimentor_canvas_history_forbidden',
                        ],
                        403
                );
        }

        $layout = isset( $_POST['layout'] ) ? wp_unslash( $_POST['layout'] ) : '';

        $meta = [
                'summary'  => isset( $_POST['summary'] ) ? wp_unslash( $_POST['summary'] ) : '',
                'provider' => isset( $_POST['provider'] ) ? wp_unslash( $_POST['provider'] ) : '',
                'model'    => isset( $_POST['model'] ) ? wp_unslash( $_POST['model'] ) : '',
                'task'     => isset( $_POST['task'] ) ? wp_unslash( $_POST['task'] ) : '',
                'tier'     => isset( $_POST['tier'] ) ? wp_unslash( $_POST['tier'] ) : '',
        ];

        $stored = aimentor_store_canvas_history_entry( $layout, $meta );

        if ( is_wp_error( $stored ) ) {
                wp_send_json_error(
                        [
                                'message' => $stored->get_error_message(),
                                'code'    => $stored->get_error_code(),
                        ],
                        absint( $stored->get_error_data()['status'] ?? 400 )
                );
        }

        wp_send_json_success(
                [
                        'entry'   => $stored,
                        'history' => aimentor_get_canvas_history(),
                ]
        );
}
add_action( 'wp_ajax_aimentor_store_canvas_history', 'aimentor_ajax_store_canvas_history' );
add_action( 'wp_ajax_jaggrok_store_canvas_history', 'aimentor_ajax_store_canvas_history' );

function aimentor_generation_history_permissions_check( WP_REST_Request $request ) {
        return current_user_can( 'edit_posts' );
}

function aimentor_rest_create_generation_history_entry( WP_REST_Request $request ) {
        $prompt   = $request->get_param( 'prompt' );
        $provider = $request->get_param( 'provider' );

        $result = aimentor_store_generation_history_entry( $prompt, $provider );

        if ( is_wp_error( $result ) ) {
                return $result;
        }

        return new WP_REST_Response(
                [
                        'success' => true,
                        'data'    => $result,
                ],
                201
        );
}

function aimentor_register_generation_history_route() {
        register_rest_route(
                'aimentor/v1',
                '/history',
                [
                        [
                                'methods'             => WP_REST_Server::CREATABLE,
                                'callback'            => 'aimentor_rest_create_generation_history_entry',
                                'permission_callback' => 'aimentor_generation_history_permissions_check',
                                'args'                => [
                                        'prompt'   => [
                                                'type'              => 'string',
                                                'required'          => true,
                                                'sanitize_callback' => 'sanitize_textarea_field',
                                        ],
                                        'provider' => [
                                                'type'              => 'string',
                                                'required'          => true,
                                                'sanitize_callback' => 'sanitize_key',
                                        ],
                                ],
                        ],
                ]
        );
}
add_action( 'rest_api_init', 'aimentor_register_generation_history_route' );

function aimentor_perform_generation_request( $prompt, $provider_key = '', $args = [] ) {
        $args = wp_parse_args(
                $args,
                [
                        'task'          => '',
                        'tier'          => '',
                        'origin'        => 'rest',
                        'max_tokens'    => null,
                        'store_history' => true,
                        'user_id'       => get_current_user_id(),
                        'variations'    => null,
                ]
        );

        $prompt = sanitize_textarea_field( (string) $prompt );

        if ( '' === $prompt ) {
                return new WP_Error( 'aimentor_missing_prompt', __( 'Prompt is required.', 'aimentor' ) );
        }

        $provider_labels = aimentor_get_provider_labels();

        if ( '' === $provider_key ) {
                $provider_key = get_option( 'aimentor_provider', 'grok' );
        }

        $provider_key = sanitize_key( (string) $provider_key );

        if ( function_exists( 'aimentor_sanitize_provider' ) ) {
                $provider_key = aimentor_sanitize_provider( $provider_key );
        }

        if ( ! array_key_exists( $provider_key, $provider_labels ) ) {
                return new WP_Error( 'aimentor_invalid_provider', __( 'Invalid provider selected.', 'aimentor' ) );
        }

        if ( ! function_exists( 'jaggrok_get_active_provider' ) ) {
                return new WP_Error( 'aimentor_missing_provider_factory', __( 'Provider factory is unavailable.', 'aimentor' ) );
        }

        $provider = jaggrok_get_active_provider( $provider_key );

        if ( ! $provider instanceof AiMentor_Provider_Interface ) {
                return new WP_Error( 'aimentor_provider_configuration', __( 'Provider configuration error.', 'aimentor' ) );
        }

        $requested_task = sanitize_key( (string) $args['task'] );
        $requested_tier = sanitize_key( (string) $args['tier'] );

        if ( function_exists( 'aimentor_sanitize_generation_type' ) ) {
                $requested_task = aimentor_sanitize_generation_type( $requested_task );
        } else {
                $requested_task = $requested_task ? jaggrok_normalize_generation_task( $requested_task ) : '';
        }

        if ( function_exists( 'aimentor_sanitize_performance_tier' ) ) {
                $requested_tier = aimentor_sanitize_performance_tier( $requested_tier );
        } else {
                $requested_tier = $requested_tier ? jaggrok_normalize_performance_tier( $requested_tier ) : '';
        }

        $is_pro          = function_exists( 'aimentor_is_pro_active' ) ? aimentor_is_pro_active() : false;
        $supports_canvas = $provider->supports_canvas();
        $resolution      = jaggrok_resolve_generation_preset(
                $provider_key,
                $requested_task,
                $requested_tier,
                $supports_canvas,
                $is_pro
        );

        $task  = $resolution['task'];
        $tier  = $resolution['tier'];
        $model = $resolution['model'];

        $variation_count = absint( $args['variations'] );

        if ( 'canvas' === $task ) {
                if ( $variation_count < 1 ) {
                        /**
                         * Filter the number of canvas variations requested via the REST helper.
                         *
                         * @param int    $count        Default variation count.
                         * @param string $provider_key Active provider key.
                         * @param string $task         Normalized task.
                         * @param string $tier         Normalized tier.
                         */
                        $variation_count = apply_filters( 'aimentor_canvas_variation_count', 3, $provider_key, $task, $tier );
                }
        }

        if ( $variation_count < 1 ) {
                $variation_count = 1;
        }

        switch ( $provider_key ) {
                case 'openai':
                        $api_key = get_option( 'aimentor_openai_api_key' );
                        break;
                case 'anthropic':
                        $api_key = get_option( 'aimentor_anthropic_api_key' );
                        break;
                case 'grok':
                default:
                        $api_key = get_option( 'aimentor_xai_api_key' );
                        break;
        }

        $max_tokens = null === $args['max_tokens'] ? get_option( 'aimentor_max_tokens', 2000 ) : absint( $args['max_tokens'] );

        if ( function_exists( 'aimentor_sanitize_max_tokens' ) ) {
                $max_tokens = aimentor_sanitize_max_tokens( $max_tokens );
        }

        $origin  = sanitize_key( (string) ( $args['origin'] ?: 'rest' ) );
        $context = [
                'task'   => $task,
                'tier'   => $tier,
                'origin' => $origin,
        ];

        $result = $provider->request(
                $prompt,
                [
                        'api_key'    => $api_key,
                        'model'      => $model,
                        'max_tokens' => $max_tokens,
                        'context'    => $context,
                        'variations' => $variation_count,
                ]
        );

        if ( is_wp_error( $result ) ) {
                if ( function_exists( 'aimentor_record_provider_usage' ) ) {
                        aimentor_record_provider_usage(
                                $provider_key,
                                'error',
                                [
                                        'model'  => $model,
                                        'task'   => $task,
                                        'tier'   => $tier,
                                        'origin' => $origin,
                                ]
                        );
                }

                if ( function_exists( 'aimentor_log_error' ) ) {
                        aimentor_log_error(
                                $result->get_error_message(),
                                [
                                        'provider' => $provider_key,
                                        'model'    => $model,
                                        'task'     => $task,
                                        'tier'     => $tier,
                                        'origin'   => $origin,
                                        'user_id'  => $args['user_id'],
                                ]
                        );
                }

                $error_data = $result->get_error_data();

                if ( ! is_array( $error_data ) ) {
                        $error_data = [];
                }

                $error_data = array_merge(
                        [
                                'provider' => $provider_key,
                                'model'    => $model,
                                'task'     => $task,
                                'tier'     => $tier,
                        ],
                        $error_data
                );

                $error_code = $result->get_error_code();

                if ( ! $error_code ) {
                        $error_code = 'aimentor_generation_failed';
                }

                return new WP_Error( $error_code, $result->get_error_message(), $error_data );
        }

        if ( function_exists( 'aimentor_record_provider_usage' ) ) {
                aimentor_record_provider_usage(
                        $provider_key,
                        'success',
                        [
                                'model'  => $model,
                                'task'   => $task,
                                'tier'   => $tier,
                                'origin' => $origin,
                        ]
                );
        }

        if ( function_exists( 'aimentor_store_generation_history_entry' ) && $args['store_history'] ) {
                $history_result = aimentor_store_generation_history_entry( $prompt, $provider_key );

                if ( is_wp_error( $history_result ) && function_exists( 'aimentor_log_error' ) ) {
                        aimentor_log_error(
                                $history_result->get_error_message(),
                                [
                                        'provider' => $provider_key,
                                        'model'    => $model,
                                        'task'     => $task,
                                        'tier'     => $tier,
                                        'origin'   => $origin,
                                        'user_id'  => $args['user_id'],
                                ]
                        );
                }
        }

        if ( function_exists( 'aimentor_maybe_archive_generation_payload' ) ) {
                $archive_type = isset( $result['type'] ) && 'canvas' === $result['type'] ? 'canvas' : 'content';

                aimentor_maybe_archive_generation_payload(
                        $result['content'],
                        [
                                'type'     => $archive_type,
                                'prompt'   => $prompt,
                                'provider' => $provider_key,
                                'model'    => $model,
                                'task'     => $task,
                                'tier'     => $tier,
                                'origin'   => $origin,
                        ]
                );
        }

        $payload = [
                'provider'       => $provider_key,
                'provider_label' => $provider_labels[ $provider_key ] ?? ucfirst( $provider_key ),
                'model'          => $model,
                'task'           => $task,
                'tier'           => $tier,
                'type'           => isset( $result['type'] ) && 'canvas' === $result['type'] ? 'canvas' : 'content',
        ];

        if ( ! empty( $result['rate_limit'] ) ) {
                $payload['rate_limit'] = $result['rate_limit'];
        }

        if ( 'canvas' === $payload['type'] ) {
                $payload['canvas_json'] = $result['content'];
                if ( ! empty( $result['canvas_variations'] ) && is_array( $result['canvas_variations'] ) ) {
                        $payload['canvas_variations'] = array_values( $result['canvas_variations'] );
                }
        } else {
                $payload['html'] = (string) $result['content'];
                if ( ! empty( $result['content_variations'] ) && is_array( $result['content_variations'] ) ) {
                        $payload['content_variations'] = array_values( $result['content_variations'] );
                }
        }

        if ( ! empty( $result['summary'] ) && is_string( $result['summary'] ) ) {
                $payload['summary'] = $result['summary'];
        }

        return $payload;
}

function aimentor_rest_generate_permissions_check( WP_REST_Request $request ) {
        return current_user_can( 'edit_posts' );
}

function aimentor_rest_generate_content( WP_REST_Request $request ) {
        $prompt        = $request->get_param( 'prompt' );
        $provider      = $request->get_param( 'provider' );
        $task          = $request->get_param( 'task' );
        $tier          = $request->get_param( 'tier' );
        $max_tokens    = $request->get_param( 'max_tokens' );
        $store_history = $request->get_param( 'store_history' );

        $result = aimentor_perform_generation_request(
                $prompt,
                $provider,
                [
                        'task'          => is_string( $task ) ? $task : '',
                        'tier'          => is_string( $tier ) ? $tier : '',
                        'origin'        => 'rest',
                        'max_tokens'    => null !== $max_tokens ? $max_tokens : null,
                        'store_history' => null === $store_history ? true : wp_validate_boolean( $store_history ),
                        'user_id'       => get_current_user_id(),
                ]
        );

        if ( is_wp_error( $result ) ) {
                return $result;
        }

        return new WP_REST_Response( $result, 200 );
}

function aimentor_register_generation_route() {
        register_rest_route(
                'aimentor/v1',
                '/generate',
                [
                        [
                                'methods'             => WP_REST_Server::CREATABLE,
                                'callback'            => 'aimentor_rest_generate_content',
                                'permission_callback' => 'aimentor_rest_generate_permissions_check',
                                'args'                => [
                                        'prompt' => [
                                                'type'              => 'string',
                                                'required'          => true,
                                                'sanitize_callback' => 'sanitize_textarea_field',
                                        ],
                                        'provider' => [
                                                'type'              => 'string',
                                                'required'          => false,
                                                'sanitize_callback' => 'sanitize_key',
                                        ],
                                        'task' => [
                                                'type'              => 'string',
                                                'required'          => false,
                                                'sanitize_callback' => 'sanitize_key',
                                        ],
                                        'tier' => [
                                                'type'              => 'string',
                                                'required'          => false,
                                                'sanitize_callback' => 'sanitize_key',
                                        ],
                                        'max_tokens' => [
                                                'type'              => 'integer',
                                                'required'          => false,
                                                'validate_callback' => 'is_numeric',
                                        ],
                                        'store_history' => [
                                                'type'              => 'boolean',
                                                'required'          => false,
                                        ],
                                ],
                        ],
                ]
        );
}
add_action( 'rest_api_init', 'aimentor_register_generation_route' );

function aimentor_get_provider_usage_summary() {
        $data            = aimentor_get_provider_usage_data();
        $labels          = aimentor_get_provider_labels();
        $allowed_models  = aimentor_get_allowed_provider_models();
        $now             = current_time( 'timestamp' );
        $separator       = _x( ' • ', 'separator between task and tier', 'aimentor' );
        $origin_labels   = [
                'generation' => __( 'Generation', 'aimentor' ),
                'test'       => __( 'Connection test', 'aimentor' ),
        ];

        $generated_at = isset( $data['generated_at'] ) ? absint( $data['generated_at'] ) : 0;

        $summary = [
                'generated_at'        => $generated_at ? $generated_at : $now,
                'generated_at_human'  => $generated_at ? sprintf( __( '%s ago', 'aimentor' ), human_time_diff( $generated_at, $now ) ) : __( 'Just now', 'aimentor' ),
                'providers'           => [],
        ];

        foreach ( $labels as $provider_key => $label ) {
                $provider_data = $data['providers'][ $provider_key ] ?? [];
                $success_total = absint( $provider_data['success_count'] ?? 0 );
                $error_total   = absint( $provider_data['error_count'] ?? 0 );
                $last_event    = absint( $provider_data['last_event'] ?? 0 );
                $last_success  = absint( $provider_data['last_success'] ?? 0 );
                $last_error    = absint( $provider_data['last_error'] ?? 0 );
                $last_model    = isset( $provider_data['last_model'] ) ? sanitize_text_field( $provider_data['last_model'] ) : '';
                $last_task     = isset( $provider_data['last_task'] ) ? sanitize_key( $provider_data['last_task'] ) : '';
                $last_tier     = isset( $provider_data['last_tier'] ) ? sanitize_key( $provider_data['last_tier'] ) : '';
                $last_origin   = isset( $provider_data['last_origin'] ) ? sanitize_key( $provider_data['last_origin'] ) : '';

                $model_label = '';

                if ( $last_model && isset( $allowed_models[ $provider_key ][ $last_model ] ) ) {
                        $model_label = $allowed_models[ $provider_key ][ $last_model ];
                } elseif ( $last_model ) {
                        $model_label = strtoupper( $last_model );
                }

                $task_label = '';

                if ( 'canvas' === $last_task ) {
                        $task_label = __( 'Canvas', 'aimentor' );
                } elseif ( 'content' === $last_task ) {
                        $task_label = __( 'Content', 'aimentor' );
                }

                $tier_label = '';

                if ( 'quality' === $last_tier ) {
                        $tier_label = __( 'Quality', 'aimentor' );
                } elseif ( 'fast' === $last_tier ) {
                        $tier_label = __( 'Fast', 'aimentor' );
                }

                $context_parts = array_filter( [ $task_label, $tier_label ] );
                $context_text  = '';

                if ( ! empty( $context_parts ) ) {
                        $context_text = implode( $separator, $context_parts );
                }

                if ( $model_label ) {
                        $context_text = $context_text
                                ? sprintf( __( '%1$s via %2$s', 'aimentor' ), $context_text, $model_label )
                                : sprintf( __( 'via %s', 'aimentor' ), $model_label );
                }

                $summary['providers'][ $provider_key ] = [
                        'label'              => $label,
                        'success_total'      => $success_total,
                        'error_total'        => $error_total,
                        'total_requests'     => $success_total + $error_total,
                        'last_event'         => $last_event,
                        'last_event_human'   => $last_event ? sprintf( __( '%s ago', 'aimentor' ), human_time_diff( $last_event, $now ) ) : __( 'No activity yet', 'aimentor' ),
                        'last_success'       => $last_success,
                        'last_success_human' => $last_success ? sprintf( __( '%s ago', 'aimentor' ), human_time_diff( $last_success, $now ) ) : __( 'Never', 'aimentor' ),
                        'last_error'         => $last_error,
                        'last_error_human'   => $last_error ? sprintf( __( '%s ago', 'aimentor' ), human_time_diff( $last_error, $now ) ) : __( 'Never', 'aimentor' ),
                        'context_summary'    => $context_text,
                        'last_origin'        => $last_origin,
                        'origin_label'       => $origin_labels[ $last_origin ] ?? '',
                ];
        }

        return $summary;
}

function aimentor_normalize_provider_test_history( $history, $limit = 10 ) {
        if ( ! is_array( $history ) ) {
                return [];
        }

        $allowed_statuses = [ 'success', 'error' ];
        $normalized       = [];

        foreach ( $history as $entry ) {
                if ( ! is_array( $entry ) ) {
                        continue;
                }

                $status = isset( $entry['status'] ) ? $entry['status'] : '';

                if ( ! in_array( $status, $allowed_statuses, true ) ) {
                        continue;
                }

                $timestamp = isset( $entry['timestamp'] ) ? absint( $entry['timestamp'] ) : 0;

                $normalized[] = [
                        'status'    => $status,
                        'timestamp' => $timestamp,
                ];
        }

        if ( $limit > 0 && count( $normalized ) > $limit ) {
                $normalized = array_slice( $normalized, - $limit );
        }

        return $normalized;
}

function aimentor_get_provider_test_statuses() {
        $providers = array_keys( aimentor_get_provider_labels() );
        $defaults  = [];
        $history_limit = apply_filters( 'aimentor_provider_test_history_limit', 10 );

        foreach ( $providers as $provider ) {
                $defaults[ $provider ] = [
                        'status'        => '',
                        'message'       => '',
                        'timestamp'     => 0,
                        'success_count' => 0,
                        'failure_count' => 0,
                        'history'       => [],
                ];
        }

        $stored = get_option( 'aimentor_provider_test_statuses', [] );

        if ( ! is_array( $stored ) ) {
                return $defaults;
        }

        foreach ( $providers as $provider ) {
                $entry = isset( $stored[ $provider ] ) && is_array( $stored[ $provider ] ) ? $stored[ $provider ] : [];

                $status = isset( $entry['status'] ) && in_array( $entry['status'], [ 'success', 'error' ], true )
                        ? $entry['status']
                        : '';

                $message = isset( $entry['message'] ) ? sanitize_text_field( $entry['message'] ) : '';
                $timestamp = isset( $entry['timestamp'] ) ? absint( $entry['timestamp'] ) : 0;
                $success_count = isset( $entry['success_count'] ) ? absint( $entry['success_count'] ) : 0;
                $failure_count = isset( $entry['failure_count'] ) ? absint( $entry['failure_count'] ) : 0;
                $history       = isset( $entry['history'] ) ? aimentor_normalize_provider_test_history( $entry['history'], $history_limit ) : [];

                $defaults[ $provider ] = [
                        'status'        => $status,
                        'message'       => $message,
                        'timestamp'     => $timestamp,
                        'success_count' => $success_count,
                        'failure_count' => $failure_count,
                        'history'       => $history,
                ];
        }

        return $defaults;
}

function aimentor_get_default_options() {
        $provider_defaults = aimentor_get_provider_model_defaults();
        $legacy_defaults   = aimentor_map_presets_to_legacy_defaults( $provider_defaults );

        return [
                'aimentor_provider'                  => 'grok',
                'aimentor_xai_api_key'               => '',
                'aimentor_anthropic_api_key'         => '',
                'aimentor_openai_api_key'            => '',
                'aimentor_auto_insert'               => 'yes',
                'aimentor_theme_style'               => 'modern',
                'aimentor_primary_color'             => '#3B82F6',
                'aimentor_tone_keywords'             => 'friendly, confident, helpful',
                'aimentor_max_tokens'                => 2000,
                'aimentor_provider_models'           => $legacy_defaults,
                'aimentor_model_presets'             => $provider_defaults,
                'aimentor_document_provider_defaults' => aimentor_get_document_provider_default_map(),
                'aimentor_model'                     => $legacy_defaults['grok'] ?? '',
                'aimentor_anthropic_model'           => $legacy_defaults['anthropic'] ?? '',
                'aimentor_openai_model'              => $legacy_defaults['openai'] ?? '',
                'aimentor_default_generation_type'   => 'content',
                'aimentor_default_performance'       => 'fast',
                'aimentor_api_tested'                => false,
                'aimentor_onboarding_dismissed'      => 'no',
                'aimentor_enable_auto_updates'       => 'yes',
                'aimentor_enable_health_checks'       => 'yes',
                'aimentor_enable_health_check_alerts' => 'yes',
                'aimentor_health_check_recipients'    => '',
                'aimentor_archive_layouts'            => 'no',
                'aimentor_archive_layouts_show_ui'    => 'no',
                'aimentor_request_overrides'          => aimentor_get_request_override_defaults(),
                'aimentor_network_lock_provider_models' => 'no',
        ];
}

function aimentor_seed_default_options() {
        $defaults = aimentor_get_default_options();

        foreach ( $defaults as $option => $default ) {
                $current = get_option( $option, false );

                if ( false === $current ) {
                        add_option( $option, $default );
                        continue;
                }

                if ( is_array( $default ) ) {
                        if ( ! is_array( $current ) || empty( $current ) ) {
                                update_option( $option, $default );
                        }

                        continue;
                }

                if ( '' === $current && '' !== $default ) {
                        update_option( $option, $default );
                }
        }
}

function aimentor_update_provider_test_status( $provider_key, $status, $message ) {
        $allowed_statuses = [ 'success', 'error' ];

        if ( ! in_array( $provider_key, array_keys( aimentor_get_provider_labels() ), true ) ) {
                return;
        }

        $statuses = get_option( 'aimentor_provider_test_statuses', [] );

        if ( ! is_array( $statuses ) ) {
                $statuses = [];
        }

        $sanitized_status = in_array( $status, $allowed_statuses, true ) ? $status : '';
        $history_limit    = apply_filters( 'aimentor_provider_test_history_limit', 10 );
        $existing         = isset( $statuses[ $provider_key ] ) && is_array( $statuses[ $provider_key ] ) ? $statuses[ $provider_key ] : [];
        $success_count    = isset( $existing['success_count'] ) ? absint( $existing['success_count'] ) : 0;
        $failure_count    = isset( $existing['failure_count'] ) ? absint( $existing['failure_count'] ) : 0;
        $history          = isset( $existing['history'] ) ? aimentor_normalize_provider_test_history( $existing['history'], $history_limit ) : [];
        $timestamp        = current_time( 'timestamp' );

        if ( 'success' === $sanitized_status ) {
                $success_count++;
        } elseif ( 'error' === $sanitized_status ) {
                $failure_count++;
        }

        if ( in_array( $sanitized_status, $allowed_statuses, true ) ) {
                $history[] = [
                        'status'    => $sanitized_status,
                        'timestamp' => $timestamp,
                ];

                if ( $history_limit > 0 && count( $history ) > $history_limit ) {
                        $history = array_slice( $history, - $history_limit );
                }
        }

        $statuses[ $provider_key ] = [
                'status'        => $sanitized_status,
                'message'       => sanitize_text_field( $message ),
                'timestamp'     => $timestamp,
                'success_count' => $success_count,
                'failure_count' => $failure_count,
                'history'       => $history,
        ];

        update_option( 'aimentor_provider_test_statuses', $statuses );
}

function aimentor_maybe_increment_provider_test_counters( $provider_key, $status, $timestamp ) {
        $allowed_statuses = [ 'success', 'error' ];

        if ( ! in_array( $status, $allowed_statuses, true ) ) {
                return;
        }

        $statuses = get_option( 'aimentor_provider_test_statuses', [] );

        if ( ! is_array( $statuses ) || ! isset( $statuses[ $provider_key ] ) || ! is_array( $statuses[ $provider_key ] ) ) {
                return;
        }

        $history_limit = apply_filters( 'aimentor_provider_test_history_limit', 10 );

        $entry         = $statuses[ $provider_key ];
        $success_count = isset( $entry['success_count'] ) ? absint( $entry['success_count'] ) : 0;
        $failure_count = isset( $entry['failure_count'] ) ? absint( $entry['failure_count'] ) : 0;
        $history       = isset( $entry['history'] ) ? aimentor_normalize_provider_test_history( $entry['history'], $history_limit ) : [];
        $timestamp     = absint( $timestamp );

        if ( $timestamp <= 0 ) {
                $timestamp = current_time( 'timestamp' );
        }

        $last_entry = end( $history );

        if ( $last_entry && $last_entry['status'] === $status && $last_entry['timestamp'] === $timestamp ) {
                return;
        }

        if ( 'success' === $status ) {
                $success_count++;
        } else {
                $failure_count++;
        }

        $history[] = [
                'status'    => $status,
                'timestamp' => $timestamp,
        ];

        if ( $history_limit > 0 && count( $history ) > $history_limit ) {
                $history = array_slice( $history, - $history_limit );
        }

        $entry['success_count'] = $success_count;
        $entry['failure_count'] = $failure_count;
        $entry['history']       = $history;

        $statuses[ $provider_key ] = $entry;

        update_option( 'aimentor_provider_test_statuses', $statuses );
}

function aimentor_format_provider_status_for_display( $provider_key, $status_data ) {
        $labels          = aimentor_get_provider_labels();
        $provider_label  = $labels[ $provider_key ] ?? ucfirst( $provider_key );
        $badge_labels    = [
                'success' => __( 'Connected', 'aimentor' ),
                'error'   => __( 'Error', 'aimentor' ),
                'idle'    => __( 'Not tested', 'aimentor' ),
                'pending' => __( 'Testing', 'aimentor' ),
        ];
        $state           = isset( $status_data['status'] ) && in_array( $status_data['status'], [ 'success', 'error' ], true )
                ? $status_data['status']
                : 'idle';
        $timestamp       = isset( $status_data['timestamp'] ) ? absint( $status_data['timestamp'] ) : 0;
        $message         = isset( $status_data['message'] ) ? $status_data['message'] : '';
        $success_count   = isset( $status_data['success_count'] ) ? absint( $status_data['success_count'] ) : 0;
        $failure_count   = isset( $status_data['failure_count'] ) ? absint( $status_data['failure_count'] ) : 0;
        $history         = isset( $status_data['history'] ) ? aimentor_normalize_provider_test_history( $status_data['history'] ) : [];
        $total_count     = $success_count + $failure_count;
        $success_rate    = $total_count > 0 ? round( ( $success_count / $total_count ) * 100, 1 ) : null;
        $rate_decimals   = ( null !== $success_rate && abs( $success_rate - (int) $success_rate ) > 0 ) ? 1 : 0;
        $success_rate_formatted = null !== $success_rate ? number_format_i18n( $success_rate, $rate_decimals ) : null;
        $summary_label = '';
        $metrics_label = '';
        $description     = __( 'No tests have been run yet.', 'aimentor' );

        if ( $timestamp > 0 ) {
                $relative = human_time_diff( $timestamp, current_time( 'timestamp' ) );

                if ( 'success' === $state ) {
                        $default_message = sprintf( __( '%s API key is valid.', 'aimentor' ), $provider_label );
                        $description     = sprintf(
                                __( 'Last tested %1$s ago — %2$s', 'aimentor' ),
                                $relative,
                                $message ? $message : $default_message
                        );
                } else {
                        $default_message = sprintf( __( 'Unable to connect to %s.', 'aimentor' ), $provider_label );
                        $description     = sprintf(
                                __( 'Last attempt %1$s ago — %2$s', 'aimentor' ),
                                $relative,
                                $message ? $message : $default_message
                        );
                }
        }

        if ( ! isset( $badge_labels[ $state ] ) ) {
                $state = 'idle';
        }

        if ( $total_count > 0 ) {
                $rate_label   = null !== $success_rate_formatted ? $success_rate_formatted . '%' : number_format_i18n( $success_count );
                $summary_label = sprintf(
                        /* translators: 1: success rate percentage, 2: successful tests, 3: total tests */
                        __( '%1$s success • %2$d/%3$d', 'aimentor' ),
                        $rate_label,
                        $success_count,
                        $total_count
                );
                $metrics_label = sprintf(
                        /* translators: 1: success rate percentage, 2: total tests, 3: successful tests, 4: failed tests */
                        __( 'Success rate %1$s across %2$d tests (%3$d success, %4$d failure).', 'aimentor' ),
                        $rate_label,
                        $total_count,
                        $success_count,
                        $failure_count
                );
        } else {
                $summary_label = __( 'No tests yet', 'aimentor' );
                $metrics_label = __( 'No connection tests recorded yet.', 'aimentor' );
        }

        return [
                'badge_state'            => $state,
                'badge_label'            => $badge_labels[ $state ],
                'description'            => $description,
                'timestamp'              => $timestamp,
                'message'                => $message,
                'success_count'          => $success_count,
                'failure_count'          => $failure_count,
                'total_count'            => $total_count,
                'success_rate'           => $success_rate,
                'success_rate_formatted' => $success_rate_formatted,
                'summary_label'          => $summary_label,
                'metrics_label'          => $metrics_label,
                'history'                => $history,
                'raw_status'             => $status_data['status'] ?? '',
        ];
}

function aimentor_add_settings_page() {
        add_options_page(
                'AiMentor Elementor Settings',
                'AiMentor Elementor',
		'manage_options',
		'aimentor-settings',
		'aimentor_settings_page_callback'
	);
}
add_action( 'admin_menu', 'aimentor_add_settings_page' );

function aimentor_register_settings() {
        $defaults = aimentor_get_default_options();

        register_setting(
                'aimentor_settings',
                'aimentor_xai_api_key',
                [
                        'sanitize_callback' => 'aimentor_sanitize_api_key',
                        'default' => $defaults['aimentor_xai_api_key'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_anthropic_api_key',
                [
                        'sanitize_callback' => 'aimentor_sanitize_api_key',
                        'default' => $defaults['aimentor_anthropic_api_key'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_openai_api_key',
                [
                        'sanitize_callback' => 'aimentor_sanitize_api_key',
                        'default' => $defaults['aimentor_openai_api_key'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_auto_insert',
                [
                        'sanitize_callback' => 'aimentor_sanitize_auto_insert',
                        'default' => $defaults['aimentor_auto_insert'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_archive_layouts',
                [
                        'sanitize_callback' => 'aimentor_sanitize_toggle',
                        'default' => $defaults['aimentor_archive_layouts'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_archive_layouts_show_ui',
                [
                        'sanitize_callback' => 'aimentor_sanitize_toggle',
                        'default' => $defaults['aimentor_archive_layouts_show_ui'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_network_lock_provider_models',
                [
                        'sanitize_callback' => 'aimentor_sanitize_toggle',
                        'default' => $defaults['aimentor_network_lock_provider_models'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_theme_style',
                [
                        'sanitize_callback' => 'aimentor_sanitize_theme_style',
                        'default' => $defaults['aimentor_theme_style'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_primary_color',
                [
                        'sanitize_callback' => 'aimentor_sanitize_primary_color',
                        'default' => $defaults['aimentor_primary_color'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_tone_keywords',
                [
                        'sanitize_callback' => 'aimentor_sanitize_tone_keywords',
                        'default' => $defaults['aimentor_tone_keywords'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_max_tokens',
                [
                        'sanitize_callback' => 'aimentor_sanitize_max_tokens',
                        'default' => $defaults['aimentor_max_tokens'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_provider_models',
                [
                        'sanitize_callback' => 'aimentor_sanitize_provider_models',
                        'default' => $defaults['aimentor_provider_models'],
                        'type' => 'array',
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_request_overrides',
                [
                        'sanitize_callback' => 'aimentor_sanitize_request_overrides',
                        'default' => $defaults['aimentor_request_overrides'],
                        'type' => 'array',
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_document_provider_defaults',
                [
                        'sanitize_callback' => 'aimentor_sanitize_document_provider_defaults',
                        'default' => $defaults['aimentor_document_provider_defaults'],
                        'type' => 'array',
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_model',
                [
                        'sanitize_callback' => 'aimentor_sanitize_model',
                        'default' => $defaults['aimentor_model'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_model',
                [
                        'sanitize_callback' => 'aimentor_sanitize_model',
                        'default' => $defaults['aimentor_model'],
                ]
        ); // v1.4.0: Better default

        register_setting(
                'aimentor_settings',
                'aimentor_openai_model',
                [
                        'sanitize_callback' => 'aimentor_sanitize_openai_model',
                        'default' => $defaults['aimentor_openai_model'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_anthropic_model',
                [
                        'sanitize_callback' => 'aimentor_sanitize_anthropic_model',
                        'default' => $defaults['aimentor_anthropic_model'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_provider',
                [
                        'sanitize_callback' => 'aimentor_sanitize_provider',
                        'default' => $defaults['aimentor_provider'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_default_generation_type',
                [
                        'sanitize_callback' => 'aimentor_sanitize_generation_type',
                        'default' => $defaults['aimentor_default_generation_type'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_default_performance',
                [
                        'sanitize_callback' => 'aimentor_sanitize_performance_tier',
                        'default' => $defaults['aimentor_default_performance'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_enable_auto_updates',
                [
                        'sanitize_callback' => 'aimentor_sanitize_toggle',
                        'default' => $defaults['aimentor_enable_auto_updates'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_enable_health_checks',
                [
                        'sanitize_callback' => 'aimentor_sanitize_toggle',
                        'default' => $defaults['aimentor_enable_health_checks'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_enable_health_check_alerts',
                [
                        'sanitize_callback' => 'aimentor_sanitize_toggle',
                        'default' => $defaults['aimentor_enable_health_check_alerts'],
                ]
        );

        register_setting(
                'aimentor_settings',
                'aimentor_health_check_recipients',
                [
                        'sanitize_callback' => 'aimentor_sanitize_health_check_recipients',
                        'default' => $defaults['aimentor_health_check_recipients'],
                ]
        );

        aimentor_seed_default_options();
}
add_action( 'admin_init', 'aimentor_register_settings' );

function aimentor_sanitize_api_key( $value ) {
        return sanitize_text_field( $value );
}

function aimentor_sanitize_auto_insert( $value ) {
        $allowed = [ 'yes', 'no' ];
        return in_array( $value, $allowed, true ) ? $value : 'yes';
}

function aimentor_sanitize_toggle( $value ) {
        $value = sanitize_key( $value );

        return in_array( $value, [ 'yes', 'no' ], true ) ? $value : 'no';
}

function aimentor_sanitize_theme_style( $value ) {
        $allowed = [ 'modern', 'bold', 'minimal' ];
        return in_array( $value, $allowed, true ) ? $value : 'modern';
}

function aimentor_sanitize_primary_color( $value ) {
        $default   = '#3B82F6';
        $sanitized = sanitize_hex_color( $value );

        if ( empty( $sanitized ) ) {
                return $default;
        }

        return strtoupper( $sanitized );
}

function aimentor_sanitize_tone_keywords( $value ) {
        $value = sanitize_textarea_field( $value );
        $value = trim( preg_replace( '/\s+/', ' ', $value ) );

        return $value;
}

function aimentor_sanitize_health_check_recipients( $value ) {
        if ( is_array( $value ) ) {
                $value = implode( ',', $value );
        }

        $value  = (string) $value;
        $parts  = preg_split( '/[\s,;]+/', $value );
        $emails = [];

        foreach ( $parts as $part ) {
                $part = trim( $part );

                if ( '' === $part ) {
                        continue;
                }

                $sanitized = sanitize_email( $part );

                if ( $sanitized && is_email( $sanitized ) ) {
                        $emails[] = $sanitized;
                }
        }

        $emails = array_unique( $emails );

        return implode( ', ', $emails );
}

function aimentor_get_brand_preferences() {
        $defaults = aimentor_get_default_options();

        $primary_color = get_option( 'aimentor_primary_color', $defaults['aimentor_primary_color'] );
        $tone_keywords = get_option( 'aimentor_tone_keywords', $defaults['aimentor_tone_keywords'] );

        $primary_color = aimentor_sanitize_primary_color( $primary_color );
        $tone_keywords = aimentor_sanitize_tone_keywords( $tone_keywords );

        return [
                'primary_color' => $primary_color,
                'tone_keywords' => $tone_keywords,
        ];
}

function aimentor_get_tone_presets() {
        $brand_preferences = aimentor_get_brand_preferences();
        $brand_tone        = isset( $brand_preferences['tone_keywords'] ) ? aimentor_sanitize_tone_keywords( $brand_preferences['tone_keywords'] ) : '';

        $presets = [];

        $brand_label = __( 'Brand tone', 'aimentor' );

        if ( '' !== $brand_tone ) {
                /* translators: %s: Tone keywords string. */
                $brand_label = sprintf( __( 'Brand tone — %s', 'aimentor' ), $brand_tone );
        }

        $presets[] = [
                'id'        => 'brand',
                'label'     => $brand_label,
                'keywords'  => $brand_tone,
                'is_brand'  => true,
        ];

        $presets = array_merge(
                $presets,
                [
                        [
                                'id'       => 'friendly_supportive',
                                'label'    => __( 'Friendly & Supportive', 'aimentor' ),
                                'keywords' => 'friendly, supportive, encouraging',
                        ],
                        [
                                'id'       => 'professional_confident',
                                'label'    => __( 'Professional & Confident', 'aimentor' ),
                                'keywords' => 'professional, confident, authoritative',
                        ],
                        [
                                'id'       => 'playful_bold',
                                'label'    => __( 'Playful & Bold', 'aimentor' ),
                                'keywords' => 'playful, bold, upbeat',
                        ],
                ]
        );

        /**
         * Filter the tone presets available in the Elementor modal.
         *
         * @param array $presets           Default preset definitions.
         * @param array $brand_preferences Current brand preference payload.
         */
        $presets = apply_filters( 'aimentor_tone_presets', $presets, $brand_preferences );

        $normalized = [];
        $seen       = [];

        foreach ( $presets as $preset ) {
                if ( ! is_array( $preset ) ) {
                        continue;
                }

                $id = isset( $preset['id'] ) ? sanitize_key( $preset['id'] ) : '';

                if ( '' === $id || isset( $seen[ $id ] ) ) {
                        continue;
                }

                $label    = isset( $preset['label'] ) ? sanitize_text_field( $preset['label'] ) : $id;
                $keywords = isset( $preset['keywords'] ) ? aimentor_sanitize_tone_keywords( $preset['keywords'] ) : '';

                $normalized[] = [
                        'id'        => $id,
                        'label'     => $label,
                        'keywords'  => $keywords,
                        'is_brand'  => ! empty( $preset['is_brand'] ),
                ];

                $seen[ $id ] = true;
        }

        return $normalized;
}

function aimentor_sanitize_generation_type( $value ) {
        $value   = sanitize_key( $value );
        $allowed = [ 'content', 'canvas' ];

        return in_array( $value, $allowed, true ) ? $value : 'content';
}

function aimentor_sanitize_performance_tier( $value ) {
        $value   = sanitize_key( $value );
        $allowed = [ 'fast', 'quality' ];

        return in_array( $value, $allowed, true ) ? $value : 'fast';
}

function aimentor_sanitize_max_tokens( $value ) {
        $value = absint( $value );
        if ( $value < 500 ) {
                $value = 500;
        } elseif ( $value > 8000 ) {
                $value = 8000;
        }

        return $value > 0 ? $value : 2000;
}

function aimentor_normalize_document_provider_defaults_structure( $value ) {
        if ( ! is_array( $value ) ) {
                return [];
        }

        if ( isset( $value['page_types'] ) && is_array( $value['page_types'] ) ) {
                $normalized = [
                        'default'    => isset( $value['default'] ) && is_array( $value['default'] ) ? $value['default'] : [],
                        'page_types' => [],
                ];

                foreach ( $value['page_types'] as $post_type => $entry ) {
                        if ( ! is_array( $entry ) ) {
                                continue;
                        }

                        $provider = '';
                        $model    = '';

                        if ( isset( $entry['provider'] ) || isset( $entry['model'] ) ) {
                                $provider = isset( $entry['provider'] ) ? $entry['provider'] : '';
                                $model    = isset( $entry['model'] ) ? $entry['model'] : '';
                        } elseif ( isset( $entry['default'] ) && is_array( $entry['default'] ) ) {
                                $provider = isset( $entry['default']['provider'] ) ? $entry['default']['provider'] : '';
                                $model    = isset( $entry['default']['model'] ) ? $entry['default']['model'] : '';
                        }

                        $templates = [];

                        if ( isset( $entry['templates'] ) && is_array( $entry['templates'] ) ) {
                                foreach ( $entry['templates'] as $template_file => $template_entry ) {
                                        if ( ! is_array( $template_entry ) ) {
                                                continue;
                                        }

                                        if ( isset( $template_entry['provider'] ) || isset( $template_entry['model'] ) ) {
                                                $templates[ $template_file ] = [
                                                        'provider' => isset( $template_entry['provider'] ) ? $template_entry['provider'] : '',
                                                        'model'    => isset( $template_entry['model'] ) ? $template_entry['model'] : '',
                                                ];
                                        } elseif ( isset( $template_entry['default'] ) && is_array( $template_entry['default'] ) ) {
                                                $templates[ $template_file ] = [
                                                        'provider' => isset( $template_entry['default']['provider'] ) ? $template_entry['default']['provider'] : '',
                                                        'model'    => isset( $template_entry['default']['model'] ) ? $template_entry['default']['model'] : '',
                                                ];
                                        }
                                }
                        }

                        $normalized['page_types'][ $post_type ] = [
                                'provider'  => $provider,
                                'model'     => $model,
                                'templates' => $templates,
                        ];
                }

                return $normalized;
        }

        $uses_legacy_keys = false;

        foreach ( array_keys( $value ) as $key ) {
                if ( is_string( $key ) && false !== strpos( $key, ':' ) ) {
                        $uses_legacy_keys = true;
                        break;
                }
        }

        if ( ! $uses_legacy_keys ) {
                return $value;
        }

        $defaults  = aimentor_get_document_provider_default_map();
        $blueprint = aimentor_get_document_context_blueprint();
        $normalized = [
                'default'    => isset( $value['default'] ) && is_array( $value['default'] ) ? $value['default'] : ( $defaults['default'] ?? [] ),
                'page_types' => [],
        ];

        foreach ( $blueprint['page_types'] as $post_type => $meta ) {
                $normalized['page_types'][ $post_type ] = [
                        'default'   => $normalized['default'],
                        'templates' => [],
                ];

                if ( empty( $meta['templates'] ) || ! is_array( $meta['templates'] ) ) {
                        continue;
                }

                foreach ( $meta['templates'] as $template_file => $template_meta ) {
                        $normalized['page_types'][ $post_type ]['templates'][ $template_file ] = $normalized['default'];
                }
        }

        foreach ( $value as $context_key => $context_value ) {
                if ( ! is_string( $context_key ) || ! is_array( $context_value ) ) {
                        continue;
                }

                if ( 'default' === $context_key ) {
                        $normalized['default'] = $context_value;
                        continue;
                }

                if ( 0 === strpos( $context_key, 'post_type:' ) ) {
                        $post_type = substr( $context_key, strlen( 'post_type:' ) );

                        if ( '' === $post_type ) {
                                continue;
                        }

                        if ( ! isset( $normalized['page_types'][ $post_type ] ) ) {
                                $normalized['page_types'][ $post_type ] = [
                                        'default'   => $normalized['default'],
                                        'templates' => [],
                                ];
                        }

                        $normalized['page_types'][ $post_type ]['default'] = $context_value;
                        continue;
                }

                if ( 0 === strpos( $context_key, 'template:' ) ) {
                        $template_file = substr( $context_key, strlen( 'template:' ) );

                        if ( '' === $template_file ) {
                                continue;
                        }

                        $assigned = false;

                        foreach ( $blueprint['page_types'] as $post_type => $meta ) {
                                if ( isset( $meta['templates'][ $template_file ] ) ) {
                                        if ( ! isset( $normalized['page_types'][ $post_type ] ) ) {
                                                $normalized['page_types'][ $post_type ] = [
                                                        'default'   => $normalized['default'],
                                                        'templates' => [],
                                                ];
                                        }

                                        $normalized['page_types'][ $post_type ]['templates'][ $template_file ] = $context_value;
                                        $assigned = true;
                                        break;
                                }
                        }

                        if ( ! $assigned ) {
                                if ( ! isset( $normalized['page_types']['__global__'] ) ) {
                                        $normalized['page_types']['__global__'] = [
                                                'default'   => $normalized['default'],
                                                'templates' => [],
                                        ];
                                }

                                $normalized['page_types']['__global__']['templates'][ $template_file ] = $context_value;
                        }
                }
        }

        return aimentor_normalize_document_provider_defaults_structure( $normalized );
}

function aimentor_sanitize_document_provider_defaults( $value ) {
        if ( ! is_array( $value ) ) {
                $value = [];
        }

        $value            = aimentor_normalize_document_provider_defaults_structure( $value );
        $defaults         = aimentor_get_document_provider_default_map();
        $blueprint        = aimentor_get_document_context_blueprint();
        $allowed_provider = array_keys( aimentor_get_provider_labels() );
        $sanitized        = [
                'default'    => [],
                'page_types' => [],
        ];

        $get_pair = static function( $entry ) {
                return [
                        'provider' => ( is_array( $entry ) && isset( $entry['provider'] ) ) ? $entry['provider'] : '',
                        'model'    => ( is_array( $entry ) && isset( $entry['model'] ) ) ? $entry['model'] : '',
                ];
        };

        $sanitize_pair = static function( $incoming, $fallback ) use ( $allowed_provider ) {
                $incoming = is_array( $incoming ) ? $incoming : [];
                $fallback = is_array( $fallback ) ? $fallback : [];

                $provider = isset( $incoming['provider'] ) ? sanitize_key( $incoming['provider'] ) : '';

                if ( ! in_array( $provider, $allowed_provider, true ) ) {
                        $provider = isset( $fallback['provider'] ) ? sanitize_key( $fallback['provider'] ) : ( $allowed_provider[0] ?? 'grok' );
                }

                $allowed_models = aimentor_flatten_allowed_models_for_provider( $provider );
                $model          = isset( $incoming['model'] ) ? sanitize_text_field( $incoming['model'] ) : '';

                if ( ! in_array( $model, $allowed_models, true ) ) {
                        $fallback_model = isset( $fallback['model'] ) ? sanitize_text_field( $fallback['model'] ) : '';
                        $model          = in_array( $fallback_model, $allowed_models, true ) ? $fallback_model : ( $allowed_models[0] ?? '' );
                }

                return [
                        'provider' => $provider,
                        'model'    => $model,
                ];
        };

        $incoming_default   = isset( $value['default'] ) ? $value['default'] : [];
        $fallback_default   = $defaults['default'] ?? [];
        $sanitized['default'] = $sanitize_pair( $incoming_default, $fallback_default );

        $incoming_page_types = isset( $value['page_types'] ) && is_array( $value['page_types'] ) ? $value['page_types'] : [];
        $default_page_types  = isset( $defaults['page_types'] ) && is_array( $defaults['page_types'] ) ? $defaults['page_types'] : [];

        foreach ( $blueprint['page_types'] as $post_type => $meta ) {
                $incoming_entry = isset( $incoming_page_types[ $post_type ] ) && is_array( $incoming_page_types[ $post_type ] )
                        ? $incoming_page_types[ $post_type ]
                        : [];

                $fallback_entry = isset( $default_page_types[ $post_type ] ) && is_array( $default_page_types[ $post_type ] )
                        ? $default_page_types[ $post_type ]
                        : [];

                $fallback_pair = $get_pair( $fallback_entry );

                if ( '' === $fallback_pair['provider'] && '' === $fallback_pair['model'] ) {
                        $fallback_pair = $sanitized['default'];
                }

                $type_default = $sanitize_pair(
                        $get_pair( $incoming_entry ),
                        $fallback_pair
                );

                $sanitized['page_types'][ $post_type ] = [
                        'provider'  => $type_default['provider'],
                        'model'     => $type_default['model'],
                        'templates' => [],
                ];

                $incoming_templates = isset( $incoming_entry['templates'] ) && is_array( $incoming_entry['templates'] )
                        ? $incoming_entry['templates']
                        : [];

                $fallback_templates = isset( $fallback_entry['templates'] ) && is_array( $fallback_entry['templates'] )
                        ? $fallback_entry['templates']
                        : [];

                if ( isset( $meta['templates'] ) && is_array( $meta['templates'] ) ) {
                        foreach ( $meta['templates'] as $template_file => $template_meta ) {
                                $incoming_template = isset( $incoming_templates[ $template_file ] ) ? $incoming_templates[ $template_file ] : [];
                                $fallback_template = isset( $fallback_templates[ $template_file ] ) ? $fallback_templates[ $template_file ] : $type_default;

                                $sanitized['page_types'][ $post_type ]['templates'][ $template_file ] = $sanitize_pair( $get_pair( $incoming_template ), $fallback_template );
                        }
                }

                foreach ( $incoming_templates as $template_file => $template_value ) {
                        if ( isset( $sanitized['page_types'][ $post_type ]['templates'][ $template_file ] ) ) {
                                continue;
                        }

                        $fallback_template = isset( $fallback_templates[ $template_file ] ) ? $fallback_templates[ $template_file ] : $type_default;
                        $sanitized['page_types'][ $post_type ]['templates'][ $template_file ] = $sanitize_pair( $get_pair( $template_value ), $fallback_template );
                }
        }

        foreach ( $incoming_page_types as $post_type => $incoming_entry ) {
                if ( isset( $sanitized['page_types'][ $post_type ] ) ) {
                        continue;
                }

                if ( ! is_array( $incoming_entry ) ) {
                        continue;
                }

                $fallback_entry = isset( $default_page_types[ $post_type ] ) && is_array( $default_page_types[ $post_type ] )
                        ? $default_page_types[ $post_type ]
                        : [];

                $fallback_pair = $get_pair( $fallback_entry );

                if ( '' === $fallback_pair['provider'] && '' === $fallback_pair['model'] ) {
                        $fallback_pair = $sanitized['default'];
                }

                $type_default = $sanitize_pair(
                        $get_pair( $incoming_entry ),
                        $fallback_pair
                );

                $sanitized['page_types'][ $post_type ] = [
                        'provider'  => $type_default['provider'],
                        'model'     => $type_default['model'],
                        'templates' => [],
                ];

                $incoming_templates = isset( $incoming_entry['templates'] ) && is_array( $incoming_entry['templates'] )
                        ? $incoming_entry['templates']
                        : [];

                $fallback_templates = isset( $fallback_entry['templates'] ) && is_array( $fallback_entry['templates'] )
                        ? $fallback_entry['templates']
                        : [];

                foreach ( $incoming_templates as $template_file => $template_value ) {
                        $fallback_template = isset( $fallback_templates[ $template_file ] ) ? $fallback_templates[ $template_file ] : $type_default;
                        $sanitized['page_types'][ $post_type ]['templates'][ $template_file ] = $sanitize_pair( $get_pair( $template_value ), $fallback_template );
                }
        }

        return $sanitized;
}

function aimentor_sanitize_model_presets( $value ) {
        $defaults = aimentor_get_provider_model_defaults();

        if ( ! is_array( $value ) ) {
                $value = [];
        }

        $sanitized = [];

        foreach ( $defaults as $provider => $tasks ) {
                $sanitized[ $provider ] = [];
                $allowed_models          = aimentor_flatten_allowed_models_for_provider( $provider );

                foreach ( $tasks as $task => $tiers ) {
                        $sanitized[ $provider ][ $task ] = [];

                        foreach ( $tiers as $tier => $default_model ) {
                                $incoming = isset( $value[ $provider ][ $task ][ $tier ] )
                                        ? sanitize_text_field( $value[ $provider ][ $task ][ $tier ] )
                                        : '';

                                $sanitized[ $provider ][ $task ][ $tier ] = in_array( $incoming, $allowed_models, true )
                                        ? $incoming
                                        : $default_model;
                        }
                }
        }

        return $sanitized;
}

function aimentor_sanitize_provider_models( $value ) {
        $defaults = aimentor_map_presets_to_legacy_defaults( aimentor_get_provider_model_defaults() );

        if ( ! is_array( $value ) ) {
                $value = [];
        }

        $sanitized = [];

        foreach ( $defaults as $provider => $default_model ) {
                $incoming       = isset( $value[ $provider ] ) ? sanitize_text_field( $value[ $provider ] ) : '';
                $allowed_models = aimentor_flatten_allowed_models_for_provider( $provider );

                $sanitized[ $provider ] = in_array( $incoming, $allowed_models, true )
                        ? $incoming
                        : $default_model;
        }

        return $sanitized;
}

function aimentor_get_model_presets() {
        $stored = get_option( 'aimentor_model_presets', [] );

        if ( ! is_array( $stored ) ) {
                $stored = [];
        }

        $presets = array_replace_recursive( aimentor_get_provider_model_defaults(), $stored );

        return aimentor_sanitize_model_presets( $presets );
}

function aimentor_get_document_provider_defaults() {
        $stored = get_option( 'aimentor_document_provider_defaults', [] );

        if ( ! is_array( $stored ) ) {
                $stored = [];
        }

        $stored  = aimentor_normalize_document_provider_defaults_structure( $stored );
        $defaults = aimentor_get_document_provider_default_map();
        $merged   = array_replace_recursive( $defaults, $stored );

        return aimentor_sanitize_document_provider_defaults( $merged );
}

function aimentor_get_provider_models() {
        $stored = get_option( 'aimentor_provider_models', [] );

        if ( ! is_array( $stored ) ) {
                $stored = [];
        }

        return aimentor_sanitize_provider_models( $stored );
}

function aimentor_sanitize_request_overrides( $value ) {
        $defaults  = aimentor_get_request_override_defaults();
        $sanitized = $defaults;

        if ( ! is_array( $value ) ) {
                return $sanitized;
        }

        foreach ( $defaults as $provider => $tasks ) {
                foreach ( array_keys( $tasks ) as $task ) {
                        $entry = isset( $value[ $provider ][ $task ] ) && is_array( $value[ $provider ][ $task ] )
                                ? $value[ $provider ][ $task ]
                                : [];

                        $temperature = isset( $entry['temperature'] ) ? trim( (string) $entry['temperature'] ) : '';

                        if ( '' !== $temperature ) {
                                $temperature = (float) $temperature;
                                $temperature = max( 0.0, min( 2.0, $temperature ) );
                                $sanitized[ $provider ][ $task ]['temperature'] = round( $temperature, 2 );
                        }

                        $timeout = isset( $entry['timeout'] ) ? $entry['timeout'] : '';

                        if ( '' !== $timeout ) {
                                $timeout = absint( $timeout );
                                $timeout = max( 5, min( 600, $timeout ) );
                                $sanitized[ $provider ][ $task ]['timeout'] = $timeout;
                        }
                }
        }

        return $sanitized;
}

function aimentor_get_request_overrides() {
        $stored = get_option( 'aimentor_request_overrides', [] );

        if ( ! is_array( $stored ) ) {
                $stored = [];
        }

        return aimentor_sanitize_request_overrides( $stored );
}

function aimentor_sanitize_model( $value ) {
        $value     = sanitize_text_field( $value );
        $allowed   = aimentor_flatten_allowed_models_for_provider( 'grok' );
        $defaults  = aimentor_map_presets_to_legacy_defaults( aimentor_get_provider_model_defaults() );
        $fallback  = $defaults['grok'] ?? '';

        return in_array( $value, $allowed, true ) ? $value : $fallback;
}

function aimentor_sanitize_openai_model( $value ) {
        $value     = sanitize_text_field( $value );
        $allowed   = aimentor_flatten_allowed_models_for_provider( 'openai' );
        $defaults  = aimentor_map_presets_to_legacy_defaults( aimentor_get_provider_model_defaults() );
        $fallback  = $defaults['openai'] ?? '';

        return in_array( $value, $allowed, true ) ? $value : $fallback;
}

function aimentor_sanitize_anthropic_model( $value ) {
        $value     = sanitize_text_field( $value );
        $allowed   = aimentor_flatten_allowed_models_for_provider( 'anthropic' );
        $defaults  = aimentor_map_presets_to_legacy_defaults( aimentor_get_provider_model_defaults() );
        $fallback  = $defaults['anthropic'] ?? '';

        return in_array( $value, $allowed, true ) ? $value : $fallback;
}

function aimentor_sync_legacy_model_options( $value, $old_value ) {
        $sanitized = aimentor_sanitize_provider_models( $value );
        $presets   = aimentor_get_provider_model_defaults();

        foreach ( $sanitized as $provider => $model ) {
                if ( '' === $model || ! isset( $presets[ $provider ] ) ) {
                        continue;
                }

                foreach ( $presets[ $provider ] as $task => $tiers ) {
                        if ( ! is_array( $tiers ) ) {
                                continue;
                        }

                        if ( isset( $presets[ $provider ][ $task ]['fast'] ) ) {
                                $presets[ $provider ][ $task ]['fast'] = $model;
                        }
                }
        }

        $sanitized_presets = aimentor_sanitize_model_presets( $presets );
        update_option( 'aimentor_model_presets', $sanitized_presets );

        if ( isset( $sanitized['grok'] ) ) {
                update_option( 'aimentor_model', aimentor_sanitize_model( $sanitized['grok'] ) );
        }

        if ( isset( $sanitized['anthropic'] ) ) {
                update_option( 'aimentor_anthropic_model', aimentor_sanitize_anthropic_model( $sanitized['anthropic'] ) );
        }

        if ( isset( $sanitized['openai'] ) ) {
                update_option( 'aimentor_openai_model', aimentor_sanitize_openai_model( $sanitized['openai'] ) );
        }

        return $sanitized;
}
add_filter( 'pre_update_option_aimentor_provider_models', 'aimentor_sync_legacy_model_options', 10, 2 );

function aimentor_sync_legacy_model_options_for_site_option( $value, $old_value, $option ) {
        return aimentor_sync_legacy_model_options( $value, $old_value );
}
add_filter( 'pre_update_site_option_aimentor_provider_models', 'aimentor_sync_legacy_model_options_for_site_option', 10, 3 );

function aimentor_sanitize_provider( $value ) {
        $allowed = array_keys( aimentor_get_provider_labels() );
        return in_array( $value, $allowed, true ) ? $value : 'grok';
}

function aimentor_parse_error_log_entry( $log_line ) {
        $log_line = trim( (string) $log_line );

        if ( '' === $log_line ) {
                return null;
        }

        $parts     = explode( ' - ', $log_line, 2 );
        $timestamp = isset( $parts[0] ) ? trim( (string) $parts[0] ) : __( 'Unknown', 'aimentor' );
        $raw_entry = isset( $parts[1] ) ? trim( (string) $parts[1] ) : '';
        $provider  = '';
        $message   = '' !== $raw_entry ? $raw_entry : $log_line;

        if ( '' !== $raw_entry ) {
                $decoded = json_decode( $raw_entry, true );

                if ( is_array( $decoded ) && isset( $decoded['message'] ) ) {
                        $message = (string) $decoded['message'];

                        if ( isset( $decoded['context']['provider'] ) ) {
                                $provider = (string) $decoded['context']['provider'];
                        }
                }
        }

        return [
                'timestamp' => '' !== $timestamp ? $timestamp : __( 'Unknown', 'aimentor' ),
                'provider'  => $provider,
                'message'   => $message,
        ];
}

function aimentor_get_error_log_entries( $args = [] ) {
        $defaults = [
                'provider' => '',
                'keyword'  => '',
                'limit'    => 10,
        ];

        $args = wp_parse_args( $args, $defaults );

        $provider_filter = sanitize_key( $args['provider'] );
        $keyword_filter  = sanitize_text_field( $args['keyword'] );
        $limit           = intval( $args['limit'] );

        if ( function_exists( 'aimentor_get_error_log_path' ) ) {
                $log_file = aimentor_get_error_log_path();
        } else {
                $log_file = plugin_dir_path( __FILE__ ) . 'aimentor-errors.log';
        }

        $is_readable = is_readable( $log_file );

        if ( ! $is_readable ) {
                return [
                        'entries'       => [],
                        'total_entries' => 0,
                        'log_file'      => $log_file,
                        'readable'      => false,
                ];
        }

        $lines = file( $log_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );

        if ( ! is_array( $lines ) || empty( $lines ) ) {
                return [
                        'entries'       => [],
                        'total_entries' => 0,
                        'log_file'      => $log_file,
                        'readable'      => true,
                ];
        }

        $entries       = [];
        $total_entries = 0;
        $keyword_match = function_exists( 'mb_stripos' ) ? 'mb_stripos' : 'stripos';

        foreach ( array_reverse( $lines ) as $line ) {
                $entry = aimentor_parse_error_log_entry( $line );

                if ( ! $entry ) {
                        continue;
                }

                ++$total_entries;

                if ( '' !== $provider_filter ) {
                        $entry_provider = sanitize_key( $entry['provider'] );

                        if ( '' === $entry_provider || $provider_filter !== $entry_provider ) {
                                continue;
                        }
                }

                if ( '' !== $keyword_filter ) {
                        $haystack = implode( ' ', array_filter( [ $entry['timestamp'], $entry['provider'], $entry['message'] ] ) );

                        if ( false === $keyword_match( $haystack, $keyword_filter ) ) {
                                continue;
                        }
                }

                $entries[] = $entry;

                if ( $limit > 0 && count( $entries ) >= $limit ) {
                        break;
                }
        }

        return [
                'entries'       => $entries,
                'total_entries' => $total_entries,
                'log_file'      => $log_file,
                'readable'      => true,
        ];
}

function aimentor_build_error_log_rows_html( $entries, $context = [] ) {
        $context = wp_parse_args(
                $context,
                [
                        'readable'      => true,
                        'had_filters'   => false,
                        'total_entries' => 0,
                ]
        );

        if ( ! $context['readable'] ) {
                return '<tr><td colspan="3">' . esc_html__( 'No errors logged yet or log file unavailable.', 'aimentor' ) . '</td></tr>';
        }

        if ( empty( $entries ) ) {
                if ( $context['had_filters'] && $context['total_entries'] > 0 ) {
                        return '<tr><td colspan="3">' . esc_html__( 'No log entries match your filters.', 'aimentor' ) . '</td></tr>';
                }

                return '<tr><td colspan="3">' . esc_html__( 'No errors logged yet or log file unavailable.', 'aimentor' ) . '</td></tr>';
        }

        $rows = '';

        foreach ( $entries as $entry ) {
                $rows .= '<tr>';
                $rows .= '<td>' . esc_html( $entry['timestamp'] ) . '</td>';
                $rows .= '<td>' . ( '' !== $entry['provider'] ? esc_html( $entry['provider'] ) : '&mdash;' ) . '</td>';
                $rows .= '<td>' . esc_html( $entry['message'] ) . '</td>';
                $rows .= '</tr>';
        }

        return $rows;
}

function aimentor_get_error_logs_ajax() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

        if ( ! wp_verify_nonce( $nonce, 'aimentor_error_log' ) && ! wp_verify_nonce( $nonce, 'jaggrok_error_log' ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'Security check failed.', 'aimentor' ),
                                'code'    => 'aimentor_invalid_nonce',
                                'nonce'   => wp_create_nonce( 'aimentor_error_log' ),
                        ],
                        403
                );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'Insufficient permissions to view the error log.', 'aimentor' ),
                                'code'    => 'aimentor_insufficient_permissions',
                                'nonce'   => wp_create_nonce( 'aimentor_error_log' ),
                        ],
                        403
                );
        }

        $provider = isset( $_POST['provider'] ) ? sanitize_key( wp_unslash( $_POST['provider'] ) ) : '';
        $keyword  = isset( $_POST['keyword'] ) ? sanitize_text_field( wp_unslash( $_POST['keyword'] ) ) : '';

        $entries = aimentor_get_error_log_entries(
                [
                        'provider' => $provider,
                        'keyword'  => $keyword,
                ]
        );

        $rows = aimentor_build_error_log_rows_html(
                $entries['entries'],
                [
                        'readable'      => $entries['readable'],
                        'had_filters'   => ( '' !== $provider || '' !== $keyword ),
                        'total_entries' => $entries['total_entries'],
                ]
        );

        wp_send_json_success(
                [
                        'rows'  => $rows,
                        'nonce' => wp_create_nonce( 'aimentor_error_log' ),
                ]
        );
}
add_action( 'wp_ajax_aimentor_get_error_logs', 'aimentor_get_error_logs_ajax' );
add_action( 'wp_ajax_jaggrok_get_error_logs', 'aimentor_get_error_logs_ajax' );

function aimentor_download_error_log_ajax() {
        $nonce = isset( $_REQUEST['nonce'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['nonce'] ) ) : '';

        if ( ! wp_verify_nonce( $nonce, 'aimentor_error_log' ) && ! wp_verify_nonce( $nonce, 'jaggrok_error_log' ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'Security check failed.', 'aimentor' ),
                                'code'    => 'aimentor_invalid_nonce',
                                'nonce'   => wp_create_nonce( 'aimentor_error_log' ),
                        ],
                        403
                );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'Insufficient permissions to download the error log.', 'aimentor' ),
                                'code'    => 'aimentor_insufficient_permissions',
                                'nonce'   => wp_create_nonce( 'aimentor_error_log' ),
                        ],
                        403
                );
        }

        if ( ! function_exists( 'aimentor_get_error_log_path' ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'Error log path is unavailable.', 'aimentor' ),
                                'code'    => 'aimentor_missing_log_path',
                                'nonce'   => wp_create_nonce( 'aimentor_error_log' ),
                        ],
                        500
                );
        }

        $log_file = aimentor_get_error_log_path();

        if ( empty( $log_file ) || ! file_exists( $log_file ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'The error log file could not be found.', 'aimentor' ),
                                'code'    => 'aimentor_missing_log_file',
                                'nonce'   => wp_create_nonce( 'aimentor_error_log' ),
                        ],
                        404
                );
        }

        if ( ! is_readable( $log_file ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'The error log file is not readable.', 'aimentor' ),
                                'code'    => 'aimentor_unreadable_log_file',
                                'nonce'   => wp_create_nonce( 'aimentor_error_log' ),
                        ],
                        500
                );
        }

        $contents = file_get_contents( $log_file );

        if ( false === $contents ) {
                wp_send_json_error(
                        [
                                'message' => __( 'Unable to read the error log file.', 'aimentor' ),
                                'code'    => 'aimentor_unreadable_log_file',
                                'nonce'   => wp_create_nonce( 'aimentor_error_log' ),
                        ],
                        500
                );
        }

        $filename  = basename( $log_file );
        $new_nonce = wp_create_nonce( 'aimentor_error_log' );

        nocache_headers();
        header( 'Content-Type: text/plain; charset=utf-8' );
        header( 'Content-Disposition: attachment; filename="' . $filename . '"' );
        header( 'Content-Length: ' . strlen( $contents ) );
        header( 'X-AiMentor-Log-Nonce: ' . $new_nonce );

        echo $contents; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        exit;
}
add_action( 'wp_ajax_aimentor_download_error_log', 'aimentor_download_error_log_ajax' );
add_action( 'wp_ajax_jaggrok_download_error_log', 'aimentor_download_error_log_ajax' );

function aimentor_clear_error_log_ajax() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

        if ( ! wp_verify_nonce( $nonce, 'aimentor_error_log' ) && ! wp_verify_nonce( $nonce, 'jaggrok_error_log' ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'Security check failed.', 'aimentor' ),
                                'code'    => 'aimentor_invalid_nonce',
                                'nonce'   => wp_create_nonce( 'aimentor_error_log' ),
                        ],
                        403
                );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'Insufficient permissions to clear the error log.', 'aimentor' ),
                                'code'    => 'aimentor_insufficient_permissions',
                                'nonce'   => wp_create_nonce( 'aimentor_error_log' ),
                        ],
                        403
                );
        }

        if ( ! function_exists( 'aimentor_get_error_log_path' ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'Error log path is unavailable.', 'aimentor' ),
                                'code'    => 'aimentor_missing_log_path',
                                'nonce'   => wp_create_nonce( 'aimentor_error_log' ),
                        ],
                        500
                );
        }

        $log_file = aimentor_get_error_log_path();

        if ( empty( $log_file ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'The error log file could not be determined.', 'aimentor' ),
                                'code'    => 'aimentor_missing_log_file',
                                'nonce'   => wp_create_nonce( 'aimentor_error_log' ),
                        ],
                        500
                );
        }

        $directory = dirname( $log_file );

        if ( file_exists( $log_file ) ) {
                if ( ! is_writable( $log_file ) ) {
                        wp_send_json_error(
                                [
                                        'message' => __( 'The error log file is not writable.', 'aimentor' ),
                                        'code'    => 'aimentor_unwritable_log_file',
                                        'nonce'   => wp_create_nonce( 'aimentor_error_log' ),
                                ],
                                500
                        );
                }

                $handle = @fopen( $log_file, 'cb' );

                if ( false === $handle ) {
                        wp_send_json_error(
                                [
                                        'message' => __( 'Unable to open the error log file.', 'aimentor' ),
                                        'code'    => 'aimentor_unwritable_log_file',
                                        'nonce'   => wp_create_nonce( 'aimentor_error_log' ),
                                ],
                                500
                        );
                }

                if ( function_exists( 'flock' ) ) {
                        @flock( $handle, LOCK_EX );
                }

                $truncated = @ftruncate( $handle, 0 );

                if ( function_exists( 'flock' ) ) {
                        @flock( $handle, LOCK_UN );
                }

                fclose( $handle );

                if ( ! $truncated ) {
                        wp_send_json_error(
                                [
                                        'message' => __( 'Unable to clear the error log file.', 'aimentor' ),
                                        'code'    => 'aimentor_unwritable_log_file',
                                        'nonce'   => wp_create_nonce( 'aimentor_error_log' ),
                                ],
                                500
                        );
                }

                clearstatcache( true, $log_file );
        } else {
                if ( ! file_exists( $directory ) ) {
                        if ( ! wp_mkdir_p( $directory ) ) {
                                wp_send_json_error(
                                        [
                                                'message' => __( 'Unable to prepare the error log directory.', 'aimentor' ),
                                                'code'    => 'aimentor_unwritable_log_file',
                                                'nonce'   => wp_create_nonce( 'aimentor_error_log' ),
                                        ],
                                        500
                                );
                        }
                }

                if ( file_exists( $directory ) && is_dir( $directory ) && ! file_exists( $log_file ) ) {
                        @touch( $log_file );
                }
        }

        wp_send_json_success(
                [
                        'message' => __( 'Error log cleared.', 'aimentor' ),
                        'nonce'   => wp_create_nonce( 'aimentor_error_log' ),
                ]
        );
}
add_action( 'wp_ajax_aimentor_clear_error_log', 'aimentor_clear_error_log_ajax' );
add_action( 'wp_ajax_jaggrok_clear_error_log', 'aimentor_clear_error_log_ajax' );

function aimentor_get_settings_view_model() {
        $defaults        = aimentor_get_default_options();
        $usage_metrics   = aimentor_get_provider_usage_summary();
        $usage_providers = isset( $usage_metrics['providers'] ) && is_array( $usage_metrics['providers'] ) ? $usage_metrics['providers'] : [];

        $is_network_admin      = function_exists( 'is_network_admin' ) && is_network_admin();
        $is_multisite_instance = function_exists( 'is_multisite' ) ? is_multisite() : false;
        $network_lock_enabled  = function_exists( 'aimentor_is_network_provider_lock_enabled' ) ? aimentor_is_network_provider_lock_enabled() : false;
        $provider_controls_locked = function_exists( 'aimentor_provider_controls_locked_for_request' )
                ? aimentor_provider_controls_locked_for_request()
                : ( $network_lock_enabled && ! $is_network_admin );

        $provider = get_option( 'aimentor_provider', $defaults['aimentor_provider'] );
        $api_keys = [
                'grok'      => get_option( 'aimentor_xai_api_key' ),
                'anthropic' => get_option( 'aimentor_anthropic_api_key' ),
                'openai'    => get_option( 'aimentor_openai_api_key' ),
        ];

        $models         = aimentor_get_provider_models();
        $allowed_models = aimentor_get_allowed_provider_models();

        $document_context_blueprint = aimentor_get_document_context_blueprint();
        $document_provider_defaults = aimentor_get_document_provider_defaults();
        $provider_labels_map        = aimentor_get_provider_labels();

        $page_type_defaults = isset( $document_provider_defaults['page_types'] ) && is_array( $document_provider_defaults['page_types'] )
                ? $document_provider_defaults['page_types']
                : [];
        $page_type_blueprint = isset( $document_context_blueprint['page_types'] ) && is_array( $document_context_blueprint['page_types'] )
                ? $document_context_blueprint['page_types']
                : [];

        $combined_page_types = $page_type_blueprint;

        foreach ( $page_type_defaults as $post_type => $defaults_entry ) {
                if ( '__global__' === $post_type ) {
                        continue;
                }

                if ( isset( $combined_page_types[ $post_type ] ) ) {
                        continue;
                }

                $template_map = [];

                if ( isset( $defaults_entry['templates'] ) && is_array( $defaults_entry['templates'] ) ) {
                        foreach ( $defaults_entry['templates'] as $template_file => $template_entry ) {
                                $template_map[ $template_file ] = [
                                        'key'   => 'template:' . $template_file,
                                        'label' => $template_file,
                                ];
                        }
                }

                $combined_page_types[ $post_type ] = [
                        'key'       => 'post_type:' . $post_type,
                        'label'     => ucfirst( trim( str_replace( [ '_', '-' ], ' ', (string) $post_type ) ) ),
                        'templates' => $template_map,
                ];
        }

        $brand_preferences         = aimentor_get_brand_preferences();
        $request_overrides         = aimentor_get_request_overrides();
        $request_override_defaults = aimentor_get_request_override_defaults();
        $advanced_has_overrides    = $request_overrides !== $request_override_defaults;

        $grok_model_labels = [
                'grok-3-mini' => __( 'Grok 3 Mini (Fast)', 'aimentor' ),
                'grok-3-beta' => __( 'Grok 3 Beta (Balanced) ★', 'aimentor' ),
                'grok-3'      => __( 'Grok 3 (Standard)', 'aimentor' ),
                'grok-4-mini' => __( 'Grok 4 Mini (Premium)', 'aimentor' ),
                'grok-4'      => __( 'Grok 4 (Flagship)', 'aimentor' ),
                'grok-4-code' => __( 'Grok 4 Code', 'aimentor' ),
        ];

        $anthropic_model_labels = [
                'claude-3-5-haiku'  => __( 'Claude 3.5 Haiku (Fast)', 'aimentor' ),
                'claude-3-5-sonnet' => __( 'Claude 3.5 Sonnet (Balanced) ★', 'aimentor' ),
                'claude-3-5-opus'   => __( 'Claude 3.5 Opus (Flagship)', 'aimentor' ),
                'claude-3-opus'     => __( 'Claude 3 Opus (Legacy)', 'aimentor' ),
        ];

        $openai_model_labels = [
                'gpt-4o-mini'  => __( 'GPT-4o mini (Balanced) ★', 'aimentor' ),
                'gpt-4o'       => __( 'GPT-4o (Flagship)', 'aimentor' ),
                'gpt-4.1'      => __( 'GPT-4.1 (Reasoning)', 'aimentor' ),
                'gpt-4.1-mini' => __( 'GPT-4.1 mini (Fast)', 'aimentor' ),
                'gpt-4.1-nano' => __( 'GPT-4.1 nano (Edge)', 'aimentor' ),
                'o4-mini'      => __( 'o4-mini (Preview)', 'aimentor' ),
                'o4'           => __( 'o4 (Preview)', 'aimentor' ),
        ];

        $provider_statuses     = aimentor_get_provider_test_statuses();
        $provider_status_views = [];

        foreach ( $provider_labels_map as $provider_key => $provider_label ) {
                $current_status = $provider_statuses[ $provider_key ] ?? [ 'status' => '', 'message' => '', 'timestamp' => 0 ];
                $provider_status_views[ $provider_key ] = aimentor_format_provider_status_for_display( $provider_key, $current_status );
        }

        $health_checks_enabled       = aimentor_health_checks_enabled();
        $health_check_alerts_enabled = aimentor_health_check_alerts_enabled();
        $health_check_recipients     = aimentor_sanitize_health_check_recipients( get_option( 'aimentor_health_check_recipients', $defaults['aimentor_health_check_recipients'] ) );
        $health_check_threshold      = aimentor_get_health_check_failure_threshold();

        $auto_updates_setting_enabled = function_exists( 'aimentor_auto_updates_enabled' ) ? aimentor_auto_updates_enabled() : true;
        $auto_updates_active          = function_exists( 'aimentor_auto_updates_active' ) ? aimentor_auto_updates_active() : $auto_updates_setting_enabled;

        $has_api_key          = ! empty( $api_keys['grok'] ) || ! empty( $api_keys['anthropic'] ) || ! empty( $api_keys['openai'] );
        $provider_tested      = (bool) get_option( 'aimentor_api_tested', $defaults['aimentor_api_tested'] );
        $onboarding_dismissed = 'yes' === get_option( 'aimentor_onboarding_dismissed', $defaults['aimentor_onboarding_dismissed'] );
        $should_show_onboarding = ! $onboarding_dismissed && ( ! $has_api_key || ! $provider_tested );

        return [
                'defaults'                    => $defaults,
                'usage_metrics'               => $usage_metrics,
                'usage_providers'             => $usage_providers,
                'is_network_admin'            => $is_network_admin,
                'is_multisite_instance'       => $is_multisite_instance,
                'network_lock_enabled'        => $network_lock_enabled,
                'provider_controls_locked'    => $provider_controls_locked,
                'provider'                    => $provider,
                'api_keys'                    => $api_keys,
                'models'                      => $models,
                'allowed_models'              => $allowed_models,
                'document_context_blueprint'  => $document_context_blueprint,
                'document_provider_defaults'  => $document_provider_defaults,
                'provider_labels_map'         => $provider_labels_map,
                'page_type_defaults'          => $page_type_defaults,
                'page_type_blueprint'         => $page_type_blueprint,
                'combined_page_types'         => $combined_page_types,
                'brand_preferences'           => $brand_preferences,
                'request_overrides'           => $request_overrides,
                'request_override_defaults'   => $request_override_defaults,
                'advanced_has_overrides'      => $advanced_has_overrides,
                'grok_model_labels'           => $grok_model_labels,
                'anthropic_model_labels'      => $anthropic_model_labels,
                'openai_model_labels'         => $openai_model_labels,
                'provider_statuses'           => $provider_statuses,
                'provider_status_views'       => $provider_status_views,
                'health_checks_enabled'       => $health_checks_enabled,
                'health_check_alerts_enabled' => $health_check_alerts_enabled,
                'health_check_recipients'     => $health_check_recipients,
                'health_check_threshold'      => $health_check_threshold,
                'auto_updates_setting_enabled' => $auto_updates_setting_enabled,
                'auto_updates_active'         => $auto_updates_active,
                'has_api_key'                 => $has_api_key,
                'provider_tested'             => $provider_tested,
                'onboarding_dismissed'        => $onboarding_dismissed,
                'should_show_onboarding'      => $should_show_onboarding,
        ];
}

function aimentor_get_settings_tabs() {
        $tabs = [
                'overview'         => [
                        'label'      => __( 'Overview', 'aimentor' ),
                        'callback'   => 'aimentor_render_settings_tab_overview',
                        'capability' => 'manage_options',
                ],
                'provider'         => [
                        'label'      => __( 'Provider Setup', 'aimentor' ),
                        'callback'   => 'aimentor_render_settings_tab_provider',
                        'capability' => 'manage_options',
                ],
                'defaults'         => [
                        'label'      => __( 'Defaults', 'aimentor' ),
                        'callback'   => 'aimentor_render_settings_tab_defaults',
                        'capability' => 'manage_options',
                ],
                'brand-automation' => [
                        'label'      => __( 'Brand & Automation', 'aimentor' ),
                        'callback'   => 'aimentor_render_settings_tab_brand',
                        'capability' => 'manage_options',
                ],
                'frame-library'    => [
                        'label'      => __( 'Frame Library', 'aimentor' ),
                        'callback'   => 'aimentor_render_settings_tab_frame_library',
                        'capability' => 'manage_options',
                ],
                'saved-prompts'    => [
                        'label'      => __( 'Saved Prompts', 'aimentor' ),
                        'callback'   => 'aimentor_render_settings_tab_saved_prompts',
                        'capability' => 'manage_options',
                ],
                'logs'             => [
                        'label'      => __( 'Logs', 'aimentor' ),
                        'callback'   => 'aimentor_render_settings_tab_logs',
                        'capability' => 'manage_options',
                ],
        ];

        return apply_filters( 'aimentor_settings_tabs', $tabs );
}

function aimentor_get_settings_tab_nonce() {
        static $nonce = null;

        if ( null === $nonce ) {
                $nonce = wp_create_nonce( 'aimentor_settings_tab' );
        }

        return $nonce;
}

function aimentor_render_settings_tab_overview() {
        $view_model       = aimentor_get_settings_view_model();
        $support_sections = aimentor_get_settings_support_resources();
        $rest_endpoint    = rest_url( 'aimentor/v1/generate' );

        ob_start();
        extract( $view_model, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract.extract
        include plugin_dir_path( __FILE__ ) . 'admin/settings/tab-overview.php';

        return (string) ob_get_clean();
}

function aimentor_render_settings_tab_provider() {
        $view_model       = aimentor_get_settings_view_model();
        $support_sections = aimentor_get_settings_support_resources();

        ob_start();
        extract( $view_model, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract.extract
        include plugin_dir_path( __FILE__ ) . 'admin/settings/tab-provider.php';

        return (string) ob_get_clean();
}

function aimentor_render_settings_tab_defaults() {
        $view_model       = aimentor_get_settings_view_model();
        $support_sections = aimentor_get_settings_support_resources();

        ob_start();
        extract( $view_model, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract.extract
        include plugin_dir_path( __FILE__ ) . 'admin/settings/tab-defaults.php';

        return (string) ob_get_clean();
}

function aimentor_render_settings_tab_brand() {
        $view_model       = aimentor_get_settings_view_model();
        $support_sections = aimentor_get_settings_support_resources();

        ob_start();
        extract( $view_model, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract.extract
        include plugin_dir_path( __FILE__ ) . 'admin/settings/tab-brand.php';

        return (string) ob_get_clean();
}

function aimentor_render_settings_tab_frame_library() {
        $provider_labels = aimentor_get_provider_labels();
        $posts           = aimentor_get_frame_library_candidates(
                [
                        'posts_per_page' => 40,
                ]
        );
        $items           = [];

        foreach ( $posts as $post ) {
                $post_id      = $post->ID;
                $enabled      = 'yes' === get_post_meta( $post_id, '_aimentor_frame_enabled', true );
                $summary_meta = get_post_meta( $post_id, '_aimentor_frame_summary', true );
                $summary      = $summary_meta ? sanitize_textarea_field( $summary_meta ) : sanitize_text_field( $post->post_excerpt );
                $sections     = aimentor_get_frame_sections( $post_id );
                $sections_str = implode( "\n", $sections );
                $provider     = sanitize_key( get_post_meta( $post_id, '_aimentor_provider', true ) );
                $model        = sanitize_text_field( get_post_meta( $post_id, '_aimentor_model', true ) );
                $task         = sanitize_key( get_post_meta( $post_id, '_aimentor_task', true ) );
                $tier         = sanitize_key( get_post_meta( $post_id, '_aimentor_tier', true ) );
                $prompt       = sanitize_textarea_field( get_post_meta( $post_id, '_aimentor_prompt', true ) );
                $preview_id   = absint( get_post_meta( $post_id, '_aimentor_frame_preview_id', true ) );

                if ( ! $preview_id ) {
                        $preview_id = absint( get_post_thumbnail_id( $post_id ) );
                }

                $preview_url = $preview_id ? wp_get_attachment_image_url( $preview_id, 'medium' ) : '';
                $modified    = mysql2date( 'U', $post->post_modified_gmt ? $post->post_modified_gmt : $post->post_modified, false );
                $modified_h  = $modified ? human_time_diff( $modified, current_time( 'timestamp' ) ) : '';

                $items[] = [
                        'id'            => $post_id,
                        'title'         => get_the_title( $post ),
                        'enabled'       => $enabled,
                        'status'        => $post->post_status,
                        'summary'       => $summary,
                        'sections'      => $sections,
                        'sections_text' => $sections_str,
                        'provider'      => $provider,
                        'provider_label' => isset( $provider_labels[ $provider ] ) ? $provider_labels[ $provider ] : strtoupper( $provider ),
                        'model'         => $model,
                        'task'          => $task,
                        'tier'          => $tier,
                        'prompt'        => $prompt,
                        'preview_id'    => $preview_id,
                        'preview_url'   => $preview_url,
                        'modified'      => $modified,
                        'modified_human' => $modified_h,
                        'edit_link'     => get_edit_post_link( $post_id, '' ),
                ];
        }

        $status_flag = isset( $_GET['aimentor_frame_status'] ) ? sanitize_key( wp_unslash( $_GET['aimentor_frame_status'] ) ) : '';
        $status_msg  = '';
        $status_type = '';

        if ( 'updated' === $status_flag ) {
                $status_type = 'updated';
                $status_msg  = __( 'Frame selections saved.', 'aimentor' );
        } elseif ( 'error' === $status_flag ) {
                $status_type = 'error';
                $status_msg  = __( 'Unable to update the frame library. Please try again.', 'aimentor' );
        }

        $archive_enabled = aimentor_is_layout_archival_enabled();

        $frame_library_endpoint = esc_url( rest_url( 'aimentor/v1/frames' ) );
        $rest_nonce             = wp_create_nonce( 'wp_rest' );
        $frame_presets          = function_exists( 'aimentor_get_frame_prompt_presets' ) ? aimentor_get_frame_prompt_presets() : [];

        ob_start();
        include plugin_dir_path( __FILE__ ) . 'admin/settings/tab-frame-library.php';

        return (string) ob_get_clean();
}

function aimentor_render_settings_tab_saved_prompts() {
        $view_model              = aimentor_get_settings_view_model();
        $support_sections        = aimentor_get_settings_support_resources();
        $saved_prompts_payload   = aimentor_get_saved_prompts_payload();
        $saved_prompts_rest_nonce = wp_create_nonce( 'wp_rest' );
        $saved_prompts_nonce     = wp_create_nonce( 'aimentor_saved_prompts' );

        ob_start();
        extract( $view_model, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract.extract
        include plugin_dir_path( __FILE__ ) . 'admin/settings/tab-saved-prompts.php';

        return (string) ob_get_clean();
}

function aimentor_render_settings_tab_logs() {
        $view_model       = aimentor_get_settings_view_model();
        $support_sections = aimentor_get_settings_support_resources();
        $history_entries  = aimentor_get_generation_history();
        $provider_meta    = aimentor_get_provider_meta_map();

        $log_filter_provider = isset( $_GET['provider'] ) ? sanitize_key( wp_unslash( $_GET['provider'] ) ) : '';
        $log_filter_keyword  = isset( $_GET['keyword'] ) ? sanitize_text_field( wp_unslash( $_GET['keyword'] ) ) : '';

        if ( 'all' === $log_filter_provider ) {
                $log_filter_provider = '';
        }

        $log_entries = aimentor_get_error_log_entries(
                [
                        'provider' => $log_filter_provider,
                        'keyword'  => $log_filter_keyword,
                ]
        );

        $log_rows = aimentor_build_error_log_rows_html(
                $log_entries['entries'],
                [
                        'readable'      => $log_entries['readable'],
                        'had_filters'   => ( '' !== $log_filter_provider || '' !== $log_filter_keyword ),
                        'total_entries' => $log_entries['total_entries'],
                ]
        );

        $log_file        = $log_entries['log_file'];
        $error_log_nonce = wp_create_nonce( 'aimentor_error_log' );

        ob_start();
        extract( $view_model, EXTR_SKIP ); // phpcs:ignore WordPress.PHP.DontExtract.extract
        include plugin_dir_path( __FILE__ ) . 'admin/settings/tab-logs.php';

        return (string) ob_get_clean();
}

function aimentor_handle_frame_library_form() {
        if ( ! current_user_can( 'manage_options' ) ) {
                wp_die( esc_html__( 'You do not have permission to manage frames.', 'aimentor' ) );
        }

        check_admin_referer( 'aimentor_save_frame_library', 'aimentor_frame_library_nonce' );

        $layouts = isset( $_POST['layouts'] ) && is_array( $_POST['layouts'] ) ? $_POST['layouts'] : [];

        foreach ( $layouts as $post_id => $fields ) {
                $post_id = absint( $post_id );

                if ( ! $post_id || 'ai_layout' !== get_post_type( $post_id ) ) {
                        continue;
                }

                $enabled = isset( $fields['enabled'] ) && 'yes' === $fields['enabled'] ? 'yes' : '';
                update_post_meta( $post_id, '_aimentor_frame_enabled', $enabled );

                $summary = isset( $fields['summary'] ) ? sanitize_textarea_field( wp_unslash( $fields['summary'] ) ) : '';

                if ( '' === $summary ) {
                        delete_post_meta( $post_id, '_aimentor_frame_summary' );
                } else {
                        update_post_meta( $post_id, '_aimentor_frame_summary', $summary );
                }

                $sections_input = isset( $fields['sections'] ) ? $fields['sections'] : '';
                $sections       = aimentor_normalize_frame_sections( $sections_input );

                if ( empty( $sections ) ) {
                        delete_post_meta( $post_id, '_aimentor_frame_sections' );
                } else {
                        update_post_meta( $post_id, '_aimentor_frame_sections', wp_json_encode( $sections ) );
                }

                $provider = isset( $fields['provider'] ) ? sanitize_key( $fields['provider'] ) : '';
                $model    = isset( $fields['model'] ) ? sanitize_text_field( wp_unslash( $fields['model'] ) ) : '';
                $task     = isset( $fields['task'] ) ? sanitize_key( $fields['task'] ) : '';
                $tier     = isset( $fields['tier'] ) ? sanitize_key( $fields['tier'] ) : '';
                $prompt   = isset( $fields['prompt'] ) ? sanitize_textarea_field( wp_unslash( $fields['prompt'] ) ) : '';

                if ( '' === $provider ) {
                        delete_post_meta( $post_id, '_aimentor_provider' );
                } else {
                        update_post_meta( $post_id, '_aimentor_provider', $provider );
                }

                if ( '' === $model ) {
                        delete_post_meta( $post_id, '_aimentor_model' );
                } else {
                        update_post_meta( $post_id, '_aimentor_model', $model );
                }

                if ( '' === $task ) {
                        delete_post_meta( $post_id, '_aimentor_task' );
                } else {
                        update_post_meta( $post_id, '_aimentor_task', $task );
                }

                if ( '' === $tier ) {
                        delete_post_meta( $post_id, '_aimentor_tier' );
                } else {
                        update_post_meta( $post_id, '_aimentor_tier', $tier );
                }

                if ( '' === $prompt ) {
                        delete_post_meta( $post_id, '_aimentor_prompt' );
                } else {
                        update_post_meta( $post_id, '_aimentor_prompt', $prompt );
                }

                $preview_id = isset( $fields['preview_id'] ) ? absint( $fields['preview_id'] ) : 0;

                if ( $preview_id ) {
                        update_post_meta( $post_id, '_aimentor_frame_preview_id', $preview_id );
                        set_post_thumbnail( $post_id, $preview_id );
                } else {
                        delete_post_meta( $post_id, '_aimentor_frame_preview_id' );
                        delete_post_thumbnail( $post_id );
                }
        }

        $redirect = add_query_arg(
                [
                        'page'                  => 'aimentor-settings',
                        'tab'                   => 'frame-library',
                        'aimentor_frame_status' => 'updated',
                ],
                admin_url( 'options-general.php' )
        );

        wp_safe_redirect( $redirect );
        exit;
}
add_action( 'admin_post_aimentor_save_frame_library', 'aimentor_handle_frame_library_form' );

function aimentor_settings_page_callback() {
        $tabs = aimentor_get_settings_tabs();

        if ( empty( $tabs ) ) {
                return;
        }

        $tab_slugs   = array_keys( $tabs );
        $default_tab = reset( $tab_slugs );
        $requested   = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : '';

        if ( $requested && isset( $tabs[ $requested ] ) ) {
                $default_tab = $requested;
        }

        $tab_nonce = aimentor_get_settings_tab_nonce();

        $initial_html = '';

        if ( isset( $tabs[ $default_tab ]['callback'] ) && is_callable( $tabs[ $default_tab ]['callback'] ) ) {
                $initial_html = (string) call_user_func( $tabs[ $default_tab ]['callback'] );
        }

        $page_url = add_query_arg(
                [
                        'page' => 'aimentor-settings',
                ],
                admin_url( 'options-general.php' )
        );

        $tab_panel_id      = 'aimentor-settings-tab-content';
        $default_button_id = 'aimentor-tab-' . sanitize_html_class( $default_tab ) . '-tab';

        echo '<div class="wrap">';
        echo '<h1>' . esc_html__( 'AiMentor Elementor Settings', 'aimentor' ) . '</h1>';

        echo '<div class="aimentor-settings-tabs" data-tab-action="aimentor_load_settings_tab" data-tab-nonce="' . esc_attr( $tab_nonce ) . '" data-default-tab="' . esc_attr( $default_tab ) . '">';
        echo '<div class="aimentor-settings-tabs__nav" role="tablist" aria-label="' . esc_attr__( 'AiMentor settings sections', 'aimentor' ) . '">';

        foreach ( $tabs as $slug => $tab ) {
                $button_id = 'aimentor-tab-' . sanitize_html_class( $slug ) . '-tab';
                $is_active = ( $slug === $default_tab );
                $href      = add_query_arg( 'tab', $slug, $page_url );

                if ( $is_active ) {
                        $default_button_id = $button_id;
                }

                echo '<a href="' . esc_url( $href ) . '" class="aimentor-settings-tabs__tab nav-tab' . ( $is_active ? ' nav-tab-active' : '' ) . '" role="tab" id="' . esc_attr( $button_id ) . '" aria-controls="' . esc_attr( $tab_panel_id ) . '" aria-selected="' . ( $is_active ? 'true' : 'false' ) . '" tabindex="' . ( $is_active ? '0' : '-1' ) . '" data-tab="' . esc_attr( $slug ) . '">';
                echo esc_html( $tab['label'] );
                echo '</a>';
        }

        echo '</div>';
        echo '<div id="' . esc_attr( $tab_panel_id ) . '" class="aimentor-settings-tabs__content" role="tabpanel" aria-live="polite" aria-labelledby="' . esc_attr( $default_button_id ) . '" aria-busy="false" data-active-tab="' . esc_attr( $default_tab ) . '" tabindex="0"></div>';

        if ( '' !== $initial_html ) {
                echo '<div id="aimentor-settings-tab-fallback" class="aimentor-settings-tab-fallback" data-tab="' . esc_attr( $default_tab ) . '">';
                echo $initial_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo '</div>';
        }

        echo '</div>';
        echo '</div>';
}

function aimentor_load_settings_tab_ajax() {
        if ( ! current_user_can( 'manage_options' ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'You do not have permission to view this tab.', 'aimentor' ),
                        ],
                        403
                );
        }

        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

        if ( ! wp_verify_nonce( $nonce, 'aimentor_settings_tab' ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'Security check failed.', 'aimentor' ),
                        ],
                        403
                );
        }

        $requested_tab = isset( $_POST['tab'] ) ? sanitize_key( wp_unslash( $_POST['tab'] ) ) : '';
        $tabs          = aimentor_get_settings_tabs();

        if ( '' === $requested_tab || ! isset( $tabs[ $requested_tab ] ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'Unknown settings tab requested.', 'aimentor' ),
                        ],
                        400
                );
        }

        $tab = $tabs[ $requested_tab ];

        if ( isset( $tab['capability'] ) && ! current_user_can( $tab['capability'] ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'You do not have permission to view this tab.', 'aimentor' ),
                        ],
                        403
                );
        }

        $callback = isset( $tab['callback'] ) ? $tab['callback'] : null;

        if ( ! $callback || ! is_callable( $callback ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'Unable to load the requested tab.', 'aimentor' ),
                        ],
                        500
                );
        }

        $html = (string) call_user_func( $callback );

        wp_send_json_success(
                [
                        'html' => $html,
                ]
        );
}
add_action( 'wp_ajax_aimentor_load_settings_tab', 'aimentor_load_settings_tab_ajax' );

function aimentor_dismiss_onboarding_notice() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

        if ( ! wp_verify_nonce( $nonce, 'aimentor_onboarding' ) && ! wp_verify_nonce( $nonce, 'jaggrok_onboarding' ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'Security check failed.', 'aimentor' ),
                                'code'    => 'aimentor_invalid_nonce',
                        ],
                        403
                );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'Insufficient permissions to update onboarding state.', 'aimentor' ),
                                'code'    => 'aimentor_insufficient_permissions',
                        ],
                        403
                );
        }

        update_option( 'aimentor_onboarding_dismissed', 'yes' );

        wp_send_json_success();
}
add_action( 'wp_ajax_aimentor_dismiss_onboarding', 'aimentor_dismiss_onboarding_notice' );
add_action( 'wp_ajax_jaggrok_dismiss_onboarding', 'aimentor_dismiss_onboarding_notice' );

function aimentor_get_usage_metrics_ajax() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

        if ( ! wp_verify_nonce( $nonce, 'aimentor_usage_metrics' ) && ! wp_verify_nonce( $nonce, 'jaggrok_usage_metrics' ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'Security check failed.', 'aimentor' ),
                                'code'    => 'aimentor_invalid_nonce',
                        ],
                        403
                );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'Insufficient permissions to view usage metrics.', 'aimentor' ),
                                'code'    => 'aimentor_insufficient_permissions',
                        ],
                        403
                );
        }

        wp_send_json_success(
                [
                        'metrics' => aimentor_get_provider_usage_summary(),
                ]
        );
}
add_action( 'wp_ajax_aimentor_get_usage_metrics', 'aimentor_get_usage_metrics_ajax' );
add_action( 'wp_ajax_jaggrok_get_usage_metrics', 'aimentor_get_usage_metrics_ajax' );

// AJAX Test API (v1.3.18 - PROVIDER TEST METRICS)
function aimentor_execute_provider_test( $provider_key, $api_key, $args = [] ) {
        $args = wp_parse_args(
                $args,
                [
                        'origin'            => 'test',
                        'update_status'     => true,
                        'record_usage'      => true,
                        'update_api_tested' => false,
                        'persist_api_key'   => true,
                        'user_id'           => get_current_user_id(),
                ]
        );

        $provider_labels = aimentor_get_provider_labels();

        if ( ! array_key_exists( $provider_key, $provider_labels ) ) {
                return new WP_Error( 'aimentor_invalid_provider', __( 'Invalid provider selected.', 'aimentor' ) );
        }

        $label   = $provider_labels[ $provider_key ];
        $api_key = sanitize_text_field( $api_key );

        if ( '' === $api_key ) {
                $message = sprintf( __( '%s API key is required to test the connection.', 'aimentor' ), $label );

                if ( $args['update_status'] ) {
                        aimentor_update_provider_test_status( $provider_key, 'error', $message );
                }

                return new WP_Error( 'aimentor_missing_api_key', $message );
        }

        $models        = aimentor_get_provider_models();
        $model_default = aimentor_get_provider_model_defaults();

        switch ( $provider_key ) {
                case 'openai':
                        $model = $models['openai'] ?? ( $model_default['openai'] ?? '' );

                        if ( $args['persist_api_key'] ) {
                                update_option( 'aimentor_openai_api_key', $api_key );
                        }
                        break;
                case 'anthropic':
                        $model = $models['anthropic'] ?? ( $model_default['anthropic'] ?? '' );

                        if ( $args['persist_api_key'] ) {
                                update_option( 'aimentor_anthropic_api_key', $api_key );
                        }
                        break;
                case 'grok':
                default:
                        $model = $models['grok'] ?? ( $model_default['grok'] ?? '' );

                        if ( $args['persist_api_key'] ) {
                                update_option( 'aimentor_xai_api_key', $api_key );
                        }
                        break;
        }

        $provider = aimentor_get_active_provider( $provider_key );

        if ( ! $provider instanceof AiMentor_Provider_Interface ) {
                $message = __( 'Provider configuration error.', 'aimentor' );

                if ( function_exists( 'aimentor_log_error' ) ) {
                        aimentor_log_error(
                                $message,
                                [
                                        'provider' => $provider_key,
                                        'model'    => $model,
                                        'user_id'  => $args['user_id'],
                                ]
                        );
                }

                if ( $args['update_status'] ) {
                        aimentor_update_provider_test_status( $provider_key, 'error', $message );
                }

                return new WP_Error( 'aimentor_provider_configuration', $message );
        }

        $result = $provider->request(
                __( 'Respond with a short confirmation to verify the AiMentor Elementor integration.', 'aimentor' ),
                [
                        'api_key'    => $api_key,
                        'model'      => $model,
                        'max_tokens' => 32,
                        'timeout'    => 20,
                ]
        );

        if ( is_wp_error( $result ) ) {
                if ( $args['record_usage'] ) {
                        aimentor_record_provider_usage(
                                $provider_key,
                                'error',
                                [
                                        'model'  => $model,
                                        'origin' => $args['origin'],
                                ]
                        );
                }

                $error_message = sprintf( __( '%1$s connection failed: %2$s', 'aimentor' ), $label, $result->get_error_message() );

                if ( function_exists( 'aimentor_log_error' ) ) {
                        aimentor_log_error(
                                $error_message . ' | Details: ' . wp_json_encode( $result->get_error_data() ),
                                [
                                        'provider' => $provider_key,
                                        'model'    => $model,
                                        'user_id'  => $args['user_id'],
                                ]
                        );
                }

                if ( $args['update_status'] ) {
                        aimentor_update_provider_test_status( $provider_key, 'error', $error_message );
                }

                return new WP_Error( 'aimentor_connection_failed', $error_message );
        }

        if ( ! is_array( $result ) || ! isset( $result['type'] ) ) {
                        if ( $args['record_usage'] ) {
                                aimentor_record_provider_usage(
                                        $provider_key,
                                        'error',
                                        [
                                                'model'  => $model,
                                                'origin' => $args['origin'],
                                        ]
                                );
                        }

                        $error_message = sprintf( __( '%s returned an unexpected response.', 'aimentor' ), $label );

                        if ( function_exists( 'aimentor_log_error' ) ) {
                                aimentor_log_error(
                                        $error_message . ' | Result: ' . wp_json_encode( $result ),
                                        [
                                                'provider' => $provider_key,
                                                'model'    => $model,
                                                'user_id'  => $args['user_id'],
                                        ]
                                );
                        }

                        if ( $args['update_status'] ) {
                                aimentor_update_provider_test_status( $provider_key, 'error', $error_message );
                        }

                        return new WP_Error( 'aimentor_unexpected_response', $error_message );
        }

        if ( $args['record_usage'] ) {
                aimentor_record_provider_usage(
                        $provider_key,
                        'success',
                        [
                                'model'  => $model,
                                'origin' => $args['origin'],
                        ]
                );
        }

        $success_message = sprintf( __( '%s API key verified successfully.', 'aimentor' ), $label );

        if ( $args['update_status'] ) {
                aimentor_update_provider_test_status( $provider_key, 'success', $success_message );
        }

        if ( $args['update_api_tested'] ) {
                update_option( 'aimentor_api_tested', true );
        }

        return [
                'provider' => $provider_key,
                'model'    => $model,
                'message'  => $success_message,
                'response' => $result,
        ];
}

/**
 * Re-run the provider connection test logic used by aimentor_test_api_connection().
 *
 * @param string $provider_key Provider identifier.
 * @param string $api_key      API key to validate.
 * @param array  $args         Optional overrides for aimentor_execute_provider_test().
 *
 * @return array|WP_Error
 */
function aimentor_run_provider_connection_test( $provider_key, $api_key, $args = [] ) {
        $args = wp_parse_args(
                $args,
                [
                        'origin'            => 'test',
                        'update_status'     => true,
                        'record_usage'      => true,
                        'update_api_tested' => true,
                        'persist_api_key'   => true,
                        'user_id'           => get_current_user_id(),
                ]
        );

        return aimentor_execute_provider_test( $provider_key, $api_key, $args );
}

function aimentor_test_api_connection() {
        $nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';

        if ( ! wp_verify_nonce( $nonce, 'aimentor_test' ) && ! wp_verify_nonce( $nonce, 'jaggrok_test' ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'Security check failed.', 'aimentor' ),
                                'code'    => 'aimentor_invalid_nonce',
                        ],
                        403
                );
        }

        if ( ! current_user_can( 'manage_options' ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'Insufficient permissions to test the API connection.', 'aimentor' ),
                                'code'    => 'aimentor_insufficient_permissions',
                        ],
                        403
                );
        }

        $provider_key    = isset( $_POST['provider'] ) ? sanitize_text_field( wp_unslash( $_POST['provider'] ) ) : 'grok';
        $provider_labels = aimentor_get_provider_labels();

        if ( ! array_key_exists( $provider_key, $provider_labels ) ) {
                wp_send_json_error(
                        [
                                'message' => __( 'Invalid provider selected.', 'aimentor' ),
                                'code'    => 'aimentor_invalid_provider',
                                'badge_state' => 'error',
                                'badge_label' => __( 'Error', 'aimentor' ),
                                'description' => __( 'Select a valid provider and try again.', 'aimentor' ),
                                'provider'    => $provider_key,
                        ],
                        400
                );
        }

        $api_key = isset( $_POST['api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['api_key'] ) ) : '';

        $result = aimentor_run_provider_connection_test(
                $provider_key,
                $api_key,
                [
                        'update_api_tested' => true,
                        'user_id'           => get_current_user_id(),
                ]
        );

        if ( is_wp_error( $result ) ) {
                $status = aimentor_get_provider_test_statuses();
                $view   = aimentor_format_provider_status_for_display( $provider_key, $status[ $provider_key ] ?? [] );
                $view['provider'] = $provider_key;
                $view['message']  = $result->get_error_message();

                aimentor_maybe_increment_provider_test_counters(
                        $provider_key,
                        $status[ $provider_key ]['status'] ?? '',
                        $status[ $provider_key ]['timestamp'] ?? 0
                );

                wp_send_json_error( $view, 400 );
        }

        $status = aimentor_get_provider_test_statuses();
        $view   = aimentor_format_provider_status_for_display( $provider_key, $status[ $provider_key ] ?? [] );
        $view['provider'] = $provider_key;

        aimentor_maybe_increment_provider_test_counters(
                $provider_key,
                $status[ $provider_key ]['status'] ?? '',
                $status[ $provider_key ]['timestamp'] ?? 0
        );

        wp_send_json_success( $view );
}
add_action( 'wp_ajax_aimentor_test_api', 'aimentor_test_api_connection' );
add_action( 'wp_ajax_jaggrok_test_api', 'aimentor_test_api_connection' );

function aimentor_health_checks_enabled() {
        $defaults = aimentor_get_default_options();
        $value    = get_option( 'aimentor_enable_health_checks', $defaults['aimentor_enable_health_checks'] );
        $value    = aimentor_sanitize_toggle( $value );

        return 'yes' === $value;
}

function aimentor_auto_updates_enabled() {
        $defaults = aimentor_get_default_options();
        $value    = get_option( 'aimentor_enable_auto_updates', $defaults['aimentor_enable_auto_updates'] );
        $value    = aimentor_sanitize_toggle( $value );

        return 'yes' === $value;
}

function aimentor_wordpress_allows_plugin_auto_updates() {
        if ( defined( 'AUTOMATIC_UPDATER_DISABLED' ) && AUTOMATIC_UPDATER_DISABLED ) {
                return false;
        }

        if ( defined( 'DISALLOW_FILE_MODS' ) && DISALLOW_FILE_MODS ) {
                return false;
        }

        if ( apply_filters( 'automatic_updater_disabled', false ) ) {
                return false;
        }

        if ( function_exists( 'wp_is_auto_update_enabled_for_type' ) ) {
                if ( ! wp_is_auto_update_enabled_for_type( 'plugin' ) ) {
                        return false;
                }
        }

        return true;
}

function aimentor_auto_updates_active() {
        if ( ! aimentor_auto_updates_enabled() ) {
                return false;
        }

        return aimentor_wordpress_allows_plugin_auto_updates();
}

function aimentor_health_check_alerts_enabled() {
        $defaults = aimentor_get_default_options();
        $value    = get_option( 'aimentor_enable_health_check_alerts', $defaults['aimentor_enable_health_check_alerts'] );
        $value    = aimentor_sanitize_toggle( $value );

        return 'yes' === $value;
}

function aimentor_get_health_check_failure_threshold() {
        $threshold = apply_filters( 'aimentor_health_check_failure_threshold', 3 );
        $threshold = absint( $threshold );

        return $threshold > 0 ? $threshold : 1;
}

function aimentor_get_health_check_recipients() {
        $stored     = get_option( 'aimentor_health_check_recipients', '' );
        $sanitized  = aimentor_sanitize_health_check_recipients( $stored );
        $recipients = [];

        if ( '' !== $sanitized ) {
                $parts = array_map( 'trim', explode( ',', $sanitized ) );

                foreach ( $parts as $email ) {
                        $clean = sanitize_email( $email );

                        if ( $clean && is_email( $clean ) ) {
                                $recipients[] = $clean;
                        }
                }
        }

        if ( empty( $recipients ) && function_exists( 'get_users' ) ) {
                $admins = get_users(
                        [
                                'role'   => 'administrator',
                                'fields' => [ 'user_email' ],
                        ]
                );

                foreach ( $admins as $admin ) {
                        if ( ! isset( $admin->user_email ) ) {
                                continue;
                        }

                        $clean = sanitize_email( $admin->user_email );

                        if ( $clean && is_email( $clean ) ) {
                                $recipients[] = $clean;
                        }
                }
        }

        if ( empty( $recipients ) ) {
                $admin_email = sanitize_email( get_option( 'admin_email' ) );

                if ( $admin_email && is_email( $admin_email ) ) {
                        $recipients[] = $admin_email;
                }
        }

        return array_values( array_unique( $recipients ) );
}

function aimentor_get_health_failure_state() {
        $state = get_option( 'aimentor_provider_health_failures', [] );

        if ( ! is_array( $state ) ) {
                $state = [];
        }

        foreach ( $state as $provider => $data ) {
                $state[ $provider ] = [
                        'count'          => isset( $data['count'] ) ? absint( $data['count'] ) : 0,
                        'last_notified'  => isset( $data['last_notified'] ) ? absint( $data['last_notified'] ) : 0,
                        'notified_count' => isset( $data['notified_count'] ) ? absint( $data['notified_count'] ) : 0,
                        'last_message'   => isset( $data['last_message'] ) ? sanitize_text_field( $data['last_message'] ) : '',
                ];
        }

        return $state;
}

function aimentor_register_provider_health_failure( $state, $provider_key, $message ) {
        if ( ! is_array( $state ) ) {
                $state = [];
        }

        if ( ! isset( $state[ $provider_key ] ) || ! is_array( $state[ $provider_key ] ) ) {
                $state[ $provider_key ] = [
                        'count'          => 0,
                        'last_notified'  => 0,
                        'notified_count' => 0,
                        'last_message'   => '',
                ];
        }

        $state[ $provider_key ]['count']        = absint( $state[ $provider_key ]['count'] ) + 1;
        $state[ $provider_key ]['last_message'] = sanitize_text_field( $message );

        return $state;
}

function aimentor_register_provider_health_recovery( $state, $provider_key ) {
        if ( isset( $state[ $provider_key ] ) ) {
                $state[ $provider_key ] = [
                        'count'          => 0,
                        'last_notified'  => 0,
                        'notified_count' => 0,
                        'last_message'   => '',
                ];
        }

        return $state;
}

function aimentor_maybe_send_health_check_alerts( &$state ) {
        if ( ! aimentor_health_check_alerts_enabled() ) {
                return;
        }

        $threshold = aimentor_get_health_check_failure_threshold();

        if ( $threshold < 1 ) {
                return;
        }

        $recipients = aimentor_get_health_check_recipients();

        if ( empty( $recipients ) ) {
                return;
        }

        $provider_labels = aimentor_get_provider_labels();
        $site_name       = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );

        foreach ( $state as $provider_key => $data ) {
                $count = isset( $data['count'] ) ? absint( $data['count'] ) : 0;

                if ( $count < $threshold ) {
                        continue;
                }

                $notified_count = isset( $data['notified_count'] ) ? absint( $data['notified_count'] ) : 0;

                if ( $notified_count >= $threshold ) {
                        continue;
                }

                $label   = $provider_labels[ $provider_key ] ?? ucfirst( $provider_key );
                $message = isset( $data['last_message'] ) && '' !== $data['last_message']
                        ? $data['last_message']
                        : __( 'No additional error details were provided.', 'aimentor' );

                $subject = sprintf(
                        __( '[%1$s] %2$s connection failures detected', 'aimentor' ),
                        $site_name,
                        $label
                );

                $body = sprintf(
                        __( "AiMentor attempted to verify the %1$s API key %2$d times and each attempt failed.\n\nMost recent error: %3$s\n\nPlease review the AiMentor settings in WordPress admin to resolve the connection issue.", 'aimentor' ),
                        $label,
                        $count,
                        $message
                );

                wp_mail( $recipients, $subject, $body );

                $state[ $provider_key ]['last_notified']  = current_time( 'timestamp' );
                $state[ $provider_key ]['notified_count'] = max( $count, $threshold );
        }
}

function aimentor_run_scheduled_provider_checks() {
        if ( ! aimentor_health_checks_enabled() ) {
                return;
        }

        $provider_labels = aimentor_get_provider_labels();

        if ( empty( $provider_labels ) ) {
                return;
        }

        $api_keys = [
                'grok'   => get_option( 'aimentor_xai_api_key', '' ),
                'openai' => get_option( 'aimentor_openai_api_key', '' ),
        ];

        $state        = aimentor_get_health_failure_state();
        $active_found = false;

        foreach ( $provider_labels as $provider_key => $label ) {
                $api_key = isset( $api_keys[ $provider_key ] ) ? $api_keys[ $provider_key ] : '';

                if ( '' === trim( (string) $api_key ) ) {
                        continue;
                }

                $active_found = true;

                $result = aimentor_run_provider_connection_test(
                        $provider_key,
                        $api_key,
                        [
                                'origin'            => 'health_check',
                                'update_api_tested' => false,
                                'user_id'           => 0,
                                'persist_api_key'   => false,
                                'record_usage'      => false,
                        ]
                );

                if ( is_wp_error( $result ) ) {
                        $state = aimentor_register_provider_health_failure( $state, $provider_key, $result->get_error_message() );
                } else {
                        $state = aimentor_register_provider_health_recovery( $state, $provider_key );
                }
        }

        if ( ! $active_found ) {
                return;
        }

        aimentor_maybe_send_health_check_alerts( $state );

        update_option( 'aimentor_provider_health_failures', $state );
}

// ERROR LOGGING FUNCTION (v1.3.18)
function aimentor_log_error( $message, $context = [] ) {
        if ( function_exists( 'aimentor_get_error_log_path' ) ) {
                $log_file = aimentor_get_error_log_path();
        } else {
                $log_file = plugin_dir_path( __FILE__ ) . 'aimentor-errors.log';
        }

        $log_dir   = dirname( $log_file );
        $timestamp = gmdate( 'Y-m-d H:i:s' );
        $log_entry = $message;

        if ( ! is_dir( $log_dir ) ) {
                if ( function_exists( 'wp_mkdir_p' ) ) {
                        wp_mkdir_p( $log_dir );
                } else {
                        @mkdir( $log_dir, 0755, true );
                }

                if ( ! is_dir( $log_dir ) ) {
                        return;
                }
        }

        if ( ! is_writable( $log_dir ) ) {
                return;
        }

        if ( is_array( $context ) && ! empty( $context ) ) {
                $allowed_keys = [ 'provider', 'model', 'user_id' ];
                $context_data = [];

		foreach ( $allowed_keys as $key ) {
			if ( array_key_exists( $key, $context ) && null !== $context[ $key ] && '' !== $context[ $key ] ) {
				$context_data[ $key ] = $context[ $key ];
			}
		}

		if ( ! empty( $context_data ) ) {
			$log_entry = wp_json_encode(
				[
					'message' => $message,
					'context' => $context_data,
				]
			);
		}
        }

        file_put_contents( $log_file, $timestamp . ' - ' . $log_entry . "\n", FILE_APPEND | LOCK_EX );
}

function aimentor_mirror_option_to_legacy( $modern_option, $value ) {
        if ( 0 !== strpos( $modern_option, 'aimentor_' ) ) {
                return;
        }

        $legacy_option = str_replace( 'aimentor_', 'jaggrok_', $modern_option );

        if ( $legacy_option === $modern_option ) {
                return;
        }

        update_option( $legacy_option, $value );
}

$aimentor_options_to_mirror = [
        'aimentor_provider',
        'aimentor_xai_api_key',
        'aimentor_openai_api_key',
        'aimentor_auto_insert',
        'aimentor_theme_style',
        'aimentor_max_tokens',
        'aimentor_model',
        'aimentor_model_presets',
        'aimentor_openai_model',
        'aimentor_provider_models',
        'aimentor_api_tested',
        'aimentor_provider_test_statuses',
        'aimentor_onboarding_dismissed',
        'aimentor_request_overrides',
];

foreach ( $aimentor_options_to_mirror as $option_name ) {
        add_action(
                "update_option_{$option_name}",
                function( $old_value, $value ) use ( $option_name ) {
                        aimentor_mirror_option_to_legacy( $option_name, $value );
                },
                10,
                2
        );

        add_action(
                "add_option_{$option_name}",
                function( $option, $value ) use ( $option_name ) {
                        aimentor_mirror_option_to_legacy( $option_name, $value );
                },
                10,
                2
        );
}
