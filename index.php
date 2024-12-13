<?php

session_start();
require "url.php";
require "database.php";
require "get_articles.php";
require "auth.php";

$conn = getDB();

$articles = getAllArticles($conn);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new'])) {
    redirect("/new_article.php");
    exit;
}

?>

<!DOCTYPE html>
<html>

<head>
    <title>My Blog</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>

    <?php include "header.php"; ?>

    <?php if (isAdmin()): ?>
        <p>Logged in as admin user: <?= htmlspecialchars($_SESSION['username']) ?>. <a href="logout.php">Log out</a></p>
        <form method="post">
            <button class="greenbutton" type="submit" name="new">New Article</button>
        </form>
    <?php elseif (isLoggedIn()): ?>
        <p>Logged in as user: <?= htmlspecialchars($_SESSION['username']) ?>. <a href="logout.php">Log out</a></p>
        <form method="post">
            <button class="greenbutton" type="submit" name="new">New Article</button>
        </form>
    <?php else: ?>
        <p>You are not logged in. <a href="login.php">Log in</a></p>
    <?php endif; ?>

    <?php if (empty($articles)): ?>
        <p>No articles found.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($articles as $article): ?>
                <li>
                    <article>
                        <h2><a href="article.php?id=<?= $article['id']; ?>"><?= htmlspecialchars($article['title']) ?></a></h2>
                        <p><?= htmlspecialchars($article['content']) ?></p>
                    </article>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</body>

</html>