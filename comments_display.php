<?php

if (!isset($book_id)) {
    echo "<p>Comments unavailable (book id missing).</p>";
    return;
}
require_once __DIR__ . '/includes/config.php';



$has_status = false;
$check = $conn->query("SHOW COLUMNS FROM comments LIKE 'status'");
if ($check && $check->num_rows > 0) {
    $has_status = true;
}

if ($has_status) {
    $sql = "SELECT username, comment, created_at FROM comments WHERE book_id = ? AND status = 'approved' ORDER BY created_at DESC";
} else {
    $sql = "SELECT username, comment, created_at FROM comments WHERE book_id = ? ORDER BY created_at DESC";
}

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo "<p>Unable to load comments.</p>";
    error_log("Prepare failed in comments_display.php: " . $conn->error);
    return;
}
$stmt->bind_param("i", $book_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "<p>No comments yet. Be the first to comment!</p>";
} else {
    while ($row = $res->fetch_assoc()) {
        $u = htmlspecialchars($row['username'] ?: 'Guest', ENT_QUOTES);
        $d = htmlspecialchars($row['created_at'], ENT_QUOTES);
        $text = nl2br(htmlspecialchars($row['comment'], ENT_QUOTES));
        echo "<div class='card mb-2'><div class='card-body'>";
        echo "<strong>{$u}</strong> <small class='text-muted ms-2'>{$d}</small>";
        echo "<div class='mt-2'>{$text}</div>";
        echo "</div></div>";
    }
}

$stmt->close();
$conn->close();
?>
