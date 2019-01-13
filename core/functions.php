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
    die();
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