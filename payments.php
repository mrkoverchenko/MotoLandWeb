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


        <div class="paymentsbody">

            <div id="types" class="container" style="margin-bottom:30px;">
                
                <form action="addToShoppingCart.php" method="POST">
                    
                    <input type="hidden" name="formName" value="paymentsForm">
                    


                    <div class="row">
                        <div class="col-sm-2 mrg" >
                            Termékadatok
                        </div>
                        <div class="col-sm-2 mrg">
                            <p class="d-inline-flex gap-1">
                                <a data-bs-toggle="collapse" 
                                    onclick="setArrow(this)" 
                                    class="icon-link" 
                                    href="#showDetails" 
                                    role="button" 
                                    aria-expanded="false" 
                                    aria-controls="showDetails">Részletek &#11167;</a>                            
                                </p>
                        </div>

                    </div>




                    <div class="collapse" id="showDetails">

                        <div id="details">


                            <div class="row">
                                <div class="col-sm-2 mrg" >
                                    Brutto (&euro;)
                                </div>
                                <div class="col-sm-2" >
                                    <div class="form-group">
                                        <input type="text" readonly class="form-control readonly" id="motopartbruttoeurprice"/>
                                    </div>                     
                                </div>
                            </div>    

                        </div>
                    </div>



                    <div class="row">
                        <div class="col-sm-2 mrg" >
                            Megrendelő
                        </div>
                        <div class="col-sm-2 mrg">
                            <p class="d-inline-flex gap-1">
                                <a data-bs-toggle="collapse" 
                                    onclick="setArrow(this)" 
                                    class="icon-link" 
                                    href="#showDetails1" 
                                    role="button" 
                                    aria-expanded="false" 
                                    aria-controls="showDetails1">Részletek &#11167;</a>                            
                                </p>
                        </div>

                    </div>




                    <div class="collapse" id="showDetails1">

                        <div id="details">


                            <div class="form-group row">
                                <label for="regEmail" class="col-sm-4 col-form-label"> E-mail cím *</label>
                                <div class="col-sm-6">
                                    <input type="email" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regEmail" 
                                            name="regEmail" 
                                            placeholder="e-mail cím"
                                            style="width:300px"
                                            maxlength="64">
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="regFirstName" class="col-sm-4 col-form-label" style="margin-top:5px"> Vezetéknév *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regFirstName" 
                                            name="regFirstName" 
                                            placeholder="vezetéknév"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regMiddleName" class="col-sm-4 col-form-label" style="margin-top:5px" > Keresztnév *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regMiddleName" 
                                            name="regMiddleName" 
                                            placeholder="keresztnév"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regLastName" class="col-sm-4 col-form-label" style="margin-top:5px"> Keresztnév</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            alt="noChecking"
                                            class="form-control-plaintext" 
                                            id="regLastName" 
                                            name="regLastName" 
                                            placeholder="keresztnév"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regCountryID" class="col-sm-4 col-form-label" style="margin-top:5px"> Ország *</label>
                                <div class="col-sm-6">
                                    <select class="form-select form-select-sm" id="regCountryID" name="regCountryID" aria-label=".form-select-sm example"></select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regPostCode" class="col-sm-4 col-form-label"> Irányítószám *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required 
                                            class="form-control-plaintext" 
                                            id="regPostCode" 
                                            name="regPostCode" 
                                            placeholder="irányítószám" 
                                            style="width:300px" 
                                            onkeypress="return onlyNumber(event)" 
                                            maxlength="8">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regCity" class="col-sm-4 col-form-label"> Város *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regCity" 
                                            name="regCity" 
                                            placeholder="város"
                                            style="width:300px"
                                            maxlength="30">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regStreet" class="col-sm-4 col-form-label"> Utca *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regStreet" 
                                            name="regStreet" 
                                            placeholder="út/utca/tér ...stb"
                                            style="width:300px"
                                            maxlength="30">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regAddress" class="col-sm-4 col-form-label"> Házszám/emelet *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regAddress" 
                                            name="regAddress" 
                                            placeholder="házszám/emelet/ajtó...stb"
                                            style="width:300px"
                                            maxlength="50">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regPhone" class="col-sm-4 col-form-label"> Telefonszám *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regPhone" 
                                            name="regPhone" 
                                            placeholder="telefonszám"
                                            style="width:300px"
                                            maxlength="30"
                                            onkeypress="return onlyPhone(event)">
                                </div>
                            </div>

                        </div>
                    </div>






                    <div class="row">
                        <div class="col-sm-2 mrg" >
                            Szállítás
                        </div>
                        <div class="col-sm-2 mrg">
                            <p class="d-inline-flex gap-1">
                                <a data-bs-toggle="collapse" 
                                    onclick="setArrow(this)" 
                                    class="icon-link" 
                                    href="#showDetails2" 
                                    role="button" 
                                    aria-expanded="false" 
                                    aria-controls="showDetails2">Részletek &#11167;</a>                            
                                </p>
                        </div>

                    </div>




                    <div class="collapse" id="showDetails2">

                        <div id="details">


                            <div class="form-group row">
                                <label for="regEmail" class="col-sm-4 col-form-label"> E-mail cím *</label>
                                <div class="col-sm-6">
                                    <input type="email" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regEmail" 
                                            name="regEmail" 
                                            placeholder="e-mail cím"
                                            style="width:300px"
                                            maxlength="64">
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="regFirstName" class="col-sm-4 col-form-label" style="margin-top:5px"> Vezetéknév *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regFirstName" 
                                            name="regFirstName" 
                                            placeholder="vezetéknév"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regMiddleName" class="col-sm-4 col-form-label" style="margin-top:5px" > Keresztnév *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regMiddleName" 
                                            name="regMiddleName" 
                                            placeholder="keresztnév"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regLastName" class="col-sm-4 col-form-label" style="margin-top:5px"> Keresztnév</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            alt="noChecking"
                                            class="form-control-plaintext" 
                                            id="regLastName" 
                                            name="regLastName" 
                                            placeholder="keresztnév"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regCountryID" class="col-sm-4 col-form-label" style="margin-top:5px"> Ország *</label>
                                <div class="col-sm-6">
                                    <select class="form-select form-select-sm" id="regCountryID" name="regCountryID" aria-label=".form-select-sm example"></select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regPostCode" class="col-sm-4 col-form-label"> Irányítószám *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required 
                                            class="form-control-plaintext" 
                                            id="regPostCode" 
                                            name="regPostCode" 
                                            placeholder="irányítószám" 
                                            style="width:300px" 
                                            onkeypress="return onlyNumber(event)" 
                                            maxlength="8">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regCity" class="col-sm-4 col-form-label"> Város *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regCity" 
                                            name="regCity" 
                                            placeholder="város"
                                            style="width:300px"
                                            maxlength="30">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regStreet" class="col-sm-4 col-form-label"> Utca *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regStreet" 
                                            name="regStreet" 
                                            placeholder="út/utca/tér ...stb"
                                            style="width:300px"
                                            maxlength="30">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regAddress" class="col-sm-4 col-form-label"> Házszám/emelet *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regAddress" 
                                            name="regAddress" 
                                            placeholder="házszám/emelet/ajtó...stb"
                                            style="width:300px"
                                            maxlength="50">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regPhone" class="col-sm-4 col-form-label"> Telefonszám *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regPhone" 
                                            name="regPhone" 
                                            placeholder="telefonszám"
                                            style="width:300px"
                                            maxlength="30"
                                            onkeypress="return onlyPhone(event)">
                                </div>
                            </div>

                        </div>
                    </div>






                    <div class="row">
                        <div class="col-sm-2 mrg" >
                            Fizetés
                        </div>
                        <div class="col-sm-2 mrg">
                            <p class="d-inline-flex gap-1">
                                <a data-bs-toggle="collapse" 
                                    onclick="setArrow(this)" 
                                    class="icon-link" 
                                    href="#showDetails3" 
                                    role="button" 
                                    aria-expanded="false" 
                                    aria-controls="showDetails3">Részletek &#11167;</a>                            
                                </p>
                        </div>

                    </div>



                    <div class="collapse" id="showDetails3">

                        <div id="details">


                            <div class="form-group row">
                                <label for="regEmail" class="col-sm-4 col-form-label"> E-mail cím *</label>
                                <div class="col-sm-6">
                                    <input type="email" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regEmail" 
                                            name="regEmail" 
                                            placeholder="e-mail cím"
                                            style="width:300px"
                                            maxlength="64">
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="regFirstName" class="col-sm-4 col-form-label" style="margin-top:5px"> Vezetéknév *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regFirstName" 
                                            name="regFirstName" 
                                            placeholder="vezetéknév"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regMiddleName" class="col-sm-4 col-form-label" style="margin-top:5px" > Keresztnév *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regMiddleName" 
                                            name="regMiddleName" 
                                            placeholder="keresztnév"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regLastName" class="col-sm-4 col-form-label" style="margin-top:5px"> Keresztnév</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            alt="noChecking"
                                            class="form-control-plaintext" 
                                            id="regLastName" 
                                            name="regLastName" 
                                            placeholder="keresztnév"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regCountryID" class="col-sm-4 col-form-label" style="margin-top:5px"> Ország *</label>
                                <div class="col-sm-6">
                                    <select class="form-select form-select-sm" id="regCountryID" name="regCountryID" aria-label=".form-select-sm example"></select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regPostCode" class="col-sm-4 col-form-label"> Irányítószám *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required 
                                            class="form-control-plaintext" 
                                            id="regPostCode" 
                                            name="regPostCode" 
                                            placeholder="irányítószám" 
                                            style="width:300px" 
                                            onkeypress="return onlyNumber(event)" 
                                            maxlength="8">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regCity" class="col-sm-4 col-form-label"> Város *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regCity" 
                                            name="regCity" 
                                            placeholder="város"
                                            style="width:300px"
                                            maxlength="30">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regStreet" class="col-sm-4 col-form-label"> Utca *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regStreet" 
                                            name="regStreet" 
                                            placeholder="út/utca/tér ...stb"
                                            style="width:300px"
                                            maxlength="30">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regAddress" class="col-sm-4 col-form-label"> Házszám/emelet *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regAddress" 
                                            name="regAddress" 
                                            placeholder="házszám/emelet/ajtó...stb"
                                            style="width:300px"
                                            maxlength="50">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regPhone" class="col-sm-4 col-form-label"> Telefonszám *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regPhone" 
                                            name="regPhone" 
                                            placeholder="telefonszám"
                                            style="width:300px"
                                            maxlength="30"
                                            onkeypress="return onlyPhone(event)">
                                </div>
                            </div>

                        </div>
                    </div>





                </form>                
            </div>







        </div>


