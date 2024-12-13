<?php

session_start();
require "url.php";
require 'auth.php';

if (!isLoggedIn()) {
	redirect("/");
	exit;
}

$title = "";
$content = "";
$published_at = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	if (isset($_POST['cancel'])) {
		redirect("/");
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
		require './database.php';

		$conn = getDB();

		$sql = "INSERT INTO article (title, content, published_at) VALUES ('"
			. mysqli_escape_string($conn, $title) . "','"
			. mysqli_escape_string($conn, $content) . "','"
			. mysqli_escape_string($conn, $published_at) . "')";

		$results = mysqli_query($conn, $sql);

		if ($results === false) {
			echo mysqli_error($conn);
		} else {
			$id = mysqli_insert_id($conn);
			echo "Inserted record with ID: {$id} <a href='article.php?id={$id}'>(Click Here to View)</a>";
		}
	}
	$_POST = [];
}

?>

<!DOCTYPE html>
<html>

<head>
	<title>New article</title>
	<link rel="stylesheet" type="text/css" href="style.css">
</head>

<body>

	<?php include "header.php"; ?>

	<h2>New article</h2>
	<form method="post">
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
			<button class="greenbutton" type="submit">Add</button>
			<button class="redbutton" type="submit" name="cancel">Cancel</button>
		</div>
	</form>
	<?php if (!empty($error)): ?>
		<p style="color: red;"><?= $error ?></p>
	<?php endif; ?>
</body>

</html>