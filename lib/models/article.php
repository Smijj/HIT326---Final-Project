<?php

class articleData {
    public int $article_id;
    public int $author_id;
    public string $title;
    public string $article_content;
    public string $keywords;
    public string $date_last_edit;
    public bool $public;
    public string $name;
    
    /**
     * __construct
     *
     * @param  int $article_id;
     * @param  int $author_id;
     * @param  string $title
     * @param  string $content
     * @param  string $keywords
     * @param  string $date_last_edit
     * @param  bool $public
     * @param  string $name
     * @return void
     */
    public function __construct($article_id = null, $author_id = null, $title = null, $article_content = null, $keywords = null, $date_last_edit = null, $public = null, $name = null) {
        $this->article_id = $article_id;
        $this->author_id = $author_id;
        $this->title = $title;
        $this->article_content = $article_content;
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
     * @param  bool $to_html when true, converts line endings to html "<br/>" tags.
     * @return articleData Returns boolean **False** on fail/not found.
     */
    public function get_article($id, $to_html = false) {
        if (!empty($id)) {
            $sql = "SELECT article_id, author_id, title, keywords, content, update_date, public, users.fname, users.lname FROM articles, users WHERE articles.author_id = users.user_id AND article_id=?";
            $stmt = $this->prepare($sql);
            if($stmt->execute(array($id))) {
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                if (!empty($result)) {
                    $article_content = $result['content'];
                    if ($to_html == true) {
                        $article_content = nl2br($article_content);     // Replaces new line codes with html ones.
                    }
                    return new articleData($result['article_id'], $result['author_id'], $result['title'], $article_content, $result['keywords'], $result['update_date'], ($result['public'] == 1)? true:false, $result['fname']." ".$result['lname']);
                } else {
                    return false;
                }
            }
        }
        return false;
    }



    /**
     *
     * @return output Returns boolean **False** on fail/not found.
     */
    
    /**
     * Return **articleData[]** class with data of all articles in the article table or empty if nothing found.
     * 
     * Can return DBException on DB error.
     *
     * @param  int $list_length Amount of articles to get, 0 for all.
     * @param  bool $only_public When true, returns only public articles.
     * @return articleData[] Returns boolean **False** on fail/not found.
     */
    public function article_list($list_length = 0, $only_public = false) {
        $output[] = null;
        $sql = "SELECT article_id, author_id, title, keywords, content, update_date, public, users.fname, users.lname FROM articles, users WHERE articles.author_id = users.user_id ORDER BY creation_date DESC";
        $stmt = $this->prepare($sql);
        if($stmt->execute()) {
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($result)) {
                $article_count = 0;
                foreach ($result as $key => $value) {
                    if ($list_length != 0 && $article_count > $list_length) {
                        break;                                                  // Break foreach loop if requested article amount is reached.
                    }
                    if (($only_public == true && $value['public'] == true) || $only_public != true) { // Only add article if only_visible is true and it is visible, or only_visible is false. 
                        if ($list_length != 0) { $article_count++; }            // If limit on article amount increment counter.
                        $output[$key] = new articleData($value['article_id'], $value['author_id'], $value['title'], $value['content'], $value['keywords'], $value['update_date'], ($value['public'] == 1)? true:false, $value['fname']." ".$value['lname']);;
                    }
                }
                return $output;
            } else {
                return false;
            }
        }
        return false;
    }

    public function update_article($id, $title, $keywords, $article_content, $public) {
        if (empty($title) || empty($keywords) || empty($article_content)) {
            throw new Exception('Empty field');
        }
    
    
        // Set-up and execute a prepared sql statement to insert the new article into the database.
        $sql = "";
        $stmt = $this->prepare($sql);
        if ($stmt->execute(array($id, $title, $keywords, $article_content, $public))) {
            return true;
        } else {
            throw new Exception('Internal error when updating article. Please try again later.');
        }
    }
}