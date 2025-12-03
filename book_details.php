<?php
include 'includes/db_connect.php';
include 'includes/header.php';

if (!isset($_GET['id'])) {
    echo "<p>Book not found!</p>";
    include 'includes/footer.php';
    exit;
}

$book_id = (int)$_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM books WHERE id=$book_id");

if (mysqli_num_rows($result) == 0) {
    echo "<p>Book not found!</p>";
    include 'includes/footer.php';
    exit;
}

$book = mysqli_fetch_assoc($result);
?>

<div class="book-details">
    <div class="book-image">
        <img src="assets/images/<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>">
    </div>
    <div class="book-info">
        <h2><?php echo $book['title']; ?></h2>
        <p><strong>Author:</strong> <?php echo $book['author']; ?></p>
        <p><strong>Publisher:</strong> <?php echo $book['publisher']; ?></p>
        <p><strong>Publishing Date:</strong> <?php echo $book['publishing_date']; ?></p>
        <p><strong>Description:</strong><br><?php echo nl2br($book['description']); ?></p>
        <p><strong>Price:</strong> BDT <?php echo $book['price']; ?></p>
        
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="user/cart.php?add=<?php echo $book['id']; ?>" class="btn">Add to Cart</a>
            <a href="user/checkout.php?buy=<?php echo $book['id']; ?>" class="btn">Buy Now</a>
        <?php else: ?>
            <a href="auth/login.php" class="btn">Add to Cart</a>
            <a href="auth/login.php" class="btn">Buy Now</a>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
