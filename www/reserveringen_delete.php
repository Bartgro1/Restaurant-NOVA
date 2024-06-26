<?php

session_start();

// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if role is not admin, directeur, manager, or medewerker
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'directeur' && $_SESSION['role'] !== 'manager' && $_SESSION['role'] !== 'medewerker') {
    echo "You are not authorized to view this page. Please log in with appropriate credentials. ";
    echo "Log in with a different role <a href='login.php'>here</a>.";
    exit;
}


// Check if the request method is not GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo "You are not allowed to view this page ";
    echo " ga terug naar <a href='reserveringen_index.php'> reserveringen </a>";
    exit;
}


require 'database.php';

// Check if the 'id' parameter is present in the URL
if (isset($_GET['id'])) {
    $reservering_id = $_GET['id'];

    // Select the record to delete
    $sql = "SELECT * FROM reserveringen WHERE reservering_id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(":id", $reservering_id);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Delete the record
        $sql = "DELETE FROM reserveringen WHERE reservering_id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":id", $reservering_id);
        if ($stmt->execute()) {
            header("Location: reserveringen_index.php"); // Redirect to reserveringen_index.php
            exit; // Make sure to exit after redirecting
        } else {
            echo "Error deleting reservering"; // Display an error message if deletion fails
            echo "<a href='reserveringen_index.php'>Ga terug naar reserveringen</a>";
            exit();
        }
    } else {
        echo "reservering not found"; // Display a message if the record is not found
        echo "<a href='reserveringen_index.php'>Ga terug naar reserveringen</a>";
        exit();
    }
} else {
    header("Location: reserveringen_index.php");
    exit;
}
?>