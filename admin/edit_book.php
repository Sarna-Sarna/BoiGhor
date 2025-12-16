<?php
include '../includes/db_connect.php';
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

$id = (int)$_GET['id'];
$book = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM books WHERE id=$id"));

if(isset($_POST['update'])){
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = (float)$_POST['price'];

    // Image update
    if(!empty($_FILES['image']['name'])){
        $image = $_FILES['image']['name'];
        $target = "../assets/images/".basename($image);
        move_uploaded_file($_FILES['image']['tmp_name'], $target);
        mysqli_query($conn, "UPDATE books SET title='$title', author='$author', description='$description', price=$price, image='$image' WHERE id=$id");
    } else {
        mysqli_query($conn, "UPDATE books SET title='$title', author='$author', description='$description', price=$price WHERE id=$id");
    }
    header("Location: dashboard.php");
    exit;
}
?>

<?php include '../includes/header.php'; ?>
<h2>Edit Book</h2>
<form method="post" enctype="multipart/form-data">
    <input type="text" name="title" value="<?php echo $book['title']; ?>" required>
    <input type="text" name="author" value="<?php echo $book['author']; ?>" required>
    <textarea name="description" required><?php echo $book['description']; ?></textarea>
    <input type="number" step="0.01" name="price" value="<?php echo $book['price']; ?>" required>
    <input type="file" name="image">
    <button type="submit" name="update">Update Book</button>
</form>
<?php include '../includes/footer.php'; ?>
