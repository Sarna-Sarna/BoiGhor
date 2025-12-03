<?php
include '../includes/db_connect.php';
session_start();

// Redirect if not logged in
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}

// Handle update form submission
if(isset($_POST['update'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    mysqli_query($conn, "UPDATE users SET name='$name', email='$email' WHERE id=".$_SESSION['user_id']);
    $success = "Account updated successfully!";
}

// Fetch user details
$result = mysqli_query($conn, "SELECT * FROM users WHERE id=".$_SESSION['user_id']);
$user = mysqli_fetch_assoc($result);
?>

<?php include '../includes/header.php'; ?>

<h2>Manage My Account</h2>
<form method="post">
    <?php if(isset($success)) echo "<p class='success'>$success</p>"; ?>
    <input type="text" name="name" value="<?php echo $user['name']; ?>" required>
    <input type="email" name="email" value="<?php echo $user['email']; ?>" required>
    <button type="submit" name="update">Update Account</button>
</form>

<?php include '../includes/footer.php'; ?>
