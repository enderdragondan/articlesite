<?php

function isLoggedIn()
{
    return isset($_SESSION['is_logged_in']) && $_SESSION['is_logged_in'];
}

function isAdmin()
{
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'];
}