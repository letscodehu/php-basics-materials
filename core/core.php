<?php

$size = $_GET["size"] ?? 15;
$page = $_GET["page"] ?? 1;

$possiblePageSizes = [10, 25, 30, 40, 50];

$pictures = array_fill(0, 100,  [
    "title" => "másik kép",
    "thumbnail" => "https://picsum.photos/200/200"
]);
$content = array_slice($pictures, ($page - 1)*$size, $size);
