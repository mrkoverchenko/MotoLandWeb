<?php
    //session_start(); 

    include "connect.php";

    $ret = "<h3>Hirdetések</h3>";

    $sql = "SELECT 
                SecondHandID_MSTR,
                CONCAT(MotoManufacturerManufacturer_MSTR, ' ', SecondHandType_MSTR) AS MotoType,
                SecondHandYear_MSTR,
                SecondHandStateState_MSTR,
                SecondHandPrice_MSTR,
                SecondHandRegDateTime_MSTR,
                SecondHandLastRegDateTime_MSTR,
                SecondHandImageFileNames_MSTR
            FROM 
                secondhand_mstr,
                secondhandstate_mstr,
                motomanufacturer_mstr
            WHERE 
                SecondHandManufacturerID_MSTR = MotoManufacturerID_MSTR AND 
                secondhand_mstr.SecondHandStateID_MSTR = secondhandstate_mstr.SecondHandStateID_MSTR";

    $value = mysqli_query($connect, $sql);
    while ($row = mysqli_fetch_assoc($value)) {
        $ID = $row["SecondHandID_MSTR"];
        $BrandAndType = $row["MotoType"];
        $Year = $row["SecondHandYear_MSTR"];
        $State = $row["SecondHandStateState_MSTR"];
        $Price = $row["SecondHandPrice_MSTR"];

        $shi = $row["SecondHandImageFileNames_MSTR"];
        $Image = (strlen($shi) > 0) 
                    ? explode(",", $shi)[0]
                    : "imgs/nopic-64.png";

        $Begin = $row["SecondHandRegDateTime_MSTR"];
        $Last = $row["SecondHandLastRegDateTime_MSTR"];

        $ret .= "  <div class='card mb-2' 
                            style='width: 400px; height:120px;
                                    background-color: rgba(0,0,0,0.3); 
                                    color:white; 
                                    border-radius:5px;padding:5px;
                                    margin:5px;
                                    display: inline-block'>

                        <div class='row g-0'>

                            <div class='col-md-6' >
                                <img src='".$_SESSION['systemPath'].$Image."'
                                    style='width:auto; max-height:110px; cursor: pointer;border-radius:3px'
                                    alt='$BrandAndType'
                                    title='$BrandAndType'
                                    class='rounded-start'/>
                            </div>

                            <div class='col-md-6'>
                                <div class='card-body'>
                                    <p class='card-title' style='font-size:12px'><b><u>$BrandAndType</u></b></p>
                                    <p class='card-text' style='font-size:10px'>
                                        Évjárat: $Year<br>
                                        Állapot: $State<br>
                                        <small class='text-muted' style='color: white; font-size:10px'><i>Utolsó frissítés: $Last</i></small>
                                    </p>
                                    <a href='#editsecondhand' 
                                            data-toggle='modal' 
                                            class='btn btn-primary btn-sm' 
                                            id='$ID' 
                                            onclick='ssd(this)'>Részletek</a>
                                </div>
                            </div>
                        </div>
                    </div>";

    }
    mysqli_close($connect);
    echo $ret;
?>
