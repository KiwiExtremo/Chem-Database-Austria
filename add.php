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

    // Check if the user has admin role or display an error message
    if (!$isAdmin) {
        $error = "You are not authorized to add a new chemical";
    } else {
        // check form inputs
        if(isset($_POST["submit"])) {

            // insert into array all GHS values
            $GHSselects = array(
                "corrosion" => isset($_POST["corrosion"]) ? $_POST["corrosion"] : "",
                "environment" => isset($_POST["environment"]) ? $_POST["environment"] : "",
                "exclamation" => isset($_POST["exclamation"]) ? $_POST["exclamation"] : "",
                "flame" => isset($_POST["flame"]) ? $_POST["flame"] : "",
                "flame_circle" => isset($_POST["flame_circle"]) ? $_POST["flame_circle"] : "",
                "gas" => isset($_POST["gas"]) ? $_POST["gas"] : "",
                "hazard" => isset($_POST["hazard"]) ? $_POST["hazard"] : "",
                "skull" => isset($_POST["skull"]) ? $_POST["skull"] : ""         
            );

            // sanitize all inputs from the user
            $product = sanitizeText($_POST["product_name"]);
            $manufacturer = sanitizeText($_POST["manufacturer_name"]);
            $storage = sanitizeText($_POST["storage_name"]);
            $SDBlink = sanitizeLink($_POST["sdb_link"]);

            $sanGHSselects = array();
            foreach ($GHSselects as $name => $value) {
                $sanitizedValue = sanitizeText($value);
                $sanGHSselects[$name] = $sanitizedValue;
            }

            $ID = sanitizeText($_POST["old_id"]);
            $SDBdate = sanitizeDate($_POST["sdb_date"]);
            $manlink = sanitizeLink($_POST["man_link"]);
            $manlink2 = sanitizeLink($_POST["man_link2"]);

            // check if product name already exists
            $queryChem = "SELECT CHEMICAL_NAME FROM chemicals WHERE LOWER(CHEMICAL_NAME) = LOWER(?)";
            $stmtProd = mysqli_prepare($conn, $queryChem);

            mysqli_stmt_bind_param($stmtProd, "s", $product);
            mysqli_stmt_execute($stmtProd);

            $chemResult = mysqli_stmt_get_result($stmtProd);

            if (mysqli_num_rows($chemResult) > 0) {
                $chemExists = true;
            } else {
                $chemExists = false;
            }
            mysqli_stmt_close($stmtProd);

            // check if manufacturer name already exists
            $queryManu = "SELECT MANUFACTURER_ID FROM manufacturer WHERE LOWER(MANUFACTURER_NAME) = LOWER(?)";
            $stmtManu = mysqli_prepare($conn, $queryManu);

            mysqli_stmt_bind_param($stmtManu, "s", $manufacturer);
            mysqli_stmt_execute($stmtManu);

            $manuResult = mysqli_stmt_get_result($stmtManu);

            if(mysqli_num_rows($manuResult) > 0) {
                $manuExists = true;
            } else {
                $manuExists = false;
            }
            mysqli_stmt_close($stmtManu);

            // check if the product-manufacturer pair already exists
            if($chemExists && $manuExists) {
                $error = "This product is already being manufactured by this fabricant";
            } else {
            // continue checking form data
                            
                // check each value from the GHS array
                $allValuesOk = true;
                
                foreach($sanGHSselects as $GHSselect => $GHSValue) {
                    $GHSValue = trim($GHSValue);

                    if(empty($GHSValue) || !in_array($GHSValue, ["nicht", "ach", "gef", "entfällt"])) {
                        $allValuesOk = false;
                        break;
                    }
                }

                if($allValuesOk) {
                    $error = "";

                    // keep checking form data

                    // check date
                    if(empty($SDBdate)) {
                        $error = "The date is not valid";
                    } else {

                        // further checking form data

                        // create the new manufacturer if needed
                        if(!$manuExists) {
                            $newManu = "INSERT INTO manufacturer (MANUFACTURER_NAME, LINK, LINK_2) VALUES (?, ?, ?)";
                            $stmtNewManu = mysqli_prepare($conn, $newManu);

                            mysqli_stmt_bind_param($stmtNewManu, "sss", $manufacturer, $manlink, $manlink2);
                            mysqli_stmt_execute($stmtNewManu);

                            // check if insertion was successfull
                            if (mysqli_stmt_affected_rows($stmtNewManu) === 1) {
                                $addedManu = "New anufacturer added correctly";
                            } else {
                                $addedManu = "";
                            }

                            // retrieve newly created manufacturer ID
                            $manufacturerID = mysqli_insert_id($conn);

                            mysqli_stmt_close($stmtNewManu);
                        } else {

                            // fetch the manufacturer ID from the checking query
                            $fetchedManuRow = mysqli_fetch_assoc($manuResult);
                            $manufacturerID = $fetchedManuRow["MANUFACTURER_ID"];
                        }

                        // check if storage name already exists
                        $queryStor = "SELECT STORAGE_ID FROM storage WHERE LOWER(STORAGE_NAME) = LOWER(?)";
                        $stmtStor = mysqli_prepare($conn, $queryStor);

                        mysqli_stmt_bind_param($stmtStor, "s", $storage);
                        mysqli_stmt_execute($stmtStor);

                        $storResult = mysqli_stmt_get_result($stmtStor);

                        if(mysqli_num_rows($storResult) > 0) {
                            $storExists = true;
                        } else {
                            $storExists = false;
                        }

                        mysqli_stmt_close($stmtStor);

                        // create the new storage if needed
                        if(!$storExists) {
                            $newStor = "INSERT INTO storage (STORAGE_NAME) VALUES (?)";
                            $stmtNewStor = mysqli_prepare($conn, $newStor);

                            mysqli_stmt_bind_param($stmtNewStor, "s", $storage);
                            mysqli_stmt_execute($stmtNewStor);

                            // check if insertion was successfull
                            if (mysqli_stmt_affected_rows($stmtNewStor) === 1) {
                                $addedStor = "New storage added correctly";
                            } else {
                                $addedStor = "";
                            }

                            // retrieve newly created storage ID
                            $storageID = mysqli_insert_id($conn);

                            mysqli_stmt_close($stmtNewStor);
                        } else {

                            // fetch the storage ID from the checking query
                            $fetchedStorRow = mysqli_fetch_assoc($storResult);
                            $storageID = $fetchedStorRow["STORAGE_ID"];
                        }

                        // check whether the link is an URL or simple text
                        if(isValidURL($SDBlink)) {
                            $checkedSDBlink = "<a href='" . $SDBlink . "' target='_blank'>" . $SDBlink . "</a>";
                        } else {
                            $checkedSDBlink = $SDBlink;
                        }
                    }
                } else {
                    $error = "There is 1 or more not accepted GHS values";
                }

                // ID given doesn't need any checks, other than sanitizing it
            }    
        } // end of form checks
    }

    // make query if all inputs are ok
    if(isset($_POST["submit"])) {
        if(!$error) {
            mysqli_autocommit($conn, false);

            mysqli_begin_transaction($conn);

            try {

                // insert product data into chemicals table
                $insertChem = "INSERT INTO chemicals (`CHEMICAL_NAME`, `MANUFACTURER_ID`, `STORAGE_ID`) VALUES (?, ?, ?)";
                $stmtInsert = mysqli_prepare($conn, $insertChem);

                mysqli_stmt_bind_param($stmtInsert, "sii", $product, $manufacturerID, $storageID);
                mysqli_stmt_execute($stmtInsert);

                // check if the product insertion was sucessful
                if(mysqli_stmt_affected_rows($stmtInsert) === 0) {
                    throw new Exception("There was a problem adding the product");
                }
                
                // fetch new product ID
                $productID = mysqli_insert_id($conn);

                mysqli_stmt_close($stmtInsert);

                // insert all GHS values into dangers table
                $insertGHS = "INSERT INTO dangers VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmtGHS = mysqli_prepare($conn, $insertGHS);

                mysqli_stmt_bind_param($stmtGHS, "issssssss", $productID,
                    $GHSselects["corrosion"],
                    $GHSselects["environment"],
                    $GHSselects["exclamation"],
                    $GHSselects["flame"],
                    $GHSselects["flame_circle"],
                    $GHSselects["gas"],
                    $GHSselects["hazard"],
                    $GHSselects["skull"]
                );

                mysqli_stmt_execute($stmtGHS);
                
                // check if the GHS insertion was successful
                if(mysqli_stmt_affected_rows($stmtGHS) === 0) {
                    throw new Exception("There was a problem adding the GHS values");
                }

                mysqli_stmt_close($stmtGHS);

                // insert ID given into internal_ids table
                $insertOldID = "INSERT INTO internal_ids VALUES (?, ?)";
                $stmtOldID = mysqli_prepare($conn, $insertOldID);

                mysqli_stmt_bind_param($stmtOldID, "is", $productID, $ID);
                mysqli_execute($stmtOldID);

                // check if the ID insertion was successful
                if(mysqli_stmt_affected_rows($stmtOldID) === 0) {
                    throw new Exception("There was a problem adding the ID - Nummer");
                }

                mysqli_stmt_close($stmtOldID);

                // insert data into SDB table
                $insertSDB = "INSERT INTO sdb VALUES (?, ?, ?)";
                $stmtSDB = mysqli_prepare($conn, $insertSDB);

                mysqli_stmt_bind_param($stmtSDB, "iss", $productID, $SDBdate, $checkedSDBlink);
                mysqli_execute($stmtSDB);

                // check if SDB insertion was successful
                if(mysqli_stmt_affected_rows($stmtSDB) === 0) {
                    throw new Exception("There was a problem adding the SDB data");
                }

                mysqli_stmt_close($stmtSDB);

                // Commit the transacion
                mysqli_commit($conn);

                $insertSuccesful = "All data was inserted successfully";
            } catch(Exception $e) {

                // Rollback the transaction
                mysqli_rollback($conn);

                // Store the error message
                $error = $e -> getMessage();
            }
        }

        mysqli_close($conn);
    }
