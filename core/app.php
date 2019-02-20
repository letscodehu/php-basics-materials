<?php
ob_start();
$uri = $_SERVER["REQUEST_URI"];
$cleaned = explode("?", $uri)[0];
route('/', 'homeController');
route('/about', 'aboutController');
route('/image/(?<id>[\d]+)', 'singleImageController');
route('/image/(?<id>[\d]+)/edit', 'singleImageEditController', "POST");
route('/image/(?<id>[\d]+)/delete', 'singleImageDeleteController', "POST");
list($view, $data) = dispatch($cleaned, 'notFoundController');
if(preg_match("%^redirect\:%", $view)) {
    $redirectTarget = substr($view, 9);
    header("Location:".$redirectTarget);
    die;
}
extract($data);
ob_clean();
require_once "templates/layout.php";
