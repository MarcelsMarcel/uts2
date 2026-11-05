<?php
session_start();
// ur mission is after create connection to database, you need to implement the following functionalities in this file:
// 1. Create a POST request to handle registration form submission
// 2. Validate the input fields: username, email, password, confirm_password make sure confirm_password matches password
// 3. Check if the username or email already exists in the database
// 4. If validation passes, hash the password and insert the new user into the database
// 5. If registration is successful, redirect to login.php with a success message


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['email']) && isset($_POST['confirm_password'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) { 
        $_SESSION['error'] = "cannot be emptyk aowkawo";
        header("Location: register.php");
        exit;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "email ga sesuai format bro akwokwaokwa";
        header("Location: register.php");
        exit;
    }

    if (strlen($password) < 8) {
        $_SESSION['error'] = "kurang panjang sus";
        header("Location: register.php");
        exit;
    }

    if ($password != $confirm_password) {
        $_SESSION['error'] = "ga sama passnya ";
        header("Location: register.php");
        exit;
    }

    $conn = new mysqli('localhost', 'root', '', 'uts2');

    if ($conn->connect_error) {
        die("connection failed" . $conn->connect_error);
    }
    

    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
    $stmt->bind_param("ss", $username, $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['error'] = "udah ada uy akunnya kawokaw";
        header("Location: register.php");
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?) ");
    $stmt->bind_param("sss", $username, $email, $hashed_password);
    $stmt->execute();

    $stmt->close();
    $conn->close();
    $_SESSION['success'] = "regist gacor gas lanjut login";
    header("location: login.php");
    exit;
} else {
    $_SESSION['error'] = "invalid request";
    header("location: register.php");
    exit;
}