?>

<!DOCTYPE html>
<html lang="en">
    <section class="container teal lighten-3">

        <!-- Form for new chemical -->
        <h4 class="center">Add a new chemical</h4>
        <form class="teal lighten-5" action="add.php" method="POST">
            <div class="input-field">
                <input type="text" name="product_name" id="prod" required/>
                <label for="prod">Stoffname: </label>
            </div>

            <div class="divider transparent"></div>

            <div class="input-field">
                <input type="text" name="manufacturer_name" id="manu" placeholder="Check correct spelling" required/>
                <label for="manu">Hersteller / Lieferant: </label>
            </div>

            <div class="divider transparent"></div>

            <div class="input-field">
                <input type="text" name="storage_name" id="stor" placeholder="Check correct spelling" required/>
                <label for="stor">Lageront: </label>
            </div>

            <div class="divider transparent"></div>

            <div class="input-field">
                <input type="text" name="sdb_link" id="sdblink" required/>
                <label for="sdblink">SDB - Link: </label>
            </div>

            <ul class="collapsible">
                <li>
                    <div class="collapsible-header">Kennzeichnung GHS (required)</div>
                    <div class="collapsible-body">
                        <div class="row">
                            <div class="input-field">
                                <div class="col s2">
                                    <img src="danger_img/FLAME.png" alt="Flame" class="form_img">
                                </div>
                                <div class="col s10">
                                    <select name="flame" id="flame" required>
                                        <option value="" disabled selected> - Select an option -</option>
                                        <option value="nicht">Nicht</option>
                                        <option value="ach">Ach</option>
                                        <option value="gef">Gef</option>
                                        <option value="entfällt">Entfällt</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="divider transparent"></div>

                        <div class="row">
                            <div class="input-field">
                                <div class="col s2">
                                <img src="danger_img/FLAME_OVER_CIRCLE.png" alt="Flame over circle" class="form_img">
                                </div>
                                <div class="col s10">
                                    <select name="flame_circle" id="flame_circle">
                                        <option value="" disabled selected> - Select an option -</option>
                                        <option value="nicht">Nicht</option>
                                        <option value="ach">Ach</option>
                                        <option value="gef">Gef</option>
                                        <option value="entfällt">Entfällt</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                  
                        <div class="divider transparent"></div>

                        <div class="row">
                            <div class="input-field">
                                <div class="col s2">
                                <img src="danger_img/CORROSION.png" alt="Corrosion" class="form_img">
                                </div>
                                <div class="col s10">
                                    <select name="corrosion" id="corrosion">
                                        <option value="" disabled selected> - Select an option -</option>
                                        <option value="nicht">Nicht</option>
                                        <option value="ach">Ach</option>
                                        <option value="gef">Gef</option>
                                        <option value="entfällt">Entfällt</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="divider transparent"></div>

                        <div class="row">
                            <div class="input-field">
                                <div class="col s2">
                                <img src="danger_img/GAS_CYLINDER.png" alt="Gas cylinder" class="form_img">
                                </div>
                                <div class="col s10">
                                    <select name="gas" id="gas">
                                        <option value="" disabled selected> - Select an option -</option>
                                        <option value="nicht">Nicht</option>
                                        <option value="ach">Ach</option>
                                        <option value="gef">Gef</option>
                                        <option value="entfällt">Entfällt</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                                            
                        <div class="divider transparent"></div>

                        <div class="row">
                            <div class="input-field">
                                <div class="col s2">
                                <img src="danger_img/SKULL_AND_CROSSBONES.png" alt="Skull and crossbones" class="form_img">
                                </div>
                                <div class="col s10">
                                    <select name="skull" id="skull">
                                        <option value="" disabled selected> - Select an option -</option>
                                        <option value="nicht">Nicht</option>
                                        <option value="ach">Ach</option>
                                        <option value="gef">Gef</option>
                                        <option value="entfällt">Entfällt</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                                            
                        <div class="divider transparent"></div>

                        <div class="row">
                            <div class="input-field">
                                <div class="col s2">
                                <img src="danger_img/EXCLAMATION_MARK.png" alt="Exclamation mark" class="form_img">
                                </div>
                                <div class="col s10">
                                    <select name="exclamation" id="exclamation">
                                        <option value="" disabled selected> - Select an option -</option>
                                        <option value="nicht">Nicht</option>
                                        <option value="ach">Ach</option>
                                        <option value="gef">Gef</option>
                                        <option value="entfällt">Entfällt</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                                            
                        <div class="divider transparent"></div>

                        <div class="row">
                            <div class="input-field">
                                <div class="col s2">
                                <img src="danger_img/HEALTH_HAZARD.png" alt="Health hazard" class="form_img">
                                </div>
                                <div class="col s10">
                                    <select name="hazard" id="hazard">
                                        <option value="" disabled selected> - Select an option -</option>
                                        <option value="nicht">Nicht</option>
                                        <option value="ach">Ach</option>
                                        <option value="gef">Gef</option>
                                        <option value="entfällt">Entfällt</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                                            
                        <div class="divider transparent"></div>

                        <div class="row">
                            <div class="input-field">
                                <div class="col s2">
                                <img src="danger_img/ENVIRONMENT.png" alt="Environment" class="form_img">
                                </div>
                                <div class="col s10">
                                    <select name="environment" id="environment">
                                        <option value="" disabled selected> - Select an option -</option>
                                        <option value="nicht">Nicht</option>
                                        <option value="ach">Ach</option>
                                        <option value="gef">Gef</option>
                                        <option value="entfällt">Entfällt</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li>
                    <div class="collapsible-header">Optional information</div>
                    <div class="collapsible-body">
                        <div class="input-field">
                            <input type="text" name="old_id" id="old_id">
                            <label for="old_id">ID - Nummer: </label>
                        </div>

                        <div class="divider transparent"></div>

                        <div class="input-field">
                            <input type="date" name="sdb_date" id="sdbdate">
                            <label for="sdbdate">SDB - Datum: </label>
                        </div>

                        <div class="divider transparent"></div>

                        <div class="input-field">
                            <input type="text" name="man_link" id="manlink" placeholder="Fill only for a new manufacturer">
                            <label for="manlink">Hersteller link: </label>
                        </div>

                        <div class="divider transparent"></div>

                        <div class="input-field">
                            <input type="text" name="man_link2" id="manlink2" placeholder="Fill only for a new manufacturer">
                            <label for="manlink2">Hersteller link (2): </label>
                        </div>
                    </div>
                </li>
            </ul>

            <div class="center">
                <input class="btn teal accent-4" type="submit" name="submit" value="submit">
            </div>
        </form>
    </section>
    <div>
        <?php 
            // Checks for correct data submitted
            if(isset($_POST["submit"])) {
                
                // red box if there is a problem
                if(!empty($error)) {
                    ?>
                        <div class="container warning red lighten-3 valign-wrapper">
                            <div>
                                <i class="material-icons red-text text-darken-3">warning</i>
                                <p class="float-text"> <?php echo $error ?> </p>
                            </div>
                        </div>
                    <?php
                }

                // green box if manufacturer was correctly inserted
                if(!empty($addedManu)) {
                    ?>
                        <div class="container green warning lighten-3 valign-wrapper">
                            <div>
                                <i class="material-icons green-text text-darken-3">done</i>
                                <p class="float-text"> <?php echo $addedManu ?> </p>
                            </div>
                        </div>
                    <?php
                }

                // green box if storage was correctly inserted
                if(!empty($addedStor)) {
                    ?>
                        <div class="container green warning lighten-3 valign-wrapper">
                            <div>
                                <i class="material-icons green-text text-darken-3">done</i>
                                <p class="float-text"> <?php echo $addedStor ?> </p>
                            </div>
                        </div>
                    <?php
                }

                // green box if product was correctly inserted
                if(!empty($insertSuccesful)) {
                    ?>
                        <div class="container green warning lighten-3 valign-wrapper">
                            <div>
                                <i class="material-icons green-text text-darken-3">done</i>
                                <p class="float-text"> <?php echo $insertSuccesful ?> </p>
                            </div>
                        </div>
                    <?php
                }
            }
        ?>
    </div>
    
    <?php include("templates/footer.php"); ?>
</html>