# Manifest Deployment Notes

Upload `aimentor-plugin-info.json` to `https://jaggrok-elementor.jagjourney.com/aimentor-plugin-info.json` alongside the existing `plugin-info.json`.

1. Serve both manifests until monitoring confirms that at least one AiMentor-branded build has checked in.
2. When ready, configure the web server to permanently redirect `/plugin-info.json` to `/aimentor-plugin-info.json` so legacy clients automatically receive the new metadata.
3. Ensure the ZIP referenced in `download_link` is available at `https://jaggrok-elementor.jagjourney.com/downloads/aimentor-elementor-v1.4.3.zip`.

The manifest fields have been updated to reflect the AiMentor product name, slug, tested WordPress versions, and AiMentor-focused description.
