<?php
try {
        $pdo = new PDO('mysql:host=localhost;dbname=db_name_here;charset=utf8mb4', 'user_name_here', 'password_here', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
}
catch (PDOException $e) {
    echo 'A problem occured with the database connection...';
    die();
}

return $pdo;
