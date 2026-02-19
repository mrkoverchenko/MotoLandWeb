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
                        if (isset($_SESSION["userid"]))
                            $userID = $_SESSION["userid"];

/*                        $sql = "SELECT 
                                    MotoPartsNumber_MSTR,
                                    MotoPartsName_MSTR,
                                    ROUND(MotoPartsBruttoEURPrice_MSTR,2) * ShoppingCartQuantity_DET AS bruttoEUR,
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
                                    MotoPartsQuantityUnitID_MSTR = QuantityUnitID_MSTR";*/



                        $sql = "SELECT 
                                    MotoPartsNumber_MSTR,
                                    MotoPartsName_MSTR,
                                    ROUND(MotoPartsBruttoEURPrice_MSTR, 2) AS bruttoEUR,
                                    ROUND(MotoPartsNettoPrice_MSTR, 0) AS netto,
                                    ROUND(MotoPartsVAT_MSTR * 100, 0) AS vat,
                                    ROUND(MotoPartsDiscount_MSTR * 100, 0) AS disc,
                                    ROUND(MotoPartsBruttoPrice_MSTR, 0) AS brutto,
                                    QuantityUnitUnit_MSTR AS mee,
                                    ShoppingCartQuantity_DET AS qua,
                                    ROUND(MotoPartsBruttoPrice_MSTR * ShoppingCartQuantity_DET, 0) AS subtotal
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
                                                <th scope='col'>No.</th>
                                                <th scope='col'>Cikkszám</th>
                                                <th scope='col'>Terméknév</th>
                                                <th scope='col'>Netto</th>
                                                <th scope='col'>Áfa</th>
                                                <th scope='col'>Kedv.</th>
                                                <th scope='col'>Egységár</th>
                                                <th scope='col'>&euro;</th>
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
                            $partDISC =  $row["disc"]."%";
                            $partBrutto = $row["brutto"].".-";
                            $partEUR = $row["bruttoEUR"]."&euro;";
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
                                    <td>$partDISC</td>
                                    <td>$partBrutto</td>
                                    <td>$partEUR</td>
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
                                    <td></td>
                                    <td style='color:green'><b>Fizetendő:</b></td>
                                    <td style='color:green'><u><strong>$partTotal.- Ft.</strong></u></td>
                                </tr>
                            </tbody></table></div>";
                        //TermékBrutto (&euro;)
                        echo $tbl;
                    ?>
                </div>




                <!--------------------------------------------------------------
                    LOGIN
                    -->
                <div class="row ttl">
                    <div class="col-sm-3 mrg">
                        <p class="d-inline-flex gap-1">
                            <a data-bs-toggle="collapse" 
                                onclick="setArrow(this)" 
                                id="isClose"
                                class="icon-link" 
                                href="#login" 
                                role="button" 
                                aria-expanded="false" 
                                aria-controls="login">Bejelentkezés &#11167;</a>                            
                        </p>
                    </div>
                </div>
                <div class="collapse row" id="login">
                    <?php
                        $country = "";
                        if (!isset($_SESSION['userid'])) {
                            echo "<form action='index.php' method='POST' id='shoppingCartLoginForm' >
                                    <input type='hidden' name='formName' value='shoppingCartLoginForm'>

                                    <div class='brdr' style='min-width:270px; color: gray; margin-bottom:5px;'>
                                        <div style='margin: 5px 15px 5px 5px;'>
                                            <span>Ha rendelkezel regisztrációval, akkor itt bejelenkezhetsz és a számlázási mezőket kitölti a rendszer</span>
                                        </div>

                                        <div style='display:block; margin:5px;'>
                                            <span style='display:inline-block; width:120px;'>Felhasználónév</span>
                                            <input type='text' 
                                                class='form-control'
                                                required 
                                                value=''
                                                id='loginUserName' 
                                                form='shoppingCartLoginForm'
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
                                                form='shoppingCartLoginForm'
                                                name='loginPassword' 
                                                style='display:inline-block; width:250px;' 
                                                placeholder='jelszó'>
                                        </div>

                                        <div style='display:inline;'>
                                            <span style='display:inline-block; width:120px;'></span>
                                            <button type='submit' class='btn btn-success' form='shoppingCartLoginForm' style='display:inline; margin-left:5px;'>
                                                <span class='glyphicon glyphicon-off'></span> 
                                                Bejelentkezés
                                            </button>
                                        </div>

                                    </div>

                                </form>";
                        } else {    
                            echo "  <div class='brdr' style='color: gray'>
                                        <div style='margin: 5px 15px 5px 5px;'>
                                            <span><b><u>Már bejelentkeztél! Csak mondom!</u></b></span>
                                        </div>
                                    </div>";
                                /*
                                    $_SESSION['usernickname'] = $row['UserNickName_MSTR'];
                                    $_SESSION['usertype'] = strtolower($row['UserTypeType_MSTR']);
                                    $_SESSION['userid'] = $row['UserID_MSTR'];
                                    $_SESSION['userfullname'] = $row['UserFullName'];
                                    */
                            $userID = $_SESSION["userid"];
                            $sql = "SELECT * FROM user_mstr, user_det, countries_mstr 
                                    WHERE UserID_MSTR = UserMSTRID_DET AND UserID_MSTR = '$userID' AND CountriesID_MSTR = UserCountryID_DET";
                            $value = mysqli_query($connect, $sql);
                            $row = mysqli_fetch_assoc($value);
                            $email = $row["UserMail_MSTR"];
                            $firstName = $row["UserFirstName_DET"];
                            $middleName = $row["UserMiddleName_DET"];
                            $lastName = $row["UserLastName_DET"];
                            $countryID = $row["UserCountryID_DET"];
                            $country = $row["CountriesCountry_MSTR"];
                            $postCode = $row["UserPostCode_DET"];
                            $city = $row["UserCity_DET"];
                            $street = $row["UserStreet_DET"];
                            $address = $row["UserAddress_DET"];
                            $phone = $row["UserPhone_DET"];
                        }
                    ?>
                </div><!-- class="collapse row" id="login"> -->


                <form action="index.php" method="POST" id="shopingCartSendOrderForm">
                    <input type="hidden" name="formName" value="shoppingCartSendOrderForm">
                    <input type="hidden" name="shoppingCartPartTotal" value="<?php echo $partTotal;?>">
                    <input type="hidden" name="shoppingCartUserCountry" value="<?php echo $country ?? '';?>" id="shoppingCartUserCountry">
                    <input type="hidden" name="shoppingCartSessionID" value="<?php echo $sessionID;?>">
                    <input type="hidden" name="shoppingCartUserID" value="<?php if (isset($userID)) echo $userID; else echo ''; ?>">


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
                        <div class="brdr" style="color: gray">

                            <div style="display:block; margin:5px;">
                                <span style="display:inline-block; width:120px;">E-mail cím</span>
                                <input type="email" 
                                    required
                                    class="form-control" 
                                    value="<?php echo (isset($userID)) ? $email : ''; ?>" 
                                    id="shoppingCartEMail" 
                                    name="shoppingCartEMail" 
                                    style="display:inline-block; width:250px;" 
                                    placeholder="e-mail cím">
                            </div>


                            <div style="display:block; margin:5px;">
                                <span style="display:inline-block; width:120px;">Vezetéknév</span>
                                <input type="text" 
                                    required
                                    class="form-control"
                                    value="<?php echo (isset($userID)) ? $firstName : ''; ?>"
                                    id="shoppingCartFirstName" 
                                    name="shoppingCartFirstName" 
                                    style="display:inline-block; width:250px;" 
                                    placeholder="vezetéknév">
                            </div>




                            <div style="display:block; margin:5px;">
                                <span style="display:inline-block; width:120px;">Keresztnév</span>
                                <input type="text" 
                                    required
                                    class="form-control"
                                    value="<?php echo (isset($userID)) ? $middleName : ''; ?>"
                                    id="shoppingCartMiddleName" 
                                    name="shoppingCartMiddleName" 
                                    style="display:inline-block; width:250px;" 
                                    placeholder="keresztnév">
                            </div>



                            <div style="display:block; margin:5px;">
                                <span style="display:inline-block; width:120px;">Keresztnév</span>
                                <input type="text" 
                                    class="form-control"
                                    value="<?php echo (isset($userID)) ? $lastName : ''; ?>"
                                    id="shoppingCartLastName" 
                                    name="shoppingCartLastName" 
                                    style="display:inline-block; width:250px;" 
                                    placeholder="keresztnév">
                            </div>





                            <div style="display:block; margin:5px;">
                                <span style="display:inline-block; width:120px;">Ország</span>
                                <select 
                                    required
                                    onchange="changeCountry(this)"
                                    class="form-select form-select-sm" 
                                    id="shoppingCartCountryID" 
                                    name="shoppingCartCountryID" 
                                    aria-label=".form-select-sm">

                                    <?php
                                        $sql = "SELECT CountriesID_MSTR, CountriesCountry_MSTR 
                                                FROM countries_mstr 
                                                ORDER BY CountriesCountry_MSTR ASC";
                                        $ret = "<option></option>";
                                        $result = mysqli_query($connect, $sql);
                                        while ($row = mysqli_fetch_assoc($result)) {                                    
                                            $id = $row["CountriesID_MSTR"];
                                            $cntr = $row["CountriesCountry_MSTR"];
                                            
                                            if ( !isset($_SESSION["userid"]) )
                                                $ret .= "<option value = '$cntr'>$cntr</option>"; 
                                            else
                                                $ret .= "<option ".(($countryID == $id)?"selected":"")." value = '$id'>$cntr</option>"; 
                                        }
                                        echo $ret;
                                    ?>

                                </select>
                            </div>




                            <div style="display:block; margin:5px;">
                                <span style="display:inline-block; width:120px;">Irányítószám</span>
                                <input type="text" 
                                    class="form-control"
                                    required
                                    value="<?php echo (isset($userID)) ? $postCode : ''; ?>"
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
                                    value="<?php echo (isset($userID)) ? $city : ''; ?>"
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
                                    value="<?php echo (isset($userID)) ? $street : ''; ?>"
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
                                    value="<?php echo (isset($userID)) ? $address : ''; ?>"
                                    id="shoppingCartAddress" 
                                    name="shoppingCartAddress" 
                                    style="display:inline-block; width:300px;"
                                    placeholder="házszám/emelet/ajtó ...stb"
                                    maxlength="50">
                            </div>





                            <div style="display:block; margin:5px;">
                                <span style="display:inline-block; width:120px;">Telefonszám</span>
                                <input type="text" 
                                    required
                                    class="form-control"
                                    value="<?php echo (isset($userID)) ? $phone : ''; ?>" 
                                    id="shoppingCartPhone" 
                                    name="shoppingCartPhone" 
                                    style="display:inline-block; width:300px;" 
                                    placeholder="+36 20/30/70 ...-...." 
                                    onkeypress="return onlyPhone(event)" 
                                    maxlength="100"> 
                            </div>

                        </div>

                    </div><!-- class="collapse row" id="accounting">-->




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
                                    <input class="form-check-input" type="radio" name="shoppingCartFut" id="fut1" required value="option1">
                                    <label class="form-check-label" for="fut1" title="GLS Futárszolgálat">
                                        <img src="imgs/gls.png" style="width:50px;">
                                        GLS
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="shoppingCartFut" id="fut2" required value="option2">
                                    <label class="form-check-label" for="fut2" title="Express One Futárszolgálat">
                                        <img src="imgs/express_one.png" style="width:50px;">
                                        Express One
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="shoppingCartFut" id="fut3" required value="option3">
                                    <label class="form-check-label" for="fut3" title="DHL Futárszolgálat">
                                        <img src="imgs/dhl.png" style="width:50px;">
                                        DHL
                                    </label>
                                </div>

                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="shoppingCartFut" id="fut4" required value="option4">
                                    <label class="form-check-label" for="fut4" title="Foxpost Futárszolgálat">
                                        <img src="imgs/foxpost.png" style="width:20px;">
                                        Foxpost
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div><!-- class="collapse row" id="delivery"> -->



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
                                        <input class="form-check-input" type="radio" name="shoppingCartFiz" id="fiz1" required value="option1">
                                        <label class="form-check-label" for="fiz1">Előreutalással</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="shoppingCartFiz" id="fiz2" required value="option2">
                                        <label class="form-check-label" for="fiz2">Leülöm a Fazekason</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="shoppingCartFiz" id="fiz3" required value="option3">
                                        <label class="form-check-label" for="fiz3">Szépkártyával és/vagy üres flakonokkal</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="shoppingCartFiz" id="fiz4" required value="option4">
                                        <label class="form-check-label" for="fiz4">Utánvéttel a futárnál</label>
                                    </div>

                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="shoppingCartFiz" id="fiz5" required value="option5">
                                        <label class="form-check-label" for="fiz5">Készpénzzel a futárnál</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div><!--  class="collapse row" id="payment"> -->





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
                                    <input class="form-check-input" 
                                            style="margin-right:50px;" 
                                            type="checkbox" 
                                            value="" 
                                            name="shoppingCartASZF"
                                            id="aszf" 
                                            onchange="aszfChange(event)">
                                    <label class="form-check-label" for="aszf">
                                    </label>
                                    <a href="docs/aszf_minta.pdf" 
                                        target="_new">
                                        <u>Kijelentem, hogy az Általános Szerződési Feltételeket átolvastam és a benne leírtakkal egyetértek!</u>
                                    </a>
                                </div>

                                <button type="submit" class="btn btn-success " id="submitBtn" value='shopingCartSendOrderForm' disabled>
                                    <span class="glyphicon glyphicon-piggy-bank"></span> 
                                    Megrendelés
                                </button>
                                <button class="btn btn-primary" data-dismiss="modal" onclick="clearOrder()">
                                    <span class="glyphicon glyphicon-remove"></span> 
                                    Mégsem
                                </button>
                            </div>
                        </div>
                    </div><!-- class="collapse row" id="ordering"> -->
                </form><!-- action="index.php" method="POST" > -->
            </div>
        </div>

        <?php mysqli_close($connect); ?>

