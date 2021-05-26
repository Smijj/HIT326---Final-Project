-- Create test article, requires a user so creating that first


-- test user
INSERT INTO `users` (`user_id`, `fname`, `lname`, `email`, `pwd`, `perm`) VALUES
(1, 'Users', 'Testing', 'users.testing@gmail.com', '$2y$10$aqkFgXCXI2xmChNoieJ34OHihOsoYGy16fw4IusCp.kR4vQmQYZvu', 3);


-- test article
INSERT INTO articles (author_id, title, keywords, content, public) VALUES ("1", "Testing Article", "Testing, Article", "This is a test article for database testing", 0)