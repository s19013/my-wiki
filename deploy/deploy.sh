#!/bin/bash
# 最新のコードを取り込んで起動し直す（サーバー上で ubuntu ユーザーとして実行）
#   deploy/deploy.sh            今のブランチの最新を取り込む
#   deploy/deploy.sh <コミット>  指定したコミットに戻す（ロールバック）
#
# config:cache などのキャッシュ作り直しはコンテナ起動時に entrypoint.sh が行う
set -euo pipefail

cd "$(dirname "$0")/.."

[ -f .env.production ] || { echo ".env.production が無い（deploy/.env.production.example を参照）" >&2; exit 1; }

if [ $# -ge 1 ]; then
    git fetch
    git checkout --detach "$1"
else
    git pull --ff-only
fi
echo "デプロイするコミット: $(git log -1 --format='%h %s')"

docker compose build app
docker compose up -d
docker compose exec app php artisan migrate --force
docker image prune -f > /dev/null

docker compose ps
