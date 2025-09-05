<?php
include 'components/connect.php';
session_start();

if(isset($_SESSION['user_id'])){
   $user_id = $_SESSION['user_id'];
}else{
   header('location:home.php');
   exit;
}

// Fetch user profile
$select_profile = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
$select_profile->execute([$user_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

$message = [];
$order_receipt = [];

if(isset($_POST['submit'])){

   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
   $method = filter_var($_POST['method'], FILTER_SANITIZE_STRING);
   $address = filter_var($_POST['address'], FILTER_SANITIZE_STRING);
   $total_products = $_POST['total_products'];
   $total_price = $_POST['total_price'];

   // Optional card info
   $card_name = isset($_POST['card_name']) ? filter_var($_POST['card_name'], FILTER_SANITIZE_STRING) : '';
   $card_number = isset($_POST['card_number']) ? filter_var($_POST['card_number'], FILTER_SANITIZE_STRING) : '';
   $expiry_date = isset($_POST['expiry_date']) ? filter_var($_POST['expiry_date'], FILTER_SANITIZE_STRING) : '';

   $check_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
   $check_cart->execute([$user_id]);

   if($check_cart->rowCount() > 0){

      if($address == ''){
         $message[] = 'please add your address!';
      }else{
         // Insert order safely: check if columns exist
         try {
            $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price, card_name, card_number, expiry_date) VALUES(?,?,?,?,?,?,?,?,?,?,?)");
            $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $total_price, $card_name, $card_number, $expiry_date]);
         } catch(PDOException $e){
            // If columns don't exist, insert without card info
            $insert_order = $conn->prepare("INSERT INTO `orders`(user_id, name, number, email, method, address, total_products, total_price) VALUES(?,?,?,?,?,?,?,?)");
            $insert_order->execute([$user_id, $name, $number, $email, $method, $address, $total_products, $total_price]);
         }

         $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
         $delete_cart->execute([$user_id]);

         $message[] = 'order placed successfully!';

         $order_receipt = [
            'name' => $name,
            'number' => $number,
            'email' => $email,
            'method' => $method,
            'address' => $address,
            'products' => $total_products,
            'total' => $total_price,
            'card_name' => $card_name,
            'card_number' => $card_number,
            'expiry_date' => $expiry_date
         ];
      }

   }else{
      $message[] = 'your cart is empty';
   }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout</title>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
<link rel="stylesheet" href="css/style.css">

<style>
.message{
    position: fixed;
    top: 10px;
    right: 10px;
    background: #27ae60;
    color: #fff;
    padding: 15px;
    border-radius: 5px;
    z-index: 1000;
}
.message i{
    margin-left: 10px;
    cursor: pointer;
}

.receipt{
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 20px;
    border: 2px solid #27ae60;
    border-radius: 10px;
    max-width: 600px;
    background: #f9f9f9;
    z-index: 1001;
    display: none;
}
.receipt h2{
    text-align: center;
    color: #27ae60;
    margin-bottom: 20px;
}
.receipt p{
    font-size: 16px;
    margin: 5px 0;
}
.receipt .close{
    position: absolute;
    top: 10px;
    right: 15px;
    font-size: 20px;
    cursor: pointer;
    color: #e74c3c;
}

.new-card-form{
    display:none;
    margin-top:10px;
}
.payment-section h4{
    margin:10px 0;
}
.payment-section div{
    margin:5px 0;
}
</style>
</head>
<body>

<?php include 'components/user_header.php'; ?>

<?php
if(isset($message)){
   foreach($message as $msg){
      echo '<div class="message">
            <span>'.$msg.'</span>
            <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
            </div>';
   }
}
?>

<div class="heading">
   <h3>checkout</h3>
   <p><a href="home.php">home</a> <span> / checkout</span></p>
</div>

<section class="checkout">

<h1 class="title">order summary</h1>

<form action="" method="post">

<div class="cart-items">
   <h3>cart items</h3>
   <?php
      $grand_total = 0;
      $cart_items = [];
      $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
      $select_cart->execute([$user_id]);
      if($select_cart->rowCount() > 0){
         while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
            $cart_items[] = $fetch_cart['name'].' ('.$fetch_cart['price'].' x '. $fetch_cart['quantity'].') - ';
            $grand_total += ($fetch_cart['price'] * $fetch_cart['quantity']);
   ?>
   <p><span class="name"><?= $fetch_cart['name']; ?></span><span class="price">$<?= $fetch_cart['price']; ?> x <?= $fetch_cart['quantity']; ?></span></p>
   <?php
         }
         $total_products = implode($cart_items);
      }else{
         echo '<p class="empty">your cart is empty!</p>';
         $total_products = '';
      }
   ?>
   <p class="grand-total"><span class="name">grand total :</span><span class="price">$<?= $grand_total; ?></span></p>
   <a href="cart.php" class="btn">view cart</a>
</div>

