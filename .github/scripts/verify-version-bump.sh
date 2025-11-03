#!/usr/bin/env bash
set -euo pipefail

error() {
  echo "::error::$1" >&2
  exit 1
}

BASE_SHA=${BASE_SHA:-}
HEAD_SHA=${HEAD_SHA:-HEAD}

if [[ -z "$BASE_SHA" ]]; then
  error "BASE_SHA environment variable is required."
fi

changed_files=$(git diff --name-only "$BASE_SHA" "$HEAD_SHA")

if [[ -z "$changed_files" ]]; then
  echo "No changes detected between $BASE_SHA and $HEAD_SHA; skipping version verification."
  exit 0
fi

plugin_changed=0
while IFS= read -r file; do
  [[ -z "$file" ]] && continue
  if [[ "$file" == *.php || "$file" == assets/* || "$file" == js/* ]]; then
    plugin_changed=1
    break
  fi
done <<< "$changed_files"

if [[ "$plugin_changed" -eq 0 ]]; then
  echo "No plugin runtime files changed; skipping version verification."
  exit 0
fi

extract_php_version() {
  local treeish="$1"
  local label="$2"
  local content
  if ! content=$(git show "${treeish}:aimentor-elementor.php" 2>/dev/null); then
    error "Unable to read aimentor-elementor.php from ${label} (${treeish})."
  fi

  local header
  header=$(echo "$content" | grep -E '^\s*\*\s*Version:\s*[0-9]' | head -n1 | sed -E 's/^\s*\*\s*Version:\s*([0-9]+(\.[0-9]+)+).*/\1/')
  if [[ -z "$header" ]]; then
    error "Could not find plugin header version in aimentor-elementor.php (${label})."
  fi

  local constant
  constant=$(echo "$content" | grep -E "define\(\s*'AIMENTOR_PLUGIN_VERSION'" | head -n1 | sed -E "s/.*'([0-9]+(\.[0-9]+)+)'.*/\1/")
  if [[ -z "$constant" ]]; then
    error "Could not find AIMENTOR_PLUGIN_VERSION constant in aimentor-elementor.php (${label})."
  fi

  if [[ "$header" != "$constant" ]]; then
    error "aimentor-elementor.php (${label}) is internally inconsistent: header version ${header} != constant ${constant}."
  fi

  printf '%s' "$header"
}

extract_readme_version() {
  local treeish="$1"
  local label="$2"
  local version
  version=$(git show "${treeish}:readme.txt" 2>/dev/null | grep -E '^Stable tag:' | head -n1 | sed -E 's/^Stable tag:\s*([0-9]+(\.[0-9]+)+).*/\1/')
  if [[ -z "$version" ]]; then
    error "Could not find Stable tag in readme.txt (${label})."
  fi
  printf '%s' "$version"
}

extract_docs_version() {
  local treeish="$1"
  local label="$2"
  mapfile -t version_array < <(git show "${treeish}:docs/landing/README.md" 2>/dev/null | grep -Eo 'v[0-9]+(\.[0-9]+)+' | sed 's/^v//' | sort -u)
  if (( ${#version_array[@]} == 0 )); then
    error "Could not find download URL version in docs/landing/README.md (${label})."
  fi
  if (( ${#version_array[@]} > 1 )); then
    error "docs/landing/README.md (${label}) references multiple versions: ${version_array[*]}."
  fi
  printf '%s' "${version_array[0]}"
}

extract_manifest_version() {
  local treeish="$1"
  local label="$2"
  local json
  if ! json=$(git show "${treeish}:manifests/aimentor-plugin-info.json" 2>/dev/null); then
    error "Unable to read manifests/aimentor-plugin-info.json from ${label} (${treeish})."
  fi

  local version download_url
  version=$(echo "$json" | jq -r '.version // empty')
  download_url=$(echo "$json" | jq -r '.download_url // empty')

  if [[ -z "$version" ]]; then
    error "Could not parse version from manifests/aimentor-plugin-info.json (${label})."
  fi
  if [[ -z "$download_url" ]]; then
    error "Could not parse download_url from manifests/aimentor-plugin-info.json (${label})."
  fi

  if [[ "$download_url" =~ /download/v([0-9]+(\.[0-9]+)+)/aimentor-elementor-v([0-9]+(\.[0-9]+)+)\.zip$ ]]; then
    local url_version="${BASH_REMATCH[1]}"
    local zip_version="${BASH_REMATCH[3]}"
    if [[ "$url_version" != "$zip_version" ]]; then
      error "Download URL in manifests/aimentor-plugin-info.json (${label}) is inconsistent (${url_version} vs ${zip_version})."
    fi
    if [[ "$url_version" != "$version" ]]; then
      error "manifests/aimentor-plugin-info.json (${label}) version ${version} does not match download URL version ${url_version}."
    fi
  else
    error "Download URL in manifests/aimentor-plugin-info.json (${label}) does not match expected pattern."
  fi

  printf '%s' "$version"
}

head_php=$(extract_php_version "$HEAD_SHA" "head")
base_php=$(extract_php_version "$BASE_SHA" "base")

head_readme=$(extract_readme_version "$HEAD_SHA" "head")
base_readme=$(extract_readme_version "$BASE_SHA" "base")

head_docs=$(extract_docs_version "$HEAD_SHA" "head")
base_docs=$(extract_docs_version "$BASE_SHA" "base")

head_manifest=$(extract_manifest_version "$HEAD_SHA" "head")
base_manifest=$(extract_manifest_version "$BASE_SHA" "base")

mapfile -t unique_head_versions < <(printf '%s\n' "$head_php" "$head_readme" "$head_docs" "$head_manifest" | sort -u)
if (( ${#unique_head_versions[@]} != 1 )); then
  error "Version strings are inconsistent across files: head versions -> ${unique_head_versions[*]}."
fi

missing_bumps=()
[[ "$head_php" == "$base_php" ]] && missing_bumps+=("aimentor-elementor.php")
[[ "$head_readme" == "$base_readme" ]] && missing_bumps+=("readme.txt")
[[ "$head_docs" == "$base_docs" ]] && missing_bumps+=("docs/landing/README.md")
[[ "$head_manifest" == "$base_manifest" ]] && missing_bumps+=("manifests/aimentor-plugin-info.json")

if (( ${#missing_bumps[@]} > 0 )); then
  echo "Version bump required: update versions in ${missing_bumps[*]} before merging." >&2
  exit 1
fi

echo "Version strings updated to ${unique_head_versions[0]} across required files."
