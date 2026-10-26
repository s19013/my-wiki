# 本番サーバーの構築・運用手順

サーバーの設定はすべてこのディレクトリと [compose.yaml](../compose.yaml) にある。
サーバー上で手作業で変えた設定は、作り直したときに消える。設定を変えたいときは、ここを直してデプロイする。

## 構成

```
Lightsail (Ubuntu 24.04 / micro_3_0: 1GB + swap 2GB / 静的 IP StaticIp-1)
└─ Docker Compose (/srv/my-wiki)
   ├─ caddy  80/443  HTTPS 証明書を自動で取得・更新し、app に転送
   ├─ app    php:8.2-apache + Laravel（イメージはサーバー上でビルド）
   └─ db     mariadb:11.4（データは volume db-data）
cron (/etc/cron.d/my-wiki、時刻は UTC)
├─ 毎日 02:22  backup.sh   DB ダンプ → /var/backups/my-wiki（14 日分）と S3（30 日で自動削除）
└─ 毎月2日 02:24 cleanup.sh 論理削除から半年たったデータを消す
```

| ファイル | 役割 |
|---|---|
| `Dockerfile` / `entrypoint.sh` / `apache.conf` / `php.ini` | app コンテナ |
| `Caddyfile` | HTTPS とリバースプロキシ |
| `.env.production.example` | 設定のテンプレート。実物はサーバーの `/srv/my-wiki/.env.production` だけにある |
| `create-instance.sh` | インスタンス作成（手元で実行） |
| `setup-server.sh` | Docker・swap・cron の用意（create-instance.sh が自動で実行） |
| `deploy.sh` | デプロイ・ロールバック（サーバーで実行） |
| `backup.sh` / `restore.sh` | DB のバックアップと復元 |
| `cleanup.sh` / `cleanup.sql` | 論理削除データの掃除 |
| `create-backup-bucket.sh` | バックアップ用 S3 バケットと書き込み専用ユーザーの作成（1 回だけ） |

秘密情報の置き場所（どれも git には入れない）:

- `/srv/my-wiki/.env.production` — APP_KEY と DB のパスワード
- `/etc/my-wiki/backup-aws-credentials` — S3 に書き込むためのキー（PutObject のみ許可。読み出しや削除はできない）

## SSH で入る

```sh
# 一時的なキーを発行して接続する（キーファイルの管理が不要）
aws lightsail get-instance-access-details --instance-name my-wiki --protocol ssh > /tmp/a.json
python3 -c "import json;d=json.load(open('/tmp/a.json'))['accessDetails'];open('/tmp/k','w').write(d['privateKey']);open('/tmp/k-cert.pub','w').write(d['certKey'])"
chmod 600 /tmp/k && rm /tmp/a.json
ssh -i /tmp/k -o CertificateFile=/tmp/k-cert.pub ubuntu@sundlf.com
```

ブラウザの Lightsail コンソールの「接続」ボタンからも入れる。

## デプロイ

```sh
cd /srv/my-wiki
deploy/deploy.sh
```

git pull → イメージのビルド（3 分ほど）→ 再起動 → migrate を行う。
`config:cache` などはコンテナの起動時に自動で行われる。

## ロールバック

```sh
deploy/deploy.sh <戻したいコミット>   # 例: deploy/deploy.sh f1d0f65
```

detached HEAD になるので、元に戻すときは `git checkout main` してから `deploy/deploy.sh` を実行する。

## よく使うコマンド（/srv/my-wiki で実行）

```sh
docker compose ps                              # 状態
docker compose logs -f app                     # Laravel と Apache のログ
docker compose logs -f caddy                   # 証明書まわりのログ
docker compose exec app php artisan tinker     # tinker
docker compose exec db sh -c 'mariadb -uroot -p"$MARIADB_ROOT_PASSWORD" my-wiki'   # SQL
docker compose restart app                     # 再起動
grep my-wiki /var/log/syslog                   # cron（バックアップと掃除）の結果
```

## バックアップと復元

バックアップは毎日自動で取られる。手動で取るときは `deploy/backup.sh` を実行する。
ダンプが空だったり途中で切れていたりすると、エラーにして保存しない（syslog に `my-wiki-backup` で記録される）。

復元:

```sh
deploy/restore.sh /var/backups/my-wiki/my-wiki-2026-10-26-0222.sql.gz
```

サーバーが壊れていて S3 から取り出すときは、手元の PC で:

```sh
aws s3 ls s3://my-wiki-backup-394572168692/my-wiki/
aws s3 cp s3://my-wiki-backup-394572168692/my-wiki/<ファイル名> .
```

## サーバーを一から作り直す

1. 手元で `deploy/create-instance.sh my-wiki-new`（既存と被らない名前にする）
2. 数分待つ（`ssh ... tail -f /var/log/cloud-init-output.log` で `setup-server.sh 完了` が出るまで）
3. サーバーで `.env.production` を作る

   ```sh
   cd /srv/my-wiki
   cp deploy/.env.production.example .env.production && chmod 600 .env.production
   # APP_KEY: echo "base64:$(openssl rand -base64 32)"
   # パスワード: openssl rand -hex 24
   # 確認が済むまでは TLS_MODE=internal にしておく
   vi .env.production
   ```

4. 手元から S3 のキーを送る（無ければ `create-backup-bucket.sh` で作り直す）

   ```sh
   ssh ... 'sudo install -m 640 -o root -g ubuntu /dev/stdin /etc/my-wiki/backup-aws-credentials' < ~/my-wiki-backup-aws-credentials
   ```

5. `deploy/deploy.sh` → `deploy/restore.sh <ダンプ>`
6. 手元で確認する: `curl -k --resolve sundlf.com:443:<新しいIP> https://sundlf.com/`
7. 切り替える（次の節）

## 切り替え（静的 IP の付け替え）

DNS（Route53 の sundlf.com）は静的 IP を向いているので、IP を付け替えるだけで切り替わる。数分止まる。

```sh
# 1. 旧サーバーを止めて最新のダンプを取る（旧サーバーで php artisan down）
# 2. 新サーバーで restore.sh
# 3. 静的 IP を付け替える
aws lightsail detach-static-ip --static-ip-name StaticIp-1
aws lightsail attach-static-ip --static-ip-name StaticIp-1 --instance-name <新しいインスタンス>
# 4. 新サーバーの .env.production を TLS_MODE=acme にして docker compose up -d caddy
#    （証明書の取得に 1 分ほどかかる。docker compose logs caddy で確認）
```

戻すときは、逆に旧インスタンスへ付け替える。新サーバーで書き込まれたデータは、先にダンプを取って旧サーバーに入れる。

## PHP を上げる

`Dockerfile` の `FROM php:8.2-apache` を変えてデプロイする。だめなら戻してデプロイする。

- PHP 8.2 のセキュリティサポートは 2026-12-31 まで
- Laravel 9 は公式には PHP 8.2 まで。8.3 は動くかもしれないが保証はない
- `inertiajs/inertia-laravel 0.5.4` は宣言上 PHP 8.1 までなので、composer は `--ignore-platform-reqs` で入れている（動作は確認済み）

## 注意

- `route:cache` は使えない（ルート名 `api.bookMark.delete` が重複しているため）
- メールは未設定（`MAIL_MAILER=log`）。パスワードリセットのメールは届かない
- `.env.production` の `MARIADB_*` は DB の初回作成時にしか使われない。後でパスワードを変えるときは、DB 上でも `ALTER USER` が必要
