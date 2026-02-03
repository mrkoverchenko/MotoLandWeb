<?php
	session_start();
/*	if (isset($_SESSION['userid']) && isset($_SESSION['usernickname'])) {

    } else {
        header('Location: index.php?logged=out');
        exit;
    }*/



    include "connect.php";





?>
        <style>
            .orderbody {
                margin-top:55px; 
                color:red;
                display: inline-block;
                width: 100%;
                background-color: transparent;
            }

        </style>


        <div class="orderbody">


  
            <h1 style='color:gray;'>Alkatrész rendelés</h1>
  
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link" id="types-tab" data-toggle="tab" href="#types" role="tab">Típus</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="filters-tab" data-toggle="tab" href="#filters" role="tab">Szűrők</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="contact-tab" data-toggle="tab" href="#secondhand" role="tab">Használt</a>
                </li>
            </ul>

            <div class="tab-content" id="myTabContent">

                <div class="tab-pane fade" id="types" role="tabpanel" aria-labelledby="types-tab">
                    <h2>Típus keresés</h2>
                    <form action="index.php">

                        <div class="row">
                            <div class="col-sm-12" style="background-color:lavender;">
                                Alkatrészrendelés
                            </div>
                        </div>



                        <div class="row">
                            <div class="col-sm-2" style="background-color:lavender;">
                                Gyártó
                            </div>


                            <div class="col-sm-4" style="background-color:lavenderblush;">
                                <div class="form-group">
                                    <select class="form-control" id="motoman" name="motoman" onchange="manSelect(this)">
                                        <option></option>
                                        <?php
                                            $sql = "SELECT MotoManufacturerID_MSTR, MotoManufacturerManufacturer_MSTR
                                                    FROM motomanufacturer_mstr
                                                    WHERE MotoManufacturerIsActive_MSTR = '1'";
                                            $result = mysqli_query($connect, $sql);
                                            while ($row = mysqli_fetch_assoc($result)) {                                    
                                                $id = $row["MotoManufacturerID_MSTR"];
                                                $man = $row["MotoManufacturerManufacturer_MSTR"];
                                                echo "<option value='$id'>$man</option>";
                                            }
                                            mysqli_close($connect);
                                        ?>

                                    </select>
                                </div>                     
                            </div>
                        </div>



                        <div class="row">
                            <div class="col-sm-2" style="background-color:lavender;">
                                Típus
                            </div>
                            <div class="col-sm-6" style="background-color:lavender;">
                                <div class="form-group">
                                    <select class="form-control" id="mototype" name="mototype" onchange="typeSelect(this)"></select>
                                </div>                     
                            </div>
                        </div>



                        <div class="row">
                            <div class="col-sm-2" style="background-color:lavender;">
                                Alkatrész kategória
                            </div>
                            <div class="col-sm-6" style="background-color:lavender;">
                                <div class="form-group">
                                    <select class="form-control" id="motopartscategory" name="motopartscategory" onchange="categorySelect(this)"></select>
                                </div>                     
                            </div>
                        </div>



                        <div class="row">
                            <div class="col-sm-2" style="background-color:lavender;">
                                Alkatrész
                            </div>
                            <div class="col-sm-6" style="background-color:lavender;">
                                <div class="form-group">
                                    <select class="form-control" id="motoparts" name="motoparts"></select>
                                </div>                     
                            </div>
                        </div>
                    </form>                
                </div>

                <div class="tab-pane fade" id="filters" role="tabpanel" aria-labelledby="filters-tab">
                    <h2>Szűrők</h2>
                    <p></p>
                </div>
                <div class="tab-pane fade" id="secondhand" role="tabpanel" aria-labelledby="secondhand-tab">
                    <h2>Használt alkatrészek</h2>
                    <p></p>
  
                </div>
            </div>
        </div>


