<?php

$automation_jobs          = isset( $automation_jobs ) && is_array( $automation_jobs ) ? array_values( $automation_jobs ) : [];
$automation_enabled       = ! empty( $automation_enabled );
$saved_prompts_payload    = isset( $saved_prompts_payload ) && is_array( $saved_prompts_payload ) ? $saved_prompts_payload : [ 'global' => [], 'user' => [] ];
$knowledge_packs          = isset( $knowledge_packs ) && is_array( $knowledge_packs ) ? $knowledge_packs : [];
$provider_labels_map      = isset( $provider_labels_map ) && is_array( $provider_labels_map ) ? $provider_labels_map : [];
$automation_queue_state   = isset( $automation_queue_state ) && is_array( $automation_queue_state ) ? $automation_queue_state : [];
$automation_results       = isset( $automation_results ) && is_array( $automation_results ) ? $automation_results : [];
$automation_pending_count = isset( $automation_pending_results ) && is_array( $automation_pending_results ) ? count( $automation_pending_results ) : 0;
$now                     = current_time( 'timestamp' );

$post_type_objects = get_post_types(
        [
                'show_ui' => true,
        ],
        'objects'
);

$post_types = [];

foreach ( $post_type_objects as $post_type => $object ) {
        $post_types[ $post_type ] = isset( $object->label ) ? $object->label : ucwords( str_replace( [ '_', '-' ], ' ', $post_type ) );
}

$cadence_options = [
        'hourly'     => __( 'Hourly', 'aimentor' ),
        'twicedaily' => __( 'Twice Daily', 'aimentor' ),
        'daily'      => __( 'Daily', 'aimentor' ),
        'weekly'     => __( 'Weekly', 'aimentor' ),
];

$post_status_options = [
        'publish' => __( 'Published', 'aimentor' ),
        'draft'   => __( 'Drafts', 'aimentor' ),
        'pending' => __( 'Pending Review', 'aimentor' ),
        'future'  => __( 'Scheduled', 'aimentor' ),
        'private' => __( 'Private', 'aimentor' ),
        'any'     => __( 'Any status', 'aimentor' ),
];

$task_options = [
        'content' => __( 'Content (copy)', 'aimentor' ),
        'canvas'  => __( 'Canvas (layout)', 'aimentor' ),
];

$tier_options = [
        ''        => __( 'Match default preference', 'aimentor' ),
        'fast'    => __( 'Fast tier', 'aimentor' ),
        'quality' => __( 'Quality tier', 'aimentor' ),
];

$provider_options = [ '' => __( 'Use default provider', 'aimentor' ) ];

foreach ( $provider_labels_map as $provider_key => $provider_label ) {
        $provider_options[ $provider_key ] = isset( $provider_label['label'] ) ? $provider_label['label'] : ucfirst( $provider_key );
}

$prompt_options = [];

foreach ( [ 'global' => __( 'Shared Prompts', 'aimentor' ), 'user' => __( 'My Prompts', 'aimentor' ) ] as $scope => $scope_label ) {
        if ( empty( $saved_prompts_payload[ $scope ] ) || ! is_array( $saved_prompts_payload[ $scope ] ) ) {
                continue;
        }

        foreach ( $saved_prompts_payload[ $scope ] as $prompt ) {
                if ( ! isset( $prompt['id'], $prompt['label'] ) ) {
                        continue;
                }

                $prompt_options[] = [
                        'id'    => $prompt['id'],
                        'label' => sprintf( '%s: %s', $scope_label, $prompt['label'] ),
                ];
        }
}

