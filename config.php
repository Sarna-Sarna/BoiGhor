<?php
$db_host = '127.0.0.1';
$db_name = 'online_book_shop';
$db_user = 'root';
$db_pass = '';

function db_connect() {
    global $db_host, $db_user, $db_pass, $db_name;
    $conn = new mysqli($db_host, $db_user, $db_pass, $db_name);
    if ($conn->connect_errno) {
        error_log("DB connect error: " . $conn->connect_error);
        die("Database connection failed.");
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}
?>
