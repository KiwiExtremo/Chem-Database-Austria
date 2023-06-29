<?php
    // include functions
    include_once("functions.php");
    
    // connect to the database
    include("templates/db_conn.php");

    // Create a query to show all database
    $query = "SELECT CHEMICAL_NAME AS 'STOFFNAME', MANUFACTURER_NAME AS 'HERSTELLER / LIEFERANT', i.ID_OLD AS 'ID - NUMMER', DATE AS 'SDB - DATUM', STORAGE_NAME AS 'LAGERONT', 
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
                ON c.ID_NEW = d.CHEMICAL_ID
                ORDER BY c.ID_NEW";

    $result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
    <?php include("templates/header.php"); ?>

    <section class="container teal lighten-3">
        <h4 class="center">All products</h4>
        <div class="container teal lighten-5">
            <div class="table-wrapper">
                

                <!-- Show a table with the query -->
                <?php
                    if(mysqli_num_rows($result) > 0) {

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
                                } else if(in_array($columna, ['FLAME', 'FLAME_OVER_CIRCLE', 'CORROSION', 'GAS_CYLINDER', 'SKULL_AND_CROSSBONES', 'EXCLAMATION_MARK', 'HEALTH_HAZARD', 'ENVIRONMENT'])) {
                                    
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

                    } else {
                        echo "0 results";
                    }
                    
                    // free the result of the query from the memory
                    mysqli_free_result($result);

                    // close the connection to the database
                    mysqli_close($conn);
                ?>
            </div>
        </div>
    </section>

    <?php include("templates/footer.php"); ?>
</html>