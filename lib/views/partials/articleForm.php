<label for="author">Author ID: <?php echo "{$author_id} \n"; ?></label><br>
<label for="title">Title:</label>
<input type="title" name="title" id="title" value="<?php if (isset($title)) {echo $title;} ?>" autocomplete="title"><br>
<label for="article_content">Content:</label>
<input type="article_content" name="article_content" id="article_content" value="<?php if (isset($article_content)) {echo $article_content;} ?>" autocomplete="article_content"><br>
