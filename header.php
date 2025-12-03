<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Determine base path based on folder
$current_folder = basename(dirname($_SERVER['PHP_SELF']));
if ($current_folder == 'auth' || $current_folder == 'user' || $current_folder == 'admin') {
    $base_path = '../';
} else {
    $base_path = '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BoiGhor</title>
    <link rel="stylesheet" href="<?php echo $base_path; ?>assets/css/style.css">
</head>
<body>
<header>

    <div class="logo"><a href="<?php echo $base_path; ?>home.php">BoiGhor</a></div>
    <nav>
        <ul>
            <?php if (isset($_SESSION['user_id'])): ?>
                
                <?php if (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                    <!-- Admin menu -->
                    <li><a href="<?php echo $base_path; ?>admin/dashboard.php">DASHBOARD</a></li>
                    <li><a href="<?php echo $base_path; ?>admin/manage_books.php">MANAGE BOOKS</a></li>
                    <li><a href="<?php echo $base_path; ?>admin/manage_comments.php">COMMENTS</a></li>
                    <li><a href="<?php echo $base_path; ?>admin/users.php">USERS</a></li>
                    <li><a href="<?php echo $base_path; ?>admin/sales.php">REPORTS</a></li>
                    <li><a href="<?php echo $base_path; ?>auth/logout.php">SIGN OUT</a></li>
                
                <?php else: ?>
                    <!-- Normal user menu -->
                    <li><a href="<?php echo $base_path; ?>user/dashboard.php">BOOKS</a></li>
                    <li><a href="<?php echo $base_path; ?>about.php">ABOUT</a></li>
                    <li><a href="<?php echo $base_path; ?>authors.php">AUTHORS</a></li>
                    <li><a href="<?php echo $base_path; ?>user/account.php">MANAGE ACCOUNT</a></li>
                    <li><a href="<?php echo $base_path; ?>user/cart.php">CART</a></li>
                    <li><a href="<?php echo $base_path; ?>user/orders.php">ORDERS</a></li>
                    <li><a href="<?php echo $base_path; ?>contact.php">HELP & SUPPORT</a></li>
                    <li><a href="<?php echo $base_path; ?>auth/logout.php">SIGN OUT</a></li>
                <?php endif; ?>

            <?php else: ?>
                <!-- Guest menu -->
                <li><a href="<?php echo $base_path; ?>books.php">BOOKS</a></li>
                <li><a href="<?php echo $base_path; ?>about.php">ABOUT</a></li>
                <li><a href="<?php echo $base_path; ?>authors.php">AUTHORS</a></li>
                <li><a href="<?php echo $base_path; ?>contact.php">HELP & SUPPORT</a></li>
                <li><a href="<?php echo $base_path; ?>auth/login.php">SIGN IN</a></li>
                <li><a href="<?php echo $base_path; ?>auth/signup.php">SIGN UP</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
<main>

