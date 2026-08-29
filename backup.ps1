# VoltVerse — back up the platform database (users, scores, settings, assignments).
# Usage:  ./backup.ps1
$ErrorActionPreference = 'Stop'
$container = 'voltverse-store'
$dir = Join-Path $PSScriptRoot 'backups'
New-Item -ItemType Directory -Force -Path $dir | Out-Null
$stamp = Get-Date -Format 'yyyy-MM-dd_HHmmss'
$dest = Join-Path $dir "voltverse_$stamp.db"
docker cp "${container}:/var/www/html/data/vv.db" $dest
Write-Host "Backup saved: $dest"
# keep only the 30 most recent backups
Get-ChildItem $dir -Filter 'voltverse_*.db' | Sort-Object LastWriteTime -Descending | Select-Object -Skip 30 | Remove-Item -Force
