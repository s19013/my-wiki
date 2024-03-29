<div align="center">
    <img src="https://sundlf.com/sundlf_logo.png" width="400">
</div>


# sundlf
## リンク
<a href="https://sundlf.com?utm_source=github" target="_blank">
    sundlf.com
</a>

## 目次
* [概要](#概要)
* [何ができるか](#何ができるか)
* [デモ](#デモ)
* [制作背景 なぜ作ったのか](#制作背景-なぜ作ったのか)
* [開発メモ](#開発メモ)
* [この作品をもとに書いた記事](#この作品をもとに書いた記事)
* [使った技術](#使った技術)
* [使っていたがやめた技術](#使っていたがやめた技術)
* [よくある質問集](#よくある質問集)
* [アプリの機能](#アプリの機能)
* [他にやったこと](#他にやったこと)
* [大きなアップデートの時間時系列](#大きなアップデートの時間時系列)
* [苦労したところ](#苦労したところ-頑張ったところ)
* [反省](#反省)
* [理解が曖昧なところ](#理解が曖昧なところ)
* [今後追加(改善)したい機能](#今後追加改善したい機能)

## 概要
ブックマークやメモをタグをつけて保存する｡

## 何ができるか
* メモをmarkdown形式でかいて保存できる
* ブックマークを保存できる
* メモ､ブックマークにタグをつけられる
* メモ､ブックマークをタグで絞ったり､並び順を変えるなどして検索できる
* chromeの拡張機能を使ってアプリを開かずともブックマークを保存できる
	* <a href="https://chrome.google.com/webstore/detail/sundlf-bookmark-addon/mfcobcdpjbgnpbkhbbfaabkkphpceoka" target="_blank">拡張機能</a>
	* <a href="https://github.com/s19013/sundlf-addon" target="_blank">拡張機能のリポジトリ</a>

### デモ
#### メモ新規作成
<video height="300" src="https://github.com/s19013/my-wiki/assets/50346558/f69075b6-602c-4b46-ba36-669920f93880" ></video>

#### タグのつけ外し
<video height="300" src="https://github.com/s19013/my-wiki/assets/50346558/cfe0bf67-04ab-4e58-97cf-63a0de6b9568" ></video>

#### メモ編集
<video height="300" src="https://github.com/s19013/my-wiki/assets/50346558/98a8ac34-4e63-44d0-aaf1-49341f5c57a3" ></video>

#### メモ閲覧
<video height="300" src="https://github.com/s19013/my-wiki/assets/50346558/c384700a-799e-4f31-b2d8-a7239e846a7d" ></video>

#### メモ削除
<video height="300" src="https://github.com/s19013/my-wiki/assets/50346558/716f74f4-1d93-4e4a-8a0a-4d996a0a1bf4" ></video>

#### 検索
<video height="300" src="https://github.com/s19013/my-wiki/assets/50346558/a7a857fb-f6b4-495a-b2df-647c314b9428" ></video>

#### 並べ替え
<video height="300" src="https://github.com/s19013/my-wiki/assets/50346558/29b7cfba-cd3f-4f43-a7f7-aaa54e1ef2d3" ></video>

#### ブックマークの保存､新規タグの作成
アドオンを使ったやり方が圧倒的に便利なのでこの方法で保存する人は少ないと思う

<video height="300" src="https://github.com/s19013/my-wiki/assets/50346558/c262bade-746a-42c4-bd76-6179532a1218" ></video>

#### アドオンを使ったブックマークの保存
むしろこのやり方を推奨している
<video height="300" src="https://github.com/s19013/my-wiki/assets/50346558/960a7448-e8aa-42c3-86e0-580d1367faa1" ></video>

## 開発メモ(すごく雑)
https://github.com/s19013/my-wiki/tree/main/dev_memo

## 制作背景(なぜ作ったのか)
* laravelとvueを組み合わせた何かを作りたかった｡  
* chromeのブックマーク機能に不満があった｡  
    * 探しにくい､整理がしにくいと思った
* qiitaのストック機能に不満があった｡  
    * ストック名の数が増えるとストック名を探しにくい
    * ストック名が重複してしまう
* 保存したブックマークなどをランダムに表示する機能が欲しかった

## 使った技術
* php
* phpUnit
* laravel
* laravel breeze
* laravel sanctum
* vue.js
* vuex
* vuetify
* inertia
* javascript
* html
* css
* aws
* lightsail
* apach
* mariaDB

## 使っていたがやめた技術
* [gcp](#一度gcpを採用した理由は)
* computer engine

## この作品をもとに書いた記事
<p>
    <a href="https://qiita.com/hideya670/items/268a9643488e4036706a" target="_blank">
        エンジニアになれないへっぽこな私が実装したタグをつける機能とタグの検索機能(n番煎じ)  
    </a>    
</p>

<p>
    <a href="https://qiita.com/hideya670/items/8a1e3fc31bf4611550a8" target="_blank">
        個人開発物のデータベース構造の変化とそうした理由
    </a>
</p>

## よくある質問集
#### Q:これを使うメリットは?  
A:タグを付けて保存するので以下の2点がメリットとなる  
* ただタイトル名を検索するよりも探しやすくなる｡
* フォルダで管理するブックマークに比べて､より詳細に整理ができる｡

#### Q:これの欠点は?  
A:以下の2点  
* ユーザーがタグをうまく使いこなせなければ帰って使いにくいアプリとなってしまう｡
    * ユーザーの能力に依存してしまう｡
* 検索機能がまだ不十分

#### Q:なぜ､わざわざランダムで表示する機能を付けたのか  
A:短く言えば偶然でもユーザーの目に触れてもらえれば何かしらのきっかけになると思うから｡  
良いサイトでもたまに見返さないと忘れてしまうので､ランダムで表示されれば見返すきっかけになると考えた｡  
特に最近保存した情報でもなければ､かなり昔に保存した情報でもない場合は検索画面にトップ当たりに表示されにくく埋もれてしまうと思っている  
それをランダムを使うことで回避できるのではと考えたから｡  

#### Q:laravelを使った理由は?    
A:現段階で私がサーバーサイド言語でまともに使える言語がphpしかなかったため｡    
  rubyよりもlaravelを採用している会社が若干多かったため  
  
#### Q:vueを採用した理由は?  
A:laravelと組み合わせて開発されることが多いため  
  reactよりも難易度が低いらしいため  
  vueを勉強すればreactも理解できるという話をきいたため
  
#### Q:一度gcpを採用した理由は?  
A:awsもしくは､gcpを採用している会社が多く､awsは前回使ったので今回はgcpを使って見ようと思った｡  

#### Q:結局awsに戻した理由は?  
A:gcpの無料期間がawsよりも短かったから｡  
awsのほうがgcpよりも安かったため｡  
[^1]:
  
#### Q:spa認証を採用した理由は?  
A:別のトークンを発行せず､クッキーを使って通信するのでこちらのほうが簡単ではと考えたから｡  
スマホアプリを作る予定はないため
  
#### Q:awsとqcpの両方を使ってみた感想は?  
A:※一部機能の一部しか触ってない,どちらも触り始めたばかりの人間の意見として聞いて頂きたい  
  awsにはlight sailというあらかじめlamp環境が用意されているサービスがあるためお手軽さはawsの方が上かもしれない  
  ただ､laravel単体だったらgcpのとあるサービス(サービス名は忘れました)に上げるだけで良いという利点がある  
  awsのドメイン発行機能とgcpのドメイン発行機能ではawsのほうが最安値が安い  
  awsは無料期間が1年なのに対し､gcpは3ヶ月のみ  



## アプリの機能
* ユーザー登録
* ログイン､ログアウト
* ブックマーク
    * 作成
    * 検索
        * ソート機能
    * 編集
    * 削除
* メモ
    * 作成
    * 一時保存
    * 検索
        * ソート機能
    * 閲覧
    * 編集
    * 削除
* タグ
    * 作成
    * 検索
    * 編集
    * 削除
* 拡張機能用api

## 他にやったこと
* ユーザーのブラウザの言語によって､英語か日本語に切り替わる
* スマホに対応
* タグ検索で一部条件でキャッシュを使うことによって負荷を減らしたり､早く動くようにした
* 広告表示
* OGP
* [サーバーサイドのテストコード](https://github.com/s19013/my-wiki/tree/main/tests)
* [シェルスクリプトによる定期的なバックアップ](https://github.com/s19013/my-wiki/blob/main/shellScript/scheduledBackup.sh)
* [シェルスクリプトによる定期的な論理削除されたデータの物理削除](https://github.com/s19013/my-wiki/blob/main/shellScript/periodicDeletion.sh)
* [このアプリを元にqiitaを書いた](この作品をもとに書いた記事)

# 大きなアップデートの時間時系列
ここの羅列しているのは｢大きな｣アップデートの話である
ここには書いてないが細かいアプデはたくさんあるし,バグ修正やリファクタリングなども沢山した
## 9月
* 最低限の機能が完成
* バックのテストコードを書いてみる
## 10月
* スマホ用のCSSを書く
* アップデートやバックアップをするためのシェルスクリプトを作った
## 11月
* モデルに書いていたデータベースに関する処理をリポジトリファイルという新しいファイルたちに移行
    * (他の人からファットモデルを防ぐために分けたほうが良いというアドバイスを受けたため)
* GCPの無料期間が切れるためawsに移行
## 12月
* SSL化
* mdエディター改良
* 利用規約作成
* プライバシーポリシー作成
* 問い合わせ機能作成
* 退会機能作成
* ショートカットキー作成
* LT大会で発表してみた
* 知り合いに使ってもらった
## 1月
* タグ編集機能作成
* ショートカットキー追加
## 2月
* OGP機能実装
* 英語と日本語の2言語化に対応
* 広告貼り付け
* Google アナリティクスのコード貼り付け
* また別の知り合いに使ってもらった
## 3月
* ソート機能実装
## 4月
* chromeアドオンを作成
* ランダムソート機能実装
## 5月
* アドオンを一般公開

## 苦労したところ,頑張ったところ
* とある目的を達成するために自作コンポーネントを作っていたが｡わざわざ自作コンポーネントをつくらなくても達成できるとわかり｡無駄な時間を浪費してしまったこと｡
* たまにスクロールバーがあらわないバグがあってそのバグが起こる原因を探る部分と解決方法
* フロントがlaravelではなくvueなので､laravelのpaginationが使えなかったので自分で実装したところ
* 一定条件下で更新したメモなどを更新しようすると更新できないバグが起こる原因を探る部分と解決方法
* metaタグ,ogp周り
    * jsだけではクローラーがうまく反応してもらえないので､一度基礎部分をlaravel側で出力して必要に応じてjsで書き換えるようにした｡
    * ここにたどり着くまでにかなりの時間がかかった｡
* クローラーの仕様
    * $_SERVER['HTTP_ACCEPT_LANGUAGE']という変数を使ってユーザーの使用言語を取得していたが､クローラーではここでエラーが出てしまう｡そのせいで､サーチコンソールにインデックスが登録されないし､ogpが表示されない｡
    * ここにたどり着くまでにかなりの時間がかかった｡
* デプロイはまだなれていない
* タグで絞り込み検索する時のsql文
* spa認証の実装
* コードの中の''の中は自動補完がきかないのでそこでの書き間違いによるバグが多々あった
* laravelのテストコードの書き方
* ssl化
他多数

## 反省
* inertiaのいいところを活かせていないと思う
* git commitの頻度が低くてctrl+zを連打するような場面が多かった
* 頭に入っている情報が断片的だとわかった｡もう一度技術書や学習サイトなどで体型的に学び治す必要がある
* transitionが全体的にわからない
* それなりに計画は立てていたが結局行き当たりばったりになった
* 最初に設計書は作ってはいたが､設計書が穴だらけだったり書き直しなどが多くなって,最終的に設計書は書かなくなってissueなどで管理するようになった
* あれが必要､これが必要と後になってわかって出戻りがたくさんあった
* 苦労というよりは時間がかかってしまったことがきになる
* .envは初期はgitで追跡されないが追跡しないといつどこを変えたかわからなくなるので追跡させたほうが良いかもしれない
* inertiaを今回で初めて触ったのでinertiaを理解するところ
* いつのまにかmainブランチで開発していた
* spaのようなmpaのような中途半端なものができてしまった｡
* テストでは更新したメモなどを再度更新するテストがなかったのでバグに気づかなかった
他多数

## 理解が曖昧なところ
* viteの設定
* spa認証
* .envの書き方
* inertia
* apache
* セキュリティ
* laravelのもっと深いところ
* vueのもっと深いところ
* v-formのprevent
* テスト
* ssl
* インフラ周り
他多数

## 今後追加(改善)したい機能
githubの[issue](https://github.com/s19013/my-wiki/issues)にも書いてあるが
* メモ､ブックマーク登録､編集
    * markedでは機能が不十分に感じるので他のパッケージを探す
        * 引用の書き方が反映されない
    * フロント側でタグ情報をキャッシュしてタグなどの検索を早くする
* メモやブックマークの検索
    * 検索オプション追加
    * 日時で絞り込む    
    * 検索などで何もヒットしなかった時のメッセージを表示させる
    * タグなしのブックマーク､メモを検索できるように
* その他
    * メモを任意で公開する機能
    * メール変更機能
    * お知らせ通知機能
    * コンポーネント化できそうなものが多いのでコンポーネント化する
    * ほぼ同じことしてる部分があるのでまとめる(かえって読みにくいコードになるのではないかと心配?)
    * メンテナンス中に表示する画面の作成
    * フロント側のテスト(環境をうまく構築できなかったため一旦断念)

## 参考にしたサイト(一部)
# vue,js
* 公式ドキュメント
* [AxiosのPOST通信でCookieをいじりたい](https://nosuke-blog.site/blog/axios-cookie)
* [](https://zenn.dev/moroshi/articles/4e08a62f3748e8)
* [LaravelのJavaScript周辺環境の整備](https://nanbu.marune205.net/2021/12/laravel7-vuetify.html?m=1)
* [イベントの伝搬をストップするv-onディレクティブの.stop修飾子 [Vue.js]](https://johobase.com/vue-js-stop-modifier/)
* [【Vue】@click.stopのstopメソッドは何をしているか？（v-on:click.stop）](https://qiita.com/shizen-shin/items/e25abd34219e1e569496)
* [Vue.jsのv-ifやv-forで無駄にタグを増やしたくない時はtemplateで代用するのが便利](https://ti-tomo-knowledge.hatenablog.com/entry/2018/10/17/100822)
* [Vue.jsで親から子のコンポーネント内メソッドを発火させる](https://proglearn.com/2021/03/12/vue-js%E3%81%A7%E8%A6%AA%E3%81%8B%E3%82%89%E5%AD%90%E3%81%AE%E3%82%B3%E3%83%B3%E3%83%9D%E3%83%BC%E3%83%8D%E3%83%B3%E3%83%88%E5%86%85%E3%83%A1%E3%82%BD%E3%83%83%E3%83%89%E3%82%92%E7%99%BA%E7%81%AB/)
* [Vue(Nuxt.js) で親子コンポーネント間でイベント（メソッド）を呼び出す方法](https://qiita.com/TK-C/items/0a3acb9d0d310f8fd380)
* [Inertia.jsでページレイアウト Inertia入門#４](https://blog.shipweb.jp/archives/456)
* [Vuetify v-iconが表示されない](https://qiita.com/buto/items/eb6bdd3391a1edcb73c4)
* [Vue.jsのtransitionの使い方！アニメーションサンプル3つ！](https://codelikes.com/vue-transition/)
* [Vue.jsについての基礎（transitionによるアニメーション）](https://qiita.com/watataku8911/items/f691a2999c3e2600173b)
* [$watchでオブジェクトの変更を監視する方法](https://qiita.com/_Keitaro_/items/8e3f8448d1a0fe281648)
* [【バックエンドLaravel】vue.jsで 自作のシンプルページネーション作成](https://reffect.co.jp/vue/laravel-vue-js-pagination)
* [Vueのv-htmlでXSSを回避する](https://qiita.com/tnemotox/items/b4b8f0f627e23dd62447)

# laravel,php
* 公式ドキュメント
* [Laravel API SanctumでSPA認証する](https://qiita.com/ucan-lab/items/3e7045e49658763a9566)
* [Laravelのモデルとマイグレーションを同時に作成する方法](https://php-junkie.net/framework/laravel/create-model-migration/)
* [データベースのカラムの設定（マイグレーションファイル）まとめ](https://laraweb.net/surrounding/4821/)
* [laravelのmigrationでintのカラムが勝手にAutoIncrementにされてハマった話](laravelのmigrationでintのカラムが勝手にAutoIncrementにされてハマった話)
    * 第2､第3引数があるとか公式ドキュメントにも書いてなかったぞ!
* [【Laravel 7.x / Vue.js】axios.postの際に500 server errorが出た時の原因突き止め方](https://note.com/code82/n/n9166b40a807c)
* [LaravelでDBにデータを保存する方法。createとinsertの違いなど](https://katsusand.dev/posts/laravel-save-data-db/)
* [Laravel,PHPで日付や時間を操作・取得したいとき](https://qiita.com/morocco/items/427914b276badcd394b6)
* [【Laravel】ファサードとは？何が便利か？どういう仕組みか？](https://qiita.com/minato-naka/items/095f2a1beec1d09f423e)
* [[PHP] 連想配列を並び替えする方法（キーでのソート、値でのソート、ソート順の独自定義）](https://www.yoheim.net/blog.php?q=20191103)
* [【Laravel】ルートのグループ化。Route::groupメソッドの役割について。（prefixとnameメソッドも））](https://qiita.com/shizen-shin/items/f20c550db5a70c86111f)
* [エスケープシーケンス](https://www.javadrive.jp/php/string/index4.html)
* [【PHP入門】正規表現で置換する方法](https://www.sejuku.net/blog/22175)
* [PHPにおける正規表現の使い方（まとめ）](https://laraweb.net/surrounding/10328/)
* [ポートフォリオを公開してたらサイバー攻撃された話 〜Laravelで多重送信対策をしよう〜](https://qiita.com/nasuB7373/items/e2c5e16a824b11610b7e)
* [Laravelクエリビルダでサブクエリを使ってデータを取得する方法](https://www.kamome-susume.com/laravel-subquery/)
* [Laravel Eloquentからサブクエリでjoinするのに苦戦した](https://pisuke-code.com/laravel-eloquent-join-subquery/)
* [Laravelでデータベースからデータ取得する時のget()とfirst()の違い](https://zenn.dev/ytksato/articles/125d3c9c79c1b5)
* [LaravelのクエリビルダでFROM句にサブクエリを利用する方法](https://alaki.co.jp/blog/?p=3231)
* [from句のサブクエリ](https://www.larajapan.com/2021/08/03/from%E5%8F%A5%E3%81%AE%E3%82%B5%E3%83%96%E3%82%AF%E3%82%A8%E3%83%AA/)
* [【Laravel】 From句にサブクエリを使用する](https://qiita.com/bashi4/items/0da586fde8cfd74667a1)
* [Laravelでgroup byしたら、"~ isn't in GROUP BY (~)"って怒られました](https://arm4.hatenablog.com/entry/2019/01/11/184734)
* [Query builder GROUP BY, HAVING, COUNT in Laravel](https://stackoverflow.com/questions/62188895/query-builder-group-by-having-count-in-laravel)
* [Trick to make laravel Inertia SEO friendly](https://dev.to/hazzazbinfaiz/trick-to-make-laravel-inertia-seo-friendly-3h3j)


# html,css
* [【習得必須】CSSでリストを横並びにする簡単な方法を徹底解説](https://web-camp.io/magazine/archives/100205)
* [要素をX軸方向へ平行移動するCSSの関数、“translateX()”について](https://web.havincoffee.com/css/value/transform/
func-translatex.html)
* [【Chrome】textareaの改行時にカーソル位置がガタっとずれる問題の対処法](https://zenn.dev/catnose99/articles/e0d42812c7588c)


# その他
* [なんでもSPAにするんじゃねぇ！という主張のその先](https://zenn.dev/rinda_1994/articles/e6d8e3150b312d)
* [SPA, SSR, SSGって結局なんなんだっけ？](https://lealog.hateblo.jp/entry/2021/08/12/103111)
* [[GCP]仮想サーバ(VMインスタンス)を構築してみた](http://shomi3023.com/2017/09/19/post-668/)
* [Amazon LightsailにPHP8系のLAMP環境を構築してみた](https://juno-blog.site/article/amazon-lightsail-ubuntu-php8/)
* [【Ubuntu】Apacheの再起動・起動・停止コマンド](https://deep-blog.jp/engineer/12315/)
* [Vue RouterのApache用 `.htaccess` の意味、理解してますか？](https://qiita.com/task4233/items/7d27759f9ebec4280753)
