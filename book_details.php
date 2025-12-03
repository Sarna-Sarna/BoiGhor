<?php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

include 'includes/db_connect.php';
include 'includes/header.php';

if (!isset($_GET['id'])) {
    echo "<p>Book not found!</p>";
    include 'includes/footer.php';
    exit;
}

$book_id = (int) $_GET['id'];

$stmt = $conn->prepare("SELECT id, title, description, author, publisher, publication_year, price, image FROM books WHERE id = ?");
$stmt->bind_param("i", $book_id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "<p>Book not found!</p>";
    include 'includes/footer.php';
    exit;
}

$book = $res->fetch_assoc();
$stmt->close();
?>

<div class="container my-4">
    <div class="row book-details">
        <div class="col-md-4 book-image">
            <?php
                $img = htmlspecialchars($book['image'] ?? '', ENT_QUOTES);
                if ($img !== ''):
            ?>
                <img src="assets/images/<?php echo $img; ?>" alt="<?php echo htmlspecialchars($book['title'], ENT_QUOTES); ?>" class="img-fluid rounded shadow-sm">
            <?php else: ?>
                <div class="border rounded p-4 text-center text-muted">No image available</div>
            <?php endif; ?>
        </div>

        <div class="col-md-8 book-info">
            <h2><?php echo htmlspecialchars($book['title'], ENT_QUOTES); ?></h2>
            <p><strong>Author:</strong> <?php echo htmlspecialchars($book['author'], ENT_QUOTES); ?></p>
            <p><strong>Publisher:</strong> <?php echo htmlspecialchars($book['publisher'], ENT_QUOTES); ?></p>
            <p><strong>Publication Year:</strong> <?php echo htmlspecialchars($book['publication_year'], ENT_QUOTES); ?></p>
            <p><strong>Description:</strong><br><?php echo nl2br(htmlspecialchars($book['description'], ENT_QUOTES)); ?></p>
            <p><strong>Price:</strong> BDT <?php echo number_format((float)$book['price'], 2); ?></p>

            <div class="mt-3">
            <?php if (!empty($_SESSION['user_id'])): ?>
                <a href="user/cart.php?add=<?php echo intval($book['id']); ?>" class="btn btn-outline-primary me-2">Add to Cart</a>
                <a href="user/checkout.php?buy=<?php echo intval($book['id']); ?>" class="btn btn-primary">Buy Now</a>
            <?php else: ?>
                <a href="auth/login.php" class="btn btn-outline-primary me-2">Add to Cart</a>
                <a href="auth/login.php" class="btn btn-primary">Buy Now</a>
            <?php endif; ?>
            </div>
        </div>
    </div>


    <?php if (!empty($_SESSION['comment_error'])): ?>
        <div class="alert alert-danger mt-3"><?php echo htmlspecialchars($_SESSION['comment_error'], ENT_QUOTES); ?></div>
        <?php unset($_SESSION['comment_error']); ?>
    <?php endif; ?>

    <hr class="my-4">

  
    <?php

    $book_id = intval($book['id']);
    include 'comment_form.php';
    ?>

  
    <div class="mt-4">
        <h4 id="comments">Comments</h4>
        <?php include 'comments_display.php'; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
