#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CACHE_DIR="$ROOT_DIR/themes/modez/assets/cache"

echo "Clearing cached JS/CSS files..."
echo "- $CACHE_DIR"

if [[ ! -d "$CACHE_DIR" ]]; then
  echo "  skip: cache directory not found"
  exit 0
fi

sudo find "$CACHE_DIR" -maxdepth 1 -type f \
  \( -name "*.js" -o -name "*.css" -o -name "*.js.map" -o -name "*.css.map" \) \
  -delete

echo "Done."
