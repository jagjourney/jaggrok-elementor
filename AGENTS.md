# Repository Instructions for Agents

## Mandatory version bump checklist
Every change set that adjusts runtime code, assets, or documentation for a release **must** complete the full version bump sequence before merging. Follow these steps without skipping any item:

1. Update `aimentor-elementor.php` with the new `Version:` header and `AIMENTOR_PLUGIN_VERSION` constant.
2. Update `readme.txt` with the matching `Stable tag` and a changelog entry describing the release.
3. Update `docs/landing/README.md` so the GitHub Releases download link references the new versioned ZIP.
4. Update `manifests/aimentor-plugin-info.json` with the `version`, `download_url`, and `released_at` timestamp for the new build.
5. Add or refresh the release notes under `docs/releases/` (create `vX.Y.Z.md` when a new tag is introduced).
6. Review `docs/release-guide.md` to confirm the process remains accurate and incorporate any required adjustments discovered during the bump.

Document any deviation from these steps in the pull request summary so reviewers understand how the release workflow stays aligned.

## Scope
These instructions apply to the entire repository. Future contributors should extend this file if additional global policies are introduced.
