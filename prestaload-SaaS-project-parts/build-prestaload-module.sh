#!/usr/bin/env bash

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"
MODULE_SOURCE_DIR="${PROJECT_ROOT}/modules/prestaload"
API_PUBLIC_DIR="${SCRIPT_DIR}/api/public"
ARCHIVE_DIR="${API_PUBLIC_DIR}/module-archives"
TARGET_ZIP_NAME="prestaload.zip"
TARGET_ZIP_PATH="${API_PUBLIC_DIR}/${TARGET_ZIP_NAME}"
TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
TMP_DIR="$(mktemp -d /tmp/prestaload-module-build.XXXXXX)"
BUILD_ROOT="${TMP_DIR}/build"
MODULE_BUILD_DIR="${BUILD_ROOT}/prestaload"
NEW_ZIP_PATH="${TMP_DIR}/${TARGET_ZIP_NAME}"

cleanup() {
  rm -rf "${TMP_DIR}"
}

trap cleanup EXIT

if [[ ! -d "${MODULE_SOURCE_DIR}" ]]; then
  echo "Module source not found: ${MODULE_SOURCE_DIR}" >&2
  exit 1
fi

mkdir -p "${BUILD_ROOT}"
mkdir -p "${ARCHIVE_DIR}"

cp -R "${MODULE_SOURCE_DIR}" "${MODULE_BUILD_DIR}"

# Remove generated runtime files before packaging.
rm -rf "${MODULE_BUILD_DIR}/cache"
rm -f "${MODULE_BUILD_DIR}/prestaload.log"

find "${MODULE_BUILD_DIR}" \
  \( -name '.DS_Store' -o -name 'Thumbs.db' \) \
  -type f -delete

(
  cd "${BUILD_ROOT}"
  zip -qr "${NEW_ZIP_PATH}" "prestaload"
)

if [[ -f "${TARGET_ZIP_PATH}" ]]; then
  mv "${TARGET_ZIP_PATH}" "${ARCHIVE_DIR}/prestaload-${TIMESTAMP}.zip"
fi

mv "${NEW_ZIP_PATH}" "${TARGET_ZIP_PATH}"

echo "Created ${TARGET_ZIP_PATH}"
