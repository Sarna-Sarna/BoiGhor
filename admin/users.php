<?php
include '../includes/db_connect.php';
session_start();

// Only admin can access
if(!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin'){
    header("Location: ../auth/login.php");
    exit;
}

// Fetch all users
$result = mysqli_query($conn, "SELECT name, email, role, created_at FROM users ORDER BY created_at DESC");

include '../includes/header.php';
?>

<h2>Users Activity</h2>

<table>
    <tr>
        <th>User Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Registered On</th>
    </tr>
<?php
while($row = mysqli_fetch_assoc($result)){
?>
    <tr>
        <td><?php echo htmlspecialchars($row['name']); ?></td>
        <td><?php echo htmlspecialchars($row['email']); ?></td>
        <td><?php echo htmlspecialchars($row['role']); ?></td>
        <td><?php echo $row['created_at']; ?></td>
    </tr>
<?php } ?>
</table>

<?php include '../includes/footer.php'; ?>
