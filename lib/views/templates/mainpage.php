<?php
    if (!empty($flash)) { echo "<div class='flash'>{$flash}</div>"; }
?>

<h1 class='article_list_header'>Recent Articles</h1>

<?php
if (!empty($top5articles)) {
    foreach ($top5articles as $article => $article_data) {
        // Show article in list if the article is public.
        if ($article_data->public == true) {
            echo "<article class='article_list'>
                <section  class='article_list_info' data-href=\"/article/{$article_data->article_id}\">
                    <h2 class='article_list_header'>{$article_data->title}</h2>
                    <p class='article_list_subheader'>Written by {$article_data->name}</p>
                    <p class='article_list_subheader'>Last Edited on {$article_data->date_last_edit}</p>
                    <p class='article_list_keywords'>Keywords: {$article_data->keywords}</p>
                </section>
            </article>";
        }
    }
}
?>

