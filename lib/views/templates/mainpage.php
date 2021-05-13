<div class="topfive">
    <?php
    foreach ($articles as $article) {
        echo "<article><img src='".$article->img."' alt='".$article->alt."'><p>".$article->title."</p><p>".$article->smallContent."</p></article>";
    }
    ?>
</div>
