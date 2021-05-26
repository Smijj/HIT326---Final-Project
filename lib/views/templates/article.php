<?php
if (!isset($articles_data->title)) {
    echo "An internal error has occurred and this article could not be displayed. Please try again later.";
}
?>
<h1 class="article_list_header"><?php echo $articles_data->title; ?></h1>
<section class="article_display_content">
    <p class="article_display_author">Author: <?php echo $articles_data->name; ?></p>
    <p class="article_display_keywords">Keywords: <?php echo $articles_data->keywords; ?></p>
    <p class="article_display_last_edit_date">Last Edited: <?php echo $articles_data->date_last_edit; ?></p>
    <hr>
    <p class="article_display_article_content"><?php echo $articles_data->article_content; ?></p>
    <hr>
</section>
