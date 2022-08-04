# book_markテーブルとbook_mark_tags,tagsを結合
以下のものは除外
* 削除された記事
* はずされたタグ
* 削除されたタグ

SELECT book_marks.id,book_marks.title,book_marks.url  
FROM book_mark_tags   
LEFT JOIN book_marks  
ON book_marks.id = book_mark_tags.book_mark_id  
LEFT JOIN tags  
on tags.id = book_mark_tags.tag_id  
where (book_marks.deleted_at is null  
	and book_mark_tags.deleted_at is null  
    and tags.deleted_at is null  
      )  

# 選択したすべてのタグが紐付けられているブックマークのid,title,urlを表示
* book_mark_tagsとbook_marksを結合
* tag_idをorで検索
* 指定したタグがすべて当てはまるのなら同じidを持つブックマークが指定したタグと数ある
->2つ指定して,すべて当てはまる記事があった場合,同じidを持つブックマークが2こ現れる

tag_id = 1とtag_id = 2でor検索
大体こんな
|id|title|url|tag_id|
|--|--|--|
|3|title1|url1|1|
|3|title1|url1|2|
|4|title2|url2|1|
|5|title3|url3|1|

指定したタグがすべてあてハマる(tag_id = 1,tag_id =2 の両方を持つ)のはid=3のブックマークである

* group byでidごとにまとめて
* HAVINGでidごとのデータの個数を数える
* 数えた個数が選択したタグと同じならばそのデータは選択したすべてのタグが紐付けられているブックマークである

タグを2つ検索して2回出てきたやつを探す






sql で書くと

SELECT book_marks.id,book_marks.title,book_marks.url  
FROM book_mark_tags   
LEFT JOIN book_marks  
ON book_marks.id = book_mark_tags.book_mark_id  
LEFT JOIN tags  
on tags.id = book_mark_tags.tag_id  
where (book_marks.deleted_at is null  
	and book_mark_tags.deleted_at is null  
    and tags.deleted_at is null  
      )  
and (book_mark_tags.tag_id = 4  
    OR book_mark_tags.tag_id = 5)  
GROUP BY book_mark_id  
HAVING COUNT(*) = 2  
