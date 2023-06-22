<?php
    // connect to the database
    include("templates/db_conn.php");

    // check form inputs
    if(isset($_POST["submit"])) {
        $errorProd = "";
        $errorChem = "";

        $product = htmlspecialchars($_POST["product_name"]);
        $chemical = htmlspecialchars($_POST["chemical_name"]);

        // check product name
        if(empty($product)) {
            $errorProd = "The product name is required";
        }

        // check chemical name
        if(empty($chemical)) {
            $errorChem = "The name of the chemical is required";
        }
    } // end of form checks

?>

<!DOCTYPE html>
<html lang="en">
    <?php include("templates/header.php"); ?>

    <section class="container teal lighten-3">

        <!-- Form for new chemical -->
        <h4 class="center">Add a new chemical</h4>
        <form class="teal lighten-5" action="add.php" method="POST">
            <div class="input-field">
                <input type="text" name="product_name" id="prod" required>
                <label for="prod">Stoffname: </label>
            </div>

            <div class="divider transparent"></div>

            <div class="input-field">
                <input type="text" name="manufacturer_name" id="manu" required>
                <label for="manu">Hersteller / Lieferant: </label>
            </div>

            <div class="divider transparent"></div>

            <div class="input-field">
                <input type="text" name="storage_name" id="stor" required>
                <label for="stor">Lageront: </label>
            </div>

            <div class="divider transparent"></div>

            <div class="input-field">
                <input type="text" name="sdb_link" id="sdblink" required>
                <label for="sdblink">SDB - Link: </label>
            </div>

            <ul class="collapsible">
                <li>
                    <div class="collapsible-header">Kennzeichnung GHS</div>
                    <div class="collapsible-body">
                        <div class="row">
                            <div class="input-field">
                                <div class="col s2">
                                    <img src="danger_img/FLAME.png" alt="Flame" class="form_img">
                                </div>
                                <div class="col s10">
                                    <select name="flame" id="flame" required>
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
                                        <option value="" disabled selected>Choose a danger class</option>
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
                                        <option value="" disabled selected>Choose a danger class</option>
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
                                        <option value="" disabled selected>Choose a danger class</option>
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
                                        <option value="" disabled selected>Choose a danger class</option>
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
                                        <option value="" disabled selected>Choose a danger class</option>
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
                                        <option value="" disabled selected>Choose a danger class</option>
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
                                        <option value="" disabled selected>Choose a danger class</option>
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
                            <input type="text" name="old_id" id="man_id">
                            <label for="man_id">ID - Nummer: </label>
                        </div>

                        <div class="divider transparent"></div>

                        <div class="input-field">
                            <input type="date" name="sdb_date" id="sdbdate">
                            <label for="sdbdate">SDB - Datum: </label>
                        </div>

                        <div class="divider transparent"></div>

                        <div class="input-field">
                            <input type="text" name="man_link" id="manlink">
                            <label for="manlink">Hersteller link: </label>
                        </div>

                        <div class="divider transparent"></div>

                        <div class="input-field">
                            <input type="text" name="man_link2" id="manlink2">
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
    
    <?php 
        // Checks for correct data submitted
        if(isset($_POST["submit"])) {

            // red box if no product name was sent
            if(!empty($errorProd)) {
                ?>
                    <div class="container warning red lighten-3 valign-wrapper">
                        <div>
                            <i class="material-icons red-text text-darken-3">warning</i>
                            <p class="float-text"> <?php echo $errorProd ?> </p>
                        </div>
                    </div>
                <?php
            }

            // red box if no manufacturer name was sent
            if(!empty($errorChem)) {
                ?>
                    <div class="container warning red lighten-3 valign-wrapper">
                        <div>
                            <i class="material-icons red-text text-darken-3">warning</i>
                            <p class="float-text"> <?php echo $errorChem ?> </p>
                        </div>
                    </div>
                <?php
            }

            // green box if all ok
            if(empty($errorProd) && empty($errorChem)) {
                ?>
                    <div class="container warning green lighten-3 valign-wrapper">
                        <div>
                            <i class="material-icons green-text text-darken-3">done</i>
                            <p class="float-text"> <?php echo 'Data inserted correctly' ?> </p>
                        </div>
                    </div>
                <?php
            }
        }
    ?>

    <?php include("templates/footer.php"); ?>
</html>