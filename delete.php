<?php
    // include functions
    include_once("functions.php");

    // connect to the database
    include("templates/db_conn.php");

    include("templates/header.php");

    // check user role
    $userRole = "";

    if (isset($_SESSION["username"])) {

        // Get the user role from the session or the database
        $username = $_SESSION["username"];
        $userRole = getUserRole($conn, $username);
    }

    // Check if the user has admin role
    $isAdmin = ($userRole == "Admin");
    
    // check form input
    if(isset($_POST["submit"])) {
        $product = sanitizeText($_POST["product_id"]);

        // check product id
        if(empty($product)) {
            $errorProdID = "A product ID is required";
        } else {
            $errorProdID = "";
        }
    } 

    // check whether user confirms or cancels deletion
    if(isset($_POST["cancelation"])) {
        header("Location: delete.php");
    } else if(isset($_POST["confirmation"])) {
        $product = sanitizeText($_POST["product_id"]);
    } // end of form checks

    // Check if the user has admin role or display an error message
    if (!$isAdmin) {
        $errorProdID = "You are not authorized to delete a chemical";
    }
?>

<!DOCTYPE html>
<html lang="en">
    <section class="container teal lighten-3">

        <!-- Form to delete chemical -->
        <h4 class="center">Delete a chemical</h4>
        <form class="teal lighten-5" action="delete.php" method="POST">
            <h6 class="center">You can only delete a product using its ID</h6> 
            <div class="input-field">
                <input type="text" name="product_id" id="prod">
                <label for="prod">Product ID: </label>
            </div>
            <div class="center">
                <input class="btn teal accent-4" type="submit" name="submit" value="submit">
            </div>
        </form>
    </section>
    <div>
        <?php 
            // Checks for correct data submitted
            if(isset($_POST["submit"])) {

                // red box if no product ID was sent
                if(!empty($errorProdID)) {
                    ?>
                        <div class="container red warning lighten-3 valign-wrapper">
                            <div>
                                <i class="material-icons red-text text-darken-3">warning</i>
                                <p class="float-text"> <?php echo $errorProdID ?> </p>
                            </div>
                        </div>
                    <?php
                }

                // warning box if all ok, to ensure deletion
                if(empty($errorProdID)) {
                    ?>
                        <form class="amber accent-1" action="delete.php" method="POST">
                            <input type="hidden" name="product_id" value="<?php echo $product; ?>">
                            <h5 class="center warning-message">Warning</h5>
                            <h6 class="center warning-message">Are you sure you want to delete this product?</h6>
                            <div class="center">
                                <input class="btn amber darken-4" type="submit" name="confirmation" value="Confirm">
                                <input class="btn amber darken-4" type="submit" name="cancelation" value="Cancel">
                            </div>
                        </form>
                    <?php
                }
            } else if(isset($_POST["confirmation"])) {
                
                // delete product from the database
                mysqli_autocommit($conn, false);

                mysqli_begin_transaction($conn);

                try {
                    // prepare a statement to fetch internal product ID
                    $IDquery = "SELECT ID_NEW FROM internal_ids WHERE LOWER(ID_OLD) = LOWER(?)";
                    $stmt = mysqli_prepare($conn, $IDquery);

                    // bind given ID into the prepared statement
                    mysqli_stmt_bind_param($stmt, "s", $product);
                    mysqli_stmt_execute($stmt);

                    // bind new ID into a variable for future use
                    mysqli_stmt_store_result($stmt);
                    mysqli_stmt_bind_result($stmt, $internal_id);
                    mysqli_stmt_fetch($stmt);

                    mysqli_stmt_close($stmt);

                    // check if the product exists by looking at fetched ID
                    if(!$internal_id) {
                        throw new Exception("There is no product with this ID");
                    }

                    // delete the GHS data from the dangers table
                    $GHSQuery = "DELETE FROM dangers WHERE CHEMICAL_ID = ?";
                    $stmt = mysqli_prepare($conn, $GHSQuery);

                    mysqli_stmt_bind_param($stmt, "i", $internal_id);
                    mysqli_stmt_execute($stmt);

                    // check if GHS deletion was successful
                    if(mysqli_stmt_affected_rows($stmt) === 0) {
                        throw new Exception("There was a problem deleting the GHS values");
                    }

                    mysqli_stmt_close($stmt);

                    // delete the product from the SDB table
                    $SDBQuery = "DELETE FROM sdb WHERE CHEMICAL_ID = ?";
                    $stmt = mysqli_prepare($conn, $SDBQuery);

                    mysqli_stmt_bind_param($stmt, "i", $internal_id);
                    mysqli_stmt_execute($stmt);

                    // check if the SDB deletion was successful
                    if(mysqli_stmt_affected_rows($stmt) === 0) {
                        throw new Exception("There was a problem deleting the SDB data");
                    }

                    mysqli_stmt_close($stmt);

                    // delete the internal ID and given ID pair from the equivalence table
                    $pairQuery = "DELETE FROM internal_ids WHERE LOWER(ID_OLD) = LOWER(?)";
                    $stmt = mysqli_prepare($conn, $pairQuery);

                    mysqli_stmt_bind_param($stmt, "s", $product);
                    mysqli_stmt_execute($stmt);

                    // check if ID deletion was sucessful
                    if(mysqli_stmt_affected_rows($stmt) === 0) {
                        throw new Exception("There was a problem deleting the product");
                    }
                    
                    mysqli_stmt_close($stmt);

                    // delete the product from the chemicals table
                    $deletingQuery = "DELETE FROM chemicals WHERE ID_NEW = ?";
                    $stmt = mysqli_prepare($conn, $deletingQuery);

                    mysqli_stmt_bind_param($stmt, "i", $internal_id);
                    mysqli_stmt_execute($stmt);

                    // check if product deletion was successful
                    if (mysqli_stmt_affected_rows($stmt) === 0) {
                        throw new Exception("There is no product with this ID");
                    }

                    mysqli_stmt_close($stmt);

                    // commit the transaction
                    mysqli_commit($conn);

                    $deleteSuccessful = "Product deleted successfully";
                } catch(Exception $e) {

                    // Rollback the transaction
                    mysqli_rollback($conn);

                    // Store the error message
                    $error = $e -> getMessage();
                }

                mysqli_close($conn);

                // green box if everything was successfully deleted
                if(!empty($deleteSuccessful)) {
                    ?>
                        <div class="container green warning lighten-3 valign-wrapper">
                            <div>
                                <i class="material-icons green-text text-darken-3">done</i>
                                <p class="float-text"> <?php echo $deleteSuccessful ?> </p>
                            </div>
                        </div>
                    <?php
                } 
                
                // red box if the product doesn't exist or there was a problem with the deletion
                if(!empty($error)) {
                    ?>
                        <div class="container red warning lighten-3 valign-wrapper">
                            <div>
                                <i class="material-icons red-text text-darken-3">warning</i>
                                <p class="float-text"> <?php echo $error ?> </p>
                            </div>
                        </div>
                    <?php
                }
            }
        ?>
    </div>
    
    <?php include("templates/footer.php"); ?>
</html>