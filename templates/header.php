<?php
    session_start();
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Compiled and minified CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">

    <!-- Material Icons font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Kanit:wght@500&display=swap">
    <link rel="stylesheet" href="templates/styles.css">

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">

    <title>Chemical Webpage</title>

    <script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>

    <!-- Compiled and minified JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>

</head>

<body class="teal darken-4">

    <!-- Web navigation bar -->
    <nav class="nav-wrapper teal darken-3">
        <div class="container">
            <a href="index.php" class="brand-logo title-text flow-text">Chemicals Database</a>
            <a href="#" class="sidenav-trigger" data-target="mobile-links">
                <i class="material-icons">menu</i>
            </a>
            <ul id="nav-mobile" class="right hide-on-med-and-down">
                <li><a href="queries.php" class="flow-text waves-effect waves-dark">Make a query</a></li>
                <li><a href="add.php" class="flow-text waves-effect waves-dark">Add chemical</a></li>
                <li><a href="delete.php" class="flow-text waves-effect waves-dark">Delete chemical</a></li>
                <?php
                    // Check if user is logged in
                    if (isset($_SESSION["username"])) {

                        // User is logged in, display "Log out" button
                        ?>
                            <li><a href="logout.php" class="flow-text waves-effect waves-dark">Log out</a></li>
                        <?php
                    } else {

                        // User is not logged in, display "Login / Register" button
                        ?>
                            <li><a href="login.php" class="flow-text waves-effect waves-dark">Login / Register</a></li>
                        <?php
                    }
                ?>
            </ul>
        </div>
    </nav>

    <!-- Mobile navigation bar -->
    <ul class="sidenav teal" id="mobile-links">
        <li><a href="queries.php" class="waves-effect mt-2 top-li flow-text center waves-dark white-text">Make a query</a></li>
        <li><a href="add.php" class="waves-effect center flow-text waves-dark white-text">Add chemical</a></li>
        <li><a href="delete.php" class="waves-effect center flow-text waves-dark white-text">Delete chemical</a></li>
        <?php
            // Check if user is logged in
            if (isset($_SESSION["username"])) {
                // User is logged in, display "Log out" button
                echo '<li><a href="logout.php" class="flow-text waves-effect waves-dark">Log out</a></li>';
            } else {
                // User is not logged in, display "Log in/Register" button
                echo '<li><a href="login.php" class="flow-text waves-effect waves-dark">Log in / Register</a></li>';
            }
        ?>
    </ul>