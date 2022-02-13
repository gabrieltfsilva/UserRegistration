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
    <link rel="stylesheet" type="text/css" href="common/stylesheet.css">
    <title>Sign in</title>
</head>
<body>
    <h1>Sign in</h1>
    <form id="login" method="post" action="index.php">
        <div>
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
                <button class="FormButton" title="Sign in" type="submit" onclick="return validateLogin();">Sign in</button>
            </div>
            <br>
            <div><a href="signup.php">Don't have an account? Sign up for free.</a></div>
        </div>
    </form>

    <script type="text/javascript" src="common/script.js"></script>

    <?php
        if (isset($_POST['username']) && 
            isset($_POST['password'])) {
            if ((strlen(trim($_POST['username'])) >= 8) &&
                (strlen(trim($_POST['password'])) >= 8) &&
                (strlen(trim($_POST['username'])) <= 20) &&
                (strlen(trim($_POST['password'])) <= 20)) {
                
                include_once "mysql.php";
            
                $database = new MySQLDatabase();
            
                $USER = base64_encode(trim($_POST['username']));
                $PASSWORD = hash('sha256', md5(trim($_POST['password'])));
            
                if ($database->loginUser($USER, $PASSWORD)) {
                    header("Location: home.php");
                    exit();
                } else {
                    echo "<p class='ErrorMessage'>The username or password is incorrect.</p>";
                }
            } else {
                echo "<p class='ErrorMessage'>Your username and password must be between 8-20 characters.</p>";
            }
        }
    ?>
</body>
</html>