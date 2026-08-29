#!/usr/bin/env bash
# VoltVerse — restore the platform database from a backup file.
#   ./restore.sh backups/voltverse_2026-08-30_120000.db
set -euo pipefail
CONTAINER=voltverse-store
FILE="${1:?Usage: ./restore.sh <backup.db>}"
[ -f "$FILE" ] || { echo "Backup file not found: $FILE" >&2; exit 1; }
docker cp "$FILE" "$CONTAINER:/var/www/html/data/vv.db"
docker exec "$CONTAINER" chown www-data:www-data /var/www/html/data/vv.db
docker exec "$CONTAINER" chmod 666 /var/www/html/data/vv.db
echo "Restored $FILE - data is live at http://localhost:8100"
