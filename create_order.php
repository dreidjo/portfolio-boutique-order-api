<?php
header('Content-Type: application/json');
require __DIR__ . '/db.php';

$data = json_decode(file_get_contents('php://input'), true);

$customerName = trim($data['customer_name'] ?? '');
$customerEmail = trim($data['customer_email'] ?? '');
$productId = (int)($data['product_id'] ?? null);
$quantity = (int)($data['quantity'] ?? null);

if(!isset($customerName) || !isset($customerEmail) || $productId <= 0 || $quantity <=0 ){
    http_response_code(404);
    echo json_encode([
            "status" => "error",
            "message" => "Missing fields"
        ]);

    return;
}


$totalPrice = $quantity;

try{
    $stmt = $pdo->prepare("SELECT * FROM `products` WHERE `id` = :id");
$stmt->bindValue(':id',$productId);
$stmt->execute();
//fetchmode is set to get only one value
$stmt->setFetchMode(PDO::FETCH_ASSOC);
$entry = $stmt->fetch();

if(!empty($entry)){
    $totalPrice = $totalPrice * $entry["price"];
    if($entry["stock_quantity"] - $quantity >= 0){
        //Update product stock 
        $newQuantity = $entry["stock_quantity"] - $quantity;

        $pdo->beginTransaction();

        $stmt = $pdo->prepare('UPDATE `products` SET `stock_quantity` = :stock_quantity  WHERE `id` = :id');
        $stmt->bindValue(':stock_quantity',$newQuantity);
        $stmt->bindValue(':id',$productId, PDO::PARAM_INT);
        $stmt->execute();




        //Insert a new row in orders
        $stmt = $pdo->prepare('INSERT INTO `orders` (`customer_name`,`customer_email`,`product_id`,`quantity`,`total_price`,`order_date`)
            VALUES(:customer_name,:customer_email,:product_id,:quantity,:total_price,:order_date)');
        $stmt->bindValue(':customer_name',$customerName);
        $stmt->bindValue(':customer_email',$customerEmail);
        $stmt->bindValue(':product_id',$entry["id"]);
        $stmt->bindValue(':quantity',$quantity);
        $stmt->bindValue(':total_price',$totalPrice);
        $stmt->bindValue(':order_date',date('Y-m-d H:i:s'));
        $stmt->execute();

        $orderId = $pdo->lastInsertId();

        $pdo->commit();

        http_response_code(201);
        echo json_encode([
            "status" => "success",
            "message" => "Order successfully placed",
            "data" => [
                "order_id" => (int)$orderId,
                "product_id" => $productId,
                "quantity" => $quantity,
                "total_price" => (float)$totalPrice
                ]
            ]
        );
        


    }else{
        http_response_code(404);
        echo json_encode([
                "status" => "error",
                "message" => "Insufficient stock"
            ]);

        return;

    }
    
    

}else{
    echo json_encode("Product does not exist");
}

}catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Database query failed"
        ]);
}



