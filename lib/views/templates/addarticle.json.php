<?php
if (!isset($html) || (isset($html) && $html == "")) {
    $html = "Success<br><a href=\"/\"><button>Home</button></a><a href=\"/addarticle\"><button>Add Another</button></a>";
}
$data = [ "result" => $result, "html"=> $html ];