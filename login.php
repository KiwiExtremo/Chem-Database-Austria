<?php
    // include functions
    include_once("functions.php");
    
    // connect to the database
    include("templates/db_conn.php");

    include("templates/header.php");

    // Define variables
    $loginError = "";
    $registerError = "";
    $successful = "";

    // Check if user is logged in
    if (isset($_SESSION["username"])) {

        // User is logged in, redirect to home page
        header("Location: index.php");
        exit(); // Stop executing the rest of the page
    }

    // Handle login form data
    if (isset($_POST["login"])) {
        $username = $_POST["username"]; 
        $password = $_POST["password"];

        // Prepare a statement to log the user in
        $logUserData = "SELECT Username, Passwords, Role_Name FROM users u JOIN roles r ON u.Role = r.Role_Id WHERE LOWER(Username) = LOWER(?)";
        $stmt = mysqli_prepare($conn, $logUserData);
        
        // bind username to the prepared statement
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if(mysqli_num_rows($result) == 1) {
            $row = mysqli_fetch_assoc($result);
            $storedUsername = $row['Username'];
            $storedPassword = $row['Passwords'];
            $role = $row["Role_Name"];

            // compare passwords
            if(password_verify($password, $storedPassword)) {

                // Username and password are valids
                $successful = "Sucessfully logged in";
                $_SESSION["username"] = $storedUsername;
                $_SESSION["role"] = $role;

                session_regenerate_id(true);
            } else {
                // User and/or password are incorrect
                $loginError = "Wrong username or password";
            }
        } else {
            // User not found
            $loginError = "Wrong username or password";
        }
        mysqli_stmt_close($stmt);
    }

    // Handle register form data
    if(isset($_POST["register"])) {
        $newUsername = $_POST["newusername"];
        $newPassword = $_POST["newpassword"];

        // Check invalid username format
        if (!preg_match('/^[a-zA-Z0-9]+$/', $newUsername)) {
            $registerError = "Invalid characters. You can only use letters and numbers on the username";
        } else {
        
            // Ensure there isn't a user with the given username
            $existingUser = "SELECT Username FROM users WHERE LOWER(Username) = LOWER(?)";
            $stmt = mysqli_prepare($conn, $existingUser);

            mysqli_stmt_bind_param($stmt, "s", $newUsername);
            mysqli_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);

            // Check if there is any user with given username
            if(mysqli_num_rows($result) == 0) {
                mysqli_stmt_close($stmt);

                // Prepare a statement to insert a new user
                $newUserData = "INSERT INTO users (Username, Passwords) VALUES (?, ?)";
                $stmt = mysqli_prepare($conn, $newUserData);

                // hash password
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                // bind given username and password to the prepared statement
                mysqli_stmt_bind_param($stmt, "ss", $newUsername, $hashedPassword);
            
                if(mysqli_stmt_execute($stmt)) {
                    $successful = "Successfully registered";
                } else {
                    $registerError = "Error in registration";
                }
            } else {
                $registerError = "This username is already in use";
            }

            mysqli_stmt_close($stmt);
        }
    }

    mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
    <section class="container teal lighten-3">

        <!-- Tabs -->
        <div class="row">
            <ul class="tabs">
                <li class="tab col s6 l2 offset-l4">
                    <a href="#loginform">Log in</a>
                </li>
                <li class="tab col s6 l2">
                    <a href="#registerform">Register</a>
                </li>
            </ul>
        </div>

        <!-- Login form -->
        <form class="teal lighten-5" action="login.php" method="POST" id="loginform">
            
            <div class="input-field">
                <input type="text" name="username" id="username">
                <label for="username">Username: </label>
            </div>

            <div class="divider transparent"></div>

            <div class="input-field">
                <input type="password" name="password" id="password" required>
                <label for="password">Password: </label>
            </div>

            <div class="center">
                <input class="btn teal accent-4" type="submit" name="login" value="Log in">
            </div>
        </form>

        <!-- Register form -->
        <form class="teal lighten-5" action="login.php" method="POST" id="registerform">
            <div class="input-field">
                <input type="text" name="newusername" id="newusername">
                <label for="newusername">Username: </label>
            </div>

            <div class="divider transparent"></div>

            <div class="input-field">
                <input type="password" name="newpassword" id="newpassword" required>
                <label for="newpassword">Password: </label>
            </div>

            <div class="center">
                <input class="btn teal accent-4" type="submit" name="register" value="Register">
            </div>
        </form>
    </section>
    <div>
        <?php 
            // Checks for data submitted
            if(isset($_POST["login"]) || isset($_POST["register"])) {

                // red box if logging error
                if(!empty($loginError)) {
                    ?>
                        <div class="container red warning lighten-3 valign-wrapper">
                            <div>
                                <i class="material-icons red-text text-darken-3">warning</i>
                                <p class="float-text"> <?php echo $loginError ?> </p>
                            </div>
                        </div>
                    <?php
                }

                // red box if registering error
                if(!empty($registerError)) {
                    ?>
                        <div class="container red warning lighten-3 valign-wrapper">
                            <div>
                                <i class="material-icons red-text text-darken-3">warning</i>
                                <p class="float-text"> <?php echo $registerError ?> </p>
                            </div>
                        </div>
                    <?php
                } else if(empty($loginError)) {
                    // green box if either log in or register was successful
                    ?>
                        <div class="container green warning lighten-3 valign-wrapper">
                            <div>
                                <i class="material-icons green-text text-darken-3">done</i>
                                <p class="float-text"> <?php echo $successful; ?> </p>
                            </div>
                        </div>
                    <?php
                }
            }
        ?>
    </div>
    
<?php include("templates/footer.php"); ?>

</html>