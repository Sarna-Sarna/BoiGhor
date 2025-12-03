<?php
include '../includes/db_connect.php';
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Update 
if(isset($_POST['update'])){
    foreach($_POST['quantities'] as $cart_id => $qty){
        $cart_id = (int)$cart_id;
        $qty = (int)$qty;
        mysqli_query($conn, "UPDATE cart SET quantity=$qty WHERE id=$cart_id AND user_id=$user_id");
    }
    header("Location: cart.php");
    exit;
}

// Remove
if(isset($_GET['remove'])){
    $cart_id = (int)$_GET['remove'];
    mysqli_query($conn, "DELETE FROM cart WHERE id=$cart_id AND user_id=$user_id");
    header("Location: cart.php");
    exit;
}
?>

<?php include '../includes/header.php'; ?>

<h1>My Cart</h1>

<div class="table-responsive">
<table class="cart-table">
    <tr>
        <th>Select</th>
        <th>Book</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Total</th>
        <th>Action</th>
    </tr>
<?php
$cart_items = mysqli_query($conn, "SELECT cart.id AS cart_id, books.*, cart.quantity 
                                   FROM cart 
                                   JOIN books ON cart.book_id = books.id 
                                   WHERE cart.user_id=$user_id");

if(mysqli_num_rows($cart_items) > 0):
    while($row = mysqli_fetch_assoc($cart_items)):
        $item_total = $row['price'] * $row['quantity'];
?>
    <tr>
        <td><input type="checkbox" class="select-book" data-price="<?php echo $row['price']; ?>" data-quantity="<?php echo $row['quantity']; ?>" name="selected_ids[]" value="<?php echo $row['cart_id']; ?>"></td>
        <td><?php echo $row['title']; ?></td>
        <td>BDT <?php echo $row['price']; ?></td>
        <td><input type="number" name="quantities[<?php echo $row['cart_id']; ?>]" value="<?php echo $row['quantity']; ?>" min="1" class="quantity-input"></td>
        <td class="item-total">BDT <?php echo $item_total; ?></td>
        <td><a href="cart.php?remove=<?php echo $row['cart_id']; ?>" class="remove-link">Remove</a></td>
    </tr>
<?php 
    endwhile; 
else: 
?>
<tr>
    <td colspan="6">Your cart is empty.</td>
</tr>
<?php endif; ?>
    <tr>
        <td colspan="4"><strong>Total Amount:</strong></td>
        <td colspan="1" id="selected-total"></td>
    </tr>
</table>

<div class="cart-actions">
    <a href="checkout.php" id="proceed-btn" class="btn disabled">Proceed to Checkout</a>
</div>
</div>

<script>
const checkboxes = document.querySelectorAll('.select-book');
const proceedBtn = document.getElementById('proceed-btn');
const selectedTotalEl = document.getElementById('selected-total');

function updateSelectedTotal() {
    let total = 0;
    let anySelected = false;
    checkboxes.forEach(cb => {
        if(cb.checked){
            const price = parseFloat(cb.dataset.price);
            const qty = parseInt(cb.dataset.quantity);
            total += price * qty;
            anySelected = true;
        }
    });
    selectedTotalEl.textContent = 'BDT ' + total.toFixed(2);
    if(anySelected){
        proceedBtn.classList.remove('disabled');
        proceedBtn.href = 'checkout.php?ids=' + Array.from(checkboxes).filter(cb => cb.checked).map(cb => cb.value).join(',');
    } else {
        proceedBtn.classList.add('disabled');
        proceedBtn.removeAttribute('href');
    }
}

// Update total
checkboxes.forEach(cb => cb.addEventListener('change', updateSelectedTotal));

document.querySelectorAll('.quantity-input').forEach(input => {
    input.addEventListener('input', e => {
        const tr = e.target.closest('tr');
        const price = parseFloat(tr.querySelector('td:nth-child(3)').textContent.replace('$',''));
        const qty = parseInt(e.target.value);
        tr.querySelector('.item-total').textContent = (price * qty).toFixed(2);
        const cb = tr.querySelector('.select-book');
        cb.dataset.quantity = qty;
        updateSelectedTotal();
    });
});

updateSelectedTotal();
</script>

<?php include '../includes/footer.php'; ?>