<input type="hidden" name="total_products" value="<?= $total_products; ?>">
<input type="hidden" name="total_price" value="<?= $grand_total; ?>">
<input type="hidden" name="name" value="<?= $fetch_profile['name'] ?>">
<input type="hidden" name="number" value="<?= $fetch_profile['number'] ?>">
<input type="hidden" name="email" value="<?= $fetch_profile['email'] ?>">
<input type="hidden" name="address" value="<?= $fetch_profile['address'] ?>">

<div class="user-info">
<h3>your info</h3>
<p><i class="fas fa-user"></i><span><?= $fetch_profile['name'] ?></span></p>
<p><i class="fas fa-phone"></i><span><?= $fetch_profile['number'] ?></span></p>
<p><i class="fas fa-envelope"></i><span><?= $fetch_profile['email'] ?></span></p>
<a href="update_profile.php" class="btn">update info</a>

<h3>delivery address</h3>
<p><i class="fas fa-map-marker-alt"></i><span><?php if($fetch_profile['address']==''){echo 'please enter your address';}else{echo $fetch_profile['address'];} ?></span></p>
<a href="update_address.php" class="btn">update address</a>

<!-- Payment Options -->
<div class="payment-section">
<h3>Select a Payment Method</h3>

<h4>Saved Debit/Credit Cards</h4>
<div>
<input type="radio" name="method" value="Commercial Bank Debit Card ending 5921" required> Commercial Bank Debit Card ending 5921 - Ram Sharma (11/24)
</div>
<div>
<input type="radio" name="method" value="Commercial Bank Debit Card ending 4547" required> Commercial Bank Debit Card ending 4547 - Vikas Sharma (08/24)
</div>
<div>
<input type="radio" name="method" value="Commercial Bank Debit Card ending 9874" required> Commercial Bank Debit Card ending 9874 - Ishant Sharma (04/24)
</div>
<div>
<input type="radio" name="method" value="new_card" required> Add Debit/Credit Card
<div class="new-card-form">
<input type="text" name="card_name" placeholder="Name on Card" style="width:100%; margin:5px 0;">
<input type="text" name="card_number" placeholder="Card Number" style="width:100%; margin:5px 0;">
<input type="text" name="expiry_date" placeholder="Expiry Date (MM/YY)" style="width:48%; margin:5px 1%;">
<input type="text" name="cvv" placeholder="CVV" style="width:48%; margin:5px 1%;">
</div>
</div>

<h4>Mobile Payments</h4>
<div><input type="radio" name="method" value="Paytm" required> Payt</div>
<div><input type="radio" name="method" value="Easy Cash" required> Easy Cash</div>

<h4>Net Banking</h4>
<select name="method" class="box" required>
<option value="" disabled selected>Choose a Bank</option>
<option value="Commercial Bank">Commercial Bank</option>
<option value="Sampath Bank">Sampath Bank</option>
<option value="HNB">HNB</option>
<option value="People's Bank">People's Bank</option>
</select>

<h4>Cash on Delivery</h4>
<div><input type="radio" name="method" value="Cash on Delivery" required> Cash on Delivery</div>
</div>

<input type="submit" value="place order" class="btn <?php if($fetch_profile['address'] == ''){echo 'disabled';} ?>" style="width:100%; background:var(--red); color:var(--white);" name="submit">
</div>
</form>
</section>

<?php if(!empty($order_receipt)): ?>
<div class="receipt" id="receipt">
<span class="close" onclick="document.getElementById('receipt').style.display='none';">&times;</span>
<h2>Order Receipt</h2>
<p><strong>Name:</strong> <?= $order_receipt['name'] ?></p>
<p><strong>Phone:</strong> <?= $order_receipt['number'] ?></p>
<p><strong>Email:</strong> <?= $order_receipt['email'] ?></p>
<p><strong>Address:</strong> <?= $order_receipt['address'] ?></p>
<p><strong>Payment Method:</strong> <?= $order_receipt['method'] ?></p>
<?php if(!empty($order_receipt['card_name'])): ?>
<p><strong>Card:</strong> <?= $order_receipt['card_name'] ?> ending <?= substr($order_receipt['card_number'],-4) ?> (<?= $order_receipt['expiry_date'] ?>)</p>
<?php endif; ?>
<p><strong>Products:</strong> <?= $order_receipt['products'] ?></p>
<p><strong>Total:</strong> $<?= $order_receipt['total'] ?></p>
</div>

<script>
document.getElementById('receipt').style.display = 'block';

// Show/hide new card form dynamically
const addCardRadio = document.querySelector('input[value="new_card"]');
const newCardForm = document.querySelector('.new-card-form');
const cardRadios = document.querySelectorAll('input[name="method"]');

newCardForm.style.display = 'none';

cardRadios.forEach(radio => {
    radio.addEventListener('change', () => {
        if(addCardRadio.checked){
            newCardForm.style.display = 'block';
        } else {
            newCardForm.style.display = 'none';
        }
    });
});
</script>
<?php endif; ?>

<?php include 'components/footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>
