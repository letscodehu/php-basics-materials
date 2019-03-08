<?php 

function getPhotosPaginated($connection, $size, $offset) {
    if ($statement = mysqli_prepare($connection, 'SELECT * FROM photos LIMIT ? OFFSET ?')) {
        mysqli_stmt_bind_param($statement, "ii", $size, $offset);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    } else {
        logMessage('ERROR','Query error: '. mysqli_error($connection));
        errorPage();
    }
}

function logMessage($level, $message) {
    $file = fopen('application.log', "a");
    fwrite($file, "[$level] $message". PHP_EOL);
    fclose($file);
}

function errorPage() {
    include "templates/error.php";
}

function getTotal($connection) {
    if ($result = mysqli_query($connection, "SELECT count(*) as count FROM photos")) {
        $row = mysqli_fetch_assoc($result);
        return $row['count'];
    } else {
        logMessage('ERROR','Query error: '. mysqli_error($connection));
        errorPage();
    }
}

function paginate($total, $currentPage, $size) {
    $page = 0;
    $markup = "";
    if ($currentPage > 1) {
        $previousPage = $currentPage - 1;
        $markup .= 
        "<li class=\"page-item\">
            <a class=\"page-link\" href=\"?size=$size&page=$previousPage\">Previous</a>
        </li>";
    }
    for ($i = 0; $i < $total; $i += $size) {
        $page++;
        $activeClass = $currentPage == $page ? 'active' : '';
        $markup .= 
        "<li class=\"page-item $activeClass\">
            <a class=\"page-link\" href=\"?size=$size&page=$page\">$page</a>
        </li>"; 
    }
    if ($currentPage < $page) {
        $nextPage = $currentPage + 1;
        $markup .= 
        "<li class=\"page-item\">
            <a class=\"page-link\" href=\"?size=$size&page=$nextPage\">Next</a>
        </li>";
    }
    return $markup;
}

$routes = [
];

function route($action, $callable, $method = "GET") {
    global $routes;
    $pattern = "%^$action$%";
    $routes[strtoupper($method)][$pattern] = $callable;
}

function dispatch($action, $notFound) {
    global $routes;
    $method = $_SERVER["REQUEST_METHOD"]; // POST GET PATCH DELETE
    if (array_key_exists($method, $routes)) {
        foreach ($routes[$method] as $pattern => $callable) {
            if (preg_match($pattern, $action, $matches)) {
                return $callable($matches);
            }
        }
    }
    return $notFound();
}

function getImageById($connection, $id) {
    if ($statement = mysqli_prepare($connection, 'SELECT * FROM photos WHERE id = ?')) {
        mysqli_stmt_bind_param($statement, "i", $id);
        mysqli_stmt_execute($statement);
        $result = mysqli_stmt_get_result($statement);
        return mysqli_fetch_assoc($result);
    } else {
        logMessage('ERROR','Query error: '. mysqli_error($connection));
        errorPage();
    }
}

function singleImageController($params) {
    $connection = getConnection();
    $picture = getImageById($connection, $params["id"]);
    return [
        "single",
        [
            "title" => $picture["title"],
            "picture" => $picture
        ]
        ];
}

function getConnection() {
    global $config;
    $connection = mysqli_connect($config['db_host'], $config['db_user'], $config['db_pass'], $config['db_name']);
    if (!$connection) {
        logMessage('ERROR',"Connection error: ". mysqli_connect_error());
        errorPage();
    } 
    return $connection;
}

function homeController() {
    $size = $_GET["size"] ?? 15;
    $page = $_GET["page"] ?? 1;    
    $connection = getConnection();
    $total = getTotal($connection);
    $offset = ($page - 1) * $size;
    $content = getPhotosPaginated($connection, $size, $offset);
    $possiblePageSizes = [10, 25, 30, 40, 50];
  
    return [
        "home",
        [
            "title" => "Home",
            "content" => $content,
            "total" => $total,
            "size" => $size,
            "page" => $page,
            "offset" => $offset,
            "possiblePageSizes" => $possiblePageSizes
        ]
    ];
}

function updateImage($connection, $id, $title) {
    if ($statement = mysqli_prepare($connection, 'UPDATE photos SET title = ? WHERE id = ?')) {
        mysqli_stmt_bind_param($statement, "si", $title, $id);
        mysqli_stmt_execute($statement);
    } else {
        logMessage('ERROR','Query error: '. mysqli_error($connection));
        errorPage();
    }
}

function singleImageEditController($params) {
    $title = $_POST["title"];
    $id = $params["id"];
    $connection = getConnection();
    updateImage($connection, $id, $title);
    return [
        "redirect:/image/$id",
        [
        ]
        ];
}

function esc($string) {
    echo htmlspecialchars($string);
}

function deleteImage($connection, $id) {
    if ($statement = mysqli_prepare($connection, 'DELETE FROM photos WHERE id = ?')) {
        mysqli_stmt_bind_param($statement, "i", $id);
        mysqli_stmt_execute($statement);
    } else {
        logMessage('ERROR','Query error: '. mysqli_error($connection));
        errorPage();
    }
}

function singleImageDeleteController($params) {
    $connection = getConnection();
    deleteImage($connection, $params["id"]);
    return [
        "redirect:/",
        [
        ]
        ];
}

function loginFormController() {
    $containsError = array_key_exists("containsError", $_COOKIE);
    setcookie("containsError", "", time() - 1);
    return [
        "login", [
            "title" => "Login",
            "containsError" => $containsError
        ]
    ];    
}

function loginSubmitController() {
    $password = trim($_POST["password"]);
    $email = trim($_POST["email"]);
    if ($password == "password" && $email == "training@gmail.com") {
        setcookie("user", $email, time() + 3600);
        $view = "redirect:/";
    } else {
        setcookie("containsError", 1, time() + 1);
        $view = "redirect:/login";
    }
    return [
        $view, []
    ];    
}


function logoutSubmitController() {
    setcookie("user", "", time() -1);
    return [
        "redirect:/", [
        ]
    ];
}


function notFoundController() {
    return [
        "404", [
            "title" => "The page you are looking for is not found."
        ]
    ];
}
