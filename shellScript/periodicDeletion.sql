-- 一意のidを参照している場合
-- 参照先を消してから参照元を消さないとエラーがでる
-- だからこの順番

-- article_tags
DELETE FROM `article_tags` WHERE article_id in (SELECT id FROM `articles` WHERE `deleted_at` is NOT null);
-- articles
DELETE FROM `articles` WHERE deleted_at is not null;
-- book_mark_tags
DELETE FROM `book_mark_tags` WHERE book_mark_id in (SELECT id FROM `book_marks` WHERE `deleted_at` is NOT null);
-- book_marks
DELETE FROM `book_marks` WHERE deleted_at is not null;
-- tags
DELETE FROM `tags` WHERE deleted_at is not null;
