<?php
    session_start(); 

    include "connect.php";
    $det = "";
    if (isset($_POST["id"]) && !empty($_POST["id"])) {

        $id = $_POST["id"];

        $sql = "SELECT 
                    SecondHandID_MSTR,
                    CONCAT(MotoManufacturerManufacturer_MSTR, ' ', SecondHandType_MSTR) AS MotoType,
                    SecondHandYear_MSTR,
                    SecondHandStateState_MSTR,
                    SecondHandPrice_MSTR,
                    SecondHandImages_MSTR,
                    SecondHandRegDateTime_MSTR,
                    SecondHandLastRegDateTime_MSTR,
                    SecondHandImageFileNames_MSTR,  
                    MotoManufacturerManufacturer_MSTR AS Brand,
                    SecondHandType_MSTR AS Type
                FROM 
                    secondhand_mstr,
                    secondhandstate_mstr,
                    motomanufacturer_mstr
                WHERE 
                    SecondHandID_MSTR = '$id' AND 
                    SecondHandManufacturerID_MSTR = MotoManufacturerID_MSTR AND 
                    secondhand_mstr.SecondHandStateID_MSTR = secondhandstate_mstr.SecondHandStateID_MSTR";
        $det = "";
        $Images = Array();
        $value = mysqli_query($connect, $sql);
        $row = mysqli_fetch_assoc($value);

            $ID = $row["SecondHandID_MSTR"];
            $BrandAndType = $row["MotoType"];
            $Brand = $row["Brand"];
            $Type = $row["Type"];
            $Year = $row["SecondHandYear_MSTR"];
            $State = $row["SecondHandStateState_MSTR"];
            $Price = $row["SecondHandPrice_MSTR"];
            $Begin = $row["SecondHandRegDateTime_MSTR"];
            $Last = $row["SecondHandLastRegDateTime_MSTR"];

            $shi = $row["SecondHandImageFileNames_MSTR"];
            $Images = explode(",", $shi);

            $det = "<div class='form-group row'>
                        <div class='col-sm-8'>
                            <span>Típus: <b>$BrandAndType</b></span><br>
                            <span>Gyártási év: <b>$Year</b></span><br>
                            <span>Állapot: <b>$State</b></span><br>
                            <span>Eladási ár: <b><u>".number_format($Price, 0, ",", " ").".- Ft.</u></b></span><br>
                            <span>Hirdetés felvéve: <i>$Begin</i></span><br>
                            <span>Utolsó módosítás: <i>$Last</i></span><br>
                            <span style='font-size:10px; color:gray;'><i>".((strlen($shi) > 0) ? count($Images)." db." : "<b><u>Nincs</u></b>")." kép a hirdetésben.</i></span>
                        </div>
                        
                    </div>
                    <div class='form-group row' style='overflow-y: scroll; max-height:300px;'>"; 
    
                        for ($ic = 0; $ic < count($Images); $ic++) { 
                                            
                            $det .= "<div>
                                        <img src='$Images[$ic]' 
                                            alt='$BrandAndType' 
                                            width='450' 
                                            height='auto' 
                                            style='margin:15px; 
                                                    border-radius:5px; 
                                                    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);'>
                                        </div>";
                        }

                    $det .= "</div>
   
                    </div>
                    
                    <div style='display:inline;'>
                        <button type='reset' class='btn btn-primary' data-dismiss='modal' >
                            <span class='glyphicon glyphicon-remove'></span> Bezárás
                        </button>
                    </div>";

        

    
    }
    mysqli_close($connect);
    echo $det;


/*


                        <div class='carousel slide' data-ride='carousel'>
                            <ol class='carousel-indicators'>";
                                for ($ic = 0; $ic < count($Images); $ic++) { 
                                    $det .= "<li data-target='#shCarousel' data-slide-to='$ic' ".(($ic == 0) ? "class='active'" : "")."></li>";
                                }
                            $det .= "</ol>
                            <div class='carousel-inner' role='listbox'>";
                                for ($ic = 0; $ic < count($Images); $ic++) {
                                    $det .="<div class='item ".(($ic == 0) ? "active" : "")."'>
                                                <img src='$Images[$ic]' alt='$BrandAndType' width='160' height='145'>
                                                <div class='carousel-caption'>
                                                    <h3>$Brand</h3>
                                                    <p>$Type ($Year)</p>
                                                </div>
                                            </div>";
                                }
                            $det .= "</div>
                            <a class='left carousel-control' href='#shCarousel' role='button' data-slide='prev'>
                                <span class='glyphicon glyphicon-chevron-left' aria-hidden='true'></span>
                                <span class='sr-only'>Previous</span>
                            </a>
                            <a class='right carousel-control' href='#shCarousel' role='button' data-slide='next'>
                                <span class='glyphicon glyphicon-chevron-right' aria-hidden='true'></span>
                                <span class='sr-only'>Next</span>
                            </a>
                        </div>





*/


?>



