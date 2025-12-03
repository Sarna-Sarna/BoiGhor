<?php
include '../includes/db_connect.php';
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit;
}
?>

<?php include '../includes/header.php'; ?>
<h1>Manage Books</h1>
<a href="add_book.php" class="btn">Add New Book</a>
<table>
<tr>
    <th>Title</th>
    <th>Author</th>
    <th>Price</th>
    <th>Actions</th>
</tr>
<?php
$result = mysqli_query($conn, "SELECT * FROM books");
while($book = mysqli_fetch_assoc($result)){
?>
<tr>
    <td><?php echo $book['title']; ?></td>
    <td><?php echo $book['author']; ?></td>
    <td>BDT <?php echo $book['price']; ?></td>
    <td>
        <a href="edit_book.php?id=<?php echo $book['id']; ?>">Edit</a> |
        <a href="delete_book.php?id=<?php echo $book['id']; ?>">Delete</a>
    </td>
</tr>
<?php } ?>
</table>
</p>

<?php include '../includes/footer.php'; ?>