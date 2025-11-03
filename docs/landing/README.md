# AiMentor Elementor Landing Content

This copy is intended for the WordPress update landing pages and manual installation instructions that are deployed to `jaggrok-elementor.jagjourney.com`.

## Headline
**AiMentor Elementor** — One prompt, complete Elementor layouts.

## Sub-headline
Upgrade from JagGrok Elementor to AiMentor's multi-provider page builder in minutes.

## Key Messaging
- AiMentor Elementor now supports multiple AI providers out of the box. Customers can keep their existing OpenAI or Grok credentials and opt-in to new providers as they are added.
- Installing the AiMentor build automatically migrates JagGrok settings, API keys, and saved provider preferences.
- The plugin continues to be tested against WordPress 6.4, Elementor 3.18, and Elementor Pro 3.18.

## Upgrade Path
1. Download the latest AiMentor package from the [GitHub Releases asset](https://github.com/aimentor/aimentor-elementor/releases/download/v1.1.00/aimentor-elementor-v1.1.00.zip) (replace the version in the URL with the currently promoted tag). Releases follow the small (`+0.01`), medium (`+0.10`), and major (`+1.00.00`) increment scheme documented in `docs/release-guide.md`.
2. In WordPress, navigate to **Plugins → Add New → Upload Plugin** and upload the ZIP.
3. Activate AiMentor Elementor. Existing settings from JagGrok Elementor are migrated automatically on first load.
4. Visit **Settings → AiMentor** to choose your preferred AI provider and review the new multi-provider options.

> **Auto-update channel:** WordPress tracks AiMentor releases through the plugin's `Update URI`, so enabling **Automatic updates** (or clicking **Update now** when prompted) fetches the same GitHub-hosted ZIP without manual intervention.

## Legacy Clients
- AiMentor now uses the plugin `Update URI` header to point WordPress directly at GitHub Releases. Automatic updates will follow the tagged ZIP attached to each release without any bespoke endpoints.
- Continue serving `plugin-info.json` for JagGrok Elementor until monitoring confirms at least one site has updated to AiMentor. Once adoption is verified, a 301 redirect to `/aimentor-plugin-info.json` keeps legacy check-ins aligned with the GitHub metadata.
- Retain the JagGrok documentation link but annotate that the GitHub-hosted AiMentor channel is the preferred update path going forward.

## Support CTA
Need help migrating? Contact **support@jagjourney.com** for concierge onboarding to the multi-provider release.
