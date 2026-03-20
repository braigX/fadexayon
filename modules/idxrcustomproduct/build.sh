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
  # `--mangle toplevel` aggressively shortens variable/function names, usually to single letters.
  # We keep quoted property names untouched to reduce runtime breakage with DOM/data access.
  eval "${TERSER_BIN} \"${src}\" \
    --compress passes=2,keep_fargs=false,pure_getters=false \
    --mangle toplevel \
    --mangle-props keep_quoted,reserved=['jQuery','prestashop','ajaxCart','Snap','XMLSerializer','FormData','Promise','Context','window','document'] \
    --output \"${target}\""
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
