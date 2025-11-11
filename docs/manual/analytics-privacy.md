# Analytics Privacy & Retention Guidelines

AiMentor logs lightweight usage analytics to help administrators monitor request volumes and guardrail status. The data never leaves your WordPress database and follows the policies below.

## What gets stored
- Timestamp, status (`success`, `error`, or `blocked`), and request channel (REST or Elementor AJAX).
- Provider slug, task/tier metadata, model identifier, and request origin (e.g., generator vs. canvas history).
- User ID (when available), token usage, and provider rate-limit headers.
- Guardrail configuration and enforcement results.

Canvas history and generation history entries now mirror the same metadata so that exports and REST responses stay consistent.

## Retention defaults
By default AiMentor keeps 30 days of analytics history and trims the option to 500 events. These values suit most single-site installs while protecting the database from unbounded growth.

You can override the retention window globally:

```php
add_filter( 'aimentor_analytics_retention_days', function() {
    return 14; // keep two weeks of analytics
} );

add_filter( 'aimentor_analytics_max_events', function() {
    return 1000; // raise the rolling cap for high-volume sites
} );
```

Remember that retention changes only apply to future writes. Existing rows older than the new window are dropped the next time an event is recorded.

## Aggregation granularity
Analytics summaries group events into hour/day/week buckets. Site owners can fine-tune the allowed intervals or the bucket math using filters:

```php
// Add a custom 15-minute interval and tweak labels.
add_filter( 'aimentor_analytics_allowed_intervals', function( $intervals ) {
    $intervals[] = 'quarter-hour';
    return $intervals;
} );

add_filter( 'aimentor_analytics_interval_config', function( $config ) {
    $config['quarter-hour'] = [
        'seconds' => 15 * MINUTE_IN_SECONDS,
        'format'  => 'M j H:i',
    ];

    return $config;
}, 10, 2 );
```

More granular adjustments are also available:

- `aimentor_analytics_bucket_seconds` — Override the seconds-per-bucket calculation.
- `aimentor_analytics_date_format` — Customize date labels rendered in the dashboard and CLI outputs.
- `aimentor_analytics_start_boundary` — Shift the rolling window start when you need to anchor analytics to a custom schedule.
- `aimentor_analytics_bucket_start` — Change how individual events are assigned to buckets.

Pair these with the WP-CLI export command (`wp aimentor analytics export`) for automated reporting pipelines.

## Privacy expectations
- Analytics live entirely inside your WordPress database; no network beacons or third-party dashboards are involved.
- Only administrators (`manage_options`) can access the analytics REST endpoints and settings tab.
- The UI surfaces guardrail warnings and limits without revealing prompt contents or sensitive payloads.
- CLI exports include counts and metadata, not generated text, so you can hand them to automation systems safely.

If your policies require stricter controls, clamp the retention window to a single day, zero out guardrail limits, or remove downstream consumers (cron jobs, exports) so analytics never leave the site. A must-use plugin can also purge the `aimentor_usage_analytics` option on a schedule to guarantee no history lingers between maintenance windows.
