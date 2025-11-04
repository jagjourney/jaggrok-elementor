#!/usr/bin/env bash
set -euo pipefail

declare -a VERSION_FILES=(
  "aimentor-elementor.php"
  "readme.txt"
  "docs/landing/README.md"
  "manifests/aimentor-plugin-info.json"
)

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

is_version_file() {
  local path="$1"
  for vf in "${VERSION_FILES[@]}"; do
    if [[ "$path" == "$vf" ]]; then
      return 0
    fi
  done
  return 1
}

non_version_changes=()
while IFS= read -r file; do
  [[ -z "$file" ]] && continue
  if ! is_version_file "$file"; then
    non_version_changes+=("$file")
  fi
done <<< "$changed_files"

if (( ${#non_version_changes[@]} == 0 )); then
  echo "Only version metadata files changed; validating consistency."
else
  printf 'Detected non-version changes:\n' >&2
  printf '  - %s\n' "${non_version_changes[@]}" >&2
  echo "Ensuring version bump accompanies repository changes." >&2
fi

require_tool() {
  if ! command -v "$1" >/dev/null 2>&1; then
    error "Required tool '$1' is not available in PATH."
  fi
}

require_tool jq

version_greater() {
  local lhs="$1"
  local rhs="$2"

  IFS='.' read -r -a lhs_parts <<< "$lhs"
  IFS='.' read -r -a rhs_parts <<< "$rhs"

  local max_len=${#lhs_parts[@]}
  if (( ${#rhs_parts[@]} > max_len )); then
    max_len=${#rhs_parts[@]}
  fi

  for (( i=0; i<max_len; i++ )); do
    local lhs_val=${lhs_parts[i]:-0}
    local rhs_val=${rhs_parts[i]:-0}

    # shellcheck disable=SC2004
    if (( 10#$lhs_val > 10#$rhs_val )); then
      return 0
    fi
    # shellcheck disable=SC2004
    if (( 10#$lhs_val < 10#$rhs_val )); then
      return 1
    fi
  done

  return 1
}

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

  if [[ "$download_url" =~ /download/v([0-9]+(\.[0-9]+)+)/aimentor-elementor(-v([0-9]+(\.[0-9]+)+))?\.zip$ ]]; then
    local url_version="${BASH_REMATCH[1]}"
    local zip_version="${BASH_REMATCH[4]:-}"
    if [[ -n "$zip_version" && "$url_version" != "$zip_version" ]]; then
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

if ! version_greater "$head_php" "$base_php"; then
  error "New version (${head_php}) must be greater than base version (${base_php})."
fi

echo "Version strings updated to ${unique_head_versions[0]} across required files."
