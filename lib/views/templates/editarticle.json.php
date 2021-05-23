<?php
if (!isset($html) || (isset($html) && $html == "")) {
    $html = "Success<br><a href=\"/\"><button>Back</button></a>";
}
$data = [ "result" => $result, "html"=> $html ];