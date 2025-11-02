=== AiMentor Elementor ===
Contributors: aimentor
Tags: elementor, ai, grok, openai, builder, automation
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 0.0.001
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

== Description ==
AiMentor Elementor turns every prompt into a polished Elementor layout. Choose between leading AI providers like xAI Grok and OpenAI, swap models per project, and keep creative control with instant previews inside the WordPress editor.

* ⚡ Rapid section, page, and template generation inside Elementor.
* 🔁 Multi-provider switching with provider-specific defaults and limits.
* 🔒 Secure key handling, visibility toggles, and connection health badges.
* 📊 Error logging and rate-limit visibility for confident production use.

AiMentor was built for agencies that need reliable AI output without leaving WordPress. Bring your preferred provider, align the models with your workflow, and let AiMentor keep the content flowing.

== Installation ==
1. Upload the `aimentor-elementor` folder to `/wp-content/plugins/` or install the ZIP through **Plugins → Add New**.
2. Activate **AiMentor Elementor** through the **Plugins** menu in WordPress.
3. Visit **Settings → AiMentor** to choose your provider and paste API keys for xAI Grok and/or OpenAI.
4. Click **Test Connection** to confirm connectivity, then start generating blocks directly from the Elementor panel.

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
* Source code: https://github.com/jagjourney/aimentor-elementor
* Latest ZIP (tagged releases): https://github.com/jagjourney/aimentor-elementor/releases
* Release automation: `.github/workflows/release.yml` validates PHP syntax, builds the ZIP package, attaches it to the release,
  and refreshes the lightweight manifest on `gh-pages`.
* Release checklist and versioning guide: `docs/release-guide.md`.

== Deployment ==
Tagged releases automatically produce `aimentor-elementor-v*.zip` via GitHub Actions and attach the artifact to the corresponding release. A JSON manifest maintained on the `gh-pages` branch mirrors the latest tag for any external tooling that still references it.
Refer to `docs/release-guide.md` for detailed tagging, workflow, and post-release steps.

Need to ship a hotfix outside of that flow? Build the archive locally with the same folder structure, then upload it to a draft GitHub release so the workflow can take over once the release is published.

== Frequently Asked Questions ==
= Does AiMentor support multiple AI providers? =
Yes. The control center lets you pick between xAI Grok and OpenAI today, with room to add additional providers as new APIs are released. Each provider keeps its own defaults and usage limits.

= Where are my API keys stored? =
Keys are saved in your WordPress database using the standard options table. You can hide or show keys on demand from the settings screen, and revoke them at any time from your AI provider dashboard.

= Can I disable automatic insertion into the canvas? =
Absolutely. Use the **Auto-Insert** toggle in the settings to decide if generated content drops straight into Elementor or if you prefer manual placement.

== Screenshots ==
1. AiMentor settings highlighting multi-provider connectivity.
2. Elementor sidebar with the AiMentor widget selected.
3. Generated layout preview ready to insert into the canvas.

== Changelog ==
= 0.0.001 =
* Rebranded the experience as **AiMentor** with refreshed assets and menu labels.
* Added migrations for legacy pre-rebrand options, maintaining backward compatibility.
* Expanded copy and interface elements to highlight multi-provider switching.

= 1.4.2 =
* Improved canvas insertion reliability and prompt persistence.
* Generated layouts stay focused on the requested page type unless headers/footers are specified.

= 1.4.1 =
* Guaranteed Elementor widget visibility with refreshed iconography.
* Restored missing logic to keep generator buttons available.

= 1.4.0 =
* Added new 2025-ready Grok model presets with `grok-3-beta` default.
* Resolved HTTP 422 responses with better request validation.

= 1.3.10 =
* Hardened array access for the error log viewer.
* Ensured emergency widget registration for all Elementor loads.
* Synced default model selection logic across providers.

= 1.3.9 =
* Finalized provider timeouts and completion handling.

= 1.3.8 =
* Updated deprecated model references and increased generation timeout.

= 1.3.6 =
* Patched error log table warnings for cleaner debugging.

= 1.3.5 =
* Added password visibility toggles, auto-sizing inputs, and improved logging.

= 1.3.4 =
* Removed duplicate settings link registration to prevent fatal errors.
* Added SSL bypass toggle for local development environments.

= 1.3.3 =
* Introduced eye icon toggle for API keys with improved spacing.

= 1.3.2 =
* Enhanced eye toggle workflow for password fields.

= 1.3.1 =
* Added uninstall script and reinforced error logging.

= 1.3.0 =
* Expanded error handling with visible logs within the settings page.

= 1.2.10 =
* Defined `ajaxurl` inline and hardened nonce usage for admin requests.

= 1.2.9 =
* Improved frontend error handling for generator interactions.

= 1.2.8 =
* Embedded the generate button directly in the Elementor widget.

= 1.2.7 =
* Updated the “Write with AiMentor” CTA and ensured widget visibility.

= 1.2.6 =
* Refined the AiMentor modal popup workflow inside Elementor.

= 1.2.5 =
* Added the first “Write with AiMentor” popup experience.

= 1.2.4 =
* Adjusted widget registration timing for Elementor compatibility.

= 1.2.3 =
* Restored the generate button and textarea pairing in the widget.

= 1.2.2 =
* Added initial screenshot assets for the plugin listing.

= 1.2.1 =
* Resolved duplicate function declarations and introduced Grok model selector.

= 1.2.0 =
* Launched Grok-powered AI generation through the xAI API.
