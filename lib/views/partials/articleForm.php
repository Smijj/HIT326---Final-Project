
<table class="form_table">
    <tr>
        <td><label for="title">Title:</label></td>
        <td><input type="title" name="title" id="title" value="<?php if (isset($title)) {echo $title;} ?>" autocomplete="title"></td>
    </tr>
    <tr>
        <td><label for="keywords">Keywords:</label></td>
        <td><input type="keywords" name="keywords" id="keywords" value="<?php if (isset($keywords)) {echo $keywords;} ?>" autocomplete="keywords"></td>
    </tr>
    <tr>
        <td colspan="2"><label for="article_content">Content:</label></td>
    </tr>
    <tr>
        <td colspan="2"><textarea type="article_content" name="article_content" id="article_content" autocomplete="article_content"><?php if (isset($article_content)) {echo $article_content;} ?></textarea></td>
    </tr>
    <tr>
        <td>
        <?php if (!empty($nav_perm) && $nav_perm >= 2) {
            echo "<label for=\"public\">Public:</label>
            <input type=\"checkbox\" name=\"public\" id=\"public\" value=\"public_true\" ".((!empty($public) && $public == true) ? "checked":"").">";
        }?>
        </td>
    </tr>
</table>


