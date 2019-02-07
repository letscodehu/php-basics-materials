<?php

$uri = $_SERVER["REQUEST_URI"];
$cleaned = explode("?", $uri)[0];

route('/', function() {
    echo 'home';
});

route('/about', function() {
    echo 'about';
});

route('/post/(?<id>[\d]+)/(?<random>\D+)', function($params) {
    echo 'post with id:'. $params["id"]. " and random: " . $params["random"];
});

route('/post/(?<id>[\w]+)', function($params) {
    echo 'post with word id:'. $params["id"];
});


dispatch($cleaned, function() {
    echo '404';
});
die;
$possiblePageSizes = [10, 25, 30, 40, 50];

$connection = mysqli_connect($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);
if (!$connection) {
    logMessage('ERROR',"Connection error: ". mysqli_connect_error());
    errorPage();
} 


if($cleaned == "/") {
    $size = $_GET["size"] ?? 15;
    $page = $_GET["page"] ?? 1;    
    $total = getTotal($connection);
    $offset = ($page - 1) * $size;
    $view = "home";
    $content = getPhotosPaginated($connection, $size, $offset);
} else {
    $view = "404";
}
