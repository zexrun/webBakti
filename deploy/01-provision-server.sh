#!/usr/bin/env bash
#
# Provisions a fresh Ubuntu server with everything Magang BAKTI needs to run:
# PHP 8.2, Composer, Node.js (runtime dependency for PDF/face-verification,
# not just asset builds), MySQL, Apache + PHP-FPM, and Supervisor.
#
# Run once, as a user with sudo. Idempotent-ish: safe to re-run, apt/npm
# will just skip what's already installed.
#
# Usage: sudo bash deploy/01-provision-server.sh

set -euo pipefail

if [[ $EUID -ne 0 ]]; then
  echo "Run this as root (sudo bash deploy/01-provision-server.sh)." >&2
  exit 1
fi

echo "==> Updating apt and installing base tools"
apt update && apt upgrade -y
apt install -y curl git unzip software-properties-common ca-certificates gnupg

echo "==> Installing PHP 8.2 and required extensions"
add-apt-repository -y ppa:ondrej/php
apt update
apt install -y php8.2 php8.2-fpm php8.2-cli php8.2-common \
  php8.2-mysql php8.2-sqlite3 php8.2-mbstring php8.2-xml php8.2-bcmath \
  php8.2-curl php8.2-zip php8.2-gd php8.2-intl

echo "==> Installing Composer"
if ! command -v composer >/dev/null 2>&1; then
  curl -sS https://getcomposer.org/installer | php
  mv composer.phar /usr/local/bin/composer
fi

echo "==> Installing Node.js 20.x (runtime dependency: PDF rendering + face verification call 'node' as a child process from PHP)"
if ! command -v node >/dev/null 2>&1; then
  curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
  apt install -y nodejs
fi

echo "==> Installing native build libraries required by the 'canvas' npm package (used by resources/face-verification/extract-descriptor.cjs)"
apt install -y build-essential libcairo2-dev libpango1.0-dev \
  libjpeg-dev libgif-dev librsvg2-dev pkg-config

echo "==> Installing MySQL server"
apt install -y mysql-server

echo "==> Installing Apache + FastCGI proxy module"
apt install -y apache2 libapache2-mod-fcgi
a2enmod proxy_fcgi setenvif rewrite
a2enconf php8.2-fpm
systemctl restart apache2

echo "==> Installing Supervisor (keeps the queue worker running)"
apt install -y supervisor

echo ""
echo "==> Done. Versions installed:"
php -v | head -1
composer --version
node -v
npm -v
mysql --version

echo ""
echo "Next steps:"
echo "  1. Run: sudo mysql_secure_installation"
echo "  2. Create the database/user (see deploy/README.md)"
echo "  3. Run deploy/02-deploy-app.sh as the deploying user"
