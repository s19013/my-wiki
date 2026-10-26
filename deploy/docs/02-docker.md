# 2. Docker 構成の仕組み

## 全体像

```
                  インターネット
                       │ 80 / 443
              ┌────────▼────────┐
              │ caddy           │  証明書の取得・更新、HTTP→HTTPS リダイレクト
              └────────┬────────┘
                       │ http://app:80（Docker の内部ネットワーク）
              ┌────────▼────────┐
              │ app             │  Apache + PHP 8.2 + Laravel
              │  volume: storage│  ← セッションなど（消えると全員ログアウト）
              └────────┬────────┘
                       │ db:3306（内部ネットワークのみ。外には出さない）
              ┌────────▼────────┐
              │ db              │  MariaDB 11.4（サーバーの 127.0.0.1:3306 にも公開）
              │  volume: db-data│  ← DB の中身
              └─────────────────┘
```

| ファイル | 役割 |
|---|---|
| [compose.yaml](../../compose.yaml) | 3 つのコンテナの構成 |
| [Dockerfile](../Dockerfile) | app のイメージの作り方 |
| [entrypoint.sh](../entrypoint.sh) | app の起動時に毎回実行される処理 |
| [apache.conf](../apache.conf) / [php.ini](../php.ini) | 旧サーバーから引き継いだ設定 |
| [Caddyfile](../Caddyfile) | HTTPS とリバースプロキシ |
| [.env.production.example](../.env.production.example) | 設定のテンプレート |
| [.dockerignore](../../.dockerignore) | イメージに入れないファイル |

## Dockerfile: 3 段階のビルド（マルチステージビルド）

イメージを 3 つの段階に分けて作り、**最後の段階だけ** を本番イメージにしている。

```
① vendor  (composer:2)         composer install → vendor/
② assets  (node:20-alpine)     npm ci → vite build → public/build/
③ 本番    (php:8.2-apache)      ①の vendor/ と ②の public/build/ だけをコピー
```

**なぜ分けるのか:** node や composer は、ビルドのときにしか使わない。最後の段階に必要なものだけをコピーすれば、本番イメージに node が入らず、軽くて安全になる。サーバー上で `npm` を直接実行する必要もなくなる。

**なぜ ① → ② の順か:** [resources/js/app.js](../../resources/js/app.js) が `vendor/tightenco/ziggy` を import しているため。Vite でビルドする時点で vendor が必要になる。

### 細かい点

