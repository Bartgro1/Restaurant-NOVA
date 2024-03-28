<?php
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Check if role is not admin, manager or medewerker
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'manager' && $_SESSION['role'] !== 'medewerker') {
    echo "You are not allowed to view this page, please login as admin, manager, or medewerker ";
    echo " login als een andere rol, hier <a href='login.php'> login </a>";
    exit;
}

// Check if the request method is not GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo "You are not allowed to view this page ";
    echo " ga terug naar <a href='gebruikers_index.php'> gebruikers </a>";
    exit;
}

require 'database.php';

$stmt = $conn->prepare("SELECT * FROM gebruikers join adressen on adressen.adres_id = gebruikers.adres_id");
$stmt->execute();
// set the resulting array to associative
$gebruikers = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Document</title>
</head>
<body>
    <?php include 'nav.php' ?>
    <?php include 'footer.php' ?>

    <main>
        <div class="container">
            <table>
                <thead>
                    <tr>
                        <th>voornaam</th>
                        <th>tussenvoegsel</th>
                        <th>achternaam</th>
                        <th>email</th>
                        <th>gebruikersnaam</th>
                        <th>woonplaats</th> 
                        <th>postcode</th>
                        <th>huisnummer</th>
                        <th>role</th>
                        <th>Acties</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($gebruikers as $gebruiker) : ?>
                        <tr>
                            <td><?php echo $gebruiker['voornaam'] ?></td>
                            <td><?php echo $gebruiker['tussenvoegsel'] ?></td>
                            <td><?php echo $gebruiker['achternaam'] ?></td>
                            <td><?php echo $gebruiker['email'] ?></td>
                            <td><?php echo $gebruiker['gebruikersnaam'] ?></td>
                            <td><?php echo $gebruiker['woonplaats'] ?></td>
                            <td><?php echo $gebruiker['postcode'] ?></td>
                            <td><?php echo $gebruiker['huisnummer'] ?></td>
                            <td><?php echo $gebruiker['rol'] ?></td>
                            <td>
                                <?php if ($_SESSION['role'] === 'admin' && ($_SESSION['user_id'] === $gebruiker['gebruiker_id'] || $gebruiker['rol'] !== 'admin')) : ?>
                                    <!-- Admin actions: View, Edit, and delete all users except for other admins -->
                                    <a href="gebruikers_detail.php?id=<?php echo $gebruiker['gebruiker_id'] ?>">Bekijk</a>
                                    <a href="gebruikers_edit.php?id=<?php echo $gebruiker['gebruiker_id'] ?>">Wijzig</a>
                                    <a href="gebruikers_delete.php?id=<?php echo $gebruiker['gebruiker_id'] ?>">Verwijder</a>
                                <?php endif; ?>
                                <?php if ($_SESSION['role'] === 'manager' && ($_SESSION['user_id'] === $gebruiker['gebruiker_id'] || $gebruiker['rol'] !== 'manager' && $gebruiker['rol'] !== 'admin')) : ?>
                                    <!-- Manager actions: View, Edit & Delete all users except for other admins and managers -->
                                    <a href="gebruikers_detail.php?id=<?php echo $gebruiker['gebruiker_id'] ?>">Bekijk</a>
                                    <a href="gebruikers_edit.php?id=<?php echo $gebruiker['gebruiker_id'] ?>">Wijzig</a>
                                    <a href="gebruikers_delete.php?id=<?php echo $gebruiker['gebruiker_id'] ?>">Verwijder</a>
                                <?php endif; ?>
                                    <!-- Medewerker actions: View and edit own profile only, including klanten -->
                               <?php if ($_SESSION['role'] === 'medewerker' && ($_SESSION['user_id'] === $gebruiker['gebruiker_id'] || ($gebruiker['rol'] !== 'manager' && $gebruiker['rol'] !== 'admin' && $_SESSION['role'] !== 'medewerker'))) : ?>
                                    <a href="gebruikers_detail.php?id=<?php echo $gebruiker['gebruiker_id'] ?>">Bekijk</a> 
                                    <a href="gebruikers_edit.php?id=<?php echo $gebruiker['gebruiker_id'] ?>">Wijzig</a>
                                <?php endif; ?>
                            </td>
                        </tr> 
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>
</html>




