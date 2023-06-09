<?php
include('../includes/connect.php');
include('../functions/common_function.php');

if (isset($_GET['user_id'])) {
	$user_id = $_GET['user_id'];
}

$get_ip_user = getIPAddress();
$total_price = 0;
$cart_query_price = "Select * from `cart_details` where ip_address = '$get_ip_user'";
$result_cart_price = mysqli_query($con, $cart_query_price);
$count_products = mysqli_num_rows($result_cart_price);
$invoice_number = mt_rand();
// echo $invoice_number;
$status = 'pending';
while ($row_price = mysqli_fetch_array($result_cart_price)) {
	$product_id = $row_price['product_id'];
	$select_product = "Select * from `products` where product_id =$product_id";
	$result_product_price = mysqli_query($con, $select_product);
	while ($row_product_price = mysqli_fetch_array($result_product_price)) {
		$product_price = array($row_product_price['product_price']);
		$product_sum_price = array_sum($product_price);
		$total_price += $product_sum_price;
	}
}

$get_cart = "select * from `cart_details`";
$run_cart = mysqli_query($con, $get_cart);
$get_item_quantity = mysqli_fetch_array($run_cart);
$quantity = $get_item_quantity['quantity'];
if ($quantity == 0) {
	$quantity = 1;
	$subtotal = $total_price;
} else {
	$quantity = $quantity;
	$subtotal = $total_price * $quantity;
}
$insert_orders = "insert into `user_orders` (user_id,amount_due,invoice_number,total_products,order_date,order_status) 
values ('$user_id','$subtotal','$invoice_number','$count_products',NOW(),'$status')";

$result_query = mysqli_query($con, $insert_orders);

if ($result_query) {
	echo "<script>alert('Orders successfully !!!')</script>";
	echo "<script>window.open('profile.php','_self');</script>";
};

$insert_pending_orders = "insert into `oders_pending` (user_id,invoice_number,product_id,quantity,order_status) 
values ('$user_id','$invoice_number','$product_id',$quantity,'$status')";
$result_pending_query = mysqli_query($con, $insert_pending_orders);

$empty_cart = "delete from `cart_details` where ip_address = '$get_ip_user'";
$result_delete = mysqli_query($con, $empty_cart);
