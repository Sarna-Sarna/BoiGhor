<?php
include '../includes/db_connect.php';
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

$id = (int)$_GET['id'];
mysqli_query($conn, "DELETE FROM books WHERE id=$id");
header("Location: dashboard.php");
exit;
