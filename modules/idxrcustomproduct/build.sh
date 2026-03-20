#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/../.." && pwd)"
OUT_DIR="${SCRIPT_DIR}/build"

FILES=(
  "${SCRIPT_DIR}/views/js/front.js"
  "${SCRIPT_DIR}/views/js/front_accordion.js"
  "${PROJECT_ROOT}/themes/modez/modules/idxrcustomproduct/views/js/front.js"
)

ensure_terser() {
  if command -v terser >/dev/null 2>&1; then
    echo "Using global terser: $(command -v terser)"
    TERSER_BIN="terser"
    return
  fi

  if ! command -v npx >/dev/null 2>&1; then
    echo "Error: neither terser nor npx is available." >&2
    exit 1
  fi

  echo "Using npx terser"
  TERSER_BIN="npx --yes terser"
}

check_file() {
  local file="$1"
  if [[ ! -f "${file}" ]]; then
    echo "Skip missing file: ${file}"
    return 1
  fi
  node --check "${file}" >/dev/null
  return 0
}

build_one() {
  local src="$1"
  local rel
  rel="$(realpath --relative-to="${PROJECT_ROOT}" "${src}")"
  local target="${OUT_DIR}/${rel}"
  mkdir -p "$(dirname "${target}")"

  echo "Obfuscating ${rel}"
  rm -f "${target}"
  # Safe profile for legacy/jQuery-heavy files:
  # - no property mangling
  # - keep compression moderate
  # - avoid top-level mangling because these legacy files reuse many block-scoped names
  eval "${TERSER_BIN} \"${src}\" \
    --compress passes=1,keep_fargs=false \
    --mangle reserved=['jQuery','prestashop','ajaxCart','Snap','XMLSerializer','FormData','Promise','Context','window','document','$'] \
    --output \"${target}\""

  node --check "${target}" >/dev/null
}

main() {
  mkdir -p "${OUT_DIR}"
  ensure_terser

  local built=0
  for file in "${FILES[@]}"; do
    if check_file "${file}"; then
      build_one "${file}"
      built=$((built + 1))
    fi
  done

  echo
  echo "Build complete: ${built} file(s)"
  echo "Output directory: ${OUT_DIR}"
  echo
  echo "Generated files:"
  find "${OUT_DIR}" -type f | sort
}

main "$@"
