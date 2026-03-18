#!/usr/bin/env bash

set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
CACHE_DIR="$ROOT_DIR/cache"
RUNTIME_CONFIG_PATH="$ROOT_DIR/runtime-config.php"
PAGES_DIR="$CACHE_DIR/pages"
MINIFIED_DIR="$CACHE_DIR/minified"
MINIFIED_BACKUPS_DIR="$MINIFIED_DIR/backups"
CRITICAL_CSS_DIR="$CACHE_DIR/critical-css"
FONT_USAGE_DIR="$CACHE_DIR/font-usage"
REPORTS_DIR="$ROOT_DIR/reports"

echo "Cleaning PrestaLoad generated files..."

mkdir -p "$PAGES_DIR" "$MINIFIED_DIR" "$MINIFIED_BACKUPS_DIR" "$CRITICAL_CSS_DIR" "$FONT_USAGE_DIR" "$REPORTS_DIR"

find "$PAGES_DIR" -mindepth 1 -delete
find "$MINIFIED_DIR" -maxdepth 1 -type f -delete
find "$MINIFIED_BACKUPS_DIR" -mindepth 1 -delete
find "$CRITICAL_CSS_DIR" -mindepth 1 -delete
find "$FONT_USAGE_DIR" -mindepth 1 -delete
find "$REPORTS_DIR" -mindepth 1 -delete

printf '[]\n' > "$CACHE_DIR/asset-rules.json"
printf '[]\n' > "$CACHE_DIR/font-rules.json"
printf '[]\n' > "$CACHE_DIR/prod_rules.json"
printf '' > "$CACHE_DIR/prestaload-requests.log"
printf '<?php return [];%s' $'\n' > "$RUNTIME_CONFIG_PATH"

echo "Reset generated settings and caches:"
echo "- $PAGES_DIR"
echo "- $MINIFIED_DIR"
echo "- $CRITICAL_CSS_DIR"
echo "- $FONT_USAGE_DIR"
echo "- $REPORTS_DIR"
echo "- $CACHE_DIR/asset-rules.json"
echo "- $CACHE_DIR/font-rules.json"
echo "- $CACHE_DIR/prod_rules.json"
echo "- $CACHE_DIR/prestaload-requests.log"
echo "- $RUNTIME_CONFIG_PATH"

echo "PrestaLoad package is ready for upload."
