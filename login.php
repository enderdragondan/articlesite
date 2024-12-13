<?php

session_start();
require 'url.php';
require 'auth.php';
require 'database.php';

if (isLoggedIn()) {
    redirect("/");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['signup'])) {
        redirect("/signup.php");
        exit;
    }

    $conn = getDB();

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt === false) {
        echo mysqli_error($conn);
    } else {
        mysqli_stmt_bind_param($stmt, "s", $username);
        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            $user = mysqli_fetch_assoc($result);
            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['is_logged_in'] = true;
                $_SESSION['username'] = $user['username'];
                if ($user['is_admin']) {
                    $_SESSION['admin_logged_in'] = true;
                }
                redirect("/");
                exit;
            } else {
                $error = "Incorrect username or password.";
            }
        } else {
            echo mysqli_error($conn);
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Log In</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>

    <?php include "header.php"; ?>

    <h2>Login</h2>
    <form method="post">
        <div>
            <label for="username">Username:</label>
            <br>
            <input type="text" name="username" id="username">
        </div>
        <br>
        <div>
            <label for="password">Password:</label>
            <br>
            <input type="password" name="password" id="password">
        </div>
        <br>
        <button class="greenbutton" type="submit">Login</button>
        <button style="margin-left: 20px;" type="submit" name="signup">Don't have an account? Sign Up</button>
    </form>
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?= $error; ?></p>
    <?php endif; ?>
</body>

</html>