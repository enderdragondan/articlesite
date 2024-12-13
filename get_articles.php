<?php

function getArticle($conn, $id)
{
    $sql = "SELECT *
            FROM article
            WHERE id = ?";

    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) {
        echo mysqli_error($conn);
        return null;
    } else {
        mysqli_stmt_bind_param($stmt, "i", $id);

        if (mysqli_stmt_execute($stmt)) {
            $result = mysqli_stmt_get_result($stmt);
            return mysqli_fetch_array($result, MYSQLI_ASSOC);
        } else {
            echo mysqli_error($conn);
            return null;
        }
    }
}

function getAllArticles($conn)
{
    $sql = "SELECT *
            FROM article
            ORDER BY published_at DESC";

    $result = mysqli_query($conn, $sql);
    if ($result === false) {
        echo mysqli_error($conn);
        return null;
    } else {
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }
}