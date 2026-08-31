<?php
header('Content-Type: application/json');
require __DIR__ . '/db.php';

try {
    $stmt = $pdo->prepare("SELECT * FROM `products` WHERE `stock_quantity` > 0");
    $stmt->execute();
    $results = $stmt->fetchAll(PDO::FETCH_CLASS);

    if(empty($results)){
        http_response_code(404);
        echo json_encode([
            "status" => "error",
            "message" => "No products found in catalog"
        ]);
        
    }else{
        http_response_code(200);
        echo json_encode([
            "status" => "success",
            "message" => $results
        ]);
    }

}catch (PDOException $e) {
    http_response_code(500);
        echo json_encode([
            "status" => "error",
            "message" => "Database query failed"
        ]);
}


