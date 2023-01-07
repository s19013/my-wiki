-- 論理削除してから3ヶ月たったデータを消す

-- articles
DELETE FROM `articles` WHERE `deleted_at` > (now() - INTERVAL 3 month);
-- book_marks
DELETE FROM `book_marks` WHERE `deleted_at` > (now() - INTERVAL 3 month);
-- tags
DELETE FROM `tags` WHERE `deleted_at` > (now() - INTERVAL 3 month);;
