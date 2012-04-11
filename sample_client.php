<?php
include('retention_science_api.php');
$api_user = "test";
$api_pass = "1234";
$testmode = true;

$client = new RetentionScienceApi($api_user, $api_pass, $testmode);

// GET the last user record id 
echo $client->get_last_user_record_id();

// POST a new or existing user
$user_record_id = 12300;
$user_array = array('email' => 'johndoe@gmail.com', 'full_name' => "John Doe", 'address1' => "123 Main St", 'city' => "San Diego", 'state' => 'CA', 'zip' => 91311, 'country' => "US", 'phone' => '8882221111', 'birth_year' => '1920', 'gender' => 'm', 'ip_address' => "127.6.6.6", 'number_logons' => 2, 'account_created_on' => date("Y-m-d"), 'last_logon_at' => date("D M j G:i:s T Y"));
$client->update_user($user_record_id, $user_array);


// POST a new / existing category
$category_record_id1 = "sports";
$category_record_id2 = "exercise";
$category_array1 = array('name' => "Sporting Goods", 'description' => 'Sporting goods and accessories', 'parent_record_id' => null);
$client->update_category($category_record_id1, $category_array1);
$category_array2 = array('name' => "Exercise", 'description' => 'Exercise toys and equipment', 'parent_record_id' => $category_record_id1);
$client->update_category($category_record_id2, $category_array2);


// POST a new or existing item
$item_record_id = 20;
$item_name = "Pogo Stick";
$item_price = "29.99";
$item_array = array("name" => $item_name, "manufacturer" => "Nike", "model" => "PG101", "quantity" => 100, "price" => $item_price, "active" => true, "image_list" => array("http://myhost.com/pogo1.jpg", "http://myhost.com/pogo2.jpg"), "categories" => array($category_record_id1,$category_record_id2));
$client->update_item($item_record_id, $item_array);

// GET the last order record id 
echo $client->get_last_order_record_id();

// POST a new or existing order with order_item
$order_record_id = 5000;
$order_item_array = array('item_record_id' => $item_record_id, 'name' => $item_name, 'quantity' => 1, 'price' => $item_price, 'final_price' => $item_price, 'categories' => array($category_record_id1, $category_record_id2));
$order_array = array('user_record_id' => $user_record_id, 'total_price' => "19.99", 'discount_amount' => "", 'ordered_at' => date("D M j G:i:s T Y"), 'payment_method' => 'Credit Card', 'order_items' => array($order_item_array));
$client->update_order($order_record_id, $order_array);


?>

