#!/usr/bin/env bash
# Builds the release zip (inner folder name: church-sermon-manager).
# Usage: build-zip.sh [--wporg] [output-path]
#   --wporg   Build a WordPress.org-directory variant: additionally strips
#             the GitHub update checker and the Update URI plugin header.
#             (Not used until the plugin is actually submitted.)
# Dev-only paths are excluded via .gitattributes export-ignore; the archive
# contains committed files only, so local scratch files can never leak in.
set -euo pipefail
cd "$(dirname "$0")/.."

WPORG=0
OUT="church-sermon-manager.zip"
for arg in "$@"; do
  case "$arg" in
    --wporg) WPORG=1 ;;
    *) OUT="$arg" ;;
  esac
done

TMP="$(mktemp -d)"
trap 'rm -rf "$TMP"' EXIT

git archive --format=tar --worktree-attributes --prefix=church-sermon-manager/ HEAD | tar -x -C "$TMP"

if [ "$WPORG" = "1" ]; then
  rm -rf "$TMP/church-sermon-manager/lib/plugin-update-checker"
  # Drop the Update URI header line; WordPress.org serves updates for the slug.
  sed -i '' '/^ \* Update URI:/d' "$TMP/church-sermon-manager/sermons.php"
  # Remove the updater bootstrap (marked block) so no updater code remains.
  sed -i '' '/WPORG-STRIP-START/,/WPORG-STRIP-END/d' "$TMP/church-sermon-manager/includes/main.php"
fi

( cd "$TMP" && zip -qr archive.zip church-sermon-manager )
mv "$TMP/archive.zip" "$OUT"
echo "built: $OUT ($(du -h "$OUT" | cut -f1)) wporg=$WPORG"
unzip -l "$OUT" | head -5
