<?php
    // connect to the database
    $dbhost = "localhost";
    $dbuser = "root";
    $dbpass = "";
    $dbname = "chem_stoff";

    $conn = mysqli_connect($dbhost, $dbuser, $dbpass, $dbname);

    // check connection to the database
    if(!$conn) {
        $status = "error " . mysqli_connect_error();
    }
    else {
        $status = "connected";
    }
?>
