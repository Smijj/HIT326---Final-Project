<?php

class Article extends Database {

	public function registerArticle($author_id, $title, $content) {
    
        if (empty($author_id) || empty($title) || empty($content)) {
            throw new Exception('Empty field');
        }
    
    
        // Set-up and execute a prepared sql statement to insert the new article into the database.
        $sql = "INSERT INTO articles (author_id, title, content) VALUES (?, ?, ?)";
        $stmt = $this->prepare($sql);
        if ($stmt->execute(array($author_id, $title, $content))) {
            return true;
        } else {
            throw new Exception('Internal error when adding article. Please try again later.');
        }

    }

}