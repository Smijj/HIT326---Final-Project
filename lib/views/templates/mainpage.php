<div class="topfive">
    <?php
    if (!empty($flash)) { echo "<div class='flash'>{$flash}</div>"; }

    if (!empty($articles)) {
        foreach ($articles as $article) {
            echo "<article><img src='".$article->img."' alt='".$article->alt."'><p>".$article->title."</p><p>".$article->smallContent."</p></article>";
        }
    }
    ?>
</div>
