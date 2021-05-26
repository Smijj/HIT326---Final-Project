-- Update test user

UPDATE users SET fname = "Users - modified", lname = "Testing - Modified", email = "users.testing(modified)@gmail.com", perm = 2 WHERE user_id = 1;