- `composer install --no-dev`: 開発用パッケージ（phpunit など）を本番に入れない。これが原因で 1 つバグが出た（[6 章](06-pitfalls.md#faker-が開発用パッケージだった)）。
- `--ignore-platform-reqs`: `inertiajs/inertia-laravel 0.5.4` が「PHP 8.1 まで」と宣言しているため、この確認を飛ばしている。動作は確認済み。
- `install-php-extensions`: PHP 拡張（pdo_mysql など）を 1 行で入れられる定番の道具。
- `COPY . .` の前に `composer.json` だけを先にコピーしているのは、**レイヤーキャッシュ** のため。コードだけを変えたときは、composer install の段階をキャッシュから再利用できるので、ビルドが速くなる。

### opcache.validate_timestamps = 0

PHP は、毎回ファイルの更新を確かめずに、コンパイル済みのコードを使い回す。コードはイメージに焼き込まれていて実行中に変わらないので、確かめる必要がない。
その代わり、**コードの変更はイメージを作り直さないと反映されない**。だからデプロイは毎回 `docker compose build` から始まる。

## entrypoint.sh: 起動時の処理

コンテナが起動するたびに、次を実行してから Apache を立ち上げる。

1. `storage/` の下のディレクトリを作り、所有者を `www-data` にする
2. `php artisan config:cache` と `view:cache`
3. `php artisan route:clear`（`route:cache` は使えない。[6 章](06-pitfalls.md#routecache-が失敗する)）

**なぜビルド時ではなく起動時に `config:cache` するのか:** `config:cache` は、その時点の環境変数（`.env.production` の値）を固めて保存する。環境変数が渡されるのはコンテナの起動時なので、ビルド時に実行すると空の設定が固まってしまう。

## compose.yaml のポイント

### env_file

3 つのコンテナすべてに `.env.production` を渡している。Laravel は `DB_*` を、MariaDB は `MARIADB_*` を、Caddy は `SITE_ADDRESS` などをそれぞれ読む。
ファイルを 1 つにまとめているぶん、`DB_PASSWORD` と `MARIADB_PASSWORD` には同じ値を 2 回書く必要がある。

`MARIADB_*` が使われるのは **DB を初めて作るときだけ**。あとから値を変えても、DB 内のユーザーのパスワードは変わらない。

### volume

| volume | 中身 | 消えたら |
|---|---|---|
| `db-data` | DB の中身 | データがすべて消える（バックアップから戻す） |
| `storage` | セッション・キャッシュ | 全員ログアウトされるだけ |
| `caddy-data` | 取得した証明書 | 再取得される（取得回数の上限に注意） |

`docker compose down` では volume は消えない。**`down -v` で消える** ので、本番では使わないこと。

### healthcheck と depends_on

```yaml
depends_on:
  db:
    condition: service_healthy
```

app は、db が「起動した」ではなく「接続を受け付けられる状態になった」まで待ってから起動する。MariaDB は起動から接続できるまでに数秒かかるので、これがないと app の最初のリクエストが失敗する。

### restart: unless-stopped

コンテナが落ちたとき、そしてサーバーを再起動したときに、自動で立ち上がる。再起動して戻ってくることは実際に確認済み。

### db のポートは 127.0.0.1 にだけ公開する

```yaml
ports:
  - "127.0.0.1:3306:3306"
```

`127.0.0.1:` を付けると、サーバー自身からしか繋がらない。インターネットからは届かず、DBeaver などからは SSH トンネルを通して使う。
`"3306:3306"` のように IP を省略すると `0.0.0.0`（すべてのアドレス）に公開されて、外から DB に繋がってしまうので注意。

最初は `ports:` を書かずに、Docker の内部ネットワークからしか繋がらないようにしていた。しかし、それだと SSH トンネルを通しても DB に届かないため、この形にした。
旧サーバーでは root が `%`（どこからでも接続可）で作られていたが、こちらでは仕組みとして外から届かない。

### logging

```yaml
options:
  max-size: "10m"
  max-file: "5"
```

ログはコンテナごとに最大 50MB で、古いものから消える。旧サーバーでは Apache のログが 258MB に膨らんでいた。

## Caddy

Caddy は、**ドメイン名を書くだけで Let's Encrypt の証明書を取ってきて、自動で更新する** Web サーバー。旧サーバーの bncert と lego、そして証明書更新用の cron が、これ 1 つで不要になった。

### TLS_MODE の切り替え

```
(tls-acme) {
}
(tls-internal) {
	tls internal
}
{$SITE_ADDRESS} {
	import tls-{$TLS_MODE:acme}
	...
}
```

- `acme`: Let's Encrypt から本物の証明書を取る（本番）
- `internal`: Caddy が自分で署名した証明書を使う（ブラウザに警告が出る）

本物の証明書を取るには、そのドメイン（sundlf.com）へのアクセスがこのサーバーに届いている必要がある。切り替える前の新サーバーにはまだ届かないので、それまでは `internal` で動かして確認した。

`{$VAR}` は Caddyfile を読み込むときに環境変数で置き換えられる。この仕組みを使って、読み込むスニペットを環境変数で選んでいる。

### Laravel 側の対応

Caddy から app へは HTTP で転送される。それでも Laravel が https の URL を作れるのは、既存の設定がそうなっていたから。

- [TrustProxies](../../app/Http/Middleware/TrustProxies.php) の `$proxies = '*'` で、Caddy が付ける `X-Forwarded-Proto` を信用する
- [AppServiceProvider](../../app/Providers/AppServiceProvider.php) で、production のときは `URL::forceScheme('https')`

apache.conf の `RemoteIPHeader` は、Apache のログに Caddy の IP ではなく本当の接続元 IP を出すための設定。

## .dockerignore

`.env`、`env2`、`*.pem` などを、イメージに入れないようにしている。`COPY . .` はディレクトリを丸ごとコピーするので、これがないと秘密情報がイメージの中に残ってしまう。

## PHP を上げるとき

```dockerfile
FROM php:8.2-apache     ← ここを php:8.3-apache に変える
```

デプロイして問題があれば、元に戻してデプロイし直す。Laravel 9 は公式には 8.2 までの対応なので、8.3 で動く保証はない。上げたあとは [6 章](06-pitfalls.md#テストの失敗が-php-のせいかを確かめる) の方法でテスト結果を比べると安心。

## ローカルで動かす

```sh
cp deploy/.env.production.example .env.production
# SITE_ADDRESS=localhost / TLS_MODE=internal / APP_URL=https://localhost
# APP_KEY・各パスワード・ACME_EMAIL を埋める
docker compose up -d --build
# https://localhost を開く（自己署名なので警告が出る）
```
