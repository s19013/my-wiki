# 6. ハマりどころと教訓

作業中に引っかかったこと。似たエラーに出会ったときのために、症状 → 原因 → 対処 の順で書く。

## アプリ側

### Faker が開発用パッケージだった

- **症状:** 本番イメージで新規登録すると 500 になる。ログには `Class "Faker\Factory" not found`
- **原因:** 新規登録時にサンプルデータを作る処理（`SampleDataForNewRegistrantsJp/En`）が Faker を使っている。しかし Faker は `require-dev` に入っていて、`composer install --no-dev` では入らない。旧サーバーは開発用も含めて全部入れていたので、表に出ていなかった
- **対処:** `fakerphp/faker` を `require` に移した（バージョンは v1.21.0 のまま）
- **教訓:** 「本番で動いていた」は「正しく設定されていた」とは限らない。環境を作り直すと、隠れていた依存が見つかる

#### composer.lock を正しく直す

`composer require --no-update` と `composer update --lock` では、composer.json は変わっても、lock ファイルの中で Faker が `packages-dev` に残ったままだった。その結果、`composer install` が `Required package "fakerphp/faker" is not present in the lock file` で失敗した。

```sh
composer update fakerphp/faker --with fakerphp/faker:1.21.0 --no-install
```

`--with` でバージョンを固定すると、ほかのパッケージを上げずに、lock の中で Faker を `packages` に移せる。直したあとは、`git diff composer.lock` で「移動しただけで、バージョンが変わっていない」ことを確かめた。

### route:cache が失敗する

- **症状:** `Unable to prepare route [api/bookmark/{bookMarkId}] for serialization. Another route has already been assigned name [api.bookMark.delete].`
- **原因:** routes に同じ名前のルートが 2 つある。ルートをキャッシュするときは、名前が重複しているとエラーになる
- **対処:** アプリのコードは触らない方針だったので、`route:cache` は使わないことにした（旧サーバーでも使っていなかった）。直すなら、どちらかのルートの名前を変える

### Accept-Language が無いと新規登録が 500

- **症状:** curl で新規登録すると `Undefined array key "HTTP_ACCEPT_LANGUAGE"`
- **原因:** `RegisteredUserController` が `$_SERVER['HTTP_ACCEPT_LANGUAGE']` を、あるかどうか確かめずに読んでいる。ブラウザは必ずこのヘッダを送るので普段は起きないが、curl やボットからのアクセスでは起きる
- **対処:** 今回は直していない（curl に `-H Accept-Language:ja` を付けて確認した）。直すなら `$request->header('Accept-Language', 'ja')`

### inertia-laravel が PHP 8.1 までと宣言している

- **症状:** PHP 8.2 で `composer install` すると `inertiajs/inertia-laravel v0.5.4 requires php ~8.0.0|~8.1.0`
- **対処:** `--ignore-platform-reqs` で確認を飛ばした。画面の動作は確認済み
- **補足:** 宣言（composer.json の `require.php`）は「作者が動作を保証している範囲」であって、それ以外では絶対に動かないという意味ではない。ただし保証はないので、動作確認が必要

### テストの失敗が PHP のせいかを確かめる

PHP 8.2 で PHPUnit を実行すると、218 件中 67 件が失敗した。PHP を上げたせいかどうかを知るには、**上げる前のバージョンでも同じテストを実行して、失敗したテストの集合を比べる**。

```sh
vendor/bin/phpunit --testdox --colors=never > t82.txt   # PHP 8.2 で
vendor/bin/phpunit --testdox --colors=never > t81.txt   # PHP 8.1 で
grep '✘' t82.txt | sort > t82.fail
grep '✘' t81.txt | sort > t81.fail
diff t82.fail t81.fail
```

結果は、8.2 で失敗したテストはすべて 8.1 でも失敗していた。つまり、もともと失敗していたテストで、PHP を上げたことによる影響ではない（日本語のメッセージの比較などが中心）。

旧本番と同じ 8.0 で比べられなかったのは、lock ファイルに PHP 8.1 以上を必要とするパッケージ（symfony 6.2）が入っていたため。旧サーバーでは `composer update` が実行されていて、リポジトリとは違う lock で動いていた（`git status` に `M composer.lock` が出ていた）。

テストは、使い捨てのコンテナの中で実行した。リポジトリを読み取り専用でマウントし、コンテナ内の `/tmp` にコピーしてから dev パッケージを入れる。こうすると、手元のディレクトリに `vendor/` を作らずに済む。

## Docker・シェル

### up --build したのに古いイメージのまま

- **症状:** composer.json を直して `docker compose up -d --build` したのに、直っていない
- **原因:** ビルドが失敗していた。しかし出力の最後の数行しか見ていなかったので、気づかなかった。失敗すると、前のイメージのまま起動する
- **対処:** `docker compose build` を単独で実行して、エラーを見た
- **教訓:** 「反映されない」と思ったら、イメージの作成日時（`docker images`）を見る

### Caddy が起動と停止を繰り返す

- **症状:** caddy コンテナが `Restarting (1)` のまま
- **原因:** `ACME_EMAIL` が空だったので、Caddyfile の `email {$ACME_EMAIL}` が `email` だけになり、設定ファイルの読み込みでエラーになっていた
- **調べ方:** ログに理由が出ていなかったので、設定ファイルだけを検証した

  ```sh
  docker compose run --rm --no-deps --entrypoint caddy caddy validate --config /etc/caddy/Caddyfile
  ```

### pipefail と grep -q

```sh
set -o pipefail
zcat dump.gz | grep -q 'INSERT'      # 正常なダンプでも失敗することがある
```

`grep -q` は、最初に見つけた時点で終了する。すると、まだ書き出している途中の zcat が SIGPIPE を受け取って異常終了し、pipefail がそれを失敗として扱う。
`zgrep -q 'INSERT' dump.gz` のように、パイプを使わない形にした。`tail` のように最後まで読むコマンドなら、この問題は起きない。

### Docker Desktop は /tmp をマウントできない

- **症状:** `mounts denied: The path /tmp/... is not shared from the host`
- **原因:** 手元の Docker は Docker Desktop で、仮想マシンの中で動いている。そのため、ホームディレクトリ以外のパスはそのままでは共有されない
- **対処:** テストのときだけ、保存先をホームディレクトリの下に変えた。サーバーの Docker Engine では起きない

### 一時的な SSH 鍵の期限切れ

`get-instance-access-details` で作る鍵は、数時間で使えなくなる。作業の途中で `Permission denied (publickey)` が出たら、発行し直す。
鍵ファイルを管理しなくてよい代わりに、毎回発行が必要になる。

## まだ残っている課題

今回は移行が目的だったので、手を付けていないもの。アプリを作り直すときに一緒に解決する想定。

| 課題 | 補足 |
|---|---|
| Laravel 9 はサポート切れ | composer が「15 パッケージに 51 件のセキュリティ勧告」と警告している |
| PHP 8.2 のサポートは 2026-12-31 まで | その後は 8.3 を試す |
| ルート名の重複 | `route:cache` が使えない |
| `HTTP_ACCEPT_LANGUAGE` の未定義エラー | ブラウザ以外からのアクセスで 500 |
| PHPUnit の 67 件の失敗 | もともと失敗していたもの |
| Playwright が Docker 環境に向けられない | テストの接続先が `http://127.0.0.1:8000` に固定されている |
| メール未設定 | パスワードリセットのメールが届かない |
