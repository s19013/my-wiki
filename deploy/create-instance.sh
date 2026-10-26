#!/bin/bash
# 新しい Lightsail インスタンスを作る（手元の PC で実行）
#   deploy/create-instance.sh [インスタンス名] [ブランチ]
#
# 起動時に setup-server.sh が実行され、Docker・swap・cron・リポジトリが用意される。
# 静的 IP の付け替えはしない（切り替えは deploy/README.md の手順で行う）。
set -euo pipefail

cd "$(dirname "$0")"

NAME=${1:-my-wiki}
BRANCH=${2:-main}
REGION=ap-northeast-1
AZ=ap-northeast-1a
BLUEPRINT=ubuntu_24_04
# 1GB / 40GB / 月 $7。足りなければ small_3_0（2GB / 月 $12）
BUNDLE=micro_3_0

# user-data の先頭でブランチを渡してから setup-server.sh を実行する
user_data=$(printf '#!/bin/bash\nexport MY_WIKI_BRANCH=%q\n' "$BRANCH"; sed 1d setup-server.sh)

aws lightsail create-instances --region "$REGION" \
    --instance-names "$NAME" \
    --availability-zone "$AZ" \
    --blueprint-id "$BLUEPRINT" \
    --bundle-id "$BUNDLE" \
    --key-pair-name LightsailDefaultKeyPair \
    --user-data "$user_data" \
    --query 'operations[0].status' --output text

echo "起動を待っています..."
until [ "$(aws lightsail get-instance-state --region "$REGION" --instance-name "$NAME" --query state.name --output text 2>/dev/null)" = running ]; do
    sleep 5
done

aws lightsail put-instance-public-ports --region "$REGION" --instance-name "$NAME" --port-infos \
    fromPort=22,toPort=22,protocol=tcp \
    fromPort=80,toPort=80,protocol=tcp \
    fromPort=443,toPort=443,protocol=tcp \
    fromPort=443,toPort=443,protocol=udp \
    --query 'operation.status' --output text

ip=$(aws lightsail get-instance --region "$REGION" --instance-name "$NAME" --query instance.publicIpAddress --output text)
echo
echo "作成しました: $NAME ($ip)"
echo "setup-server.sh の完了まで数分かかる。進み具合は:"
echo "  ssh ubuntu@$ip tail -f /var/log/cloud-init-output.log"
