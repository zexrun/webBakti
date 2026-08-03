#!/usr/bin/env bash
#
# Installs the Laravel scheduler cron entry for www-data.
# The app has one scheduled job (tasks:send-deadline-reminders, daily 08:00,
# see routes/console.php) which is only triggered if `schedule:run` runs
# every minute.
#
# Usage: sudo bash deploy/04-setup-scheduler-cron.sh [app-dir]

set -euo pipefail

APP_DIR="${1:-/var/www/webbakti}"
CRON_LINE="* * * * * cd $APP_DIR && php artisan schedule:run >> /dev/null 2>&1"

if [[ $EUID -ne 0 ]]; then
  echo "Run this as root (sudo bash deploy/04-setup-scheduler-cron.sh)." >&2
  exit 1
fi

CURRENT_CRON="$(crontab -u www-data -l 2>/dev/null || true)"

if echo "$CURRENT_CRON" | grep -qF "$APP_DIR" ; then
  echo "==> Cron entry for $APP_DIR already present for www-data, skipping."
else
  echo "==> Adding scheduler cron entry for www-data"
  { echo "$CURRENT_CRON"; echo "$CRON_LINE"; } | crontab -u www-data -
fi

echo "==> Current www-data crontab:"
crontab -u www-data -l
