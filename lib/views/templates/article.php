<?php
if (!isset($articles_data->title)) {
    echo "An internal error has occurred and this article could not be displayed. Please try again later.";
}
?>
<h1><?php echo $articles_data->title; ?></h1>
<p class="author"><?php echo $articles_data->name; ?></p>
<p class="keywords"><?php echo $articles_data->keywords; ?></p>
<p class="last_edit_date"><?php echo $articles_data->date_last_edit; ?></p>
<p class="article_content"><?php echo $articles_data->content; ?></p>