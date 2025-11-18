# Automation Safeguards & Scheduling Guidance

Automation jobs can publish a significant amount of generated content, so guardrails keep the workflow predictable and safe.

## Safeguards
- **Draft-first outputs:** Automation runs always write to the private `ai_layout` post type. Editors must approve and apply a draft before it overwrites the source post.
- **Source linkage:** Each layout stores `_aimentor_automation_job_id` and `_aimentor_automation_source_id` metadata so the Logs tab and admin actions can trace results back to their originating job and post.
- **Rate-limit awareness:** Provider responses that include cooldown metadata update the automation queue state and log a warning through `aimentor_log_error`. The runner skips execution until the cooldown expires.
- **Per-post throttling:** Jobs record `_aimentor_automation_*` meta on each source post. A single post will not be reprocessed faster than the configured cadence, even if the job limits allow more results.
- **Approval workflow:** Admins can approve/apply or dismiss outputs from **Settings → AiMentor → Logs** without leaving the dashboard. Actions update queue metrics immediately.

## Recommended Frequencies
| Cadence | When to use | Notes |
| --- | --- | --- |
| Hourly | High-volume sites updating landing pages or listings continuously. | Keep the job limit low (1–3 posts) to avoid rate-limit pressure. |
| Twice Daily | Newsrooms or blogs with multiple daily posts. | Balance freshness with API usage. Monitor queue logs for cooldown notices. |
| Daily | Standard editorial calendars. | Ideal default. Provides a 24-hour buffer between runs of the same post. |
| Weekly | Evergreen campaigns or seasonal refreshes. | Useful for long-form content that only needs periodic reviews. |

## Operational Checklist
1. Enable automation from the Automation settings tab and confirm WP-Cron (or a server cron) is active.
2. Start with a single job targeting a limited content subset. Review outputs for a week before expanding coverage.
3. Combine knowledge packs with automation jobs to enforce brand and compliance language in every run.
4. Review the Logs tab after each run to approve or dismiss drafts. Leaving drafts pending keeps them out of production.
5. Watch the queue snapshot for rate-limit warnings. If they appear frequently, reduce the cadence or limit, or stagger jobs across providers.
