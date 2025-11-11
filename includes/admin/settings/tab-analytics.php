<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

$summary_endpoint   = rest_url( 'aimentor/v1/analytics/summary' );
$guardrails_endpoint = rest_url( 'aimentor/v1/analytics/guardrails' );
$rest_nonce          = wp_create_nonce( 'wp_rest' );
?>
<div class="aimentor-settings-layout aimentor-settings-analytics" data-rest-nonce="<?php echo esc_attr( $rest_nonce ); ?>">
    <div class="aimentor-settings-main">
        <section class="aimentor-analytics__intro">
            <h2><?php esc_html_e( 'Usage analytics', 'aimentor' ); ?></h2>
            <p class="description"><?php esc_html_e( 'Track on-site generation activity and monitor guardrails before you reach provider limits. Analytics are stored locally and respect your site’s retention settings.', 'aimentor' ); ?></p>
            <button type="button" class="button button-secondary aimentor-analytics__refresh" data-endpoint="<?php echo esc_url( $summary_endpoint ); ?>"><?php esc_html_e( 'Refresh analytics', 'aimentor' ); ?></button>
        </section>

        <section class="aimentor-analytics__summary" data-role="analytics-summary" aria-live="polite">
            <p class="aimentor-analytics__placeholder"><?php esc_html_e( 'Loading analytics…', 'aimentor' ); ?></p>
        </section>

        <section class="aimentor-analytics__chart" data-role="analytics-chart" aria-live="polite"></section>

        <section class="aimentor-analytics__table" data-role="analytics-table" aria-live="polite"></section>

        <section class="aimentor-analytics__guardrails" aria-live="polite">
            <h3><?php esc_html_e( 'Guardrail controls', 'aimentor' ); ?></h3>
            <p class="description"><?php esc_html_e( 'Guardrails prevent runaway usage by blocking requests that exceed configured thresholds. Set any value to 0 to disable that guardrail.', 'aimentor' ); ?></p>
            <form class="aimentor-analytics-guardrails__form" data-endpoint="<?php echo esc_url( $guardrails_endpoint ); ?>">
                <table class="form-table" role="presentation">
                    <tbody>
                        <tr>
                            <th scope="row"><label for="aimentor-guardrail-daily"><?php esc_html_e( 'Daily site limit', 'aimentor' ); ?></label></th>
                            <td>
                                <input type="number" id="aimentor-guardrail-daily" name="daily_limit" min="0" class="small-text" />
                                <p class="description"><?php esc_html_e( 'Maximum successful generations allowed per 24 hours.', 'aimentor' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="aimentor-guardrail-hourly"><?php esc_html_e( 'Hourly site limit', 'aimentor' ); ?></label></th>
                            <td>
                                <input type="number" id="aimentor-guardrail-hourly" name="hourly_limit" min="0" class="small-text" />
                                <p class="description"><?php esc_html_e( 'Cap how many generations can run in any rolling hour.', 'aimentor' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="aimentor-guardrail-user"><?php esc_html_e( 'Per-user daily limit', 'aimentor' ); ?></label></th>
                            <td>
                                <input type="number" id="aimentor-guardrail-user" name="user_daily_limit" min="0" class="small-text" />
                                <p class="description"><?php esc_html_e( 'Optional cap for individual editors within a day.', 'aimentor' ); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><label for="aimentor-guardrail-warning"><?php esc_html_e( 'Warning threshold', 'aimentor' ); ?></label></th>
                            <td>
                                <input type="number" id="aimentor-guardrail-warning" name="warning_ratio" min="0.1" max="1" step="0.05" class="small-text" />
                                <span class="description"><?php esc_html_e( 'Trigger warnings once usage crosses this percentage of a limit.', 'aimentor' ); ?></span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <p class="submit">
                    <button type="submit" class="button button-primary"><?php esc_html_e( 'Save guardrails', 'aimentor' ); ?></button>
                    <span class="aimentor-analytics-guardrails__status" aria-live="polite"></span>
                </p>
            </form>
        </section>
    </div>
</div>
