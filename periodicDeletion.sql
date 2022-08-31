-- articles
SELECT id,title,deleted_at FROM `articles` WHERE `deleted_at` IS NOT NULL;
-- article_tags
-- SELECT article_id , deleted_at FROM `article_tags` WHERE `deleted_at` is NOT null;
-- book_marks
-- SELECT id FROM `book_marks` WHERE `deleted_at` IS NOT NULL;
-- book_mark_tags
-- SELECT deleted_at FROM `book_mark_tags` WHERE `deleted_at` is NOT null;
-- tags
-- SELECT * FROM `tags` WHERE `deleted_at` is not null
