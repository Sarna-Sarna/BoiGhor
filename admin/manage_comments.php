<?php

session_start();

if (empty($_SESSION['user_id']) || empty($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

if (file_exists(__DIR__ . '/../includes/db_connect.php')) {
    require_once __DIR__ . '/../includes/db_connect.php'; // should set $conn
} elseif (file_exists(__DIR__ . '/../includes/config.php')) {
    require_once __DIR__ . '/../includes/config.php'; // expect db_connect() function
    if (!isset($conn) && function_exists('db_connect')) $conn = db_connect();
} else {
  
    if (file_exists(__DIR__ . '/../../config.php')) {
        require_once __DIR__ . '/../../config.php';
        if (!isset($conn) && function_exists('db_connect')) $conn = db_connect();
    }
}

// If still no $conn, create simple fallback (edit credentials if needed)
if (!isset($conn) || !($conn instanceof mysqli)) {
    $conn = new mysqli('127.0.0.1', 'root', '', 'online_book_shop');
    if ($conn->connect_errno) {
        die('DB connection failed: ' . $conn->connect_error);
    }
    $conn->set_charset('utf8mb4');
}

// --- 2) CSRF token for POST actions
if (empty($_SESSION['admin_csrf'])) {
    $_SESSION['admin_csrf'] = bin2hex(random_bytes(16));
}
$csrf_token = $_SESSION['admin_csrf'];

// --- 3) Handle POST actions (approve/reject/delete)
$action_msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['id'])) {
    // CSRF check
    $posted_token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($csrf_token, $posted_token)) {
        $action_msg = 'Invalid CSRF token.';
    } else {
        $id = intval($_POST['id']);
        $action = $_POST['action'];

        if ($action === 'approve') {
            $stmt = $conn->prepare("UPDATE comments SET status = 'approved' WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
            $action_msg = "Comment #$id approved.";
        } elseif ($action === 'reject') {
            $stmt = $conn->prepare("UPDATE comments SET status = 'rejected' WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
            $action_msg = "Comment #$id rejected.";
        } elseif ($action === 'delete') {
            $stmt = $conn->prepare("DELETE FROM comments WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();
            $action_msg = "Comment #$id deleted.";
        }
    }

    // After POST-redirect-GET to avoid re-submission
    $_SESSION['action_msg'] = $action_msg;
    header("Location: manage_comments.php");
    exit;
}

// read possible action message from session
if (!empty($_SESSION['action_msg'])) {
    $action_msg = $_SESSION['action_msg'];
    unset($_SESSION['action_msg']);
}

// --- 4) Filter / paging (optional) - show pending & flagged first
$filter = $_GET['filter'] ?? 'pending_flagged'; // options: all | pending_flagged | approved | flagged | rejected
$valid_filters = ['all', 'pending_flagged', 'approved', 'flagged', 'rejected'];
if (!in_array($filter, $valid_filters)) $filter = 'pending_flagged';

// Build SQL with appropriate WHERE clause
$where = "";
$params = [];
if ($filter === 'pending_flagged') {
    $where = "WHERE comments.status IN ('pending','flagged')";
} elseif ($filter === 'approved') {
    $where = "WHERE comments.status = 'approved'";
} elseif ($filter === 'flagged') {
    $where = "WHERE comments.status = 'flagged'";
} elseif ($filter === 'rejected') {
    $where = "WHERE comments.status = 'rejected'";
} else {
    $where = ""; // all
}

$sql = "
    SELECT comments.id, comments.book_id, comments.user_id, comments.username, comments.email,
           comments.comment, comments.status, comments.detector_score, comments.detector_label,
           comments.created_at, books.title AS book_title, users.name AS user_name
    FROM comments
    LEFT JOIN books ON comments.book_id = books.id
    LEFT JOIN users ON comments.user_id = users.id
    $where
    ORDER BY comments.created_at DESC
    LIMIT 500
";

$res = $conn->query($sql);
if ($res === false) {
    die("Query failed: " . $conn->error);
}

// --- 6) Render page (use your header/footer includes)
if (file_exists(__DIR__ . '/../includes/header.php')) {
    include __DIR__ . '/../includes/header.php';
}
?>
<div class="container mt-4">
    <h2>Manage Comments</h2>

    <?php if ($action_msg): ?>
        <div class="alert alert-info"><?php echo htmlspecialchars($action_msg, ENT_QUOTES); ?></div>
    <?php endif; ?>

    <div class="mb-3">
        <a href="manage_comments.php?filter=pending_flagged" class="btn btn-outline-primary btn-sm">Pending & Flagged</a>
        <a href="manage_comments.php?filter=approved" class="btn btn-outline-success btn-sm">Approved</a>
        <a href="manage_comments.php?filter=flagged" class="btn btn-outline-warning btn-sm">Flagged</a>
        <a href="manage_comments.php?filter=rejected" class="btn btn-outline-danger btn-sm">Rejected</a>
        <a href="manage_comments.php?filter=all" class="btn btn-outline-secondary btn-sm">All</a>
    </div>

    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Book</th>
                    <th>User</th>
                    <th>Email</th>
                    <th>Comment</th>
                    <th>Status</th>
                    <th>Detector</th>
                    <th>Posted</th>
                    <th style="min-width:170px">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $res->fetch_assoc()): ?>
                    <?php
                        $cid = intval($row['id']);
                        $book_title = htmlspecialchars($row['book_title'] ?? ('Book #' . intval($row['book_id'])), ENT_QUOTES);
                        $user_display = $row['user_name'] ? htmlspecialchars($row['user_name'], ENT_QUOTES) : htmlspecialchars($row['username'] ?? 'Guest', ENT_QUOTES);
                        $email = htmlspecialchars($row['email'] ?? '', ENT_QUOTES);
                        $comment_text = nl2br(htmlspecialchars($row['comment'] ?? '', ENT_QUOTES));
                        $status = htmlspecialchars($row['status'] ?? 'pending', ENT_QUOTES);
                        $det_score = ($row['detector_score'] !== null) ? number_format(floatval($row['detector_score']), 2) : '';
                        $det_label = htmlspecialchars($row['detector_label'] ?? '', ENT_QUOTES);
                        $posted = htmlspecialchars($row['created_at'] ?? '', ENT_QUOTES);
                    ?>
                    <tr>
                        <td><?php echo $cid; ?></td>
                        <td><a href="../book_details.php?id=<?php echo intval($row['book_id']); ?>" target="_blank"><?php echo $book_title; ?></a></td>
                        <td><?php echo $user_display; ?></td>
                        <td><?php echo $email; ?></td>
                        <td style="max-width:400px; white-space:normal"><?php echo $comment_text; ?></td>
                        <td><?php
                            if ($status === 'approved') echo '<span class="badge bg-success">Approved</span>';
                            elseif ($status === 'pending') echo '<span class="badge bg-warning">Pending</span>';
                            elseif ($status === 'flagged') echo '<span class="badge bg-danger">Flagged</span>';
                            elseif ($status === 'rejected') echo '<span class="badge bg-secondary">Rejected</span>';
                        ?><td>
  <div class="admin-action-group">
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
      <input type="hidden" name="id" value="<?php echo $cid; ?>">
      <button name="action" value="approve" class="btn" style="background:#2ecc71;color:#fff;border:none;">
        Approve
      </button>
    </form>

    <form method="post">
      <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
      <input type="hidden" name="id" value="<?php echo $cid; ?>">
      <button name="action" value="reject" class="btn" style="background:#f0ad4e;color:#fff;border:none;">
        Reject
      </button>
    </form>

    <form method="post">
      <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
      <input type="hidden" name="id" value="<?php echo $cid; ?>">
      <button name="action" value="delete" class="btn" style="background:#d9534f;color:#fff;border:none;">
        Delete
      </button>
    </form>
  </div>
</td>



                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php

if (file_exists(__DIR__ . '/../includes/footer.php')) {
    include __DIR__ . '/../includes/footer.php';
}
$res->free();
$conn->close();
?>
