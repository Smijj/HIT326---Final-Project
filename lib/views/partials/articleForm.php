<label for="author">Author: <?php echo "{$name} \n"; ?></label><br>
<label for="title">Title:</label>
<input type="title" name="title" id="title" value="<?php if (isset($title)) {echo $title;} ?>" autocomplete="title"><br>
<label for="keywords">Keywords:</label>
<input type="keywords" name="keywords" id="keywords" value="<?php if (isset($keywords)) {echo $keywords;} ?>" autocomplete="keywords"><br>
<label for="article_content">Content:</label><br/>
<textarea type="article_content" name="article_content" id="article_content" value="<?php if (isset($article_content)) {echo $article_content;} ?>" autocomplete="article_content" style="min-width:70%;"></textarea><br>
