#!/usr/bin/env bash
# Captures and normalizes the parity page matrix. Usage: capture.sh <run-name>
set -euo pipefail
cd "$(dirname "$0")"
RUN="runs/${1:?usage: capture.sh <run-name>}"
SITE="${SITE:-http://epiclesis-dev.local}"
mkdir -p "$RUN"
while IFS=$'\t' read -r label path; do
  [ -z "$label" ] && continue
  case "$label" in \#*) continue ;; esac
  curl -s "${SITE}${path}" \
    | sed -E 's/\?ver=[0-9a-zA-Z.\-]+//g' \
    | sed -E 's/id="index-[0-9]+"/id="index-X"/g' \
    | sed -E 's/(name="_wpnonce" value=")[a-f0-9]+(")/\1X\2/g' \
    > "$RUN/${label}.html"
  echo "captured: $label"
done < urls.txt
echo "run complete: $RUN"