$render_job = static function( $job, $index, $context ) {
        $post_types        = $context['post_types'];
        $cadence_options   = $context['cadence_options'];
        $post_status_opts  = $context['post_status_options'];
        $provider_options  = $context['provider_options'];
        $tier_options      = $context['tier_options'];
        $task_options      = $context['task_options'];
        $prompt_options    = $context['prompt_options'];
        $knowledge_packs   = $context['knowledge_packs'];
        $is_template       = ! empty( $context['template'] );
        $post_ids_string   = isset( $job['post_ids'] ) && is_array( $job['post_ids'] ) ? implode( ', ', array_map( 'absint', $job['post_ids'] ) ) : '';
        $knowledge_ids     = isset( $job['knowledge_ids'] ) && is_array( $job['knowledge_ids'] ) ? array_map( 'sanitize_text_field', $job['knowledge_ids'] ) : [];
        $field_name        = static function( $field ) use ( $index ) {
                return sprintf( 'aimentor_automation_jobs[%s][%s]', $index, $field );
        };
        $field_id = static function( $field ) use ( $index ) {
                return sprintf( 'aimentor_automation_%s_%s', $field, $index );
        };
        $prompt_type = isset( $job['prompt_type'] ) ? $job['prompt_type'] : 'saved_prompt';
        $status      = isset( $job['status'] ) ? $job['status'] : 'active';
        $post_type   = isset( $job['post_type'] ) ? $job['post_type'] : 'post';
        $post_status = isset( $job['post_status'] ) ? $job['post_status'] : 'publish';
        $selection   = isset( $job['selection'] ) ? $job['selection'] : 'recent';
        $cadence     = isset( $job['cadence'] ) ? $job['cadence'] : 'daily';
        $task        = isset( $job['task'] ) ? $job['task'] : 'content';
        $provider    = isset( $job['provider'] ) ? $job['provider'] : '';
        $tier        = isset( $job['tier'] ) ? $job['tier'] : '';
        $label       = isset( $job['label'] ) ? $job['label'] : '';
        $limit       = isset( $job['limit'] ) ? absint( $job['limit'] ) : 1;
        $saved_prompt_id = isset( $job['saved_prompt_id'] ) ? $job['saved_prompt_id'] : '';
        $prompt_template = isset( $job['prompt_template'] ) ? $job['prompt_template'] : '';
        $job_id          = isset( $job['id'] ) ? $job['id'] : '';
        ?>
        <div class="aimentor-automation-job" data-index="<?php echo esc_attr( $index ); ?>">
            <div class="aimentor-automation-job__header">
                <h3 class="aimentor-automation-job__title"><?php echo esc_html( $label ? $label : __( 'New automation job', 'aimentor' ) ); ?></h3>
                <?php if ( ! $is_template ) : ?>
                <button type="button" class="button button-link-delete aimentor-automation-job__remove"><?php esc_html_e( 'Remove job', 'aimentor' ); ?></button>
                <?php endif; ?>
            </div>
            <div class="aimentor-automation-job__body">
                <input type="hidden" name="<?php echo esc_attr( $field_name( 'id' ) ); ?>" value="<?php echo esc_attr( $job_id ); ?>" />
                <p>
                    <label for="<?php echo esc_attr( $field_id( 'label' ) ); ?>" class="aimentor-automation-job__label"><?php esc_html_e( 'Job label', 'aimentor' ); ?></label>
                    <input type="text" class="regular-text" id="<?php echo esc_attr( $field_id( 'label' ) ); ?>" name="<?php echo esc_attr( $field_name( 'label' ) ); ?>" value="<?php echo esc_attr( $label ); ?>" placeholder="<?php esc_attr_e( 'e.g., Weekly blog refresh', 'aimentor' ); ?>" />
                </p>
                <p>
                    <label for="<?php echo esc_attr( $field_id( 'status' ) ); ?>" class="aimentor-automation-job__label"><?php esc_html_e( 'Status', 'aimentor' ); ?></label>
                    <select id="<?php echo esc_attr( $field_id( 'status' ) ); ?>" name="<?php echo esc_attr( $field_name( 'status' ) ); ?>">
                        <option value="active" <?php selected( $status, 'active' ); ?>><?php esc_html_e( 'Active', 'aimentor' ); ?></option>
                        <option value="paused" <?php selected( $status, 'paused' ); ?>><?php esc_html_e( 'Paused', 'aimentor' ); ?></option>
                    </select>
                </p>
                <div class="aimentor-automation-job__row">
                    <p>
                        <label for="<?php echo esc_attr( $field_id( 'post_type' ) ); ?>" class="aimentor-automation-job__label"><?php esc_html_e( 'Content type', 'aimentor' ); ?></label>
                        <select id="<?php echo esc_attr( $field_id( 'post_type' ) ); ?>" name="<?php echo esc_attr( $field_name( 'post_type' ) ); ?>">
                            <?php foreach ( $post_types as $type_key => $type_label ) : ?>
                            <option value="<?php echo esc_attr( $type_key ); ?>" <?php selected( $post_type, $type_key ); ?>><?php echo esc_html( $type_label ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                    <p>
                        <label for="<?php echo esc_attr( $field_id( 'post_status' ) ); ?>" class="aimentor-automation-job__label"><?php esc_html_e( 'Filter by status', 'aimentor' ); ?></label>
                        <select id="<?php echo esc_attr( $field_id( 'post_status' ) ); ?>" name="<?php echo esc_attr( $field_name( 'post_status' ) ); ?>">
                            <?php foreach ( $post_status_opts as $status_key => $status_label ) : ?>
                            <option value="<?php echo esc_attr( $status_key ); ?>" <?php selected( $post_status, $status_key ); ?>><?php echo esc_html( $status_label ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                    <p>
                        <label for="<?php echo esc_attr( $field_id( 'selection' ) ); ?>" class="aimentor-automation-job__label"><?php esc_html_e( 'Which records?', 'aimentor' ); ?></label>
                        <select class="aimentor-automation-job__content-selection" id="<?php echo esc_attr( $field_id( 'selection' ) ); ?>" name="<?php echo esc_attr( $field_name( 'selection' ) ); ?>">
                            <option value="recent" <?php selected( $selection, 'recent' ); ?>><?php esc_html_e( 'Most recent posts', 'aimentor' ); ?></option>
                            <option value="ids" <?php selected( $selection, 'ids' ); ?>><?php esc_html_e( 'Specific IDs', 'aimentor' ); ?></option>
                        </select>
                    </p>
                    <p>
                        <label for="<?php echo esc_attr( $field_id( 'limit' ) ); ?>" class="aimentor-automation-job__label"><?php esc_html_e( 'Posts per run', 'aimentor' ); ?></label>
                        <input type="number" min="1" max="20" id="<?php echo esc_attr( $field_id( 'limit' ) ); ?>" name="<?php echo esc_attr( $field_name( 'limit' ) ); ?>" value="<?php echo esc_attr( $limit ? $limit : 1 ); ?>" />
                    </p>
                </div>
                <p class="aimentor-automation-job__ids-field" <?php if ( 'ids' !== $selection ) : ?>style="display:none"<?php endif; ?>>
                    <label for="<?php echo esc_attr( $field_id( 'post_ids' ) ); ?>" class="aimentor-automation-job__label"><?php esc_html_e( 'Post IDs (comma separated)', 'aimentor' ); ?></label>
                    <input type="text" class="regular-text" id="<?php echo esc_attr( $field_id( 'post_ids' ) ); ?>" name="<?php echo esc_attr( $field_name( 'post_ids' ) ); ?>" value="<?php echo esc_attr( $post_ids_string ); ?>" placeholder="<?php esc_attr_e( 'e.g., 12, 34, 56', 'aimentor' ); ?>" />
                </p>
                <div class="aimentor-automation-job__row">
                    <p>
                        <label for="<?php echo esc_attr( $field_id( 'cadence' ) ); ?>" class="aimentor-automation-job__label"><?php esc_html_e( 'Cadence', 'aimentor' ); ?></label>
                        <select id="<?php echo esc_attr( $field_id( 'cadence' ) ); ?>" name="<?php echo esc_attr( $field_name( 'cadence' ) ); ?>">
                            <?php foreach ( $cadence_options as $cadence_key => $cadence_label ) : ?>
                            <option value="<?php echo esc_attr( $cadence_key ); ?>" <?php selected( $cadence, $cadence_key ); ?>><?php echo esc_html( $cadence_label ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                    <p>
                        <label for="<?php echo esc_attr( $field_id( 'task' ) ); ?>" class="aimentor-automation-job__label"><?php esc_html_e( 'Generation type', 'aimentor' ); ?></label>
                        <select id="<?php echo esc_attr( $field_id( 'task' ) ); ?>" name="<?php echo esc_attr( $field_name( 'task' ) ); ?>">
                            <?php foreach ( $task_options as $task_key => $task_label ) : ?>
                            <option value="<?php echo esc_attr( $task_key ); ?>" <?php selected( $task, $task_key ); ?>><?php echo esc_html( $task_label ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                    <p>
                        <label for="<?php echo esc_attr( $field_id( 'provider' ) ); ?>" class="aimentor-automation-job__label"><?php esc_html_e( 'Provider override', 'aimentor' ); ?></label>
                        <select id="<?php echo esc_attr( $field_id( 'provider' ) ); ?>" name="<?php echo esc_attr( $field_name( 'provider' ) ); ?>">
                            <?php foreach ( $provider_options as $provider_key => $provider_label ) : ?>
                            <option value="<?php echo esc_attr( $provider_key ); ?>" <?php selected( $provider, $provider_key ); ?>><?php echo esc_html( $provider_label ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                    <p>
                        <label for="<?php echo esc_attr( $field_id( 'tier' ) ); ?>" class="aimentor-automation-job__label"><?php esc_html_e( 'Performance tier', 'aimentor' ); ?></label>
                        <select id="<?php echo esc_attr( $field_id( 'tier' ) ); ?>" name="<?php echo esc_attr( $field_name( 'tier' ) ); ?>">
                            <?php foreach ( $tier_options as $tier_key => $tier_label ) : ?>
                            <option value="<?php echo esc_attr( $tier_key ); ?>" <?php selected( $tier, $tier_key ); ?>><?php echo esc_html( $tier_label ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </p>
                </div>
                <div class="aimentor-automation-job__prompt">
                    <span class="aimentor-automation-job__label"><?php esc_html_e( 'Prompt source', 'aimentor' ); ?></span>
                    <div class="aimentor-automation-job__prompt-type">
                        <label>
                            <input type="radio" name="<?php echo esc_attr( $field_name( 'prompt_type' ) ); ?>" value="saved_prompt" <?php checked( $prompt_type, 'saved_prompt' ); ?> />
                            <?php esc_html_e( 'Saved prompt', 'aimentor' ); ?>
                        </label>
                        <label>
                            <input type="radio" name="<?php echo esc_attr( $field_name( 'prompt_type' ) ); ?>" value="manual" <?php checked( $prompt_type, 'manual' ); ?> />
                            <?php esc_html_e( 'Manual template', 'aimentor' ); ?>
                        </label>
                    </div>
                    <div class="aimentor-automation-job__prompt-saved" <?php if ( 'saved_prompt' !== $prompt_type ) : ?>style="display:none"<?php endif; ?>>
                        <label for="<?php echo esc_attr( $field_id( 'saved_prompt_id' ) ); ?>" class="screen-reader-text"><?php esc_html_e( 'Saved prompt', 'aimentor' ); ?></label>
                        <select id="<?php echo esc_attr( $field_id( 'saved_prompt_id' ) ); ?>" name="<?php echo esc_attr( $field_name( 'saved_prompt_id' ) ); ?>">
                            <option value=""><?php esc_html_e( 'Select a saved prompt…', 'aimentor' ); ?></option>
                            <?php foreach ( $prompt_options as $option ) : ?>
                            <option value="<?php echo esc_attr( $option['id'] ); ?>" <?php selected( $saved_prompt_id, $option['id'] ); ?>><?php echo esc_html( $option['label'] ); ?></option>
                            <?php endforeach; ?>
                        </select>
                        <p class="description"><?php esc_html_e( 'Choose a saved prompt to run for each post.', 'aimentor' ); ?></p>
                    </div>
                    <div class="aimentor-automation-job__prompt-manual" <?php if ( 'manual' !== $prompt_type ) : ?>style="display:none"<?php endif; ?>>
                        <label for="<?php echo esc_attr( $field_id( 'prompt_template' ) ); ?>" class="screen-reader-text"><?php esc_html_e( 'Prompt template', 'aimentor' ); ?></label>
                        <textarea id="<?php echo esc_attr( $field_id( 'prompt_template' ) ); ?>" name="<?php echo esc_attr( $field_name( 'prompt_template' ) ); ?>" rows="5" class="large-text" placeholder="<?php esc_attr_e( 'Use placeholders like {{post_title}} or {{post_excerpt}}', 'aimentor' ); ?>"><?php echo esc_textarea( $prompt_template ); ?></textarea>
                        <p class="description"><?php esc_html_e( 'Templates support tokens such as {{post_title}}, {{post_excerpt}}, {{post_url}}, and {{site_name}}.', 'aimentor' ); ?></p>
                    </div>
                </div>
                <div class="aimentor-automation-job__knowledge">
                    <label class="aimentor-automation-job__label" for="<?php echo esc_attr( $field_id( 'knowledge_ids' ) ); ?>"><?php esc_html_e( 'Knowledge packs', 'aimentor' ); ?></label>
                    <select multiple id="<?php echo esc_attr( $field_id( 'knowledge_ids' ) ); ?>" name="<?php echo esc_attr( $field_name( 'knowledge_ids' ) ); ?>[]">
                        <?php if ( empty( $knowledge_packs ) ) : ?>
                        <option value="" disabled><?php esc_html_e( 'No knowledge packs available yet.', 'aimentor' ); ?></option>
                        <?php else : ?>
                        <?php foreach ( $knowledge_packs as $pack ) :
                                if ( empty( $pack['id'] ) ) {
                                        continue;
                                }
                                $selected = in_array( $pack['id'], $knowledge_ids, true );
                                ?>
                        <option value="<?php echo esc_attr( $pack['id'] ); ?>" <?php selected( $selected ); ?>><?php echo esc_html( $pack['title'] ); ?></option>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <p class="description"><?php esc_html_e( 'Optional grounding to keep outputs aligned with brand guidance.', 'aimentor' ); ?></p>
                </div>
            </div>
        </div>
        <?php
};

$next_index      = count( $automation_jobs );
$next_index_attr = $next_index ? $next_index : 1;
$job_render_context = [
        'post_types'          => $post_types,
        'cadence_options'     => $cadence_options,
        'post_status_options' => $post_status_options,
        'provider_options'    => $provider_options,
        'tier_options'        => $tier_options,
        'task_options'        => $task_options,
        'prompt_options'      => $prompt_options,
        'knowledge_packs'     => $knowledge_packs,
];

$template_job = [
        'id'              => '',
        'label'           => '',
        'status'          => 'active',
        'post_type'       => 'post',
        'post_status'     => 'publish',
        'selection'       => 'recent',
        'post_ids'        => [],
        'limit'           => 1,
        'cadence'         => 'daily',
        'task'            => 'content',
        'provider'        => '',
        'tier'            => '',
        'prompt_type'     => 'saved_prompt',
        'saved_prompt_id' => '',
        'prompt_template' => '',
        'knowledge_ids'   => [],
];
?>
<div class="aimentor-settings-layout">
    <div class="aimentor-settings-main">
        <form method="post" action="options.php" class="aimentor-settings-form aimentor-settings-form--automation">
            <?php settings_fields( 'aimentor_settings' ); ?>
            <table class="form-table">
                <tr>
                    <th scope="row"><?php esc_html_e( 'Enable automation', 'aimentor' ); ?></th>
                    <td>
                        <input type="hidden" name="aimentor_enable_automation" value="no" />
                        <label>
                            <input type="checkbox" name="aimentor_enable_automation" value="yes" <?php checked( $automation_enabled ); ?> />
                            <?php esc_html_e( 'Run automation jobs on the hourly schedule.', 'aimentor' ); ?>
                        </label>
                        <p class="description"><?php esc_html_e( 'WordPress cron must be active to trigger each run. Configure your server cron if the site receives low traffic.', 'aimentor' ); ?></p>
                    </td>
                </tr>
            </table>
            <div class="aimentor-automation" data-next-index="<?php echo esc_attr( $next_index_attr ); ?>">
                <div class="aimentor-automation-jobs" data-next-index="<?php echo esc_attr( $next_index_attr ); ?>">
                    <?php if ( empty( $automation_jobs ) ) : ?>
                    <?php call_user_func( $render_job, $template_job, 0, array_merge( $job_render_context, [ 'template' => false ] ) ); ?>
                    <?php $next_index = 1; ?>
                    <?php else : ?>
                        <?php foreach ( $automation_jobs as $index => $job ) : ?>
                            <?php call_user_func( $render_job, $job, $index, array_merge( $job_render_context, [ 'template' => false ] ) ); ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <p>
                    <button type="button" class="button button-secondary aimentor-automation-add"><?php esc_html_e( 'Add automation job', 'aimentor' ); ?></button>
                </p>
            </div>
            <?php submit_button(); ?>
        </form>
        <template id="aimentor-automation-job-template">
            <?php call_user_func( $render_job, $template_job, '__INDEX__', array_merge( $job_render_context, [ 'template' => true ] ) ); ?>
        </template>
        <div class="aimentor-automation-summary">
            <h2><?php esc_html_e( 'Automation queue snapshot', 'aimentor' ); ?></h2>
            <?php if ( empty( $automation_queue_state['jobs'] ) ) : ?>
                <p class="description"><?php esc_html_e( 'No job history recorded yet. Save a job to start tracking runs.', 'aimentor' ); ?></p>
            <?php else : ?>
                <ul class="aimentor-automation-summary__list">
                    <?php foreach ( $automation_queue_state['jobs'] as $job_id => $state ) :
                            $job_entry = null;
                            foreach ( $automation_jobs as $job_candidate ) {
                                    if ( isset( $job_candidate['id'] ) && (string) $job_candidate['id'] === (string) $job_id ) {
                                            $job_entry = $job_candidate;
                                            break;
                                    }
                            }
                            $label_text = $job_entry && ! empty( $job_entry['label'] ) ? $job_entry['label'] : $job_id;
                            $last_run  = isset( $state['last_run'] ) ? absint( $state['last_run'] ) : 0;
                            $next_run  = isset( $state['next_run'] ) ? absint( $state['next_run'] ) : 0;
                            ?>
                    <li>
                        <strong><?php echo esc_html( $label_text ); ?></strong>
                        <span class="aimentor-automation-summary__meta">
                            <?php if ( $last_run ) : ?>
                                <?php
                                $last_run_human = function_exists( 'wp_date' ) ? wp_date( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $last_run ) : date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), $last_run );
                                printf( esc_html__( 'Last run: %s', 'aimentor' ), esc_html( $last_run_human ) );
                                ?>
                            <?php else : ?>
                                <?php esc_html_e( 'Not yet run', 'aimentor' ); ?>
                            <?php endif; ?>
                            <?php if ( $next_run ) : ?>
                                <?php
                                $next_run_human = function_exists( 'human_time_diff' ) ? human_time_diff( $now, $next_run ) : '';
                                if ( $next_run_human ) {
                                        printf( ' · %s', esc_html( sprintf( __( 'Next in %s', 'aimentor' ), $next_run_human ) ) );
                                }
                                ?>
                            <?php endif; ?>
                            <?php if ( ! empty( $state['pending'] ) ) : ?>
                                <?php printf( ' · %s', esc_html( sprintf( _n( '%d pending draft', '%d pending drafts', $state['pending'], 'aimentor' ), $state['pending'] ) ) ); ?>
                            <?php endif; ?>
                            <?php if ( ! empty( $state['last_error'] ) ) : ?>
                                <?php printf( ' · %s', esc_html( $state['last_error'] ) ); ?>
                            <?php endif; ?>
                        </span>
                    </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
        <?php if ( $automation_pending_count ) : ?>
        <div class="notice notice-warning inline">
            <p><?php echo esc_html( sprintf( _n( 'There is %d automation draft awaiting review.', 'There are %d automation drafts awaiting review.', $automation_pending_count, 'aimentor' ), $automation_pending_count ) ); ?></p>
            <p><a class="button" href="<?php echo esc_url( add_query_arg( [ 'page' => 'aimentor-settings', 'tab' => 'logs' ], admin_url( 'admin.php' ) ) ); ?>"><?php esc_html_e( 'Review queue', 'aimentor' ); ?></a></p>
        </div>
        <?php endif; ?>
    </div>
    <?php include plugin_dir_path( __FILE__ ) . 'sidebar-support.php'; ?>
</div>
