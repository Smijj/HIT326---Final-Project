<?php 
    if (!empty($flash)) { 
        echo "<div class='flash'>{$flash}</div>"; 
    } 
?>

<p>This is the article list page</p>

<?php 

    foreach ($article_list as $article => $article_data) {
        // Show article in list if the article is public or the current logged in user has high enough permissions
        if ($article_data->public == true || $high_level_user == true) {
            echo "<article class='article_list'>
                <div  data-href=\"/article/{$article_data->article_id}\">
                <h2 class='article_list_header'>{$article_data->title}</h2>
                <p class='article_list_subheader'>Written by {$article_data->name}</p>
                <p class='article_list_subheader'>Last Edited on {$article_data->date_last_edit}</p>
                <p class='article_list_keywords'>Keywords: {$article_data->keywords}</p>
                </div>";
            if ($high_level_user == true) {
                echo "<div class='article_list_buttons'>
                        <div data-href='/editarticle/{$article_data->article_id}'>Edit</div>
                        <div data-href='#'>Delete</div>
                    </div>
                </article>";
            } else {
                echo "</article>";
            }
        }
    }
?>