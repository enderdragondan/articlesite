<?php

session_start();
require "url.php";
require "auth.php";
require "database.php";

if (isLoggedIn()) {
    redirect("/");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['login'])) {
        redirect("/login.php");
        exit;
    }

    $conn = getDB();

    if (isset($_POST['username']) && isset($_POST['password']) && isset($_POST['password2'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $password2 = $_POST['password2'];

        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt === false) {
            echo mysqli_error($conn);
        } else {

            mysqli_stmt_bind_param($stmt, "s", $username);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);

            if (mysqli_stmt_num_rows($stmt) > 0) {
                $error = "Username is taken.";
            } elseif ($password !== $password2) {
                $error = "Passwords do not match.";
            } else {
                $sql = "INSERT INTO users (username, password) VALUES (?, ?)";
                $stmt = mysqli_prepare($conn, $sql);

                if ($stmt === false) {
                    echo mysqli_error($conn);
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    mysqli_stmt_bind_param($stmt, "ss", $username, $hash);
                    if (mysqli_stmt_execute($stmt)) {
                        $_SESSION['is_logged_in'] = true;
                        $_SESSION['username'] = $username;
                        redirect("/");
                        exit;
                    } else {
                        echo mysqli_error($conn);
                    }
                }
            }
        }
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>Sign Up</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>

    <?php include "header.php"; ?>

    <h2>Sign up</h2>
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
        <div>
            <label for="password2">Confirm Password:</label>
            <br>
            <input type="password" name="password2" id="password2">
        </div>
        <br>
        <button type="submit">Sign Up</button>
        <button style="margin-left: 20px;" class="greenbutton" type="submit" name="login">Already have an account?
            Login</button>
    </form>
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?= $error; ?></p>
    <?php endif; ?>
</body>

</html>