<?php
// admin/comments.php
session_start();
require_once '../config.php';

// basic admin guard (adjust to your auth system)
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die('Access denied. Admins only.');
}

$conn = db_connect();

// handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $action = $_POST['action'] ?? '';
    if ($id > 0 && in_array($action, ['approve','reject'])) {
        $status = ($action === 'approve') ? 'approved' : 'rejected';
        $u = $conn->prepare("UPDATE comments SET status = ? WHERE id = ?");
        $u->bind_param("si", $status, $id);
        $u->execute();
        $u->close();
    }
    header("Location: comments.php");
    exit;
}

// fetch pending comments
$stmt = $conn->prepare("SELECT id, book_id, username, email, comment, created_at FROM comments WHERE status = 'pending' ORDER BY created_at DESC");
$stmt->execute();
$res = $stmt->get_result();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin - Pending Comments</title>
  <link href="../assets/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
  <h3>Pending Comments</h3>
  <?php while ($row = $res->fetch_assoc()): ?>
    <div class="card mb-2">
      <div class="card-body">
        <div><strong><?php echo htmlspecialchars($row['username'], ENT_QUOTES); ?></strong>
          <small class="text-muted"> on book ID <?php echo intval($row['book_id']); ?> — <?php echo htmlspecialchars($row['created_at'], ENT_QUOTES); ?></small>
        </div>
        <div class="mt-2"><?php echo nl2br(htmlspecialchars($row['comment'], ENT_QUOTES)); ?></div>

        <form method="post" class="mt-3">
          <input type="hidden" name="id" value="<?php echo intval($row['id']); ?>">
          <button name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
          <button name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
        </form>
      </div>
    </div>
  <?php endwhile; ?>
</body>
</html>
<?php
$stmt->close();
$conn->close();
?>
