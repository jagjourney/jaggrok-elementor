# Manifest Deployment Notes

The `gh-pages` branch now hosts `manifests/aimentor-plugin-info.json`. The release workflow updates this file after every
published tag so any tooling that still references the manifest can follow along with GitHub Releases.

1. Keep the `gh-pages` branch published (for example via GitHub Pages) so legacy clients that expect a JSON manifest continue to
   work.
2. No additional uploads to `updates.jagjourney.com` or `jaggrok-elementor.jagjourney.com` are required—the GitHub release asset is
   the canonical download location.
3. If you need to make a manual correction, apply the change directly to `gh-pages` and let the next tagged release reconcile the
   automated output.

The bespoke PHP/JSON update service has been retired now that WordPress consumes the GitHub metadata via the plugin's `Update
URI` header.
