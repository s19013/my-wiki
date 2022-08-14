少し複雑な部分なところだけ解説
# searchArticle
### 目的
記事をデータベースから探す  


タグ検索をするかしないかでsql文の作り方を分岐  
->sql文のつくり方が大きくことなるため
#### タグも検索する場合
articleテーブルとarticle_tags,tagsを結合したサブテーブル(副問合せ)を作る 
articledテーブルをメイン  
  
##### 以下の条件で左外部結合  
leftJoin('articles','articles.id','=','article_tags.article_id')  
leftJoin('tags','tags.id','=','article_tags.tag_id')  
  
<!-- 今さらながらもう少し効率のよい?検索方法を思いついた -->
##### 以下の条件で絞り込み
* ログインユーザーのだけを取ってくる
* 削除されてないものたちだけを取り出す
* 検索条件のタグが紐付けられている記事を取り出す

###### `articles.id`でまとめる
###### `articles.id`の個数がタグリストの長さと同じものを抽出
このサブテーブルから'articles.id','articles.title','articles.body','articles.updated_at'を取り出す

### サブテーブルを作る目的
* 一度に書くとごちゃごちゃしたsqlになるため
* 検索したいタグがすべて紐付けられている記事を探すため

## タグを検索する場合のイメージ (色々略してます)(わかりやすさのため順番変えたりしてます)
ログインユーザー:1
検索するタグ:[apple,banana]

### articles
|id|user_id|title|body|delete_at|update_at|
|--|--|--|--|--|--|
|1|1|title|test,test|null|2022-08-12|
|2|1|test|test,test|null|2022-08-12|
|3|2|title|test,test,|null|2022-08-12|
|4|1|title2|tetetetetete|2022-08-14|2022-08-13|

### tags
|id|user_id|name|delete_at|
|--|--|--|--|
|1|1|apple|null|
|2|2|apple|null|
|3|2|banana|null|
|4|1|banana|null|
|5|1|melon|2022-08-14|
|6|1|pain|null|

### article_tags
|article_id|tag_id|delete_at|
|--|--|--|
|1|1|null|
|1|2|null|
|1|6|2022-08-14|
|2|4|null|
|2|6|null|
|3|6|null|

## ログインユーザーのだけを取ってくる
articles  
|id|user_id|title|body|delete_at|update_at|
|--|--|--|--|--|--|
|1|1|title|test,test|null|2022-08-12|
|2|1|test|test,test|null|2022-08-12|
|3|2|title|test,test,|null|2022-08-12|
|4|1|title2|tetetetetete|2022-08-14|2022-08-13|

↓

|id|user_id|title|body|delete_at|update_at|
|--|--|--|--|--|--|
|1|1|title|test,test|null|2022-08-12|
|2|1|test|test,test|null|2022-08-12|
|4|1|title2|tetetetetete|2022-08-14|2022-08-13|

tags  
|id|user_id|name|delete_at|
|--|--|--|--|
|1|1|apple|null|
|2|2|apple|null|
|3|2|banana|null|
|4|1|banana|null|
|5|1|melon|2022-08-14|
|6|1|pain|null|

↓

|id|user_id|name|delete_at|
|--|--|--|--|
|1|1|apple|null|
|4|1|banana|null|
|5|1|melon|2022-08-14|
|6|1|pain|null|

## 削除されてないものたちだけを取り出す
articles  
|id|user_id|title|body|delete_at|update_at|
|--|--|--|--|--|--|
|1|1|title|test,test|null|2022-08-12|
|2|1|test|test,test|null|2022-08-12|
|4|1|title2|tetetetetete|2022-08-14|2022-08-13|

↓

|id|user_id|title|body|delete_at|update_at|
|--|--|--|--|--|--|
|1|1|title|test,test|null|2022-08-12|
|2|1|test|test,test|null|2022-08-12|

tags  
|id|user_id|name|delete_at|
|--|--|--|--|
|1|1|apple|null|
|4|1|banana|null|
|5|1|melon|2022-08-14|
|6|1|pain|null|

↓

|id|user_id|name|delete_at|
|--|--|--|--|
|1|1|apple|null|
|4|1|banana|null|
|6|1|pain|null|

article_tags
|article_id|tag_id|delete_at|
|--|--|--|
|1|1|null|
|1|2|null|
|1|6|2022-08-14|
|2|4|null|
|1|4|null|

↓

|article_id|tag_id|delete_at|
|--|--|--|
|1|1|null|
|1|2|null|
|2|4|null|
|1|4|null|

## 結合
|id|user_id|title|body|delete_at|update_at|tag_id|delete_at|name|delete_at|
|--|--|--|--|--|--|--|--|--|--|
|1|1|title|test,test|null|2022-08-12|1|null|apple|null|
|1|1|title|test,test|null|2022-08-12|4|null|banana|null|
|2|1|test|test,test|null|2022-08-12|4|null|banana|null|
|2|1|test|test,test|null|2022-08-12|6|null|pain|null|

## 検索条件のタグが紐付けられている記事を取り出す
|id|user_id|title|body|delete_at|update_at|tag_id|delete_at|name|delete_at|
|--|--|--|--|--|--|--|--|--|--|
|1|1|title|test,test|null|2022-08-12|1|null|apple|null|
|1|1|title|test,test|null|2022-08-12|4|null|banana|null|
|2|1|test|test,test|null|2022-08-12|4|null|banana|null|
|2|1|test|test,test|null|2022-08-12|6|null|pain|null|

↓

|id|user_id|title|body|delete_at|update_at|tag_id|delete_at|name|delete_at|
|--|--|--|--|--|--|--|--|--|--|
|1|1|title|test,test|null|2022-08-12|1|null|apple|null|
|1|1|title|test,test|null|2022-08-12|4|null|banana|null|
|2|1|test|test,test|null|2022-08-12|4|null|banana|null|

## idでまとめて数える
|id|count|
|--|--|
|1|2|
|2|1|

## 個数が検索するタグの個数と同じものを抽出
id:1

## サブテーブルの中身
|id|user_id|title|body|delete_at|update_at|
|--|--|--|--|--|--|
|1|1|title|test,test|null|2022-08-12|
