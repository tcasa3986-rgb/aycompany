#!/bin/bash
set -e

php setup_db.php 2>&1 || true

exec php -S 127.0.0.1:${DEMO_PORT:-5221} -t public/