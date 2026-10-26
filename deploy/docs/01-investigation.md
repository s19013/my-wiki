# 1. 調査: 旧サーバーで何が起きていたか

## きっかけ

2026-10 に PHP の重大な脆弱性（深刻度 9.2、リモートコード実行）が公表された。修正版は 8.5.12 / 8.4.27 / 8.3.36 / 8.2.35。
本番は PHP 8.0.24 で、8.0 は 2023-11 にサポートが終わっている。つまり修正版は出ない。

## 調べ方

**最初は読み取り専用のコマンドだけを使う。** 何が動いているか分からないサーバーで設定を変えると、何が原因で壊れたのか分からなくなる。

### AWS 側（手元の aws cli）

```sh
aws lightsail get-instance --instance-name LAMP_PHP_8-1     # プラン・ブループリント・ポート
aws lightsail get-static-ips                                # 静的 IP がどれに付いているか
aws lightsail get-instance-snapshots                        # スナップショットの有無
aws route53 list-resource-record-sets --hosted-zone-id ...  # DNS
```

### サーバーの中（SSH）

秘密鍵ファイルを探さなくても、aws cli で **一時的な SSH 鍵** を発行して入れる（数時間で無効になる）。

```sh
aws lightsail get-instance-access-details --instance-name LAMP_PHP_8-1 --protocol ssh
```

見た場所:

| 見たもの | コマンドの例 | 分かること |
|---|---|---|
| バージョン | `php -v`、`/opt/bitnami/properties.ini`、`/etc/os-release` | PHP・スタック・OS |
| Apache の設定 | `/opt/bitnami/apache/conf/bitnami/*.conf`、`httpd.conf` の末尾 | どのドメインがどのディレクトリか、自分で足した設定 |
| 証明書 | `openssl x509 -in ... -noout -dates` | 期限と、更新が動いているか |
| cron | `crontab -l`、`/etc/cron.d/` | 定期的に何が動いているか |
| DB | `my.cnf`、`information_schema.tables` | 設定とデータ量 |
| アプリ | `git log -1`、`git status`、`.env`（値は伏せて見る） | 本番にどのコミットが出ているか |
| 作業の跡 | `~/.bash_history`、`~/.mysql_history` | 過去に自分が何をしたか |

`.env` は `sed -E "s/=.*/=***/"` のように値を伏せて、どのキーがあるかだけを見た。

ファイルの更新日時（`ls -la --time-style=long-iso`）を見ると、「初期状態のままのファイル」と「自分で触ったファイル」を見分けやすい。

## 分かったこと

### 構成

| 項目 | 値 |
|---|---|
| インスタンス | `LAMP_PHP_8-1`（1GB / 40GB、2022-12 作成、673 日連続で稼働） |
| スタック | Bitnami LAMP 8.0.24-2 / Debian 11（どちらもサポート切れ） |
| アプリ | my-wiki（Laravel 9.48）と knowledge-sharing-board（ドメイン失効で実質停止） |
| 証明書 | bncert で導入した Let's Encrypt。cron で自動更新されていた（ここは正常） |

### 問題

| 問題 | 影響 |
|---|---|
| **DB バックアップが 2023-02 から 1362 件すべて空**（20 バイト） | 3 年半、復元できるバックアップが 1 つも無かった。原因は [3 章](03-backup.md) |
| DB の root パスワードがスクリプトに直書きされ、公開リポジトリに載っていた | 外からは DB に繋がらない設定だったが、パスワードの変更が必要 |
| `APP_KEY` が `app.yaml`（昔の GAE 用）に載っていた | セッションや Cookie の暗号鍵。変更が必要 |
| 本番が `APP_ENV=local` / `APP_DEBUG=true` | エラー時にスタックトレースや設定値が画面に出る恐れ |
| 本番が 89 コミット分古い | ローカルの main と本番がずれていた |
| `laravel.log` が 50MB で書き込めなくなっていた | ファイルの所有者が変わっていた |
| メールが未設定（`mailhog`） | パスワードリセットのメールが届かない（今回は対象外） |

## 方針を決める

### なぜ「その場で PHP を上げる」ではないのか

Bitnami は PHP・Apache・MariaDB を 1 つの塊として配布している。PHP だけを入れ替える方法は用意されておらず、公式の手順も「新しいインスタンスを作って移す」になっている。
つまり、どのみち新しいサーバーへの移行になる。

### なぜ Docker か

| | 新しい Bitnami に移す | Docker で作り直す |
|---|---|---|
| 設定の記録 | 今回と同じく、サーバーの中にだけ残る | すべてリポジトリのファイルに残る |
| PHP のバージョン | ブループリントが決める（Laravel 9 では動かない 8.5 しか無かった） | イメージのタグで自由に選べる |
| 次に上げるとき | また移行 | `FROM` の 1 行を変えてデプロイ |

「何を設定したか覚えていない」がそもそもの困りごとだったので、設定がコードとして残る Docker を選んだ。

### なぜ PHP 8.2 か

Laravel 9 が公式に対応しているのは PHP 8.0〜8.2。アプリごと作り直す予定があるので、Laravel は上げず、Laravel 9 が動く範囲で修正済みの 8.2.35 にした。
8.2 のセキュリティサポートは 2026-12-31 までなので、それ以降は 8.3 を試す（[2 章](02-docker.md#php-を上げるとき)）。

### その他の決定

- knowledge-sharing-board は廃止。DB ダンプだけ手元に保存した。
- 1GB プランのまま、swap を 2GB 足す（費用を抑えるため）。
- 自動スナップショットは使わず、DB ダンプを S3 に送る（月に約 1 円）。
- Terraform は使わず、aws cli のスクリプトで済ませる（覚えることを減らすため）。

## 最初にやったこと: 保全

計画が決まって最初にしたのは、作業ではなく **今の状態を守ること**。

```sh
aws lightsail create-instance-snapshot --instance-name LAMP_PHP_8-1 \
    --instance-snapshot-name LAMP_PHP_8-1-before-docker-2026-10-26

# mysqldump はフルパスで呼ぶ（理由は 3 章）
/opt/bitnami/mariadb/bin/mysqldump --single-transaction --routines --triggers \
    -uroot -p... --databases my-wiki | gzip > my-wiki-2026-10-26.sql.gz
```

ダンプは取ったあとに、壊れていないこと（`gzip -t`）と、最後の行が `-- Dump completed` であること（最後まで書き出せた印）を確かめた。
