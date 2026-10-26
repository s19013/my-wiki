#!/bin/bash
# backup.sh で作ったダンプ（.sql.gz）を DB に戻す
#   deploy/restore.sh /var/backups/my-wiki/my-wiki-2026-10-26-0222.sql.gz
#
# S3 のダンプは、サーバーのキーでは読めない（書き込み専用）。手元の PC で
#   aws s3 cp s3://<バケット>/my-wiki/<ファイル名> .
# としてダウンロードし、scp でサーバーに送ってから使う。
set -euo pipefail

cd "$(dirname "$0")/.."

file=${1:?使い方: $0 <ダンプファイル.sql.gz>}
[ -r "$file" ] || { echo "ファイルが読めない: $file" >&2; exit 1; }
gzip -t "$file"

read -r -p "今の DB の内容を $file で上書きします。よろしいですか？ [y/N] " answer
[ "$answer" = y ] || { echo "中止しました"; exit 1; }

# ダンプには DROP TABLE IF EXISTS が入っているので、テーブルごと置き換わる
zcat "$file" | docker compose exec -T db sh -c 'mariadb -uroot -p"$MARIADB_ROOT_PASSWORD"'
docker compose exec app php artisan migrate --force
echo "復元しました"
