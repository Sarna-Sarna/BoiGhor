<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

if (!isset($book_id)) {
    echo "<p>Error: book id missing.</p>";
    return;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['csrf_token'];
?>
<div class="card mt-4">
  <div class="card-body">
    <h5 class="card-title">Leave a Comment</h5>
    <form method="post" action="submit_comment.php" id="commentForm">
      <input type="hidden" name="book_id" value="<?php echo htmlspecialchars($book_id, ENT_QUOTES); ?>">
      <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">

      <div class="mb-2">
        <label for="username" class="form-label">Name</label>
        <input id="username" name="username" class="form-control" maxlength="100" required>
      </div>

      <div class="mb-2">
        <label for="email" class="form-label">Email (optional)</label>
        <input id="email" name="email" type="email" class="form-control">
      </div>

      <div class="mb-2">
        <label for="comment" class="form-label">Comment</label>
        <textarea id="comment" name="comment" class="form-control" rows="4" maxlength="2000" required></textarea>
      </div>

      <button type="submit" class="btn btn-primary">Post Comment</button>
    </form>
  </div>
</div>
