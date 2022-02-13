<?php
    session_start();
    if (!isset($_SESSION["id"])) {
        // If the user is not connected, the page redirects him to the login page.
        header("Location: index.php");
        exit();
    }

    //Logout action.
    if (isset($_GET['action']) && strtolower($_GET['action']) === "logout") {
        unset($_SESSION["id"]);
        unset($_SESSION["name"]);
        session_unset();
        header("Location: index.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="common/stylesheet.css">
    <title>Home</title>
</head>
<body>
    <h1>Home</h1>

    <?php
        // htmlspecialchars is used to prevent HTML injection, XSS and session hijacking.
        echo "<p>Hello, " . htmlspecialchars(base64_decode($_SESSION["name"])) . ".</p><br>"
    ?>

    <form action="home.php" method="get">
        <input class="FormButton" title="Logout" type="submit" name="action" value="Logout" />
    </form>
</body>
</html>