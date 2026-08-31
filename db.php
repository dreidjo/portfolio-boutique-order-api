<?php

try {
    $pdo = new PDO('mysql:host=localhost;dbname=boutique_db;charset=utf8mb4', 'cms', 'nvNUVkgG)!yT4VrV', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
}
catch (PDOException $e) {
    // var_dump($e->getMessage());
    echo 'A problem occured with the database connection...';
    die();
}