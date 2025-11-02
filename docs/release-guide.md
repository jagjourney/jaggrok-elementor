# AiMentor Elementor Release Guide

This guide documents the steps for preparing, publishing, and cleaning up a GitHub-backed release of the AiMentor Elementor plugin. Follow each section in order whenever you need to ship a new version or reset the published version number.

## Prerequisites

Before you start, make sure you have:

- Push access to both the `main` branch and the `gh-pages` branch of `jagjourney/aimentor-elementor`.
- Permission to create GitHub Releases and tags in the repository.
- A local Git environment configured with your GitHub account and GPG/SSH keys (if required by your organization).
- PHP 8.1+ available locally when you need to validate or reproduce build steps outside of CI.
- Time to update the changelog, metadata, and manifest so the workflow can publish the correct artifact and metadata.

## Version bump checklist

Each new release must consistently advertise the target version across source, documentation, and manifests. Update the following files together:

1. **`aimentor-elementor.php`**
   - Update the plugin header `Version:` field.
   - Update the `AIMENTOR_PLUGIN_VERSION` constant.
2. **`readme.txt`**
   - Update the `Stable tag` value.
   - Add or revise the changelog section for the new version.
3. **`docs/landing/README.md`**
   - Update the download URL reference so it points to the new ZIP name.
4. **`manifests/aimentor-plugin-info.json`** (for local testing)
   - Update `version`, `download_url`, and any hash placeholder if you are simulating a release. The GitHub Action overwrites this file on `gh-pages` during a real release, but keeping the default branch copy current prevents confusion.
5. **ZIP artifacts in `/downloads`** (only if you are attaching a handcrafted ZIP to a draft release)
   - Ensure the filename matches the `aimentor-elementor-vX.Y.Z.zip` pattern so the workflow can reuse it.

Commit the version bump alongside the changelog updates. Tagging should only happen after the pull request is merged into `main`.

## Resetting to version `0.0.001`

Occasionally you may need to reset the published version to `0.0.001` (for example, after migrating legacy customers back to a baseline build or when re-establishing plugin update channels following a rebrand). When that happens:

- Revert any in-flight version bumps and set all version fields listed above back to `0.0.001`.
- Document the rationale in the changelog so users understand why the version dropped.
- Publish the reset tag (`v0.0.001`) so WordPress update clients receive the corrective build. WordPress will treat the release as the latest version because it follows GitHub's published timestamp, not semver ordering.

## Cutting a release

1. **Merge preparation work**
   - Ensure `main` contains the desired code, documentation, and version bump changes.
   - Confirm the changelog entry accurately describes the release.
2. **Create a tag**
   - Use annotated tags that follow the `vX.Y.Z` convention (for example, `git tag -a v1.5.0 -m "Release v1.5.0"`).
   - Push the tag to GitHub with `git push origin vX.Y.Z`.
3. **Draft the GitHub Release**
   - Navigate to **Releases → Draft a new release**.
   - Select the new tag, fill in the release notes (consider reusing the changelog entry), and leave the release as a draft if you still need review from stakeholders.
   - Publishing the release triggers `.github/workflows/release.yml` automatically because it listens for the `release.published` event.
4. **Monitor the workflow**
   - The workflow validates PHP syntax, builds `aimentor-elementor-vX.Y.Z.zip`, attaches it to the release, and updates `gh-pages/manifests/aimentor-plugin-info.json` with the new metadata.
   - Wait for the workflow to finish successfully before sharing the release publicly.
5. **Verify GitHub assets**
   - Open the published release and confirm the ZIP asset exists with the expected filename.
   - Download the asset locally to spot-check the archive contents if necessary.
6. **Confirm the `gh-pages` manifest**
   - Visit the `gh-pages` branch and verify that `manifests/aimentor-plugin-info.json` now references the new tag, download URL, and checksum.
   - If the manifest failed to update, rerun the workflow or manually update the branch following the same JSON format.

## Post-release cleanup

After the workflow completes successfully:

- Update any outstanding documentation or marketing pages with the new version highlights.
- Double-check that `manifests/aimentor-plugin-info.json` in the default branch matches the published metadata so future diffs remain clean.
- Close or move tickets linked to the release and notify stakeholders (internal teams, agency partners, support) that the release is live.
- Queue any follow-up work (for example, documentation fixes or bugfixes) in a new issue so the release branch stays clean.
- If the next development cycle starts immediately, bump the plugin version in a new commit (for example, to a `-dev` suffix) so commits after release cannot be mistaken for the shipped version.

## Running the release workflow manually

If you need to re-run the GitHub Action without publishing a new release:

1. Navigate to **Actions → Release**.
2. Select the failed or completed run and click **Re-run job** to rebuild and reattach the artifact.
3. If you must trigger a fresh run with new code, draft a new release tied to the correct tag and publish it once everything is ready.

## Notifying stakeholders

When the release is verified:

- Share the release notes URL and the updated download link with stakeholders.
- Notify support and success teams so they can update customers.
- Post an announcement in the agreed internal channel (Slack/Teams/email) summarizing what shipped and any follow-up instructions.

Keep this guide handy and update it whenever the release workflow or supporting automation changes.
