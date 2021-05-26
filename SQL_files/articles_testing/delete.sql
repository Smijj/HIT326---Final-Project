-- Delete test article, followed by test user used to create this article

-- delete test article
DELETE FROM articles WHERE title = "Testing Article" OR title = "Testing Article - Modified";


-- delete test user
DELETE FROM users WHERE user_id = 1;