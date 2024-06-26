<?php

session_start();

// Check if the request method is not POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "You are not allowed to view this page ";
    echo " ga terug naar <a href='login.php'> login </a>";
    exit;
}

// Include database connection
require 'database.php';


function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$errors = [];

// Sanitize and validate form fields
$requiredFields = ['voornaam', 'achternaam', 'email', 'role', 'woonplaats', 'postcode', 'huisnummer', 'gebruikersnaam', 'wachtwoord', 'verzeker_wachtwoord'];

foreach ($requiredFields as $field) {
    // Check if the field is required and not empty, except for tussenvoegsel
    if ($field !== 'tussenvoegsel' && empty($_POST[$field])) {
        $errors[] = "Please fill in all fields";
        break; // Stop checking further fields if one is found empty
    }
}

// Check for email format
$email = test_input($_POST["email"]);
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format";
}

// Validate firstname, lastname, and username
foreach (['voornaam', 'achternaam'] as $nameField) {
    if (!preg_match("/^[a-zA-Z-' ]*$/", $_POST[$nameField])) {
        $errors[] = "Only letters and white space allowed for " . ($nameField == 'voornaam' ? 'voornaam' : 'achternaam');
    }
}

// Validate username
if (!preg_match("/^[a-zA-Z0-9\s]*$/", $_POST['gebruikersnaam'])) {
    $errors[] = "Only letters, numbers, and white space allowed for gebruikersnaam";
}

// Validate tussenvoegsel if present
if (!empty($_POST['tussenvoegsel']) && !preg_match("/^[a-zA-Z-' ]*$/", $_POST['tussenvoegsel'])) {
    $errors[] = "Only letters and white space allowed for tussenvoegsel";
}

// Verify if the passwords match
$wachtwoord = $_POST['wachtwoord'];
$verzeker_wachtwoord = $_POST['verzeker_wachtwoord'];
if ($verzeker_wachtwoord !== $wachtwoord) {
    $errors[] = "Passwords do not match.";
}

if (empty($errors)) {

    // Prepare concatenated data
    $concatenated_data_email_username = $_POST['email'] . '|' . $_POST['gebruikersnaam'];

    // Check if user already exists with the same email, gebruikersnaam, or their combination
    $stmt = $conn->prepare("SELECT COUNT(*) FROM gebruikers WHERE CONCAT(email, '|', gebruikersnaam) = :concatenated_data_email_username OR email = :email OR gebruikersnaam = :gebruikersnaam");
    $stmt->bindParam(':concatenated_data_email_username', $concatenated_data_email_username);
    $stmt->bindParam(':email', $_POST['email']);
    $stmt->bindParam(':gebruikersnaam', $_POST['gebruikersnaam']);
    $stmt->execute();
    $count = $stmt->fetchColumn();

if ($count > 0) {
    $errors[] = "User with the same email, username, or their combination already exists";

    } else {
        // Insert address details
        $sql = "INSERT INTO adressen (woonplaats, postcode, huisnummer) VALUES (:woonplaats, :postcode, :huisnummer)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':woonplaats', $_POST['woonplaats']);
        $stmt->bindParam(':postcode', $_POST['postcode']);
        $stmt->bindParam(':huisnummer', $_POST['huisnummer']);
        $stmt->execute();

        // Check if the address insertion was successful
        if ($stmt->rowCount() > 0) {
            $address_id = $conn->lastInsertId();
            // Insert user details
            $hashed_password = password_hash($_POST['wachtwoord'], PASSWORD_DEFAULT);
            $sql = "INSERT INTO gebruikers (adres_id, voornaam, achternaam, tussenvoegsel, email, gebruikersnaam, wachtwoord, rol) VALUES (:adres_id, :voornaam, :achternaam, :tussenvoegsel, :email, :gebruikersnaam, :wachtwoord, :rol)";
            $stmt2 = $conn->prepare($sql);
            $stmt2->bindParam(':adres_id', $address_id);
            $stmt2->bindParam(':voornaam', $_POST['voornaam']);
            $stmt2->bindParam(':achternaam', $_POST['achternaam']);
            $stmt2->bindParam(':tussenvoegsel', $_POST['tussenvoegsel']);
            $stmt2->bindParam(':email', $_POST['email']);
            $stmt2->bindParam(':gebruikersnaam', $_POST['gebruikersnaam']);
            $stmt2->bindParam(':wachtwoord', $hashed_password);
            $stmt2->bindParam(':rol', $_POST['role']);
            $stmt2->execute();

            if ($stmt2->rowCount() > 0) {
                if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'directeur' || $_SESSION['role'] === 'manager' || $_SESSION['role'] === 'medewerker') {
                    header("Location: gebruikers_index.php"); 
                    exit;
                } else {                 
                    // For other roles, redirect to login.php
                    header("Location: login.php");
                    exit;
                }
            } else {
                $errors[] = "Error inserting user data. ";
            }
        }
    }
}            

// Display errors or redirect
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo $error . "<br>";
        
    }
    echo " ga terug naar <a href='gebruikers_create.php'> registeren </a> ";
    exit;
}

?>


