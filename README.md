<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# my-wiki
## リンク
http://35.230.90.39/

## 概要
ブックマークや記事をタグをつけて保存する｡

## 使った技術
* php
* laravel
* vue.js
* javascript
* html
* css
* gcp
* computer engine
* apach
* mariaDB

## 制作背景
laravelとvueを組み合わせた何かを作りたかった｡  
chromeのブックマーク機能に不満があった｡  

## 実装した大まかな機能
* ユーザー登録
* ログイン
* ログアウト
* ブックマーク
    * 作成
    * 検索
    * 編集
* 記事
    * 作成
    * 検索
    * 閲覧
    * 編集
* タグ
    * 作成
    * 検索
    * 編集
## 苦労したところ
* デプロイはまだなれていない
* タグで絞り込み検索する時のsql文
* spa認証の実装
* ''の中は自動補完がきかないのでミスが多々あった

## 反省
* inertiaのいいところを活かせていないと思う
* git commitの頻度が低くてctrl+zを連打するような場面が多かった
* 頭に入っている情報が断片的だとわかった｡もう一度技術書や学習サイトなどで体型的に学び治す必要がある
* transitionが全体的にわからない
* それなりに計画は立てていたが結局行き当たりばったりになった
* あれが必要､これが必要と後になってわかって出戻りが多々あった
* 苦労というよりは時間がかかってしまったことがきになる
* .envは初期はgitで追跡されないが追跡しないといつどこを変えたかわからなくなるので追跡させたほうが良いかもしれない
* inertiaを今回で初めて触ったのでinertiaを理解するところ

## 理解が曖昧なところ
* viteの設定
* spa認証
* .envの書き方
* inertia
* apache
* セキュリティ

## 今後追加(改善)したい機能
* 見た目
    * スマホに対応させる
* 記事､ブックマーク登録､編集
    * よくよく考えればブックマークを保存する時,urlの前後の空白を消さなくてはならない(正規表現で消せば良いと思う)
    * fromを正しく使う
    * vueのバリデーションを正しく使う
    * 記事の登録はapi通信でなくても良いと思うので修正
    * markedでは機能が不十分に感じるので他のパッケージを探す
        * 引用の書き方が反映されない
* 記事やブックマークの検索
    * 検索オプション追加
    * 日時で絞り込む
    * ならびえ
    * 記事検索で本文の最初1行だけ表示されるようにする(パフォーマンスと相談)
    * 検索などで何もヒットしなかった時のメッセージを表示させる
    * pagination Postにする必要はなかった
    * タグなしのブックマーク､記事を検索できるように
* その他
    * たまにスクロールバーがあらわないバグ
    * apiで通信する必要がないのにapi通信しているのでそこを治す
    * コンポーネント化できそうなものが多いのでコンポーネント化する
    * テスト系のコードを書いて見る(assert()とかphpunitとかを使えばよいらしい?)
    * `cron`やシェルスクリプトを使ってデータベースの定期自動バックアップを取りたい
    * `https`化する
    * ほぼ同じことしてる部分があるのでまとめてしまいたいという気持ちもあるが帰って読みにくいコードになるのではないか?
    * transitionContorollerをarticleとbookmarkで分ける

## faq
Q:laravelを使った理由は?  
A:現段階でサーバーサイド言語でまともに使える言語がphpしかなかったため｡  
  rubyよりもlaravelを採用している会社が若干多かったため  
  
Q:vueを採用した理由は?  
A:laravelと組み合わせて開発されることが多いため  
  reactよりも難易度が低いらしいため  
  
Q:gcpを採用した理由は?  
A:awsもしくは､gcpを採用している会社が多く､awsは前回使ったので今回はgcpを使って見ようと思った｡  
  
Q:spa認証を採用した理由は?  
A:トークンを発行せず､クッキーを使って通信するのでこちらのほうが簡単ではと考えたから｡  
スマホアプリを作る予定はないため  
  
Q:独自ドメインを設定していない理由は  
A:お金がかかるから  
  
Q:awsとqcpの両方を使ってみた感想は?  
A:※一部機能の一部しか触ってない人間の意見として聞いて頂きたい  
  awsにはlight sailというあらかじめlamp環境が用意されているサービスがあるためお手軽さはawsの方が上かもしれない  

