<?php
include '../includes/db_connect.php';
session_start();

// Only admin can access
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

include '../includes/header.php';
?>

<h2>Sales & Payment History</h2>

<table>
    <tr>
        <th>Order ID</th>
        <th>User</th>
        <th>Book</th>
        <th>Quantity</th>
        <th>Total Price</th>
        <th>Payment Method</th>
        <th>Status</th>
        <th>Order Date</th>
    </tr>
<?php
$result = mysqli_query($conn, "SELECT orders.id, users.name AS user_name, books.title AS book_name, orders.quantity, orders.total_price, orders.payment_method, orders.status, orders.created_at 
                               FROM orders 
                               JOIN users ON orders.user_id = users.id 
                               JOIN books ON orders.book_id = books.id 
                               ORDER BY orders.created_at DESC");
while($row = mysqli_fetch_assoc($result)){
?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['user_name']; ?></td>
        <td><?php echo $row['book_name']; ?></td>
        <td><?php echo $row['quantity']; ?></td>
        <td><?php echo $row['total_price']; ?></td>
        <td><?php echo $row['payment_method']; ?></td>
        <td><?php echo $row['status']; ?></td>
        <td><?php echo $row['created_at']; ?></td>
    </tr>
<?php } ?>
</table>

<?php include '../includes/footer.php'; ?>
