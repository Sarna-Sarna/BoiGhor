<?php
include '../includes/db_connect.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle add to cart from dashboard
if(isset($_GET['add'])){
    $book_id = (int)$_GET['add'];

    // Check if already in cart
    $check = mysqli_query($conn, "SELECT * FROM cart WHERE user_id=$user_id AND book_id=$book_id");
    if(mysqli_num_rows($check) == 0){
        mysqli_query($conn, "INSERT INTO cart (user_id, book_id, quantity) VALUES($user_id, $book_id, 1)");
    } else {
        // Increase quantity if already in cart
        mysqli_query($conn, "UPDATE cart SET quantity = quantity + 1 WHERE user_id=$user_id AND book_id=$book_id");
    }

    header("Location: cart.php"); // redirect to cart page after adding
    exit;
}

?>

<?php include '../includes/header.php'; ?>

<h1>Welcome, <?php echo $_SESSION['user_name']; ?>!</h1>
<h2>Available Books</h2>
<div class="books-container">
<?php
$result = mysqli_query($conn, "SELECT * FROM books");
while($book = mysqli_fetch_assoc($result)){
?>
    <div class="book-card" onclick="window.location='../book_details.php?id=<?php echo $book['id']; ?>'">
        <img src="../assets/images/<?php echo $book['image']; ?>" alt="<?php echo $book['title']; ?>">
        <h3><?php echo $book['title']; ?></h3>
        <p>Author: <?php echo $book['author']; ?></p>
        <p>Price: BDT <?php echo $book['price']; ?></p>
        <a href="?add=<?php echo $book['id']; ?>" class="btn">Add to Cart</a>
        <a href="checkout.php?buy=<?php echo $book['id']; ?>" class="btn">Buy Now</a>
    </div>
<?php } ?>
</div>

<?php include '../includes/footer.php'; ?>
