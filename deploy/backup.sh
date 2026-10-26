#!/bin/bash
# DB をダンプしてサーバー上と S3 に保存する（cron から毎日呼ぶ）
#
# 使う設定:
#   .env.production の BACKUP_S3_BUCKET   空なら S3 には送らない
#   /etc/my-wiki/backup-aws-credentials   PutObject だけ許可した IAM ユーザーのキー
#                                         (create-backup-bucket.sh で作る)
set -euo pipefail

cd "$(dirname "$0")/.."

BACKUP_DIR=${BACKUP_DIR:-/var/backups/my-wiki}
KEEP_DAYS=${KEEP_DAYS:-14}
AWS_CREDENTIALS=${AWS_CREDENTIALS:-/etc/my-wiki/backup-aws-credentials}
BUCKET=$(grep -E '^BACKUP_S3_BUCKET=' .env.production | cut -d= -f2- || true)

log() { echo "$*"; logger -t my-wiki-backup -- "$*" 2>/dev/null || true; }
fail() { log "ERROR: $*"; exit 1; }

mkdir -p "$BACKUP_DIR"
name="my-wiki-$(date -u +%Y-%m-%d-%H%M).sql.gz"
tmp="$BACKUP_DIR/.$name.tmp"
trap 'rm -f "$tmp"' EXIT

docker compose exec -T db sh -c \
    'mariadb-dump --single-transaction --routines --triggers -uroot -p"$MARIADB_ROOT_PASSWORD" --databases "$MARIADB_DATABASE"' \
    | gzip > "$tmp"

# 旧サーバーでは空のダンプが何年も作られ続けていたので、中身を確かめてから保存する
gzip -t "$tmp" || fail "gzip が壊れている: $name"
zcat "$tmp" | tail -n 1 | grep -q '^-- Dump completed' || fail "ダンプが最後まで書かれていない: $name"
zgrep -q '^INSERT INTO `articles`' "$tmp" || fail "articles のデータが無い: $name"
mv "$tmp" "$BACKUP_DIR/$name"
log "saved $BACKUP_DIR/$name ($(du -h "$BACKUP_DIR/$name" | cut -f1))"

find "$BACKUP_DIR" -name 'my-wiki-*.sql.gz' -mtime +"$KEEP_DAYS" -delete

if [ -n "$BUCKET" ]; then
    [ -r "$AWS_CREDENTIALS" ] || fail "$AWS_CREDENTIALS が読めない"
    docker run --rm \
        -v "$BACKUP_DIR/$name:/backup/$name:ro" \
        -v "$AWS_CREDENTIALS:/root/.aws/credentials:ro" \
        amazon/aws-cli s3 cp --only-show-errors "/backup/$name" "s3://$BUCKET/my-wiki/$name" \
        || fail "S3 へのアップロードに失敗: $name"
    log "uploaded s3://$BUCKET/my-wiki/$name"
fi
