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

// Check if the request method is not GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    echo "You are not allowed to view this page ";
    echo " ga terug naar <a href='menugang.php'> menugang </a>";
    exit;
}

require 'database.php';

if (isset($_GET['id'])) {
    $menugang_id = $_GET['id'];

    $sql = "SELECT * FROM menugangen WHERE menugang_id = :menugang_id";
    $stmt = $conn->prepare($sql);

    $stmt->bindParam(":menugang_id", $menugang_id);

    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            $menugang = $stmt->fetch(PDO::FETCH_ASSOC);

            
            // Process the retrieved data (if needed)
        } else {
            // No category found with the given ID
            echo "No category found with this ID <br>";
            echo "<a href='tool_index.php'>Go back</a>";
            exit; 
        }
    } else {
        // Error in executing SQL statement
        echo "Error executing SQL statement";
        exit; 
    }
}

require 'nav.php';
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
<main>
    <div class="account-pagina2">
        <div class="form-panel">    
            <h1>menugang bijwerken</h1> <!-- Form title -->
            <hr class="separator"> <!-- Add horizontal line as a separator -->
            <form action="menugang_update.php?id=<?php echo $menugang_id ?>" method="POST">
                    <div class="input-groep">
                        <label for="naam">naam</label>
                        <input type="text" id="naam" name="naam" value="<?php echo $menugang['naam'] ?>">
                    </div>
                    <div class="input-groep">
                        <button type="submit" class="input-button"> bijwerken </button>
                    </div> 
            </form>
        </div>
    </div>
</main>
<?php require 'footer.php' ?>
</body>
</html>


