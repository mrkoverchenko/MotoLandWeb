<?php

    if (session_status() != PHP_SESSION_ACTIVE) {
        session_start();
    }


    /*****************************************************************
     * SESSION CHECKING
    */
    if (!empty($_SESSION['cartdeadline']) && $_SESSION['cartdeadline'] < time() - $_SESSION["sessionDeadline"]) {
        unset($_SESSION['cartdeadline']);
        unset($_SESSION['cartid']);
        session_unset();
        session_destroy();
        session_start();
    }

    include "connect.php";
?>
        <style>
            .paymentsbody {
                margin-top:55px; 
                color:red;
                display: inline-block;
                width: 100%;
                background-color: transparent;
            }
            .mrg {
                margin-top: 5px;
            }
            .brdr {
                border: 1px solid lightgray;
                padding: 5px;
                border-radius:5px;
            }
            .ttl {
                background-color: #ACCB88;
                border: 1px solid gray;
                border-radius: 5px;
                margin-bottom:2px;
                margin-top:5px;
            }

            .readonly {
                color: gray;
            } 

        </style>


        <div class="paymentsbody" style="margin-bottom:70px;">

            <div id="types" class="container" style="margin-bottom:30px;">
                
                    

                    <!--------------------------------------------------------------
                     PARTS
                     -->
                    <div class="row ttl">
                        <div class="col-sm-2 mrg">
                            <p class="d-inline-flex gap-1">
                                <a data-bs-toggle="collapse" 
                                    onclick="setArrow(this)" 
                                    id="isOpen"
                                    class="icon-link" 
                                    href="#parts" 
                                    role="button" 
                                    aria-expanded="false" 
                                    aria-controls="parts">Termékadatok &#11165;</a>                            
                                </p>
                        </div>

                    </div>

                    <div class="collapse show" id="parts">

                            <?php
                                $sessionID = $_SESSION["cartid"];

                                $sql = "SELECT 
                                            MotoPartsNumber_MSTR,
                                            MotoPartsName_MSTR,
                                            ROUND(MotoPartsBruttoEURPrice_MSTR,2) AS bruttoEUR,
                                            ROUND(MotoPartsNettoPrice_MSTR, 0) AS netto,
                                            ROUND(MotoPartsVAT_MSTR * 100, 0) AS vat,
                                            ROUND((ROUND(MotoPartsNettoPrice_MSTR, 0) * MotoPartsVAT_MSTR) + ROUND(MotoPartsNettoPrice_MSTR, 0)) AS brutto,
                                            QuantityUnitUnit_MSTR AS mee,
                                            ShoppingCartQuantity_DET AS qua,
                                            ROUND((ROUND(MotoPartsNettoPrice_MSTR, 0) * MotoPartsVAT_MSTR) + ROUND(MotoPartsNettoPrice_MSTR, 0)) * ShoppingCartQuantity_DET AS subtotal
                                        FROM 
                                            shoppingcart_mstr, shoppingcart_det, motoparts_mstr, quantityunit_mstr
                                        WHERE 
                                            ShoppingCartSessionID_MSTR = '$sessionID' AND 
                                            ShoppingCartID_MSTR = ShoppingCartMSTRID_DET AND 
                                            ShoppingCartMotoPartsID_DET = MotoPartsID_MSTR AND
                                            MotoPartsQuantityUnitID_MSTR = QuantityUnitID_MSTR";

                                $tbl = " <div class='row brdr'>
                                            <table class='table table-hover' style='color: gray;'>
                                                <thead>
                                                    <tr>
                                                        <th scope='col'></th>
                                                        <th scope='col'>Cikkszám</th>
                                                        <th scope='col'>Terméknév</th>
                                                        <th scope='col'>Netto</th>
                                                        <th scope='col'>Áfa</th>
                                                        <th scope='col'>&euro;</th>
                                                        <th scope='col'>Egységár</th>
                                                        <th scope='col'>Menny.</th>
                                                        <th scope='col'>Összesen</th>
                                                    </tr>
                                                </thead>
                                                <tbody>";

                                $rowID = 0;
                                $partTotal = 0;
                                $result = mysqli_query($connect, $sql);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    $rowID++;
                                    $partNumber = $row["MotoPartsNumber_MSTR"];
                                    $partName = $row["MotoPartsName_MSTR"];
                                    $partNetto = $row["netto"].".-";
                                    $partVAT =  $row["vat"]."%";
                                    $partEUR = $row["bruttoEUR"]."&euro;";
                                    $partBrutto = $row["brutto"].".-";
                                    $partMee = $row["mee"];
                                    $partQua = $row["qua"]." $partMee";
                                    $partSubtotal = $row["subtotal"];
                                    $partTotal = $partTotal + $partSubtotal;
                                    $tbl.="<tr>
                                            <th scope='row'>$rowID</th>
                                            <td>$partNumber</td>
                                            <td>$partName</td>
                                            <td>$partNetto</td>
                                            <td>$partVAT</td>
                                            <td>$partEUR</td>
                                            <td>$partBrutto</td>
                                            <td>$partQua</td>
                                            <td>$partSubtotal.-</td>
                                          </tr>";
                               
                                }
                                $tbl.= "<tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>Fizetendő:</td>
                                            <td><u><strong>$partTotal.- Ft.</strong></u></td>
                                        </tr>
                                    </tbody></table></div>";
                                //TermékBrutto (&euro;)
                                echo $tbl;
                            ?>


                    </div>



                    <!--------------------------------------------------------------
                        ACCOUNTING
                        -->
                    <div class="row ttl">
                        <div class="col-sm-3 mrg">
                            <p class="d-inline-flex gap-1">
                                <a data-bs-toggle="collapse" 
                                    onclick="setArrow(this)" 
                                    id="isClose"
                                    class="icon-link" 
                                    href="#accounting" 
                                    role="button" 
                                    aria-expanded="false" 
                                    aria-controls="accounting">Számlázás &#11167;</a>                            
                                </p>
                        </div>

                    </div>





                    <div class="collapse row" id="accounting">

                        <form action="index.php" method="POST" >
                            <input type="hidden" name="formName" value="shoppingCartLoginForm">

                            <div class="brdr" style="min-width:270px; color: gray; margin-bottom:5px;">
                                <div style="margin: 5px 15px 5px 5px;">
                                    <span>Ha rendelkezel regisztrációval, akkor itt bejelenkezhetsz és a számlázási mezőket kitölti a rendszer.</span>
                                </div>
                                <div style="display:block; margin:5px;">
                                    <span style="display:inline-block; width:120px;">Felhasználónév</span>
                                    <input type="text" 
                                        class="form-control"
                                        required 
                                        value="LoIs"
                                        id="shoppingCartloginUserName" 
                                        name="shoppingCartloginUserName" 
                                        style="display:inline-block; width:250px;" 
                                        placeholder="e-mail">
                                </div>

                                <div style="display:block; margin:5px;">
                                    <span style="display:inline-block; width:120px;">Jelszó</span>
                                    <input type="password" 
                                        class="form-control"
                                        required 
                                        id="shoppingCartLoginPassword" 
                                        name="shoppingCartLoginPassword" 
                                        style="display:inline-block; width:250px;" 
                                        placeholder="jelszó">
                                </div>

                                <div style="display:inline;">
                                    <span style="display:inline-block; width:120px;"></span>
                                    <button type="submit" class="btn btn-success" style="display:inline; margin-left:5px;">
                                        <span class="glyphicon glyphicon-off"></span> 
                                        Bejelentkezés
                                    </button>
                                </div>

                            </div>

                        </form>


                        <form action="" method="POST" >
                            <input type="hidden" name="formName" value="shoppingCartSendOrderForm">

                            <div class="brdr" style="color: gray">

                                <div style="display:block; margin:5px;">
                                    <span style="display:inline-block; width:120px;">E-mail cím</span>
                                    <input type="email" 
                                        class="form-control"
                                        required 
                                        id="shoppingCartEMail" 
                                        name="shoppingCartEMail" 
                                        style="display:inline-block; width:250px;" 
                                        placeholder="e-mail cím">
                                </div>


                                <div style="display:block; margin:5px;">
                                    <span style="display:inline-block; width:120px;">Vezetéknév</span>
                                    <input type="email" 
                                        class="form-control"
                                        required 
                                        id="shoppingCartFirstName" 
                                        name="shoppingCartFirstName" 
                                        style="display:inline-block; width:250px;" 
                                        placeholder="vezetéknév">
                                </div>




                                <div style="display:block; margin:5px;">
                                    <span style="display:inline-block; width:120px;">Keresztnév</span>
                                    <input type="text" 
                                        class="form-control"
                                        required 
                                        id="shoppingCartMiddleName" 
                                        name="shoppingCartMiddleName" 
                                        style="display:inline-block; width:250px;" 
                                        placeholder="keresztnév">
                                </div>



                                <div style="display:block; margin:5px;">
                                    <span style="display:inline-block; width:120px;">Keresztnév</span>
                                    <input type="text" 
                                        class="form-control"
                                        required 
                                        id="shoppingCartLastName" 
                                        name="shoppingCartLastName" 
                                        style="display:inline-block; width:250px;" 
                                        placeholder="keresztnév">
                                </div>




                                <div style="display:block; margin:5px;">
                                    <span style="display:inline-block; width:120px;">Ország</span>
                                    <select 
                                        class="form-select form-select-sm" 
                                        id="shoppingCartCountryID" 
                                        name="shoppingCartCountryID" 
                                        aria-label=".form-select-sm"></select>
                                </div>




                                <div style="display:block; margin:5px;">
                                    <span style="display:inline-block; width:120px;">Irányítószám</span>
                                    <input type="text" 
                                        class="form-control"
                                        required 
                                        id="shoppingCartPostCode" 
                                        name="shoppingCartPostCode" 
                                        placeholder="0123456789" 
                                        style="display:inline-block; width:120px;"
                                        onkeypress="return onlyNumber(event)" 
                                        maxlength="8">
                                </div>




                                <div style="display:block; margin:5px;">
                                    <span style="display:inline-block; width:120px;">Város</span>
                                    <input type="text" 
                                        class="form-control"
                                        required 
                                        id="shoppingCartCity" 
                                        name="shoppingCartCity" 
                                        style="display:inline-block; width:300px;"
                                        placeholder="város"
                                        maxlength="30">
                                </div>




                                <div style="display:block; margin:5px;">
                                    <span style="display:inline-block; width:120px;">Utca</span>
                                    <input type="text" 
                                        class="form-control"
                                        required 
                                        id="shoppingCartStreet" 
                                        name="shoppingCartStreet" 
                                        style="display:inline-block; width:300px;"
                                        placeholder="út/utca/tér ...stb"
                                        maxlength="50">
                                </div>




                                <div style="display:block; margin:5px;">
                                    <span style="display:inline-block; width:120px;">Házszám/emelet</span>
                                    <input type="text" 
                                        class="form-control"
                                        required 
                                        id="shoppingCartAddress" 
                                        name="shoppingCartAddress" 
                                        style="display:inline-block; width:300px;"
                                        placeholder="házszám/emelet/ajtó ...stb"
                                        maxlength="50">
                                </div>





                                <div style="display:block; margin:5px;">
                                    <span style="display:inline-block; width:120px;">Telefonszám</span>
                                    <input type="text" 
                                        class="form-control"
                                        required 
                                        id="shoppingCartPhone" 
                                        name="shoppingCartPhone" 
                                        style="display:inline-block; width:300px;"
                                        placeholder="+36 20/30/70 ...-...."
                                        onkeypress="return onlyPhone(event)"
                                        maxlength="100">
                                </div>

                            </div>

                        </div>














                    <!--------------------------------------------------------------
                        DELIVERY
                        -->
                    <div class="row ttl">
                        <div class="col-sm-3 mrg">
                            <p class="d-inline-flex gap-1">
                                <a data-bs-toggle="collapse" 
                                    onclick="setArrow(this)" 
                                    class="icon-link" 
                                    id="isClose"
                                    href="#delivery" 
                                    role="button" 
                                    aria-expanded="false" 
                                    aria-controls="delivery">Szállítás &#11167;</a>                            
                                </p>
                        </div>
                    </div>


                    <div class="collapse row" id="delivery">

                        <div class="brdr" style="color: gray">

                                <p class="form-check-label" style="margin:20px"><u><b>Kérlek válassz az alábbi futárszolgálatok közül:</b></u></p>

                                <div style="margin:20px">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="fut" id="fut1" value="option1">
                                        <label class="form-check-label" for="fut1" title="GLS Futárszolgálat">
                                            <img src="imgs/gls.png" style="width:50px;">
                                            GLS
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="fut" id="fut2" value="option2">
                                        <label class="form-check-label" for="fut2" title="Express One Futárszolgálat">
                                            <img src="imgs/express_one.png" style="width:50px;">
                                            Express One
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="fut" id="fut3" value="option3">
                                        <label class="form-check-label" for="fut3" title="DHL Futárszolgálat">
                                            <img src="imgs/dhl.png" style="width:50px;">
                                            DHL
                                        </label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="fut" id="fut4" value="option4">
                                        <label class="form-check-label" for="fut4" title="Foxpost Futárszolgálat">
                                            <img src="imgs/foxpost.png" style="width:20px;">
                                            Foxpost
                                        </label>
                                    </div>
                                </div>





                        </div>
                    </div>









                    <!--------------------------------------------------------------
                        PAYMENT
                        -->
                    <div class="row ttl">
                        <div class="col-sm-3 mrg">
                            <p class="d-inline-flex gap-1">
                                <a data-bs-toggle="collapse" 
                                    onclick="setArrow(this)" 
                                    class="icon-link" 
                                    id="isClose"
                                    href="#payment" 
                                    role="button" 
                                    aria-expanded="false" 
                                    aria-controls="payment">Fizetés &#11167;</a>                            
                                </p>
                        </div>
                    </div>


                    <div class="collapse row" id="payment">

                        <div class="brdr" style="color: gray">

                            <div style="display:block; margin:5px;">


                                <p class="form-check-label" style="margin:20px"><u><b>Hogyan szeretnéd kifizetni a rendelésed?</b></u></p>

                                <div style="margin:20px">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="fiz" id="fiz1" value="option1">
                                        <label class="form-check-label" for="fiz1">Előreutalással</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="fiz" id="fiz2" value="option2">
                                        <label class="form-check-label" for="fiz2">Leülöm a Fazekason</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="fiz" id="fiz3" value="option3">
                                        <label class="form-check-label" for="fiz3">Szépkártyával és/vagy üres flakonokkal</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="fiz" id="fiz4" value="option4">
                                        <label class="form-check-label" for="fiz4">Utánvéttel a futárnál</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="fiz" id="fiz5" value="option5">
                                        <label class="form-check-label" for="fiz5">Készpénzzel a futárnál</label>
                                    </div>

                                </div>





                            </div>


                        </div>
                    </div>








                    <!--------------------------------------------------------------
                        SUBMIT
                        -->
                    <div class="row ttl">
                        <div class="col-sm-3 mrg">
                            <p class="d-inline-flex gap-1">
                                <a data-bs-toggle="collapse" 
                                    onclick="setArrow(this)" 
                                    class="icon-link" 
                                    id="isClose"
                                    href="#ordering" 
                                    role="button" 
                                    aria-expanded="false" 
                                    aria-controls="payment">Megrendelés &#11167;</a>                            
                                </p>
                        </div>
                    </div>


                    <div class="collapse row" id="ordering">

                        <div class="brdr" style="color: gray">

                            <div style="display:block; margin:5px;">

                                <div class="form-check" style="margin:10px;">
                                    <input class="form-check-input" style="margin-right:50px;" type="checkbox" value="" id="aszf" onchange="aszfChange(event)">
                                    <label class="form-check-label" for="aszf">
                                    </label>
                                    <a href="docs/aszf_minta.pdf" target="_new"><u>Kijelentem, hogy az Általános Szerződési Feltételeket átolvastam és a benne leírtakkal egyetértek!</u></a>
                                </div>

                                <button type="submit" class="btn btn-success " id="submitBtn" disabled>
                                    <span class="glyphicon glyphicon-piggy-bank"></span> 
                                    Megrendelés
                                </button>
                                <button class="btn btn-primary" data-dismiss="modal" onclick="clearOrder()">
                                    <span class="glyphicon glyphicon-remove"></span> 
                                    Mégsem
                                </button>

                            </div>

                        </div>
                    </div>











                </form>

            </div>

        </div>


