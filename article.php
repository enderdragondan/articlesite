<?php

session_start();

require "url.php";
require "database.php";
require "get_articles.php";
require "auth.php";

$conn = getDB();

$id = $_GET['id'] ?? null;

$article = getArticle($conn, $_GET['id']);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['return'])) {
    redirect("/");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) {
    redirect("/edit_article.php?id={$id}");
    exit;
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>
        <?php if ($article === null): ?>
            Article not found
        <?php else: ?>
            <?= htmlspecialchars($article['title']) ?>
        <?php endif; ?>
    </title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>

    <?php include "header.php"; ?>

    <form method="post">
        <button type="submit" name="return">Back to Articles</button>
    </form>

    <?php if ($article === null): ?>
        <p>No article found.</p>
    <?php else: ?>
        <article>
            <h2><?= htmlspecialchars($article['itle']); ?></h2>
            <p><?= htmlspecialchars($article['content']) ?></p>
        </article>
        <?php if (isLoggedIn()): ?>
            <form method="post">
                <button class="greenbutton" type="submit" name="edit">Edit Article</button>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</body>

</html>