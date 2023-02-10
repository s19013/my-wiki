-- 論理削除してから半年たったデータを消す

-- articles
DELETE FROM `articles` WHERE `deleted_at` < (now() - INTERVAL 6 month);
-- book_marks
DELETE FROM `book_marks` WHERE `deleted_at` < (now() - INTERVAL 6 month);
-- tags
DELETE FROM `tags` WHERE `deleted_at` < (now() - INTERVAL 6 month);;
