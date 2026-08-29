# VoltVerse — restore the platform database from a backup file.
# Usage:  ./restore.ps1 -File .\backups\voltverse_2026-08-30_120000.db
param([Parameter(Mandatory=$true)][string]$File)
$ErrorActionPreference = 'Stop'
$container = 'voltverse-store'
if (-not (Test-Path $File)) { Write-Error "Backup file not found: $File"; exit 1 }
docker cp $File "${container}:/var/www/html/data/vv.db"
docker exec $container chown www-data:www-data /var/www/html/data/vv.db
docker exec $container chmod 666 /var/www/html/data/vv.db
Write-Host "Restored $File - data is live at http://localhost:8100"
