<?php 
    if (!empty($flash)) { 
        echo "<div class='flash'>{$flash}</div>"; 
    } 
?>

<p>This is the article list page</p>

<?php 

    foreach ($article_list as $article => $article_data) {
        echo "<div>{$article_data->title}</div>";
    }

?>