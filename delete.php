<?php
    // connect to the database
    include("templates/db_conn.php");

    // check form input
    if(isset($_POST["submit"])) {
        $product = htmlspecialchars($_POST["product_id"]);

        // check product id
        if(empty($product)) {
            $errorProd = "A product ID is required";
        } else {
            $errorProd = "";
        }
    } 

    // check whether user confirms or cancels deletion
    if(isset($_POST["cancelation"])) {
        header("Location: delete.php");
    } else if($_POST["confirmation"]) {
        $product = $_POST["product_id"];
    } // end of form checks

?>

<!DOCTYPE html>
<html lang="en">
    <?php include("templates/header.php"); ?>

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
    
    <?php 
        // Checks for correct data submitted
        if(isset($_POST["submit"])) {

            // red box if no product name was sent
            if(!empty($errorProd)) {
                ?>
                    <div class="container red warning lighten-3 valign-wrapper">
                        <div>
                            <i class="material-icons red-text text-darken-3">warning</i>
                            <p class="float-text"> <?php echo $errorProd ?> </p>
                        </div>
                    </div>
                <?php
            }

            // warning box if all ok, to ensure deletion
            if(empty($errorProd)) {
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
                // prepare a statement to fetch internal product ID
                $IDquery = "SELECT ID_NEW FROM id_equivalent WHERE ID_OLD = ?";
                $stmt = mysqli_prepare($conn, $IDquery);

                // bind given ID into the prepared statement
                mysqli_stmt_bind_param($stmt, "s", $product);
                mysqli_stmt_execute($stmt);

                // bind new ID into a variable for future use
                mysqli_stmt_store_result($stmt);
                mysqli_stmt_bind_result($stmt, $internal_id);
                mysqli_stmt_fetch($stmt);

                mysqli_stmt_close($stmt);

                // preare a statement to delete the product using its internal ID
                $deletingQuery = "DELETE FROM chemicals WHERE ID_NEW = ?";
                $stmt = mysqli_prepare($conn, $deletingQuery);

                mysqli_stmt_bind_param($stmt, "i", $internal_id);
                mysqli_stmt_execute($stmt);

                // check if deletion was successfull
                if (mysqli_stmt_affected_rows($stmt) === 1) {
                    $deleted = "Product deleted correctly";
                } else {
                    $deleted = "";
                }

                mysqli_stmt_close($stmt);

                // delete the internal ID and given ID pair from the equivalence table
                $pairQuery = "DELETE FROM id_equivalent WHERE ID_OLD = ?";
                $stmt = mysqli_prepare($conn, $pairQuery);

                mysqli_stmt_bind_param($stmt, "s", $product);
                mysqli_stmt_execute($stmt);

                mysqli_stmt_close($stmt);
                mysqli_close($conn);

            // green box if chemical was correctly deleted
            if(!empty($deleted)) {
                ?>
                    <div class="container green warning lighten-3 valign-wrapper">
                        <div>
                            <i class="material-icons green-text text-darken-3">done</i>
                            <p class="float-text"> <?php echo $deleted ?> </p>
                        </div>
                    </div>
                <?php
            } else {
                // red box if there is no product to delete
                ?>
                    <div class="container red warning lighten-3 valign-wrapper">
                        <div>
                            <i class="material-icons red-text text-darken-3">warning</i>
                            <p class="float-text"> <?php echo 'There is no product with this ID' ?> </p>
                        </div>
                    </div>
                <?php
            }
        }
    ?>

    <?php include("templates/footer.php"); ?>
</html>