<?php

session_start();

// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if role is not admin, manager or medewerker
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager') {
    echo "You are not allowed to view this page, please login as admin, manager, or medewerker ";
    echo " ga terug naar <a href='login.php'> menugang </a>";
    exit;
}

// Check if the request method is not POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "You are not allowed to view this page ";
    echo " ga terug <a href='menugang.php'> menugang </a>";
}

$naam = $_POST['naam'];
$id = $_GET['id'];

require 'database.php';

$sql = "UPDATE menugangen
        SET naam = :naam
        WHERE menugang_id = :menugang_id";

$stmt = $conn->prepare($sql);
$stmt->bindParam(':naam', $naam);
$stmt->bindParam(':menugang_id', $id);

if ($stmt->execute()) {
    header("Location: menugang_index.php"); 
    exit; 
} else {
    echo "Error updating menugang";
}
?>


