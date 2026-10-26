#!/bin/bash
# cleanup.sql を実行する（cron から毎月呼ぶ）
set -euo pipefail

cd "$(dirname "$0")/.."

docker compose exec -T db sh -c 'mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" "$MARIADB_DATABASE"' < deploy/cleanup.sql
logger -t my-wiki-cleanup -- "cleanup.sql done" 2>/dev/null || true
