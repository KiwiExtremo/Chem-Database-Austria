<?php
    // include functions
    include_once("functions.php");
    
    // connect to the database
    include("templates/db_conn.php");

    // check form inputs
    if(isset($_POST["submit"])) {

        $product = sanitizeText($_POST["product_name"]);
        $product = strtolower($product);

        $manufacturer = sanitizeText($_POST["manufacturer_name"]);
        $manufacturer = strtolower($manufacturer);

        $storage = sanitizeText($_POST["storage_name"]);
        $storage = strtolower($storage);

        $old_id = sanitizeText($_POST["old_id"]);
        $old_id = strtolower($old_id);
    }
?>
<!DOCTYPE html>
<html lang="en">
    <?php include("templates/header.php"); ?>

    <section class="container teal lighten-3">
    <?php
        // Check the variables and query them
        if(isset($_POST["submit"])) {

            $search = false;

            $baseQuery = "SELECT CHEMICAL_NAME AS 'STOFFNAME', MANUFACTURER_NAME AS 'HERSTELLER / LIEFERANT', i.ID_OLD AS 'ID - NUMMER', DATE AS 'SDB - DATUM', STORAGE_NAME AS 'LAGERONT', 
            FLAME, FLAME_OVER_CIRCLE, CORROSION, GAS_CYLINDER, SKULL_AND_CROSSBONES, EXCLAMATION_MARK, HEALTH_HAZARD, ENVIRONMENT, s.LINK AS 'SDB - LINK'
            FROM chemicals c JOIN manufacturer m 
            ON c.MANUFACTURER_ID = m.MANUFACTURER_ID 
            JOIN internal_ids i 
            ON c.ID_NEW = i.ID_NEW
            JOIN sdb s
            ON c.ID_NEW = s.CHEMICAL_ID
            JOIN storage st
            ON c.STORAGE_ID = st.STORAGE_ID
            JOIN dangers d
            ON c.ID_NEW = d.CHEMICAL_ID";

            // check if there was a product name given and add it to the query
            if(!empty($product)) {
                $baseQuery .= " WHERE LOWER(CHEMICAL_NAME) LIKE CONCAT('%', ?, '%')";
                $search = true;
            }

            // check if there was a manufacturer name given and add it to the query
            if(!empty($manufacturer) && $search) {
                $baseQuery .= " AND LOWER(MANUFACTURER_NAME) LIKE CONCAT('%', ?, '%')";
            } else if(!empty($manufacturer)) {
                $baseQuery .= " WHERE LOWER(MANUFACTURER_NAME) LIKE CONCAT('%', ?, '%')";
                $search = true;
            }

            // check if there was a storage name given and add it to the query
            if(!empty($storage) && $search) {
                $baseQuery .= " AND LOWER(STORAGE_NAME) LIKE CONCAT('%', ?, '%')";
            } else if(!empty($storage)) {
                $baseQuery .= " WHERE LOWER(STORAGE_NAME) LIKE CONCAT('%', ?, '%')";
                $search = true;
            }

            // check if there was a old_id given and add it to the query
            if(!empty($old_id) && $search) {
                $baseQuery .= " AND LOWER(ID_OLD) = ?";
            } else if(!empty($old_id)) {
                $baseQuery .= " WHERE LOWER(ID_OLD) = ?";
                $search = true;
            }

            // check that there is at least 1 camp given
            if($search) {
                $baseQuery .= ";";

                $stmt = mysqli_prepare($conn, $baseQuery);

                // Prepare an array to store the parameters and their corresponding types
                $params = array();
                $types = "";

                // Bind the parameters based on their values
                if (!empty($product)) {
                    $params[] = &$product;
                    $types .= "s";
                }

                if (!empty($manufacturer)) {
                    $params[] = &$manufacturer;
                    $types .= "s";
                }

                if (!empty($storage)) {
                    $params[] = &$storage;
                    $types .= "s";
                }

                if (!empty($old_id)) {
                    $params[] = &$old_id;
                    $types .= "s";
                }

                // Check if any parameters were provided
                if (!empty($params)) {

                    // Prepend the types string with the parameter count
                    $bindParams = array_merge(array($stmt, $types), $params);

                    // Call mysqli_stmt_bind_param with the dynamically generated parameters
                    call_user_func_array('mysqli_stmt_bind_param', $bindParams);
                }

                mysqli_stmt_execute($stmt);

                $result = mysqli_stmt_get_result($stmt);
                
                if(mysqli_num_rows($result) > 0) {

                    ?>  
                        <h4 class="center">Query result</h4>
                        <div class="container teal lighten-5">
                            <div class="table-wrapper">
                    <?php

                    // start the html table
                    echo "<table>";

                    $columns = array();

                    while($row = mysqli_fetch_assoc($result)) {
                        foreach($row as $columna => $value) {
                            $columns[] = $columna;
                        }

                        break;
                    }

                    // make the heading of the table
                    echo "<thead class='sticky-header'><tr>";

                    $firstColumn = true;

                    foreach($columns as $columna) {

                        // make the header sticky, and the first cell doubly sticky
                        if ($columna === 'STOFFNAME') {
                            if ($firstColumn) {
                                echo "<th class='sticky-column-header'>$columna</th>";
                                $firstColumn = false;
                            } else {
                                echo "<th>$columna</th>";
                            }
                        
                        // insert pictograms instead of text for the danger classes
                        } else if (in_array($columna, ['FLAME', 'FLAME_OVER_CIRCLE', 'CORROSION', 'GAS_CYLINDER', 'SKULL_AND_CROSSBONES', 'EXCLAMATION_MARK', 'HEALTH_HAZARD', 'ENVIRONMENT'])) {
                            echo "<th><img src='danger_img/$columna.png' alt='$columna' class='image'/></th>";
                        } else {
                            echo "<th>$columna</th>";
                        }
                    }

                    echo "</tr></thead>";

                    echo "<tbody>"; 

                    // create each row with the query results
                    mysqli_data_seek($result, 0);
                    while($row = mysqli_fetch_assoc($result)) {
                        echo "<tr>";

                        $firstColumn = true;
                        
                        foreach($row as $columna => $value) {
                            
                            // make the first column sticky
                            if ($columna === 'STOFFNAME') {
                                echo "<td class='sticky-column'>$value</td>";
                            } else if (in_array($columna, ['FLAME', 'FLAME_OVER_CIRCLE', 'CORROSION', 'GAS_CYLINDER', 'SKULL_AND_CROSSBONES', 'EXCLAMATION_MARK', 'HEALTH_HAZARD', 'ENVIRONMENT'])) {
                                
                                // Apply different background color classes based on the cell value
                                $bgClass = '';
                                switch ($value) {
                                    case 'nicht':
                                        $bgClass = 'red-bg';
                                        break;
                                    case 'ach':
                                        $bgClass = 'yellow-bg';
                                        break;
                                    case 'gef':
                                        $bgClass = 'green-bg';
                                        break;
                                    case 'entfällt':
                                        $bgClass = 'blue-bg';
                                        break;
                                    default:
                                        $bgClass = '';
                                        break;
                                }

                                // Add the CSS class to the cell
                                echo "<td class='centered-text table-cell $bgClass'>$value</td>";
                            } else {
                            echo "<td>$value</td>";
                            }
                        }

                        echo "</tr>";
                    }

                    echo "</tbody></table>";

                    ?>
                            </div>
                        </div>
                    <?php
                    // Display "New Query" button
                    ?>
                        <div class="center">
                            <a class="btn teal accent-4 warning" href="queries.php">New Query</a>
                        </div>
                    <?php
                } else {
                    $error = "There are no results that match the search criteria";
                }

                // free the result of the query from the memory
                mysqli_free_result($result);
            } else {
                $error = "Please fill in at least one field";
            }

            // show form if there is an error with the query
            if(!empty($error)) {
                ?>
                    <h4 class="center">Query maker</h4>
                    <form class="teal lighten-5" action="queries.php" method="POST">
                        <h6 class="center">Fill in one or more fields</h6>
                        <div class="input-field">
                            <input type="text" name="product_name" id="prod"/>
                            <label for="prod">Stoffname: </label>
                        </div>

                        <div class="divider transparent"></div>

                        <div class="input-field">
                            <input type="text" name="manufacturer_name" id="manu" placeholder="Check correct spelling"/>
                            <label for="manu">Hersteller / Lieferant: </label>
                        </div>

                        <div class="divider transparent"></div>

                        <div class="input-field">
                            <input type="text" name="storage_name" id="stor" placeholder="Check correct spelling"/>
                            <label for="stor">Lageront: </label>
                        </div>

                        <div class="divider transparent"></div>
                        
                        <div class="input-field">
                            <input type="text" name="old_id" id="old_id">
                            <label for="old_id">ID - Nummer: </label>
                        </div>

                        <div class="center">
                            <input class="btn teal accent-4" type="submit" name="submit" value="query">
                        </div>
                    </form>
                <?php
            }
        } else {
            ?>
                <!-- show form when entering the page for the first time -->
                <h4 class="center">Query maker</h4>
                <form class="teal lighten-5" action="queries.php" method="POST">
                    <h6 class="center">Fill in one or more fields</h6>
                    <div class="input-field">
                        <input type="text" name="product_name" id="prod"/>
                        <label for="prod">Stoffname: </label>
                    </div>

                    <div class="divider transparent"></div>

                    <div class="input-field">
                        <input type="text" name="manufacturer_name" id="manu" placeholder="Check correct spelling"/>
                        <label for="manu">Hersteller / Lieferant: </label>
                    </div>

                    <div class="divider transparent"></div>

                    <div class="input-field">
                        <input type="text" name="storage_name" id="stor" placeholder="Check correct spelling"/>
                        <label for="stor">Lageront: </label>
                    </div>

                    <div class="divider transparent"></div>
                    
                    <div class="input-field">
                        <input type="text" name="old_id" id="old_id">
                        <label for="old_id">ID - Nummer: </label>
                    </div>

                    <div class="center">
                        <input class="btn teal accent-4" type="submit" name="submit" value="query">
                    </div>
                </form>
            <?php
        }
    ?>
    </section>
    <div>
        <?php
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

        // close the connection to the database
        mysqli_close($conn);
        ?>
    </div>
    <?php include("templates/footer.php"); ?>
</html>