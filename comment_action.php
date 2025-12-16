<?php
session_start();
require '../includes/config.php'; // has $conn

if ($_SESSION['role'] !== 'admin') {
    header('Location: ../home.php');
    exit;
}

$id     = intval($_GET['id'] ?? 0);
$action = $_GET['action'] ?? '';

if ($id <= 0) {
    header('Location: manage_comments.php');
    exit;
}

// get comment + user
$stmt = $conn->prepare("SELECT user_id FROM comments WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$comment = $res->fetch_assoc();
$stmt->close();

if (!$comment) {
    header('Location: manage_comments.php');
    exit;
}

if ($action === 'approve') {
    $u = $conn->prepare("UPDATE comments SET status='approved' WHERE id=?");
    $u->bind_param("i", $id);
    $u->execute();
    $u->close();

} elseif ($action === 'reject') {
    $u = $conn->prepare("UPDATE comments SET status='rejected' WHERE id=?");
    $u->bind_param("i", $id);
    $u->execute();
    $u->close();

} elseif ($action === 'block_user' && $comment['user_id']) {
    $uid = (int)$comment['user_id'];
    $u = $conn->prepare("UPDATE users SET is_blocked=1 WHERE id=?");
    $u->bind_param("i", $uid);
    $u->execute();
    $u->close();
}

header('Location: manage_comments.php');
exit;
