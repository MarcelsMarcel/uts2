<?php
session_start();
// ur mission is after create connection to database, you need to implement the following functionalities in this file:
// 1. Create a POST request to handle login form submission
// 2. Validate the username and password against the database
// 3. If the credentials are valid, start a session and redirect to index.php
// 4. If the credentials are invalid, redirect back to login.php with an error message
// 5. Dont forget to include session_start() at the beginning of the file to manage user sessions

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['username']) && isset($_POST['password'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $conn = new mysqli("localhost", "root", '', 'uts2');

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $saved_password);
        $stmt->fetch();

        if(password_verify($password, $saved_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            session_regenerate_id(true);
            header("location: index.php");
            exit;
        } else { 
            $_SESSION['error'] = "ga sesuai woi";
            header("location: login.php");
            exit;
        }
    }
}
