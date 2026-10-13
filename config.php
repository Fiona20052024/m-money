<?php
session_start();

$host = "localhost";
$user = "root";
$pass = ""; // Default XAMPP password is empty
$db = "m_money_db";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
