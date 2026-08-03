#!/usr/bin/env bash
#
# Creates the MySQL database and application user for Magang BAKTI.
# Prompts for the app DB password interactively so it never ends up in
# shell history or this script.
#
# Usage: bash deploy/03-create-database.sh
# (will prompt for the MySQL root password and the new app DB password)

set -euo pipefail

DB_NAME="magang_bakti"
DB_USER="bakti_user"

read -rsp "New password for MySQL user '$DB_USER': " DB_PASS
echo ""
read -rsp "Confirm password: " DB_PASS_CONFIRM
echo ""

if [[ "$DB_PASS" != "$DB_PASS_CONFIRM" ]]; then
  echo "Passwords did not match." >&2
  exit 1
fi

sudo mysql <<SQL
CREATE DATABASE IF NOT EXISTS ${DB_NAME} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON ${DB_NAME}.* TO '${DB_USER}'@'localhost';
FLUSH PRIVILEGES;
SQL

echo ""
echo "==> Database '$DB_NAME' and user '$DB_USER' are ready."
echo "Add these to your .env:"
echo ""
echo "  DB_CONNECTION=mysql"
echo "  DB_HOST=127.0.0.1"
echo "  DB_PORT=3306"
echo "  DB_DATABASE=${DB_NAME}"
echo "  DB_USERNAME=${DB_USER}"
echo "  DB_PASSWORD=<the password you just entered>"
