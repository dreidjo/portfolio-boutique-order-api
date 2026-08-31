<?php
header('Content-Type: application/json');
require __DIR__ . '/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if(!isset($data['customer_name']) || !isset($data['customer_email']) || !isset($data['product_id']) || !isset($data['quantity'])){
    http_response_code(404);
    echo json_encode([
            "status" => "error",
            "message" => "Missing fields"
        ]);

    return;
}


$totalPrice = $data['quantity'];

$stmt = $pdo->prepare("SELECT * FROM `products` WHERE `id` = :id");
$stmt->bindValue(':id',$data["product_id"]);
$stmt->execute();
//fetchmode is set to get only one value
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$entry = $stmt->fetch();

if(!empty($entry)){
    $totalPrice = $totalPrice * $entry["price"];
    if($entry["stock_quantity"] - $data['quantity'] >= 0){
        //Update product stock 
        $newQuantity = $entry["stock_quantity"] - $data['quantity'];

        $stmt = $pdo->prepare('UPDATE `products` SET `stock_quantity` = :stock_quantity  WHERE `id` = :id');
        $stmt->bindValue(':stock_quantity',$newQuantity);
        $stmt->bindValue(':id',$data["product_id"], PDO::PARAM_INT);
        $stmt->execute();




        //Insert a new row in orders
        $stmt = $pdo->prepare('INSERT INTO `orders` (`customer_name`,`customer_email`,`product_id`,`quantity`,`total_price`,`order_date`)
            VALUES(:customer_name,:customer_email,:product_id,:quantity,:total_price,:order_date)');
        $stmt->bindValue(':customer_name',$data['customer_name']);
        $stmt->bindValue(':customer_email',$data['customer_email']);
        $stmt->bindValue(':product_id',$entry["id"]);
        $stmt->bindValue(':quantity',$data['quantity']);
        $stmt->bindValue(':total_price',$totalPrice);
        $stmt->bindValue(':order_date',date('Y-m-d H:i:s'));
        $stmt->execute();

        $results = "NEW ORDER";
        echo json_encode($results);
        


    }else{
        echo json_encode("Insufficient stock");

    }
    
    

}else{
    echo json_encode("Product does not exist");
}

