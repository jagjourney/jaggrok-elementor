<div class="aimentor-settings-layout">
    <div class="aimentor-settings-main">
        <p class="description aimentor-defaults-notice jaggrok-defaults-notice">
            <?php
            printf(
                    /* translators: 1: Grok model, 2: Anthropic model, 3: OpenAI model, 4: max tokens */
                    esc_html__( 'Defaults: Grok starts on %1$s, Anthropic loads %2$s, OpenAI uses %3$s, and requests are capped at %4$s tokens until you change them.', 'aimentor' ),
                    esc_html( strtoupper( $defaults['aimentor_model'] ) ),
                    esc_html( strtoupper( $defaults['aimentor_anthropic_model'] ) ),
                    esc_html( strtoupper( $defaults['aimentor_openai_model'] ) ),
                    esc_html( number_format_i18n( $defaults['aimentor_max_tokens'] ) )
            );
            ?>
        </p>
        <form method="post" action="options.php" class="aimentor-settings-form aimentor-settings-form--defaults">
            <?php settings_fields( 'aimentor_settings' ); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Default Model', 'aimentor' ); ?></th>
                    <td>
                        <div class="aimentor-provider-group jaggrok-provider-group" data-provider="grok">
                            <label class="screen-reader-text" for="aimentor_provider_models_grok"><?php esc_html_e( 'xAI Grok default model', 'aimentor' ); ?></label>
                            <select name="aimentor_provider_models[grok]" id="aimentor_provider_models_grok" class="regular-text" <?php disabled( $provider_controls_locked ); ?>>
                                <?php foreach ( array_keys( $allowed_models['grok'] ) as $model_key ) : ?>
                                <option value="<?php echo esc_attr( $model_key ); ?>" <?php selected( $models['grok'], $model_key ); ?>><?php echo esc_html( $grok_model_labels[ $model_key ] ?? strtoupper( $model_key ) ); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ( $provider_controls_locked ) : ?>
                                <input type="hidden" name="aimentor_provider_models[grok]" value="<?php echo esc_attr( $models['grok'] ); ?>" />
                            <?php endif; ?>
                            <p class="description"><?php esc_html_e( 'Grok 3 Beta is a reliable balance of quality and speed for most Elementor flows.', 'aimentor' ); ?></p>
                        </div>
                        <div class="aimentor-provider-group jaggrok-provider-group" data-provider="anthropic">
                            <label class="screen-reader-text" for="aimentor_provider_models_anthropic"><?php esc_html_e( 'Anthropic Claude default model', 'aimentor' ); ?></label>
                            <select name="aimentor_provider_models[anthropic]" id="aimentor_provider_models_anthropic" class="regular-text" <?php disabled( $provider_controls_locked ); ?>>
                                <?php foreach ( array_keys( $allowed_models['anthropic'] ) as $model_key ) : ?>
                                <option value="<?php echo esc_attr( $model_key ); ?>" <?php selected( $models['anthropic'], $model_key ); ?>><?php echo esc_html( $anthropic_model_labels[ $model_key ] ?? strtoupper( $model_key ) ); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ( $provider_controls_locked ) : ?>
                                <input type="hidden" name="aimentor_provider_models[anthropic]" value="<?php echo esc_attr( $models['anthropic'] ); ?>" />
                            <?php endif; ?>
                            <p class="description"><?php esc_html_e( 'Claude Sonnet balances fast responses with strong reasoning for content and layout tasks.', 'aimentor' ); ?></p>
                        </div>
                        <div class="aimentor-provider-group jaggrok-provider-group" data-provider="openai">
                            <label class="screen-reader-text" for="aimentor_provider_models_openai"><?php esc_html_e( 'OpenAI default model', 'aimentor' ); ?></label>
                            <select name="aimentor_provider_models[openai]" id="aimentor_provider_models_openai" class="regular-text" <?php disabled( $provider_controls_locked ); ?>>
                                <?php foreach ( array_keys( $allowed_models['openai'] ) as $model_key ) : ?>
                                <option value="<?php echo esc_attr( $model_key ); ?>" <?php selected( $models['openai'], $model_key ); ?>><?php echo esc_html( $openai_model_labels[ $model_key ] ?? strtoupper( $model_key ) ); ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if ( $provider_controls_locked ) : ?>
                                <input type="hidden" name="aimentor_provider_models[openai]" value="<?php echo esc_attr( $models['openai'] ); ?>" />
                            <?php endif; ?>
                            <p class="description"><?php esc_html_e( 'GPT-4o mini delivers strong reasoning with lower cost; upgrade as your budget allows.', 'aimentor' ); ?></p>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Context Defaults', 'aimentor' ); ?></th>
                    <td>
                        <p class="description"><?php esc_html_e( 'Choose which provider and model should load automatically for each Elementor document type.', 'aimentor' ); ?></p>
                        <?php
                        $global_defaults      = isset( $document_provider_defaults['default'] ) ? $document_provider_defaults['default'] : [];
                        $global_provider      = isset( $global_defaults['provider'] ) ? $global_defaults['provider'] : 'grok';
                        $global_model         = isset( $global_defaults['model'] ) ? $global_defaults['model'] : '';
                        $has_page_type_groups = ! empty( $combined_page_types );
                        ?>
                        <table class="widefat striped aimentor-context-defaults-table" style="max-width:680px;">
                            <thead>
                                <tr>
                                    <th scope="col"><?php esc_html_e( 'Context', 'aimentor' ); ?></th>
                                    <th scope="col"><?php esc_html_e( 'Provider', 'aimentor' ); ?></th>
                                    <th scope="col"><?php esc_html_e( 'Model', 'aimentor' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <strong><?php esc_html_e( 'Global Default', 'aimentor' ); ?></strong>
                                        <p class="description" style="margin:4px 0 0;">
                                            <?php esc_html_e( 'Used when no specific mapping is found.', 'aimentor' ); ?>
                                        </p>
                                    </td>
                                    <td>
                                        <label class="screen-reader-text" for="aimentor-context-provider-default"><?php esc_html_e( 'Preferred provider', 'aimentor' ); ?></label>
                                        <select name="aimentor_document_provider_defaults[default][provider]" id="aimentor-context-provider-default" class="aimentor-context-provider" style="min-width:160px;" <?php disabled( $provider_controls_locked ); ?>>
                                            <?php foreach ( $provider_labels_map as $provider_key => $provider_label ) : ?>
                                            <option value="<?php echo esc_attr( $provider_key ); ?>" <?php selected( $global_provider, $provider_key ); ?>><?php echo esc_html( $provider_label ); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if ( $provider_controls_locked ) : ?>
                                            <input type="hidden" name="aimentor_document_provider_defaults[default][provider]" value="<?php echo esc_attr( $global_provider ); ?>" />
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <label class="screen-reader-text" for="aimentor-context-model-default"><?php esc_html_e( 'Preferred model', 'aimentor' ); ?></label>
                                        <select name="aimentor_document_provider_defaults[default][model]" id="aimentor-context-model-default" class="aimentor-context-model" style="min-width:200px;" <?php disabled( $provider_controls_locked ); ?>>
                                            <?php foreach ( $allowed_models as $provider_key => $model_group ) :
                                                    $group_label = isset( $provider_labels_map[ $provider_key ] ) ? $provider_labels_map[ $provider_key ] : strtoupper( $provider_key );
                                            ?>
                                            <optgroup label="<?php echo esc_attr( $group_label ); ?>">
                                                <?php foreach ( $model_group as $model_key => $model_label ) : ?>
                                                <option value="<?php echo esc_attr( $model_key ); ?>" data-provider="<?php echo esc_attr( $provider_key ); ?>" <?php selected( $global_model, $model_key ); ?>><?php echo esc_html( $model_label ); ?></option>
                                                <?php endforeach; ?>
                                            </optgroup>
                                            <?php endforeach; ?>
                                        </select>
                                        <?php if ( $provider_controls_locked ) : ?>
                                            <input type="hidden" name="aimentor_document_provider_defaults[default][model]" value="<?php echo esc_attr( $global_model ); ?>" />
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <?php if ( $has_page_type_groups ) : ?>
                            <?php foreach ( $combined_page_types as $post_type => $meta ) :
                                    $post_type_label    = isset( $meta['label'] ) && '' !== $meta['label'] ? $meta['label'] : ucfirst( (string) $post_type );
                                    $post_type_defaults = isset( $page_type_defaults[ $post_type ] ) && is_array( $page_type_defaults[ $post_type ] ) ? $page_type_defaults[ $post_type ] : [];
                                    $post_type_provider = isset( $post_type_defaults['provider'] ) ? $post_type_defaults['provider'] : $global_provider;
                                    $post_type_model    = isset( $post_type_defaults['model'] ) ? $post_type_defaults['model'] : $global_model;
                                    $template_blueprint = isset( $meta['templates'] ) && is_array( $meta['templates'] ) ? $meta['templates'] : [];
                                    $template_default_map = isset( $post_type_defaults['templates'] ) && is_array( $post_type_defaults['templates'] ) ? $post_type_defaults['templates'] : [];
                            ?>
                            <div class="aimentor-context-group" style="margin-top:24px;">
                                <h3 style="margin:0 0 8px;"><?php echo esc_html( sprintf( __( 'Post Type: %s', 'aimentor' ), $post_type_label ) ); ?></h3>
                                <table class="widefat striped aimentor-context-defaults-table" style="max-width:680px;">
                                    <thead>
                                        <tr>
                                            <th scope="col"><?php esc_html_e( 'Context', 'aimentor' ); ?></th>
                                            <th scope="col"><?php esc_html_e( 'Provider', 'aimentor' ); ?></th>
                                            <th scope="col"><?php esc_html_e( 'Model', 'aimentor' ); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <strong><?php esc_html_e( 'Post Type Default', 'aimentor' ); ?></strong>
                                                <p class="description" style="margin:4px 0 0;">
                                                    <?php esc_html_e( 'Applies to Elementor documents for this type when no specific template match is found.', 'aimentor' ); ?>
                                                </p>
                                            </td>
                                            <td>
                                                <?php $provider_id = 'aimentor-context-provider-' . md5( 'post_type:' . $post_type ); ?>
                                                <label class="screen-reader-text" for="<?php echo esc_attr( $provider_id ); ?>"><?php esc_html_e( 'Preferred provider', 'aimentor' ); ?></label>
                                                <select name="aimentor_document_provider_defaults[page_types][<?php echo esc_attr( $post_type ); ?>][provider]" id="<?php echo esc_attr( $provider_id ); ?>" class="aimentor-context-provider" style="min-width:160px;" <?php disabled( $provider_controls_locked ); ?>>
                                                    <?php foreach ( $provider_labels_map as $provider_key => $provider_label ) : ?>
                                                    <option value="<?php echo esc_attr( $provider_key ); ?>" <?php selected( $post_type_provider, $provider_key ); ?>><?php echo esc_html( $provider_label ); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <?php if ( $provider_controls_locked ) : ?>
                                                    <input type="hidden" name="aimentor_document_provider_defaults[page_types][<?php echo esc_attr( $post_type ); ?>][provider]" value="<?php echo esc_attr( $post_type_provider ); ?>" />
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php $model_id = 'aimentor-context-model-' . md5( 'post_type:' . $post_type ); ?>
                                                <label class="screen-reader-text" for="<?php echo esc_attr( $model_id ); ?>"><?php esc_html_e( 'Preferred model', 'aimentor' ); ?></label>
                                                <select name="aimentor_document_provider_defaults[page_types][<?php echo esc_attr( $post_type ); ?>][model]" id="<?php echo esc_attr( $model_id ); ?>" class="aimentor-context-model" style="min-width:200px;" <?php disabled( $provider_controls_locked ); ?>>
                                                    <?php foreach ( $allowed_models as $provider_key => $model_group ) :
                                                            $group_label = isset( $provider_labels_map[ $provider_key ] ) ? $provider_labels_map[ $provider_key ] : strtoupper( $provider_key );
                                                    ?>
                                                    <optgroup label="<?php echo esc_attr( $group_label ); ?>">
                                                        <?php foreach ( $model_group as $model_key => $model_label ) : ?>
                                                        <option value="<?php echo esc_attr( $model_key ); ?>" data-provider="<?php echo esc_attr( $provider_key ); ?>" <?php selected( $post_type_model, $model_key ); ?>><?php echo esc_html( $model_label ); ?></option>
                                                        <?php endforeach; ?>
                                                    </optgroup>
                                                    <?php endforeach; ?>
                                                </select>
                                                <?php if ( $provider_controls_locked ) : ?>
                                                    <input type="hidden" name="aimentor_document_provider_defaults[page_types][<?php echo esc_attr( $post_type ); ?>][model]" value="<?php echo esc_attr( $post_type_model ); ?>" />
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php if ( ! empty( $template_blueprint ) ) : ?>
                                            <?php foreach ( $template_blueprint as $template_file => $template_meta ) :
                                                    $template_label = is_array( $template_meta ) && isset( $template_meta['label'] ) ? $template_meta['label'] : ( is_string( $template_meta ) ? $template_meta : $template_file );
                                                    $template_entry    = isset( $template_default_map[ $template_file ] ) && is_array( $template_default_map[ $template_file ] ) ? $template_default_map[ $template_file ] : [];
                                                    $template_provider = isset( $template_entry['provider'] ) ? $template_entry['provider'] : $post_type_provider;
                                                    $template_model    = isset( $template_entry['model'] ) ? $template_entry['model'] : $post_type_model;
                                                    $template_provider_id = 'aimentor-context-provider-' . md5( $post_type . '|' . $template_file );
                                                    $template_model_id    = 'aimentor-context-model-' . md5( $post_type . '|' . $template_file );
                                            ?>
                                            <tr>
                                                <td>
                                                    <strong><?php echo esc_html( sprintf( __( 'Template: %s', 'aimentor' ), $template_label ) ); ?></strong>
                                                    <p class="description" style="margin:4px 0 0;">
                                                        <?php esc_html_e( 'Overrides the post type default when the Elementor document uses this template.', 'aimentor' ); ?>
                                                    </p>
                                                </td>
                                                <td>
                                                    <label class="screen-reader-text" for="<?php echo esc_attr( $template_provider_id ); ?>"><?php esc_html_e( 'Preferred provider', 'aimentor' ); ?></label>
                                                    <select name="aimentor_document_provider_defaults[page_types][<?php echo esc_attr( $post_type ); ?>][templates][<?php echo esc_attr( $template_file ); ?>][provider]" id="<?php echo esc_attr( $template_provider_id ); ?>" class="aimentor-context-provider" style="min-width:160px;" <?php disabled( $provider_controls_locked ); ?>>
                                                        <?php foreach ( $provider_labels_map as $provider_key => $provider_label ) : ?>
                                                        <option value="<?php echo esc_attr( $provider_key ); ?>" <?php selected( $template_provider, $provider_key ); ?>><?php echo esc_html( $provider_label ); ?></option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <?php if ( $provider_controls_locked ) : ?>
                                                        <input type="hidden" name="aimentor_document_provider_defaults[page_types][<?php echo esc_attr( $post_type ); ?>][templates][<?php echo esc_attr( $template_file ); ?>][provider]" value="<?php echo esc_attr( $template_provider ); ?>" />
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <label class="screen-reader-text" for="<?php echo esc_attr( $template_model_id ); ?>"><?php esc_html_e( 'Preferred model', 'aimentor' ); ?></label>
                                                    <select name="aimentor_document_provider_defaults[page_types][<?php echo esc_attr( $post_type ); ?>][templates][<?php echo esc_attr( $template_file ); ?>][model]" id="<?php echo esc_attr( $template_model_id ); ?>" class="aimentor-context-model" style="min-width:200px;" <?php disabled( $provider_controls_locked ); ?>>
                                                        <?php foreach ( $allowed_models as $provider_key => $model_group ) :
                                                                $group_label = isset( $provider_labels_map[ $provider_key ] ) ? $provider_labels_map[ $provider_key ] : strtoupper( $provider_key );
                                                        ?>
                                                        <optgroup label="<?php echo esc_attr( $group_label ); ?>">
                                                            <?php foreach ( $model_group as $model_key => $model_label ) : ?>
                                                            <option value="<?php echo esc_attr( $model_key ); ?>" data-provider="<?php echo esc_attr( $provider_key ); ?>" <?php selected( $template_model, $model_key ); ?>><?php echo esc_html( $model_label ); ?></option>
                                                            <?php endforeach; ?>
                                                        </optgroup>
                                                        <?php endforeach; ?>
                                                    </select>
                                                    <?php if ( $provider_controls_locked ) : ?>
                                                        <input type="hidden" name="aimentor_document_provider_defaults[page_types][<?php echo esc_attr( $post_type ); ?>][templates][<?php echo esc_attr( $template_file ); ?>][model]" value="<?php echo esc_attr( $template_model ); ?>" />
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <tr>
                                                <td colspan="3">
                                                    <p class="description" style="margin:8px 0;">
                                                        <?php esc_html_e( 'No templates detected for this post type. The post type default will apply to all Elementor documents.', 'aimentor' ); ?>
                                                    </p>
                                                </td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <p class="description" style="margin-top:8px;">
                            <?php esc_html_e( 'Editors inherit the global default when no page type or template mapping is defined.', 'aimentor' ); ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Max Tokens', 'aimentor' ); ?></th>
                    <td>
                        <input type="number" name="aimentor_max_tokens" value="<?php echo esc_attr( get_option( 'aimentor_max_tokens', 2000 ) ); ?>" min="500" max="8000" class="small-text" /> <?php esc_html_e( 'tokens', 'aimentor' ); ?>
                        <p class="description"><?php esc_html_e( 'Higher values allow for more detailed layouts. Stay within your provider limits.', 'aimentor' ); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><?php esc_html_e( 'Advanced', 'aimentor' ); ?></th>
                    <td>
                        <details class="aimentor-advanced-settings" <?php echo $advanced_has_overrides ? ' open' : ''; ?>>
                            <summary><?php esc_html_e( 'Advanced overrides', 'aimentor' ); ?></summary>
                            <p class="description"><?php esc_html_e( 'Adjust per-provider request behaviour. Leave fields blank to fall back to built-in defaults.', 'aimentor' ); ?></p>
                            <?php
                            $advanced_provider_labels = aimentor_get_provider_labels();
                            $advanced_task_labels = [
                                    'canvas'  => __( 'Canvas', 'aimentor' ),
                                    'content' => __( 'Content', 'aimentor' ),
                            ];

                            foreach ( $advanced_provider_labels as $advanced_provider_key => $advanced_provider_label ) :
                                    $provider_overrides = isset( $request_overrides[ $advanced_provider_key ] ) && is_array( $request_overrides[ $advanced_provider_key ] )
                                            ? $request_overrides[ $advanced_provider_key ]
                                            : [];
                            ?>
                            <div class="aimentor-advanced-provider" data-provider="<?php echo esc_attr( $advanced_provider_key ); ?>">
                                <h4><?php echo esc_html( $advanced_provider_label ); ?></h4>
                                <div class="aimentor-advanced-grid">
                                    <?php foreach ( $advanced_task_labels as $advanced_task_key => $advanced_task_label ) :
                                            $task_overrides      = isset( $provider_overrides[ $advanced_task_key ] ) && is_array( $provider_overrides[ $advanced_task_key ] ) ? $provider_overrides[ $advanced_task_key ] : [];
                                            $temperature_value   = array_key_exists( 'temperature', $task_overrides ) ? $task_overrides['temperature'] : '';
                                            $timeout_value       = array_key_exists( 'timeout', $task_overrides ) ? $task_overrides['timeout'] : '';
                                            $temperature_display = '' === $temperature_value ? '' : $temperature_value;
                                            $timeout_display     = '' === $timeout_value ? '' : $timeout_value;
                                    ?>
                                    <fieldset class="aimentor-advanced-task">
                                        <legend><?php echo esc_html( $advanced_task_label ); ?></legend>
                                        <label class="aimentor-advanced-field">
                                            <span class="aimentor-advanced-label"><?php esc_html_e( 'Temperature', 'aimentor' ); ?></span>
                                            <input type="number" step="0.05" min="0" max="2" class="small-text" name="aimentor_request_overrides[<?php echo esc_attr( $advanced_provider_key ); ?>][<?php echo esc_attr( $advanced_task_key ); ?>][temperature]" value="<?php echo '' === $temperature_display ? '' : esc_attr( $temperature_display ); ?>" />
                                        </label>
                                        <label class="aimentor-advanced-field">
                                            <span class="aimentor-advanced-label"><?php esc_html_e( 'Timeout (seconds)', 'aimentor' ); ?></span>
                                            <input type="number" min="5" max="600" class="small-text" name="aimentor_request_overrides[<?php echo esc_attr( $advanced_provider_key ); ?>][<?php echo esc_attr( $advanced_task_key ); ?>][timeout]" value="<?php echo '' === $timeout_display ? '' : esc_attr( $timeout_display ); ?>" />
                                        </label>
                                    </fieldset>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </details>
                    </td>
                </tr>
            </table>
            <input type="hidden" name="aimentor_model" id="aimentor_model_legacy" value="<?php echo esc_attr( $models['grok'] ); ?>" />
            <input type="hidden" name="aimentor_anthropic_model" id="aimentor_anthropic_model_legacy" value="<?php echo esc_attr( $models['anthropic'] ); ?>" />
            <input type="hidden" name="aimentor_openai_model" id="aimentor_openai_model_legacy" value="<?php echo esc_attr( $models['openai'] ); ?>" />
            <?php submit_button(); ?>
        </form>
    </div>
    <?php include plugin_dir_path( __FILE__ ) . 'sidebar-support.php'; ?>
</div>
