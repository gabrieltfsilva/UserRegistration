<?php
    session_start();
    if (isset($_SESSION["id"])) {
        // If the user is connected, the page redirects him to the home page.
        header("Location: home.php");
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="-1">
    <link rel="stylesheet" type="text/css" href="common/stylesheet.css">
    <title>Sign up</title>
</head>
<body>
    <h1>Sign up</h1>
    <form id="login" method="post" action="signup.php">
        <div>
            <div>
                <label class="FormLabel"><b>Name</b></label>
                <br>
                <input class="FormInput" type="text" placeholder="Name" id="name" name="name" required>
            </div>
            <br>
            <div>
                <label class="FormLabel"><b>Username</b></label>
                <br>
                <input class="FormInput" type="text" placeholder="Username" id="username" name="username" required>
            </div>
            <br>
            <div>
                <label class="FormLabel"><b>Password</b></label>
                <br>
                <input class="FormInput" type="password" placeholder="Password" id="password" name="password" required>
            </div>
            <br>
            <div>
                <button class="FormButton" title="Sign up" type="submit" onclick="return validateUser();">Sign up</button>
            </div>
            <br>
        </div>
    </form>

    <script type="text/javascript" src="common/script.js"></script>

    <?php
        if (isset($_POST['name']) && 
            isset($_POST['username']) &&
            isset($_POST['password'])) {

            if ((strlen(trim($_POST['name'])) > 0) &&
                (strlen(trim($_POST['username'])) >= 8) &&
                (strlen(trim($_POST['password'])) >= 8) &&
                (strlen(trim($_POST['name'])) <= 20) &&
                (strlen(trim($_POST['username'])) <= 20) &&
                (strlen(trim($_POST['password'])) <= 20)) {
                
                include_once "mysql.php";
            
                $database = new MySQLDatabase();
            
                $NAME = base64_encode(trim($_POST['name']));
                $USER = base64_encode(trim($_POST['username']));
                $PASSWORD = hash('sha256', md5(trim($_POST['password'])));
            
                if ($database->insertUser($NAME, $USER, $PASSWORD)) {
                    header("Location: index.php");
                    exit();
                } else {
                    echo "<p class='ErrorMessage'>Failed to create user. Try another username.</p>";
                }
            } else {
                echo "<p class='ErrorMessage'>Your name is required.</p>";
                echo "<p class='ErrorMessage'>Your username and password must be between 8-20 characters.</p>";
            }
        }
    ?>
</body>
</html>