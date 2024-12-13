<?php

function redirect($path) {
    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
        $protocol = "https";
    } else {
        $protocol = "http";
    }

    header("Location: $protocol://" . $_SERVER['HTTP_HOST'] . "/articlesite" . $path);
}