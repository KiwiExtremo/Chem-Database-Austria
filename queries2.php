<?php
    // connect to the database
    include("templates/db_conn.php");

    // fetch tables for the table selector
    $sqltables = "show tables from $dbname";
    $alltables = mysqli_query($conn, $sqltables);

    if(mysqli_num_rows($alltables) > 0) {
        $tables = array();

        while($row = mysqli_fetch_row($alltables)) {
            $tables[] = $row[0];
        }
    } else {
        $tables[] = "No tables found";
    }


?>
<!DOCTYPE html>
<html lang="en">
    <?php include("templates/header.php"); ?>

    <section class="container teal lighten-3">

        <!-- Query Selector -->
        <h4 class="center">Query maker</h4>
        <form class="teal lighten-5" action="queries.php" method="POST">
            <div class="input-field">
                <select name="campSelector" id="campSelect">
                    <option value="" disabled selected>Choose a camp</option>
                    <option value="">ID - nummer</option>
                    <option value="">Stoffname</option>
                    <option value="">Hersteller / Lieferant</option>
                    <option value="">Lageront</option>
                </select>
                <label for="campSelect">Camp to search: </label>
            </div>

            <div class="divider transparent"></div>
            
            <div class="input-field">
                <input type="text" name="search" id="search">
                <label for="search">Variable to search: </label>
            </div>

            <div class="center">
                <input class="btn teal accent-4" type="submit" name="submit" value="query">
            </div>
        </form>
    </section>
    
    <?php
    // Checks the table submitted
    if(isset($_POST["submit"])) {
        if($_POST["tableSelector"] == "") {
            echo "<h4>no hay tabla seleccionada</h4>";
        }
        echo "<h4>" . $_POST['tableSelector'] . "</h4>";
    }

    // close the connection to the database
    mysqli_close($conn);
    ?>

    <?php include("templates/footer.php"); ?>
                            
    <script>
        $(document).ready(function() {

            // fetch change in table selection
            $('#tableSelect').change(function() {
                var table = $(this).val();

                console.log("Selected table: " + table);

                $.ajax({
                    url: "fetch_columns.php",
                    method: "POST",
                    data: {table: table},
                    success: function(data) {

                        // shows the data fetched on console
                        console.log("Response data: " + data);

                        // clean column selector
                        $('#columnSelect').empty();
                        
                        // add generated options
                        $('#columnSelect').html(data);

                        // reinitialize Materialize CSS selector
                        $('select').formSelect();
                    },
                    error: function(xhr, status, error) {

                        // shows any error on the AJAX call on console
                        console.log("AJAX error: " + error);
                    }
                })
            })
        })
    </script>
</html>