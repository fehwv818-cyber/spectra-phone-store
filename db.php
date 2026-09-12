<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "phone_store";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "فشل الاتصال: " . $e->getMessage();
}
?>