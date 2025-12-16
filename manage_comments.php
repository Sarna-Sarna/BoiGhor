<?php
// admin/manage_comments.php
session_start();

require_once __DIR__ . '/../includes/config.php';

// ------ 1. Access control ------
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// ------ 2. Handle actions (approve / reject / delete) ------
if (isset($_GET['action'], $_GET['id'])) {
    $action    = $_GET['action'];
    $commentId = intval($_GET['id']);

    if ($commentId > 0) {
        if ($action === 'approve') {
            $stmt = $conn->prepare("UPDATE comments SET status='approved' WHERE id=?");
            $stmt->bind_param("i", $commentId);
            $stmt->execute();
            $stmt->close();
            $_SESSION['admin_msg'] = "Comment #{$commentId} approved.";

        } elseif ($action === 'reject') {
            // 1) Mark comment as rejected
            $stmt = $conn->prepare("UPDATE comments SET status='rejected', detector_label='fake' WHERE id=?");
            $stmt->bind_param("i", $commentId);
            $stmt->execute();
            $stmt->close();

            // 2) Find user_id of this comment
            $stmt = $conn->prepare("SELECT user_id FROM comments WHERE id=?");
            $stmt->bind_param("i", $commentId);
            $stmt->execute();
            $stmt->bind_result($commentUserId);
            $stmt->fetch();
            $stmt->close();

            // 3) If it belongs to a registered user, increase fake_count
            if (!empty($commentUserId)) {
                $commentUserId = (int)$commentUserId;

                // Increase fake_count
                $conn->query("UPDATE users SET fake_count = fake_count + 1 WHERE id = {$commentUserId}");

                // Auto-block if fake_count >= 5
                $res = $conn->query("SELECT fake_count FROM users WHERE id = {$commentUserId} LIMIT 1");
                if ($res && $row = $res->fetch_assoc()) {
                    if ((int)$row['fake_count'] >= 5) {
                        $conn->query("UPDATE users SET is_blocked = 1 WHERE id = {$commentUserId}");
                        $_SESSION['admin_msg'] = "Comment #{$commentId} rejected and user #{$commentUserId} has been blocked (5+ fake reviews).";
                    } else {
                        $_SESSION['admin_msg'] = "Comment #{$commentId} rejected and fake count increased for user #{$commentUserId}.";
                    }
                }
            } else {
                $_SESSION['admin_msg'] = "Comment #{$commentId} rejected.";
            }

        } elseif ($action === 'delete') {
            $stmt = $conn->prepare("DELETE FROM comments WHERE id=?");
            $stmt->bind_param("i", $commentId);
            $stmt->execute();
            $stmt->close();
            $_SESSION['admin_msg'] = "Comment #{$commentId} deleted.";
        }
    }

    header('Location: manage_comments.php');
    exit;
}

// ------ 3. Filter (optional) ------
$filter = $_GET['filter'] ?? 'all';
$where  = '';

if ($filter === 'approved') {
    $where = "WHERE c.status = 'approved'";
} elseif ($filter === 'flagged') {
    $where = "WHERE c.status = 'flagged'";
} elseif ($filter === 'rejected') {
    $where = "WHERE c.status = 'rejected'";
} elseif ($filter === 'pending') {
    $where = "WHERE c.status = 'pending'";
}

// ------ 4. Fetch comments with book + user info ------
$sql = "
    SELECT 
        c.*,
        b.title AS book_title,
        u.name  AS user_name
    FROM comments c
    LEFT JOIN books b ON c.book_id = b.id
    LEFT JOIN users u ON c.user_id = u.id
    $where
    ORDER BY c.created_at DESC
";

$result = $conn->query($sql);

?>

<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="admin-container" style="max-width: 1100px; margin:40px auto;">
    <h2>Manage Comments</h2>

    <?php if (!empty($_SESSION['admin_msg'])): ?>
        <div style="padding:10px; margin-bottom:15px; background:#e1f5fe; border-radius:4px;">
            <?php 
                echo htmlspecialchars($_SESSION['admin_msg']); 
                unset($_SESSION['admin_msg']);
            ?>
        </div>
    <?php endif; ?>

    <!-- Filter links -->
    <div style="margin-bottom:15px;">
        <a href="manage_comments.php?filter=all">All</a> |
        <a href="manage_comments.php?filter=approved">Approved</a> |
        <a href="manage_comments.php?filter=flagged">Flagged</a> |
        <a href="manage_comments.php?filter=rejected">Rejected</a> |
        <a href="manage_comments.php?filter=pending">Pending</a>
    </div>

    <table style="width:100%; border-collapse:collapse; background:#fff;">
        <thead>
            <tr style="background:#7da5e3; color:#fff;">
                <th style="padding:8px;">#</th>
                <th style="padding:8px;">Book</th>
                <th style="padding:8px;">User</th>
                <th style="padding:8px;">Email</th>
                <th style="padding:8px;">Comment</th>
                <th style="padding:8px;">Status</th>
                <th style="padding:8px;">Detector</th>
                <th style="padding:8px;">Posted</th>
                <th style="padding:8px;">Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                    $status = $row['status'];
                    // Simple color for status
                    $statusColor = '#ccc';
                    if ($status === 'approved') $statusColor = '#a5d6a7';
                    elseif ($status === 'flagged') $statusColor = '#ffe082';
                    elseif ($status === 'rejected') $statusColor = '#ef9a9a';
                ?>
                <tr style="border-bottom:1px solid #eee;">
                    <td style="padding:8px; text-align:center;"><?php echo (int)$row['id']; ?></td>
                    <td style="padding:8px;"><?php echo htmlspecialchars($row['book_title'] ?? 'Unknown'); ?></td>
                    <td style="padding:8px;"><?php echo htmlspecialchars($row['user_name'] ?? $row['username']); ?></td>
                    <td style="padding:8px;"><?php echo htmlspecialchars($row['email']); ?></td>
                    <td style="padding:8px;"><?php echo htmlspecialchars($row['comment']); ?></td>
                    <td style="padding:8px;">
                        <span style="padding:4px 8px; border-radius:12px; background:<?php echo $statusColor; ?>;">
                            <?php echo htmlspecialchars($status); ?>
                        </span>
                    </td>
                    <td style="padding:8px;">
                        Score: <?php echo number_format((float)$row['detector_score'], 2); ?><br>
                        Label: <?php echo htmlspecialchars($row['detector_label']); ?>
                    </td>
                    <td style="padding:8px;"><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td style="padding:8px; text-align:center;">
                        <a href="manage_comments.php?action=approve&id=<?php echo (int)$row['id']; ?>" 
                           style="display:inline-block; margin:2px; padding:4px 8px; border-radius:6px; background:#a5d6a7; text-decoration:none; font-size:12px;">
                           Approve
                        </a>
                        <a href="manage_comments.php?action=reject&id=<?php echo (int)$row['id']; ?>" 
                           style="display:inline-block; margin:2px; padding:4px 8px; border-radius:6px; background:#ffcc80; text-decoration:none; font-size:12px;">
                           Reject
                        </a>
                        <a href="manage_comments.php?action=delete&id=<?php echo (int)$row['id']; ?>" 
                           style="display:inline-block; margin:2px; padding:4px 8px; border-radius:6px; background:#ef9a9a; text-decoration:none; font-size:12px;"
                           onclick="return confirm('Delete this comment?');">
                           Delete
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="9" style="padding:12px; text-align:center;">No comments found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
