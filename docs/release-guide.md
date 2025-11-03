# AiMentor Elementor Release Guide

This guide documents the steps for preparing, publishing, and cleaning up a GitHub-backed release of the AiMentor Elementor plugin. Follow each section in order whenever you need to ship a new version or reset the published version number.

> **Always bump the version.** Every pull request that changes runtime code or user-visible behavior must increment the plugin version and associated metadata before it is merged. Shipping work without a coordinated version bump blocks the release workflow and leaves WordPress sites unaware of the update.

## Prerequisites

Before you start, make sure you have:

- Push access to both the `main` branch and the `gh-pages` branch of `aimentor/aimentor-elementor`.
- Permission to create GitHub Releases and tags in the repository.
- A local Git environment configured with your GitHub account and GPG/SSH keys (if required by your organization).
- PHP 8.1+ available locally when you need to validate or reproduce build steps outside of CI.
- Time to update the changelog, metadata, and manifest so the workflow can publish the correct artifact and metadata.

## Version numbering and bump checklist

AiMentor Elementor uses a three-part numeric version where each slot has a defined increment size so releases stay in sync with WordPress auto-update expectations:

- **Small fix release (`+0.01`)** – For hotfixes or copy-only changes, bump the third slot by `0.01` (for example, `v1.0.05 → v1.0.06`).
- **Medium feature release (`+0.10`)** – For additive features that touch PHP or JavaScript, bump the middle slot by `0.10` and reset the last slot to `00` (for example, `v1.0.06 → v1.1.00`).
- **Major milestone release (`+1.00.00`)** – For large breaking changes or rebrands, bump the first slot by `1`, reset the remaining slots to `.0.00`, and ensure the changelog calls out upgrade guidance (for example, `v1.1.00 → v2.0.00`).

Every bump must land in a dedicated pull request so the merged commit and the annotated tag share the exact version string. Keep the following files aligned:

1. **`aimentor-elementor.php`**
   - Update the plugin header `Version:` field.
   - Update the `AIMENTOR_PLUGIN_VERSION` constant.
2. **`readme.txt`**
   - Update the `Stable tag` value.
   - Add or revise the changelog section for the new version.
3. **`docs/landing/README.md`**
   - Update the GitHub Releases download URL reference so it points to the new ZIP name.
4. **`manifests/aimentor-plugin-info.json`** (for local testing)
   - Update `version`, `download_url`, and any hash placeholder if you are simulating a release. The GitHub Action overwrites this file on `gh-pages` during a real release, but keeping the default branch copy current prevents confusion.
5. **ZIP artifacts in `/downloads`** (only if you are attaching a handcrafted ZIP to a draft release)
   - Ensure the filename matches the `aimentor-elementor-vX.Y.Z.zip` pattern so the workflow can reuse it.

### Increment examples

- **Small fix (`v1.0.05 → v1.0.06`)** – Update only the README copy and changelog, adjust the plugin header and constants, commit, then tag with `git tag -a v1.0.06 -m 'Release v1.0.06'`.
- **Medium feature (`v1.0.06 → v1.1.00`)** – After merging PHP/JS enhancements, reset the third slot to `00` across the files above, document the feature in `readme.txt`, and create the `v1.1.00` tag.
- **Major milestone (`v1.1.00 → v2.0.00`)** – Coordinate breaking changes, migrate documentation, ensure upgrade notes are prominent, and tag `v2.0.00` once all references match.

> **CI guardrail:** Every pull request that changes anything beyond the version metadata files must update all of the version references above. The `Version bump check` workflow fails with a message like `Version bump required: update versions in … before merging.` listing whichever files still need a bump, and it also verifies that the new version is strictly greater than the previous release.

Commit the version bump alongside the changelog updates. Once the pull request merges into `main`, automation handles tagging and drafting the corresponding GitHub release.

## Resetting to version `0.0.001`

Occasionally you may need to reset the published version to `0.0.001` (for example, after migrating legacy customers back to a baseline build or when re-establishing plugin update channels following a rebrand). When that happens:

- Revert any in-flight version bumps and set all version fields listed above back to `0.0.001`.
- Document the rationale in the changelog so users understand why the version dropped.
- Publish the reset tag (`v0.0.001`) so WordPress update clients receive the corrective build. WordPress will treat the release as the latest version because it follows GitHub's published timestamp, not semver ordering.

## Cut the next release

1. **Prepare code & changelog**
   - [ ] Confirm `main` includes the merged version bump, changelog entry, and supporting documentation updates.
   - [ ] Review this guide and other checklists for accuracy, updating them if the release introduces new requirements.
   - [ ] Verify the changelog clearly highlights user-facing changes, upgrade notes, and any known issues.
2. **Let CI tag & draft the release**
   - [ ] Merge the version-bump pull request into `main`. The `Auto tag and release` workflow (triggered when the version metadata files change) checks out the latest `main`, reads the plugin header version, and creates an annotated `vX.Y.Z` tag if one does not already exist.
   - [ ] Wait for the workflow to finish. It pushes the tag to GitHub and immediately opens a draft release using the matching notes file in `docs/releases/` when available. If the tag already exists—because you re-ran the workflow or tagged manually—it exits without modifying anything.
3. **Polish the release draft**
   - [ ] Open the newly created draft release and edit the notes if required (for example, to expand on the auto-imported changelog entry or attach supplemental assets).
   - [ ] Attach any prebuilt ZIP asset if the automation should reuse a handcrafted package; otherwise document that CI will build it.
   - [ ] Leave the release as a **draft** (or mark it as a **pre-release**) until QA approves publication. The downstream `.github/workflows/release.yml` run starts as soon as the draft is created and attaches the canonical ZIP even while the release remains private. The workflow logs explicitly call out that the manifest refresh is deferred until publication.
4. **Monitor automation**
   - [ ] Watch the release workflow in **Actions → Release** and confirm each job (build, artifact upload, manifest update) succeeds. Draft or pre-release runs will skip the manifest step by design; re-run the workflow after publishing if you make note edits while the release is live.
   - [ ] If the workflow fails on **Verify archive structure**, inspect the `zipinfo -1` output in the logs. Every entry must be nested under `aimentor-elementor/`; a file at the archive root usually means an excluded path slipped back into the rsync allowlist or a manual asset was attached. Fix the packaging rules, rebuild, and rerun the job before publishing.

> **Publish when ready:** Once QA signs off, publish the release (or toggle off the pre-release flag). Publication triggers another workflow run that updates the `gh-pages` manifest so WordPress sites see the new version.
   - [ ] Capture logs or screenshots if you need to document unusual warnings for later follow-up.
   - [ ] If the workflow fails, remediate the issue, retag if necessary, and rerun the workflow before announcing the release.
5. **Verify assets & manifest**
   - [ ] Open the published release to ensure the ZIP asset exists, is named `aimentor-elementor-vX.Y.Z.zip`, and lists the correct build timestamp.
   - [ ] Download the asset locally to spot-check the archive contents and plugin metadata.
   - [ ] Inspect `gh-pages/manifests/aimentor-plugin-info.json` to confirm it references the new tag, download URL, and checksum.

### Follow-up tasks

- [ ] Socialize any updates you made to this checklist with collaborators through the agreed documentation or communication channels.
- [ ] Monitor support channels after publication, document confirmed issues for the next release cycle, and schedule a retrospective if needed.

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
