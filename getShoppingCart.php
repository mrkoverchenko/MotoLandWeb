<?php
    session_start(); 
    include "connect.php";
    $ret = "";

    if (!empty($_SESSION['cartdeadline']) && $_SESSION['cartdeadline'] < time() - $_SESSION["sessionDeadline"]) {
        unset($_SESSION['cartdeadline']);
        unset($_SESSION['cartid']);
        session_unset();
        session_destroy();
        session_start();

        header("Location: index.php");        
        exit();
    } 
    
    

    $_SESSION['cartdeadline'] = time();

    if (isset($_POST["field"]) && $_POST["field"] === "0" && isset($_SESSION["cartid"])) {

        $cartid = $_SESSION["cartid"];

        $sql = "SELECT 
                    LockedQuantityID_MSTR,
                    ShoppingCartID_DET,
                    ShoppingCartMSTRID_DET,
                    ShoppingCartSessionID_MSTR,
                    MotoPartsID_MSTR,
                    MotoPartsNumber_MSTR,
                    MotoPartsName_MSTR,
                    ROUND(MotoPartsNettoPrice_MSTR, 0) AS netto,
                    ROUND(MotoPartsVAT_MSTR * 100, 1) AS vat,
                    ROUND((MotoPartsNettoPrice_MSTR * MotoPartsVAT_MSTR) + MotoPartsNettoPrice_MSTR, 0) AS brutto,
                    QuantityUnitUnit_MSTR AS mee,
                    ShoppingCartQuantity_DET AS qua,
                    MotoPartsDiscount_MSTR AS disc
                FROM 
                    shoppingcart_mstr, shoppingcart_det, motoparts_mstr, quantityunit_mstr, lockedquantity_mstr
                WHERE 
                    LockedQuantityShoppingCartDETID_MSTR = ShoppingCartID_DET AND 
                    ShoppingCartSessionID_MSTR = '$cartid' AND 
                    ShoppingCartID_MSTR = ShoppingCartMSTRID_DET AND 
                    MotoPartsID_MSTR = ShoppingCartMotoPartsID_DET AND
                    MotoPartsQuantityUnitID_MSTR = QuantityUnitID_MSTR";

                    //ROUND((MotoPartsNettoPrice_MSTR * MotoPartsVAT_MSTR) + MotoPartsNettoPrice_MSTR) * ShoppingCartQuantity_DET AS subtotal    
        $result = mysqli_query($connect, $sql);
        $total = 0;
        while ($row = mysqli_fetch_assoc($result)) {       
            $idDET = $row["ShoppingCartID_DET"];
            $idMSTR = $row["ShoppingCartMSTRID_DET"];
            $lockedID = $row["LockedQuantityID_MSTR"];
            $sessionID = $row["ShoppingCartSessionID_MSTR"];
            $qua = $row["qua"];
            $partID = $row["MotoPartsID_MSTR"];
            $brutto = $row["brutto"];
            $disc = $row["disc"];
            $partSubTotal = round(($brutto -($brutto * $disc)) * $qua);
            //$row["subtotal"];
            $total += $partSubTotal;
            $ret .= "<div class='card-body' style='margin-bottom:10px;' id='row_$idDET'>
                        <div>
                            <h5 class='card-title'>
                                <b>".$row["MotoPartsNumber_MSTR"]." - ".$row["MotoPartsName_MSTR"]."</b>
                            </h5>
                        </div>
                        <div style='font-size:0.8em; display:inline-block;'>
                            <input type='hidden' style='width:50px;' value='$idDET' name='idDET'>
                            <input type='hidden' style='width:50px;' value='$idMSTR' name='idMSTR'>
                            <input type='hidden' style='width:50px;' value='$lockedID' name='lockedID'>
                            <input type='hidden' style='width:50px;' value='$sessionID' name='sessionID'>
                            <input type='hidden' style='width:50px;' value='$qua' name='qua'>
                            <input type='hidden' style='width:50px;' value='$partID' name='partID'>
                            <input type='hidden' style='width:50px;' value='$partSubTotal' name='partSubTotal'>

                            <div style='float: left;'>
                                <p class='card-text'>Nettó: <b>".number_format($row["netto"],0,","," ").".- Ft.</b></p>
                            </div>

                            <div style='float: left; margin-left:15px;'>
                                <p class='card-text'>ÁFA: <b>".number_format($row["vat"],0)."%</b></p>
                            </div>

                            <div style='float: left; margin-left:15px;'>
                                <p class='card-text'>Egységár: <b>".number_format($row["brutto"], 0,","," ").".- Ft.</b></p>
                            </div>

                            <div style='float: left; margin-left:15px;'>
                                <p class='card-text'>Menny.: <b>".number_format($qua,1,","," ")." ".$row["mee"]."</b></p>
                            </div>

                            <div style='float: left; margin-left:15px; '>
                                <p class='card-text'>Br.: <u><b>".number_format($partSubTotal, 0,","," ").".- Ft.</b></u></p>
                            </div>

                            <div style='float: left; margin-left:20px;' title='törlés'>
                                <a href='#' onclick='removeFromShoppingCart(this)' id='$idDET' >
                                    <span class='glyphicon glyphicon-trash' style='font-size:14px;'></span>
                                </a>
                            </div>
                            

                        </div>

                    </div>";
        }
        $ret .= "<div style='font-size:1em; display:inline-block;'>
                    <div style='margin-bottom:30px;'>
                        <strong><u><p class='card-text' id='total'>Fizetendő: ".number_format($total, 0, ",", " ").".- Ft.</p></u></strong>
                    </div>
                </div>";
    
    }

    mysqli_close($connect);
    echo $ret;
    
?>
    