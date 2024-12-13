<?php

session_start();

require "database.php";
require "get_articles.php";
require "url.php";
require 'auth.php';

if (!isLoggedIn()) {
    redirect("/");
    exit;
}

$conn = getDB();

if (isset($_GET['id'])) {
    $article = getArticle($conn, $_GET['id']);
    if ($article) {
        $id = $article['id'];
        $title = $article['title'];
        $content = $article['content'];
        $published_at = $article['published_at'];
    } else {
        echo "Article not found";
        exit;
    }
} else {
    echo "No article ID specified";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    if (isset($_POST['cancel'])) {
        redirect("/article.php?id={$id}");
        exit;
    }

    $error = '';
    if (empty($_POST['title']) && empty($_POST['content'])) {
        $error = "Title and content are required";
    } elseif (empty($_POST['title'])) {
        $error = "Title is required";
    } elseif (empty($_POST['content'])) {
        $error = "Content is required";
    }

    if (!empty($error)) {
        $failed = true;
    }

    $title = $_POST['title'];
    $content = $_POST['content'];
    $published_at = $_POST['published_at'];

    if ($published_at == '') {
        $published_at = null;
    }

    if (!$failed) {
        $sql = "UPDATE article
                SET title = ?, content = ?, published_at = ?
                WHERE id = ?";

        $stmt = mysqli_prepare($conn, $sql);

        if ($stmt === false) {
            echo mysqli_error($conn);
        } else {
            mysqli_stmt_bind_param($stmt, "sssi", $title, $content, $published_at, $id);

            if (mysqli_stmt_execute($stmt)) {
                redirect("/article.php?id={$id}");
                exit;
            } else {
                echo mysqli_error($conn);
            }
        }
    }
    $_POST = [];
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {

    $sql = "DELETE FROM article WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt === false) {
        echo mysqli_error($conn);
    } else {
        mysqli_stmt_bind_param($stmt, "i", $id);

        if (mysqli_stmt_execute($stmt)) {
            redirect("/");
            exit;
        } else {
            echo mysqli_error($conn);
        }
    }
    $_POST = [];
}


?>

<!DOCTYPE html>
<html>

<head>
    <title>Edit Article</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>

    <?php include "header.php"; ?>

    <h2>Edit article</h2>
    <form method="post">
        <input type="hidden" name="id" value="<?= $id; ?>">
        <div>
            <label for="title">Title:</label>
            <br>
            <input name="title" id="title" placeholder="Article title" value="<?= htmlspecialchars($title) ?>">
            <br><br>
        </div>
        <div>
            <label for="content">Content:</label>
            <br>
            <textarea name="content" rows="4" cols="40" id="content"
                placeholder="Article content"><?= htmlspecialchars($content) ?></textarea>
            <br><br>
        </div>
        <div>
            <label for="published_at">Publication date and time:</label>
            <br>
            <input type="datetime-local" name="published_at" id="published_at" value="<?= htmlspecialchars($published_at) ?>">
            <br><br>
        </div>
        <div>
            <button class="greenbutton" type="submit" name="submit">Save Changes</button>
            <button class="redbutton" type="submit" name="cancel">Cancel</button>
            <button style="margin-left: 70px;" type="submit" name="delete">Delete Article</button>
        </div>
    </form>
    <?php if (!empty($error)): ?>
        <p style="color: red;"><?= $error; ?></p>
    <?php endif; ?>
</body>

</html>