<?php
include '../includes/db_connect.php';
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

if(isset($_POST['add'])){
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $author = mysqli_real_escape_string($conn, $_POST['author']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = (float)$_POST['price'];

    // Handle image upload
    $image = $_FILES['image']['name'];
    $target = "../assets/images/".basename($image);
    move_uploaded_file($_FILES['image']['tmp_name'], $target);

    mysqli_query($conn, "INSERT INTO books (title, author, description, price, image) VALUES ('$title','$author','$description',$price,'$image')");
    header("Location: dashboard.php");
    exit;
}
?>

<?php include '../includes/header.php'; ?>
<h2>Add New Book</h2>
<form method="post" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="Book Title" required>
    <input type="text" name="author" placeholder="Author Name" required>
    <textarea name="description" placeholder="Book Description" required></textarea>
    <input type="number" step="0.01" name="price" placeholder="Price" required>
    <input type="file" name="image" required>
    <button type="submit" name="add">Add Book</button>
</form>
<?php include '../includes/footer.php'; ?>
