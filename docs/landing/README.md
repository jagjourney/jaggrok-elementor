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
1. Download the latest AiMentor package from [`https://jaggrok-elementor.jagjourney.com/downloads/aimentor-elementor-v1.4.3.zip`](https://jaggrok-elementor.jagjourney.com/downloads/aimentor-elementor-v1.4.3.zip).
2. In WordPress, navigate to **Plugins → Add New → Upload Plugin** and upload the ZIP.
3. Activate AiMentor Elementor. Existing settings from JagGrok Elementor are migrated automatically on first load.
4. Visit **Settings → AiMentor** to choose your preferred AI provider and review the new multi-provider options.

## Legacy Clients
- Continue serving `plugin-info.json` for JagGrok Elementor until monitoring confirms at least one site has updated to AiMentor.
- Once adoption is verified, add an HTTP 301 redirect from `/plugin-info.json` to `/aimentor-plugin-info.json` so legacy builds can fetch the new metadata automatically.
- Retain the JagGrok documentation link but annotate that the AiMentor channel is the preferred update path going forward.

## Support CTA
Need help migrating? Contact **support@aimentor.ai** for concierge onboarding to the multi-provider release.
