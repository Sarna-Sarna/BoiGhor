<?php include 'includes/header.php'; ?>
<?php include 'includes/db_connect.php'; ?>

<h2>Available Books</h2>
<div class="books-container">
<?php
$result = mysqli_query($conn, "SELECT * FROM books");
while($book = mysqli_fetch_assoc($result)){
?>
    <div class="book-card" onclick="window.location='book_details.php?id=<?php echo $book['id']; ?>'">
        <img src="assets/images/<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>">
        <h3><?php echo $book['title']; ?></h3>
        <p>Author: <?php echo $book['author']; ?></p>
        <p>Price: BDT <?php echo $book['price']; ?></p>
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="user/cart.php?add=<?php echo $book['id']; ?>" class="btn">Add to Cart</a>
            <a href="user/checkout.php?buy=<?php echo $book['id']; ?>" class="btn">Buy Now</a>
        <?php else: ?>
            <a href="auth/login.php" class="btn">Add to Cart</a>
            <a href="auth/login.php" class="btn">Buy Now</a>
        <?php endif; ?>
    </div>
<?php } ?>
</div>

<?php include 'includes/footer.php'; ?>
