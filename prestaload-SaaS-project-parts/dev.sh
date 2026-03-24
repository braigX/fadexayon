#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"

WEB_PID=""
API_PID=""

cleanup() {
  echo
  echo "🛑 Stopping dev servers..."

  # Try graceful stop first
  [[ -n "${WEB_PID}" ]] && kill -TERM "${WEB_PID}" 2>/dev/null || true
  [[ -n "${API_PID}" ]] && kill -TERM "${API_PID}" 2>/dev/null || true

  # Give them a moment, then force if needed
  sleep 0.5
  [[ -n "${WEB_PID}" ]] && kill -KILL "${WEB_PID}" 2>/dev/null || true
  [[ -n "${API_PID}" ]] && kill -KILL "${API_PID}" 2>/dev/null || true

  # Prevent zombie processes
  wait 2>/dev/null || true
  echo "✅ Done."
}

# Stop both on Ctrl+C, termination, or script exit
trap cleanup INT TERM EXIT

echo "🚀 Starting Vite (web) and Laravel (api)..."

# Vite
(
  cd "${ROOT_DIR}/web"
  npm run dev
) &
WEB_PID=$!

# Laravel API
(
  cd "${ROOT_DIR}/api"
  php artisan serve
) &
API_PID=$!

echo "📌 PIDs => web:${WEB_PID} api:${API_PID}"
echo "Press Ctrl+C to stop both."

# If either process exits, stop the other too
wait -n "${WEB_PID}" "${API_PID}" || true
exit 0
