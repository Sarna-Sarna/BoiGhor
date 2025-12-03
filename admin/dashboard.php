<?php
include '../includes/db_connect.php';
session_start();

// Redirect if not admin
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

include '../includes/header.php';
?>

<h1>Admin Dashboard</h1>
</br>
<h3>Total Books: <?php echo mysqli_num_rows(mysqli_query($conn, "SELECT * FROM books")); ?></h3>
<h3>Total Users: <?php echo mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE role='user'")); ?></h3>
<h3>Total Sales: BDT <?php 
$res = mysqli_query($conn, "SELECT SUM(total_price) AS sales FROM orders");
$row = mysqli_fetch_assoc($res);
echo $row['sales'] ? $row['sales'] : 0;
?></h3>
<?php include '../includes/footer.php'; ?>
