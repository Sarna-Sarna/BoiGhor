<?php include 'includes/header.php'; ?>
<?php include 'includes/db_connect.php'; ?>

<h2>Authors</h2>
<div class="authors-container">
<?php
$result = mysqli_query($conn, "SELECT * FROM authors");
while($author = mysqli_fetch_assoc($result)){
?>
    <div class="author-card">
        <img src="assets/images/<?php echo $author['image']; ?>" alt="<?php echo $author['name']; ?>">
        <h3><?php echo $author['name']; ?></h3>
    </div>
<?php } ?>
</div>

<?php include 'includes/footer.php'; ?>
