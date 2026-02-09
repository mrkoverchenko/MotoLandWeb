<?php
    session_start(); 
    include "connect.php";
    $ret = "";

    if (!empty($_SESSION['cartdeadline']) && $_SESSION['cartdeadline'] < time() - 1200) {
        session_unset();
        session_destroy();
        session_start();

        header("Location: index.php");        
        exit();
    }        

    //$_SESSION['cartdeadline'] = time();

    if (isset($_POST["field"]) && $_POST["field"] === "0" && isset($_SESSION["cartid"])) {

        $cartid = $_SESSION["cartid"];

        $sql = "SELECT 
                    MotoPartsNumber_MSTR,
                    MotoPartsName_MSTR,
                    ROUND(MotoPartsNettoPrice_MSTR, 0) AS netto,
                    ROUND(MotoPartsVAT_MSTR * 100, 1) AS vat,
                    ROUND((ROUND(MotoPartsNettoPrice_MSTR, 0) * MotoPartsVAT_MSTR) + ROUND(MotoPartsNettoPrice_MSTR, 0)) AS brutto,
                    QuantityUnitUnit_MSTR AS mee,
                    ShoppingCartQuantity_DET AS qua,
                    ROUND((ROUND(MotoPartsNettoPrice_MSTR, 0) * MotoPartsVAT_MSTR) + ROUND(MotoPartsNettoPrice_MSTR, 0)) * ShoppingCartQuantity_DET AS subtotal
                FROM 
                    shoppingcart_mstr, shoppingcart_det, motoparts_mstr, quantityunit_mstr
                WHERE 
                    ShoppingCartSessionID_MSTR = '$cartid' AND 
                    ShoppingCartID_MSTR = ShoppingCartMSTRID_DET AND 
                    MotoPartsID_MSTR = ShoppingCartMotoPartsID_DET AND
                    MotoPartsQuantityUnitID_MSTR = QuantityUnitID_MSTR";

        $result = mysqli_query($connect, $sql);
        $total = 0;
        while ($row = mysqli_fetch_assoc($result)) {                            
            $total += $row["subtotal"];
            $ret .= "<div class='card-body' style='margin-bottom:10px;'>
                        <div>
                            <h5 class='card-title'>
                                <b>".$row["MotoPartsNumber_MSTR"]." - ".$row["MotoPartsName_MSTR"]."</b>
                            </h5>
                        </div>
                        <div style='font-size:0.8em; display:inline-block;'>

                            <div style='float: left;'>
                                <p class='card-text'>Nettó: ".$row["netto"]." HUF. </p>
                            </div>

                            <div style='float: left; margin-left:15px;'>
                                <p class='card-text'>ÁFA: ".$row["vat"]."%</p>
                            </div>

                            <div style='float: left; margin-left:15px;'>
                                <p class='card-text'><strong>Egységár: ".$row["brutto"]." HUF.</strong></p>
                            </div>

                            <div style='float: left; margin-left:15px;'>
                                <p class='card-text'><strong>Menny.: ".$row["qua"]." ".$row["mee"]."</strong></p>
                            </div>

                            <div style='float: left; margin-left:15px; '>
                                <p class='card-text'><strong>Br.: <u>".$row["subtotal"]." HUF.</u> </strong></p>
                            </div>

                            <div style='float: left; margin-left:20px;' title='törlés'>
                                <a href='#'><span class='glyphicon glyphicon-trash' style='font-size:14px;'></span></a>
                            </div>
                            
                        </div>

                    </div>";
        }
        $ret .= "<div style='font-size:1em; display:inline-block;'>
                    <div style='margin-bottom:30px;'>
                        <p class='card-text'><strong>Fizetendő: <u>".$total." HUF.</u> </strong></p>
                    </div>
                </div>";
    
    }

    mysqli_close($connect);
    echo $ret;
    
?>
    