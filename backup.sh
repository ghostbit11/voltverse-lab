#!/usr/bin/env bash
# VoltVerse — back up the platform database (users, scores, settings, assignments).
#   ./backup.sh            → writes backups/voltverse_<timestamp>.db
#   ./restore.sh <file>    → restores a backup
set -euo pipefail
CONTAINER=voltverse-store
DIR="$(cd "$(dirname "$0")" && pwd)/backups"
mkdir -p "$DIR"
DEST="$DIR/voltverse_$(date +%Y-%m-%d_%H%M%S).db"
docker cp "$CONTAINER:/var/www/html/data/vv.db" "$DEST"
echo "Backup saved: $DEST"
# keep only the 30 most recent
ls -1t "$DIR"/voltverse_*.db 2>/dev/null | tail -n +31 | xargs -r rm -f
