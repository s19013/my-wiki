# 4. サーバー構築

## 流れ

```
手元の PC                                  新しいサーバー
─────────────────────────────────          ─────────────────────────────────
create-instance.sh
  ├─ Lightsail インスタンス作成 ──────────→ 初回起動時に user-data を実行
  │   (user-data に setup-server.sh)           └─ setup-server.sh
  └─ ポート 22/80/443 を開ける                      swap / Docker / clone / cron
                                           .env.production を作る（秘密情報はここで生成）
S3 のキーを送る ───────────────────────────→ /etc/my-wiki/backup-aws-credentials
                                           deploy.sh（ビルド → 起動 → migrate）
ダンプを送る ──────────────────────────────→ restore.sh
curl --resolve で動作確認
```

## create-instance.sh

[create-instance.sh](../create-instance.sh) で、aws cli を使ってインスタンスを作る。

| 設定 | 値 | 理由 |
|---|---|---|
| ブループリント | `ubuntu_24_04` | アプリ入りではなく OS だけのもの。中身は Docker で用意する |
| プラン | `micro_3_0`（1GB / 40GB / 月 $7） | 旧サーバーと同じ料金 |
| ポート | 22 / 80 / 443（443 は UDP も） | UDP は HTTP/3 用 |

静的 IP の付け替えは、このスクリプトではやらない。付け替えは本番の切り替えそのものなので、手順を分けている（[5 章](05-cutover.md)）。

## user-data と、Lightsail の落とし穴

user-data は、インスタンスの初回起動時に自動で実行されるスクリプト。最初は `setup-server.sh` の中身をそのまま渡していたが、途中で止まった。

```
/var/lib/cloud/instance/scripts/part-001: 20: set: Illegal option -o pipefail
```

実際に実行されたファイルを見ると、Lightsail は渡した user-data を **自前の起動スクリプト（`#!/bin/sh`）の末尾に貼り付けて**、`sh` で実行していた。

```sh
#!/bin/sh                              ← Lightsail のスクリプト
echo Lightsail: Starting Instance Initialization.
...（SSH の設定）
#!/bin/bash                            ← 渡した user-data。もうただのコメント扱い
set -euo pipefail                      ← sh には pipefail が無いのでエラー
```

そこで、user-data の中では「setup-server.sh をファイルに書き出して、それを bash で実行する」ようにした。

```sh
cat > /root/setup-server.sh <<'MY_WIKI_SETUP_EOF'
（setup-server.sh の中身）
MY_WIKI_SETUP_EOF
MY_WIKI_BRANCH=main bash /root/setup-server.sh
```

`<<'EOF'` のように終わりの印をクォートすると、中の `$変数` が展開されずにそのまま書き出される。

> **教訓:** 動かなかったら、実際に実行されたもの（ここでは `/var/lib/cloud/instance/scripts/part-001`）を見る。ログは `/var/log/cloud-init-output.log`。

## setup-server.sh

[setup-server.sh](../setup-server.sh) は **何回実行しても同じ結果になる**（冪等）ように書いてある。今回のように途中で止まっても、手で実行し直せば続きから整う。

```sh
if [ ! -f /swapfile ]; then ...    # 既にあれば作らない
command -v docker || install ...   # 入っていれば入れない
[ -d "$APP_DIR/.git" ] || git clone ...
```

| やること | 理由 |
|---|---|
| swap 2GB、`vm.swappiness=10` | メモリ 1GB ではイメージのビルド時に足りなくなる。swappiness を下げて、普段はなるべくメモリだけで動かす |
| Docker（`get.docker.com`） | 公式のインストールスクリプト |
| `usermod -aG docker ubuntu` | ubuntu ユーザーが sudo なしで docker を使えるように |
| `/etc/my-wiki`（750, root:ubuntu） | S3 のキーの置き場所 |
| `/var/backups/my-wiki` | ダンプの置き場所 |
| `/etc/cron.d/my-wiki` | バックアップと掃除（PATH を明示。[3 章](03-backup.md)） |

## 秘密情報はサーバーの上で作る

`.env.production` のパスワードや APP_KEY は、**サーバーの上で** `openssl rand` を使って作った。手元の PC にも、作業のログにも値が出てこない。

```sh
umask 077                                  # 作るファイルを自分だけが読めるように
P=$(openssl rand -hex 24)                  # パスワード
K="base64:$(openssl rand -base64 32)"      # APP_KEY
sed -e "s|^DB_PASSWORD=.*|DB_PASSWORD=$P|" ... deploy/.env.production.example > .env.production
```

旧サーバーの値は公開リポジトリに載っていたので、すべて新しい値にした。

- **DB**: root とは別に、アプリ専用のユーザー（`my-wiki`）を作った
- **APP_KEY**: このアプリで暗号化しているのは Cookie とセッションだけ（`Crypt::` や encrypted キャストは使っていない）。そのため変えても、全員が一度ログアウトされるだけで済む

## deploy.sh

[deploy.sh](../deploy.sh) の中身は、旧サーバーの `update_back.sh`（`git pull` → `composer install` → `migrate` → キャッシュ削除）を Docker 用に置き換えたもの。

```sh
git pull --ff-only          # fast-forward できないとき（履歴が分岐しているとき）は止まる
docker compose build app    # メモリ 1GB + swap で 3 分ほど
docker compose up -d        # 変わったコンテナだけ作り直す
docker compose exec app php artisan migrate --force
docker image prune -f       # 使わなくなった古いイメージを消す（ディスク節約）
```

引数にコミットを渡すと、そのコミットに戻してデプロイする（ロールバック）。

## 本番として公開する前の確認

### curl --resolve

DNS はまだ旧サーバーを向いている。それでも「sundlf.com として新サーバーにアクセスする」ことができる。

```sh
curl -k --resolve sundlf.com:443:3.112.218.252 https://sundlf.com/
```

`--resolve` は、curl の中だけで「sundlf.com = この IP」と決め打ちする。ブラウザで同じことをするには、`/etc/hosts` に 1 行足す。

### 確認したこと

| 確認 | 方法 |
|---|---|
| PHP のバージョン | `docker compose exec app php -v` → 8.2.35 |
| リダイレクト・ヘッダ | `curl -I`（308 → https、`Vary` ヘッダ） |
| 主な操作 | curl で、新規登録 → 記事表示 → 検索 → ログアウト → ログイン |
| 他人の記事を見られないこと | 他人の記事の URL → 302 で一覧に戻される |
| エラーログ | `docker compose logs app` に `production.ERROR` が 0 件 |
| バックアップ | `backup.sh` → サーバー上と S3 の両方にある |
| 再起動からの復帰 | `sudo systemctl reboot` → swap とコンテナ 3 つが自動で戻る |
| メモリ | 使用中 522MB、swap 123MB（1GB で足りている） |

curl でログインするには、まず GET で `XSRF-TOKEN` の Cookie を受け取り、その値を `X-XSRF-TOKEN` ヘッダに付けて POST する（Laravel の CSRF 対策）。
