#!/usr/bin/env bash
# Daily/6-hourly database backup. Run as root (uses socket login, no password stored).
# Install:  sudo crontab -e   then add:   0 */6 * * * /full/path/to/backup.sh
set -euo pipefail
DIR="${BACKUP_DIR:-/var/backups/symposium}"
KEEP_DAYS=14
mkdir -p "$DIR"; chmod 700 "$DIR"
FILE="$DIR/symposium-$(date +%F-%H%M).sql.gz"
mysqldump --single-transaction myedvt_symposium | gzip > "$FILE"
chmod 600 "$FILE"
find "$DIR" -name 'symposium-*.sql.gz' -mtime +"$KEEP_DAYS" -delete
echo "Backup written: $FILE"
# IMPORTANT: also copy these files to another machine/location (e.g. scp or rclone). A backup on the same server is not enough.
