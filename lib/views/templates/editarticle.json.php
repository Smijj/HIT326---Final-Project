<?php
if (!isset($html) || (isset($html) && $html == "")) {
    $html = "Success<br><a href=\"/articlelist\"><button>Back</button></a>";
}
$data = [ "result" => $result, "html"=> $html ];