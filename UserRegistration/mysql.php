<?php

// Constants of brute force protect.
define("MAX_LOGIN", 5); // Max login attempts.

class MySQLDatabase {
    public string $mysql_host;
    public string $mysql_user;
    public string $mysql_password;
    public string $mysql_database;
    public string $mysql_table;

    function __construct($host = 'localhost', $user = 'root', $password = '', $database = 'mydb', $table = 'users') {
        $this->mysql_host = $host;
        $this->mysql_user = $user;
        $this->mysql_password = $password;
        $this->mysql_database = $database;
        $this->mysql_table = $table;

        $mysql_connection = new mysqli($this->mysql_host, $this->mysql_user, $this->mysql_password);

        if ($mysql_connection->connect_errno) {
            echo "<p class='ErrorMessage'>Failed to connect to database.</p>";
            include_once "register.php";
            exit();
        }

        $query = "CREATE DATABASE IF NOT EXISTS " . $this->mysql_database . ";";

        if (!mysqli_query($mysql_connection, $query)) {
            echo "<p class='ErrorMessage'>Failed to create database.</p>";
            include_once "register.php";
            $mysql_connection->close();
            exit();
        }

        $mysql_connection->close();
        unset($mysql_connection);

        $mysql_connection = new mysqli($this->mysql_host, $this->mysql_user, $this->mysql_password, $this->mysql_database);

        $query = "";

        // This table is used to store users.
        $query .= "CREATE TABLE IF NOT EXISTS " .
                  $this->mysql_table . " " .
                  "(id INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
                  datetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
                  name VARCHAR(30) NOT NULL, 
                  username VARCHAR(30) NOT NULL UNIQUE, 
                  password VARCHAR(64) NOT NULL);";

        // This table is used to prevent brute force attacks.
        $query .= "CREATE TABLE IF NOT EXISTS logs " .
                  "(datetime TIMESTAMP DEFAULT CURRENT_TIMESTAMP, 
                  ip VARCHAR(15) NOT NULL, 
                  username VARCHAR(30) NOT NULL);";

        if (!mysqli_multi_query($mysql_connection, $query)) {
            echo "Failed to create table.";
            $mysql_connection->close();
            exit();
        }

        $mysql_connection->close();
    }

    public function insertUser($name, $username, $password) {
        $mysql_connection = new mysqli($this->mysql_host, $this->mysql_user, $this->mysql_password, $this->mysql_database);

        // Use prepared statments to prevent SQL Injection.
        if (!($statement = $mysql_connection->prepare("INSERT INTO users (name, username, password) VALUES (?, ?, ?);"))) {
            $mysql_connection->close();
            return false;
        }

        if (!$statement->bind_param("sss", $name, $username, $password)) {
            $mysql_connection->close();
            return false;
        }

        if (!$statement->execute()) {
            $mysql_connection->close();
            return false;
        } else {
            $mysql_connection->close();
            return true;
        }
    }

    public function loginUser($username, $password) {
        $mysql_connection = new mysqli($this->mysql_host, $this->mysql_user, $this->mysql_password, $this->mysql_database);

        // To prevent brute force attacks, the IP and login failed is checked in last hour.

        // Use prepared statments to prevent SQL Injection.
        if (!($statement = $mysql_connection->prepare(
            "SELECT COUNT(ip) AS n_ip FROM logs WHERE ip = ? AND datetime >= DATE_SUB(NOW(), INTERVAL 1 HOUR);"))) {
            $mysql_connection->close();
            return false;
        }

        $ip = $_SERVER['REMOTE_ADDR'];

        if (!$statement->bind_param("s", $ip)) {
            $mysql_connection->close();
            return false;
        }

        $statement->execute();
        $result = $statement->get_result();
        $result_query = $result->fetch_all();

        $ip_count = $result_query[0][0];
        
        $statement->free_result();

        // Use prepared statments to prevent SQL Injection.
        if (!($statement = $mysql_connection->prepare(
            "SELECT COUNT(username) AS n_username FROM logs WHERE username = ? AND datetime >= DATE_SUB(NOW(), INTERVAL 1 HOUR);"))) {
            $mysql_connection->close();
            return false;
        }

        if (!$statement->bind_param("s", $username)) {
            $mysql_connection->close();
            return false;
        }

        $statement->execute();
        $result = $statement->get_result();
        $result_query = $result->fetch_all();

        $username_count = $result_query[0][0];

        if ($ip_count > constant("MAX_LOGIN") || $username_count > constant("MAX_LOGIN")) {
            header("HTTP/1.0 404 Not Found");
            echo "<p class='ErrorMessage'>You are blocked. Wait some minutes to try again.</p>";
            $mysql_connection->close();
            exit();
        }

        $statement->free_result();

        // Use prepared statments to prevent SQL Injection.
        if (!($statement = $mysql_connection->prepare("SELECT id, name, username, password FROM users WHERE username = ?;"))) {
            $mysql_connection->close();
            return false;
        }

        if (!$statement->bind_param("s", $username)) {
            $mysql_connection->close();
            return false;
        }

        $statement->execute();
        $result = $statement->get_result();
        $result_user = $result->fetch_all();

        if (!$statement->execute()) {
            $mysql_connection->close();
            return false;
        }

        // HTTP is a text protocol, but strong comparison is recommended for sensitive data.
        // The user field is unique in the database. Only one value needs to be checked.
        if (count($result_user) != 0 && $result_user[0][2] === $username && $result_user[0][3] === $password) {
            $mysql_connection->close();
            session_start();
            // Username must not be used as a cookie.
            $_SESSION["id"] = $result_user[0][0];
            $_SESSION["name"] = $result_user[0][1];
            return true;
        } else {
            $statement->free_result();

            // Use prepared statments to prevent SQL Injection.
            if (!($statement = $mysql_connection->prepare("INSERT INTO logs (ip, username) VALUES (?, ?);"))) {
                $mysql_connection->close();
                return false;
            }

            $ip = $_SERVER['REMOTE_ADDR'];

            if (!$statement->bind_param("ss", $ip, $username)) {
                $mysql_connection->close();
                return false;
            }

            $statement->execute();

            $mysql_connection->close();

            return false;
        }
    }
}

// According to the php documentation:
// The file contains only PHP code. The script ends here with no PHP closing tag.