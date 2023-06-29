<?php
    session_start();

    // Check if user is logged in
    if (isset($_SESSION["username"])) {
        // User is logged in, destroy the session
        session_destroy();
    }

    // Redirect to the desired page
    header("Location: index.php");
    exit();
?>