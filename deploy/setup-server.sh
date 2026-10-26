#!/bin/bash
# サーバーの初期設定（Ubuntu 24.04 で root として実行。何度実行しても同じ結果になる）
# create-instance.sh が user-data として渡すので、普段は手で実行しない
set -euo pipefail

REPO=https://github.com/s19013/my-wiki.git
BRANCH=${MY_WIKI_BRANCH:-main}
APP_DIR=/srv/my-wiki
APP_USER=ubuntu

# --- swap 2GB（メモリ 1GB なので、イメージのビルド時に足りなくなる） ---
if [ ! -f /swapfile ]; then
    fallocate -l 2G /swapfile
    chmod 600 /swapfile
    mkswap /swapfile
    swapon /swapfile
    echo '/swapfile none swap sw 0 0' >> /etc/fstab
fi
echo 'vm.swappiness=10' > /etc/sysctl.d/90-my-wiki.conf
sysctl -p /etc/sysctl.d/90-my-wiki.conf

# --- Docker ---
if ! command -v docker > /dev/null; then
    curl -fsSL https://get.docker.com | sh
fi
usermod -aG docker "$APP_USER"
systemctl enable --now docker

# --- リポジトリ ---
if [ ! -d "$APP_DIR/.git" ]; then
    git clone --branch "$BRANCH" "$REPO" "$APP_DIR"
fi
chown -R "$APP_USER:$APP_USER" "$APP_DIR"

# --- 秘密情報とバックアップの置き場所 ---
# /etc/my-wiki/backup-aws-credentials はここで作らず、手で置く（deploy/README.md）
install -d -m 750 -o root -g "$APP_USER" /etc/my-wiki
install -d -m 750 -o "$APP_USER" -g "$APP_USER" /var/backups/my-wiki

# --- cron（時刻は UTC。旧サーバーと同じ時刻） ---
# 旧サーバーは PATH が足りずにバックアップが空になっていたので、PATH を明示する
cat > /etc/cron.d/my-wiki <<EOF
PATH=/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin
22 2 * * * $APP_USER $APP_DIR/deploy/backup.sh
24 2 2 * * $APP_USER $APP_DIR/deploy/cleanup.sh
EOF
chmod 644 /etc/cron.d/my-wiki

echo "setup-server.sh 完了。次は $APP_DIR/.env.production を置いて deploy/deploy.sh を実行する"
