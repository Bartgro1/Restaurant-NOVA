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
    echo " ga terug naar <a href='dashboard.php'> dashboard </a>";
    exit;
}

require 'database.php';

$stmt = $conn->prepare("SELECT menugang_id, naam FROM menugangen");
$stmt->execute();
// set the resulting array to associative
$menugangen = $stmt->fetchAll(PDO::FETCH_ASSOC);


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
        <main>
            <div class="container">
                 <div class="menugang-container">
                <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>id</th>
                        <th>naam</th>
                        <th>Acties</th> 
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($menugangen as $menugang) : ?>
                    <tr>
                        <td><?php echo $menugang['menugang_id'] ?></td>
                        <td><?php echo $menugang['naam'] ?></td>
                        <td>
                            <a href="menugang_detail.php?id=<?php echo $menugang['menugang_id'] ?>">Bekijk</a>
                            <a href="menugang_edit.php?id=<?php echo $menugang['menugang_id'] ?>">Wijzig</a>
                            <a href="menugang_delete.php?id=<?php echo $menugang['menugang_id'] ?>">Verwijder</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        </div>
        </div>
        </main>
    <?php include 'footer.php' ?>    
</body>
</html>

</div>

