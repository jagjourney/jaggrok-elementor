=== AiMentor Elementor ===
Contributors: jagjourney
Tags: elementor, ai, grok, page builder, xai
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.05
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==
🚀 FREE AI Page Builder - Generate full Elementor layouts with AiMentor. One prompt = complete pages!

* ⚡ Rapid section, page, and template generation inside Elementor.
* 🔁 Multi-provider switching with provider-specific defaults and limits.
* 🔒 Secure key handling, visibility toggles, and connection health badges.
* 📊 Error logging and rate-limit visibility for confident production use.

AiMentor was built for agencies that need reliable AI output without leaving WordPress. Bring your preferred provider, align the models with your workflow, and let AiMentor keep the content flowing.

== Features ==
* **Provider model presets:** Ship with tuned defaults for Grok and OpenAI so each provider loads with recommended models, temperature, and safety guardrails tailored to their API capabilities.
* **Auto-Insert toggle:** Decide if generated layouts land directly in the Elementor canvas or wait for manual placement—perfect for teams that want review checkpoints.
* **Theme styling controls:** Keep generated sections on-brand by toggling AiMentor's theme-aware styling helpers on or off per site.
* **Token limit management:** Configure per-provider token ceilings to prevent runaway prompts and stay inside your contract limits.
* **API key visibility controls:** Reveal or mask keys instantly with the built-in eye toggle so only trusted teammates can view sensitive credentials.
* **Connection status badges:** Test each provider and get immediate success, pending, or error badges that confirm when an integration is ready for production.
* **Built-in error log viewer:** Inspect the most recent API issues without leaving WordPress; the underlying log file lives at `wp-content/uploads/aimentor/aimentor-errors.log` (with automatic fallbacks if that path is unavailable).

== Compatibility ==
AiMentor requires the free version of Elementor to be active for its widget, settings, and copy workflows to load. When Elementor Pro is detected, advanced experiences—including canvas JSON generation and the in-dashboard **Pro Features** toggle—automatically light up. If Elementor Pro is not available, AiMentor gracefully downgrades canvas requests to copy output while keeping all text generation flows first-class for free users, so teams can still ship content without compromise.

=== Roadmap / Suggestions ===
We're exploring deeper Pro-aware enhancements such as exporting full template kits, supporting Elementor Theme Builder sections, and broader automation hooks so Pro customers can unlock richer handoff options while the free tier keeps its streamlined experience.

== Elementor Widget Experience ==
AiMentor's widget mirrors the native Elementor workflow: pick your provider from the branded badge selector, drop a prompt into the focused input field, and trigger generation without losing context. Provider-colored badges keep the active model obvious, while the same connection health indicators used in the settings surface directly in the widget so editors know when it's safe to launch a run.

== Operational Tooling ==
* **Error log access:** Every request writes structured context to `wp-content/uploads/aimentor/aimentor-errors.log`, and the settings screen renders the last 10 entries with timestamps and provider names for fast triage.
* **Connection health badges:** The settings dashboard keeps a persistent badge per provider—Success, Pending, Error, or Idle—so agencies can verify API uptime before teams start building.

== Installation ==
1. Upload ZIP.
2. Bring API credentials for your preferred provider—generate an xAI Grok key at https://x.ai/api or create an OpenAI key at https://platform.openai.com/account/api-keys.
3. Open **Settings → AiMentor Elementor** and choose **xAI Grok** or **OpenAI** under **Provider** to set the active radio option.
4. Paste your keys and click the **Test Connection** buttons to verify each provider so you know when their API keys are valid.

== Automatic Updates ==
AiMentor Elementor now advertises GitHub Releases as its canonical update source. The plugin bootstrap includes an `Update URI`
that matches this repository, so WordPress core surfaces each published release in the **Plugins** screen.

* Enable **Automatic updates** for AiMentor Elementor in **Plugins → Installed Plugins** to let WordPress install new GitHub
  releases as soon as they are published.
* Prefer manual control? When a new AiMentor Elementor version appears, click **Update now** in the plugin list to fetch the
  GitHub-hosted ZIP without leaving the dashboard.

No additional AiMentor-specific updater endpoints are required—the historical PHP/JSON feed has been retired in favor of the
native GitHub channel.

== Development ==
* Source code: https://github.com/aimentor/aimentor-elementor
* Latest ZIP (tagged releases): https://github.com/aimentor/aimentor-elementor/releases
* Release automation: `.github/workflows/release.yml` validates PHP syntax, builds the ZIP package, attaches it to the release,
  and refreshes the lightweight manifest on `gh-pages`.
* Release checklist and versioning guide: `docs/release-guide.md`.

== Deployment ==
Tagged releases automatically produce `aimentor-elementor-v*.zip` via GitHub Actions and attach the artifact to the corresponding release. A JSON manifest maintained on the `gh-pages` branch mirrors the latest tag for any external tooling that still references it.
Refer to `docs/release-guide.md` for detailed tagging, workflow, and post-release steps.

Need to stage a build before publication? Save the GitHub release as a draft—the automation now packages and uploads the official ZIP while deferring the manifest update until you publish. Manual ZIP uploads remain optional for bespoke artifacts.

== Frequently Asked Questions ==
= Does AiMentor support multiple AI providers? =
Yes. The control center lets you pick between xAI Grok and OpenAI today, with room to add additional providers as new APIs are released. Each provider keeps its own defaults and usage limits.

= Where are my API keys stored? =
Keys are saved in your WordPress database using the standard options table. You can hide or show keys on demand from the settings screen, and revoke them at any time from your AI provider dashboard.

= Can I disable automatic insertion into the canvas? =
Absolutely. Use the **Auto-Insert** toggle in the settings to decide if generated content drops straight into Elementor or if you prefer manual placement.

== Screenshots ==
1. Drag AiMentor widget to canvas
2. Enter prompt in left panel
3. Generate content in middle canvas

== Changelog ==
= 1.0.05 =
* Allow the release automation to process draft and pre-release tags so teams can validate packaged ZIPs before publication while keeping the WordPress manifest untouched until the release goes live.

= 1.0.04 =
* Hardened the CI guardrail so every change to the repository requires a synchronized version bump before merging.

= 1.0.03 =
* Added an opt-in daily provider health check that re-tests stored API keys with WP-Cron.
* Captured consecutive connection failures and alerted administrators once the threshold is reached.
* Introduced settings to toggle automated checks and manage notification recipients.

= 1.0.00 =
* Reset the public changelog to establish AiMentor Elementor as the new baseline.
* Rebranded the plugin from JagGrok to AiMentor Elementor with compatibility shims for existing installs.
* Added migrations to mirror legacy options and asset paths so previous settings continue to work.
* Updated scripts, classes, and AJAX endpoints to use the new AiMentor handles across WordPress and Elementor.
