<?php
include 'includes/db_connect.php';
include 'includes/header.php';
?>

<main>
    <section class="hero">
        <h1>Welcome to BoiGhor</h1>
        <div class="showcase-container">
            <div class="showcase-item">
                <img src="assets/images/BookBazaar.png" alt="BookBazaar">
            </div>
        </div>
        <p>Discover your next favorite book!</p>
    <section class="featured-books">
        <h2>Latest Arrivals</h2>
        <div class="books-container">
            <?php
            $result = mysqli_query($conn, "SELECT * FROM books ORDER BY id DESC LIMIT 8");
            while($book = mysqli_fetch_assoc($result)):
            ?>
            <a class="book-link" href="book_details.php?id=<?php echo $book['id']; ?>">
                <div class="book-card">
                    <img src="assets/images/<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>">
                    <h3><?php echo $book['title']; ?></h3>
                    <p><?php echo $book['author']; ?></p>
                </div>
            </a>
            <?php endwhile; ?>
        </div>
    </section>
    </section>
    <section class="showcase">
        <h2>Why Choose Us?</h2>
        <div class="showcase-container">
            <div class="showcase-item">
                <img src="assets/images/shopp.webp" alt="Shop">
                <p>Read anywhere, anytime</p>
            </div>
            <div class="showcase-item">
                <img src="assets/images/book.png" alt="Book">
                <p>Huge collection of books</p>
            </div>
        </div>
    </section>
</main>

<?php include 'includes/footer.php'; ?>
