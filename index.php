<?php

error_reporting(E_ALL);
ini_set("display_errors", 1);

function someFunction($someString, $someInt) {
    echo $someString . $someInt;
}

someFunction("valami");
someFunction("valami 2", 6);

// require_once "core/core.php";
// require_once "templates/layout.php";
