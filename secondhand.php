<?php
/*	if (isset($_SESSION['userid']) && isset($_SESSION['usernickname'])) {

    } else {
        header('Location: index.php?logged=out');
        exit;
    }*/


    include "connect.php";
?>
        <style>
            .secondhandbody {
                margin-top:55px; 
                margin-bottom:60px; 
                color:red;
                display: inline-block;
                width: 100%;
                background-color: transparent;
            }
            .tabcontain {
                color: gray;
            }
            .mrg {
                margin-top: 5px;
            }
            .readonly {
                color: gray;
            } 
        </style>


        <div class="secondhandbody">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#secondhandmoto">Motorok</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#sacondhandhandling">Feladás</a>
                </li>
            </ul>



            <div class="tab-content">

                <div id="secondhandmoto" class="container tab-pane active tabcontain" style="margin-bottom:30px;">
                    <h3>Hirdetések</h3>

                    <?php
                            $sql = "SELECT 
                                        SecondHandID_MSTR,
                                        CONCAT(MotoManufacturerManufacturer_MSTR, ' ', SecondHandType_MSTR) AS MotoType,
                                        SecondHandYear_MSTR,
                                        SecondHandStateState_MSTR,
                                        SecondHandPrice_MSTR,
                                        SecondHandImages_MSTR,
                                        SecondHandRegDateTime_MSTR,
                                        SecondHandLastRegDateTime_MSTR
                                    FROM 
                                        secondhand_mstr,
                                        secondhandstate_mstr,
                                        motomanufacturer_mstr
                                    WHERE 
                                        SecondHandManufacturerID_MSTR = MotoManufacturerID_MSTR AND 
                                        secondhand_mstr.SecondHandStateID_MSTR = secondhandstate_mstr.SecondHandStateID_MSTR";

                            //$Images = Array();
                            $value = mysqli_query($connect, $sql);
                            while ($row = mysqli_fetch_assoc($value)) {
                                $ID = $row["SecondHandID_MSTR"];
                                $BrandAndType = $row["MotoType"];
                                $Year = $row["SecondHandYear_MSTR"];
                                $State = $row["SecondHandStateState_MSTR"];
                                $Price = $row["SecondHandPrice_MSTR"];
                                $Image = explode(",", $row["SecondHandImages_MSTR"])[0];
                                $Begin = $row["SecondHandRegDateTime_MSTR"];
                                $Last = $row["SecondHandLastRegDateTime_MSTR"];
                                //array_push($Images, $ImagesA[0]);


                                echo "  <div class='card mb-3' style='min-width:180px;  max-width: 450px; background-color: rgba(0,0,0,0.3); color:white; border-radius:5px;padding:5px;'>
                                            <div class='row g-0'>
                                                <div class='col-md-5'>
                                                    <img src='".$_SESSION['systemPath'].$Image."'
                                                        style='width:auto; max-width:180px; cursor: pointer;'
                                                        alt=''
                                                        class='rounded-start'/>
                                                </div>
                                                <div class='col-md-6'>
                                                    <div class='card-body'>
                                                        <h5 class='card-title'><b><u>$BrandAndType</u></b></h5>
                                                        <p class='card-text'>
                                                            Évjárat: $Year<br>
                                                            Állapot: $State
                                                        </p>
                                                        <p class='card-text'>
                                                            <small class='text-muted' style='color: white; font-size:10px'><i>Utolsó frissítés: $Last</i></small>
                                                        </p>
                                                        <a href='#editsecondhand' data-toggle='modal' class='btn btn-primary btn-sm'>Részletek</a>
                                                        <input type='hidden' id='detailID' value='$ID'>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>";

                            }
                    ?>
                </div>



                <div id="sacondhandhandling" class="container tab-pane tabcontain"><br>
                    <h3>Hirdetésfeladás</h3>


                    <!--------------------------------------------------------------
                    LOGIN
                    -->
                    <?php
                        if (empty($_SESSION['userid'])) {
                            echo "<div>

                                    <form action='index.php' method='POST' id='secondhandLoginForm' >
                                        <input type='hidden' name='formName' value='secondhandLoginForm'>

                                        <div class='brdr' style='min-width:270px; color: gray; margin-bottom:5px;'>
                                            <div style='margin: 5px 15px 5px 5px;'>
                                                <span>Új feladáshoz, meglévő hirdetéseid kezeléséhez be kell jelentkezned!</span>
                                            </div>

                                            <div style='display:block; margin:5px;'>
                                                <span style='display:inline-block; width:120px;'>Felhasználónév</span>
                                                <input type='text' 
                                                    class='form-control'
                                                    required 
                                                    value=''
                                                    id='loginUserName' 
                                                    form='secondhandLoginForm'
                                                    name='loginUserName' 
                                                    style='display:inline-block; width:250px;' 
                                                    placeholder='e-mail'>
                                            </div>

                                            <div style='display:block; margin:5px;'>
                                                <span style='display:inline-block; width:120px;'>Jelszó</span>
                                                <input type='password' 
                                                    class='form-control'
                                                    required 
                                                    id='loginPassword' 
                                                    form='secondhandLoginForm'
                                                    name='loginPassword' 
                                                    style='display:inline-block; width:250px;' 
                                                    placeholder='jelszó'>
                                            </div>

                                            <div style='display:inline;'>
                                                <span style='display:inline-block; width:120px;'></span>
                                                <button type='submit' class='btn btn-success' form='secondhandLoginForm' style='display:inline; margin-left:5px;'>
                                                    <span class='glyphicon glyphicon-off'></span> 
                                                    Bejelentkezés
                                                </button>
                                            </div>

                                        </div>

                                    </form>
                                </div>";

                        } else {
                            $userId = $_SESSION['userid'];
                            echo " <div>
                                        <div class='brdr' style='color: gray'>

                                            <div style='display:block; margin:5px;'>
                                                <span style='display:inline-block; width:120px;'>Saját hirdetéseim</span>
                                                <select 
                                                    onchange=''
                                                    class='form-select form-select-lg' 
                                                    aria-label='.form-select-lg'
                                                    style='margin-bottom:20px' >";

                                                        $sql = "SELECT * FROM secondhand_mstr, motomanufacturer_mstr
                                                                WHERE 
                                                                    SecondHandUserID_MSTR = '$userId' AND
                                                                    MotoManufacturerID_MSTR = SecondHandManufacturerID_MSTR 
                                                                ORDER BY 
                                                                    SecondHandRegDateTime_MSTR ASC";
                                                        $ret = "<option>Új hirdetés feladása</option>";
                                                        $result = mysqli_query($connect, $sql);
                                                        while ($row = mysqli_fetch_assoc($result)) {                                    
                                                            $cntr = $row['SecondHandID_MSTR'];
                                                            $type = $row['MotoManufacturerManufacturer_MSTR']." ".
                                                                    $row['SecondHandType_MSTR']." [".$row['SecondHandYear_MSTR']."]";
                                                            $ret .= "<option value='$cntr'>$cntr. $type</option>"; 
                                                        }
                                                        echo $ret;

                                                echo "</select>
                                            </div>


                                            <form action='index.php' method='POST' id='secondhandAddForm'>
                                                <input type='hidden' name='formName' value='secondhandAddForm'>


                                                <div style='display:block; margin:5px;'>
                                                    <span style='display:inline-block; width:120px;'>Márkanév</span>
                                                    <select 
                                                        required    
                                                        class='form-select form-select-lg' 
                                                        id='shoppingCartCountryID' 
                                                        aria-label='.form-select-lg'>";

                                                            $sql = "SELECT * FROM motomanufacturer_mstr
                                                                    ORDER BY MotoManufacturerManufacturer_MSTR ASC";
                                                            $ret = "<option></option>";
                                                            $result = mysqli_query($connect, $sql);
                                                            while ($row = mysqli_fetch_assoc($result)) {                                    
                                                                $cntr = $row['MotoManufacturerID_MSTR'];
                                                                $type = $row['MotoManufacturerManufacturer_MSTR'];
                                                                $ret .= "<option value='$cntr'>$type</option>"; 
                                                            }
                                                            echo $ret;

                                                    echo "</select>
                                                </div>





                                                <div style='display:block; margin:5px;'>
                                                    <span style='display:inline-block; width:120px;'>Típus</span>
                                                    <input type='text' 
                                                        required
                                                        maxlength=30
                                                        class='form-control' 
                                                        id='shoppingCartEMail' 
                                                        name='shoppingCartEMail' 
                                                        style='display:inline-block; width:250px;' 
                                                        placeholder='típus'>
                                                </div>


                                                <div style='display:block; margin:5px;'>
                                                    <span style='display:inline-block; width:120px;'>Évjárat</span>
                                                    <input type='text' 
                                                        required
                                                        maxlength=4
                                                        class='form-control'
                                                        style='display:inline-block; width:60px;' 
                                                        onkeypress='return onlyNumber(event)'
                                                        placeholder='2020'>
                                                </div>



                                                <div style='display:block; margin:5px;'>
                                                    <span style='display:inline-block; width:120px;'>Állapot</span>
                                                    <select 
                                                        onchange=''
                                                        required
                                                        class='form-select form-select-lg' 
                                                        id='shoppingCartCountryID' 
                                                        aria-label='.form-select-lg'>";

                                                            $sql = "SELECT * FROM secondhandstate_mstr
                                                                    ORDER BY SecondHandStateID_MSTR ASC";
                                                            $ret = "<option></option>";
                                                            $result = mysqli_query($connect, $sql);
                                                            while ($row = mysqli_fetch_assoc($result)) {                                    
                                                                $cntr = $row['SecondHandStateID_MSTR'];
                                                                $type = $row['SecondHandStateState_MSTR'];
                                                                $ret .= "<option value='$cntr'>$type</option>"; 
                                                            }
                                                            echo $ret;

                                                    echo "</select>
                                                </div>


                                                <div style='display:block; margin:5px;'>
                                                    <span style='display:inline-block; width:120px;'>Ár</span>
                                                    <input type='text' 
                                                        required
                                                        maxlength=9
                                                        class='form-control'
                                                        style='display:inline-block; width:160px;' 
                                                        onkeypress='return onlyNumber(event)'
                                                        placeholder='irányár'>
                                                </div>



                                                <div style='display:inline;'>
                                                    <span style='display:inline-block; width:120px;'></span>
                                                    <button type='submit' class='btn btn-success' form='secondhandAddForm' style='display:inline; margin-left:5px;'>
                                                        Feladás
                                                    </button>
                                                </div>

                                            </form>

                                        </div>

                                   </div>";
                        
                        
                        }
                    ?>

                </div>

            </div>



            
















        </div>


