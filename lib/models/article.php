<?php

class articleData {
    public string $title;
    public string $content;
    public string $keywords;
    public datetime $date_last_edit;
    public bool $public;
    public string $name;
    
    /**
     * __construct
     *
     * @param  string $title
     * @param  string $content
     * @param  string $keywords
     * @param  datetime $date_last_edit
     * @param  bool $public
     * @param  string $name
     * @return void
     */
    public function __construct($title = null, $content = null, $keywords = null, $date_last_edit = null, $public = null, $name = null) {
        $this->title = $title;
        $this->content = $content;
        $this->keywords = $keywords;
        $this->date_last_edit = $date_last_edit;
        $this->public = $public;
        $this->name = $name;
    }
}

class Article extends Database {

	public function registerArticle($author_id, $title, $keywords, $article_content) {
    
        if (empty($author_id) || empty($title) || empty($keywords) || empty($article_content)) {
            throw new Exception('Empty field');
        }
    
    
        // Set-up and execute a prepared sql statement to insert the new article into the database.
        $sql = "INSERT INTO articles (author_id, title, keywords, content) VALUES (?, ?, ?, ?)";
        $stmt = $this->prepare($sql);
        if ($stmt->execute(array($author_id, $title, $keywords, $article_content))) {
            return true;
        } else {
            throw new Exception('Internal error when adding article. Please try again later.');
        }

    }
    
    /**
     * Return **articleData** class with data of article or empty if nothing found.
     * 
     * Can return DBException on DB error.
     *
     * @param  string $id
     * @return articleData
     */
    public function get_article($id): articleData {
        if (!empty($id)) {
            $sql = "SELECT title, keywords, content, update_date, public, users.fname, users.lname FROM articles, users WHERE articles.author_id = users.user_id AND article_id=?";

            $stmt = $this->prepare($sql);
            if($stmt->execute($id)) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!empty($result)) {
                    return new articleData($result['title'], $result['content'], $result['keywords'], $result['update_date'], ($result['public'] == 1)? true:false, $result['users.fname']." ".$result['users.lname']);
                }
            }
        }
        return new articleData();
    }
}