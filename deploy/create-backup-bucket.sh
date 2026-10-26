#!/bin/bash
# バックアップ用の S3 バケットと、書き込み専用の IAM ユーザーを作る（手元の PC で 1 回だけ実行）
#
# 作るもの:
#   S3 バケット my-wiki-backup-<アカウントID>  公開はすべてブロック、30 日で自動削除
#   IAM ユーザー my-wiki-backup               このバケットへの PutObject だけ許可
#   ~/my-wiki-backup-aws-credentials          そのユーザーのキー。サーバーの
#                                             /etc/my-wiki/backup-aws-credentials に置く
set -euo pipefail

REGION=ap-northeast-1
USER_NAME=my-wiki-backup
OUT=${1:-$HOME/my-wiki-backup-aws-credentials}

account=$(aws sts get-caller-identity --query Account --output text)
bucket="my-wiki-backup-$account"

if aws s3api head-bucket --bucket "$bucket" 2>/dev/null; then
    echo "バケットは作成済み: $bucket"
else
    aws s3api create-bucket --bucket "$bucket" --region "$REGION" \
        --create-bucket-configuration LocationConstraint="$REGION" > /dev/null
    echo "バケットを作成: $bucket"
fi

aws s3api put-public-access-block --bucket "$bucket" --public-access-block-configuration \
    BlockPublicAcls=true,IgnorePublicAcls=true,BlockPublicPolicy=true,RestrictPublicBuckets=true

aws s3api put-bucket-lifecycle-configuration --bucket "$bucket" --lifecycle-configuration '{
  "Rules": [{
    "ID": "expire-30-days",
    "Status": "Enabled",
    "Filter": {},
    "Expiration": {"Days": 30},
    "NoncurrentVersionExpiration": {"NoncurrentDays": 1},
    "AbortIncompleteMultipartUpload": {"DaysAfterInitiation": 1}
  }]
}'

aws iam get-user --user-name "$USER_NAME" > /dev/null 2>&1 \
    || aws iam create-user --user-name "$USER_NAME" > /dev/null
aws iam put-user-policy --user-name "$USER_NAME" --policy-name put-backup-only --policy-document "{
  \"Version\": \"2012-10-17\",
  \"Statement\": [{
    \"Effect\": \"Allow\",
    \"Action\": \"s3:PutObject\",
    \"Resource\": \"arn:aws:s3:::$bucket/my-wiki/*\"
  }]
}"

if [ -e "$OUT" ]; then
    echo "$OUT が既にあるので、キーは新しく作らない"
else
    umask 077
    aws iam create-access-key --user-name "$USER_NAME" \
        --query 'AccessKey.[AccessKeyId,SecretAccessKey]' --output text \
        | awk -v r="$REGION" '{print "[default]\naws_access_key_id = " $1 "\naws_secret_access_key = " $2 "\nregion = " r}' > "$OUT"
    echo "キーを保存: $OUT（サーバーの /etc/my-wiki/backup-aws-credentials に置き、手元からは消す）"
fi

echo
echo ".env.production に次の行を書く:"
echo "BACKUP_S3_BUCKET=$bucket"
