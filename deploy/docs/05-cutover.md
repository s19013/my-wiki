# 5. 切り替え

## 静的 IP を付け替えるだけで移行できる仕組み

```
変更前:  sundlf.com ──(Route53 の A レコード)──→ 52.193.96.129 (StaticIp-1) ──→ LAMP_PHP_8-1
変更後:  sundlf.com ──(Route53 の A レコード)──→ 52.193.96.129 (StaticIp-1) ──→ my-wiki
```

DNS は「静的 IP」を向いていて、インスタンスを直接向いているわけではない。だから静的 IP をほかのインスタンスに付け替えれば、**DNS を変えなくても** アクセスが新サーバーに届く。

DNS を書き換える方法だと、世界中の DNS キャッシュが切れるまで（TTL の間）、旧サーバーと新サーバーにアクセスが分かれてしまう。静的 IP の付け替えなら、ほぼ一瞬で切り替わる。

## 手順と、それぞれの理由

| # | やったこと | 理由 |
|---|---|---|
| 1 | 旧サーバーで `php artisan down` | 新しい書き込みを止める。止めないと、ダンプを取ったあとの書き込みが新サーバーに移らずに消える |
| 2 | 旧サーバーで最新のダンプを取り、新サーバーで `restore.sh` | 止めた時点の最新のデータを移す |
| 2' | 新旧で件数と最終更新日時を比べる | 記事 2576 / ブックマーク 5905 / ユーザー 24 / 最終更新日時が一致。テストで作ったユーザーが消えていることも確認 |
| 3 | 静的 IP を新サーバーに付け替える | ここで切り替わる |
| 4 | `TLS_MODE=acme` にして Caddy を再起動 | 本物の証明書を取る |

止まっていたのは、1 から 4 までの数分だけ。

### なぜ 4 は 3 のあとなのか

Let's Encrypt は、証明書を発行する前に「本当にそのドメインを持っているか」を確かめる。やり方は、`http://sundlf.com/.well-known/acme-challenge/...` にアクセスして、決められた応答が返るかを見るというもの（HTTP-01 チャレンジ）。
つまり、**sundlf.com へのアクセスが新サーバーに届いてからでないと、証明書は取れない**。それまでは `TLS_MODE=internal`（自己署名）で動かしていた。

## コマンド

```sh
# 3. 手元の PC で
aws lightsail detach-static-ip --static-ip-name StaticIp-1
aws lightsail attach-static-ip --static-ip-name StaticIp-1 --instance-name my-wiki

# 4. 新サーバーで
cd /srv/my-wiki
sed -i 's/^TLS_MODE=.*/TLS_MODE=acme/' .env.production
docker compose up -d caddy
docker compose logs -f caddy      # "certificate obtained successfully" が出れば完了
```

切り替え後に確認したこと:

- 証明書の発行元が Let's Encrypt になっていること（`openssl s_client`）
- HTTP → HTTPS のリダイレクト、`Vary` ヘッダ
- ページとアセットが 200 で返ること
- `docker compose logs app` にエラーが無いこと

## ロールバックの用意

切り替えは、**戻し方が決まっている状態で** 始めた。

| 起きたこと | 戻し方 |
|---|---|
| 新サーバーで重大な問題 | 静的 IP を `LAMP_PHP_8-1` に付け替え直し、旧サーバーで `php artisan up`。ただし、切り替え後に新サーバーで書き込まれたデータは、先にダンプを取って旧サーバーに入れる |
| コードの問題 | `deploy/deploy.sh <前のコミット>` |
| PHP のバージョンの問題 | Dockerfile の `FROM` を戻してデプロイ |

旧サーバーは、すぐには消さない。止めた状態で 2 週間ほど様子を見てから消す。

## 段階ごとのコミット

作業は `docker-infra` ブランチで進め、段階ごとにコミットした。

```
a696604 fakerphp/faker を require に移動          ← アプリ側の修正は分ける
f1d0f65 Docker Compose で本番環境を構成 (段階1)
01b80e9 DB バックアップ・復元・定期削除 (段階2)
0adf2f5 サーバー構築・デプロイのスクリプト (段階3)
b738cd7 create-instance.sh: user-data を bash で実行させる
f256009 deploy/README.md を追加 (段階4)
```

- どのコミットも「そこまでの動作確認が通った状態」なので、どこにでも戻せる
- main へのマージは、切り替え後に数日問題がないことを確かめてから
- 旧スクリプト（`shellScript/`・`app.yaml`・`update_*.sh`）の削除は、旧サーバーを消すときに別のコミットで行う。それまでは旧サーバーに戻る可能性がある

## 後片付け（残っているもの）

- [ ] 旧インスタンス `LAMP_PHP_8-1` を停止する
- [ ] 翌日、cron のバックアップがサーバー上と S3 にできているか確認する
- [ ] 数日後、`docker-infra` を main にマージする
- [ ] 2 週間後、旧インスタンスとスナップショット 2 つを削除し、旧スクリプトを削除するコミットを作る
