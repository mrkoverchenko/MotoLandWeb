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


        <div class="orderbody">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#types">Gyári alkatrészek</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#filters">Szűrőkereső</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#secondhand">Használt alkatrészek</a>
                </li>
            </ul>



            <div class="tab-content">
                <div id="types" class="container tab-pane active tabcontain" style="margin-bottom:80px;"><br>
                    <h3>Keresés típus szerint</h3>

                    <form action="addToShoppingCart.php" method="POST">

                        <input type="hidden" name="formName" value="orderingForm">



                        <div class="row">
                            <div class="col-sm-2 mrg">
                                Gyártó
                            </div>


                            <div class="col-sm-4" >
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
                            <div class="col-sm-2 mrg" >
                                Típus
                            </div>
                            <div class="col-sm-6" >
                                <div class="form-group">
                                    <select class="form-control" id="mototype" name="mototype" onchange="typeSelect(this)"></select>
                                </div>                     
                            </div>
                        </div>



                        <div class="row">
                            <div class="col-sm-2 mrg" >
                                Alkatrész kategória
                            </div>
                            <div class="col-sm-6" >
                                <div class="form-group">
                                    <select class="form-control" id="motopartscategory" name="motopartscategory" onchange="categorySelect(this)"></select>
                                </div>                     
                            </div>
                            <div class="col-sm-2 mrg">
                                <p class="d-inline-flex gap-1">
                                    <a data-bs-toggle="collapse" 
                                        onclick="setArrow(this)" 
                                        class="icon-link" 
                                        href="#explodedView" 
                                        role="button" 
                                        aria-expanded="false" 
                                        aria-controls="explodedView">Ábra &#11167;</a>                            
                                 </p>
                            </div>

                        </div>


                        <div class="collapse" id="explodedView">
                            <div class="row">
                                <div class="col-sm-2 mrg" >
                                    Robbantott nézet
                                </div>
                                <div class="col-sm-6" >
                                    <div>
                                        <img id="explodedViewIMG" style="width:100px; height:auto; margin-bottom:20px;">
                                    </div>
                                </div>
                                <div class="col-sm-1" >
                                    <div>
                                        <img title="Nagyítás" 
                                            id="magni"
                                            src="http://localhost/mrkoverchenko/MotoLandWeb/imgs/magni+.png" 
                                            style="width:20px; margin-bottom:20px; cursor: pointer"
                                            onclick="changeSize(false)">
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="row">
                            <div class="col-sm-2 mrg" >
                                Alkatrész
                            </div>
                            <div class="col-sm-6" >
                                <div class="form-group">
                                    <select class="form-control" id="motoparts" name="motoparts" onchange="partSelect(this)"></select>
                                </div>                     
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
                                        Brutto (Ft.)
                                    </div>
                                    <div class="col-sm-2" >
                                        <div class="form-group">
                                            <input type="text" readonly class="form-control readonly" id="motopartbruttoprice" name="motopartbruttoprice"/>
                                        </div>                     
                                    </div>
                                </div>    

                                <div class="row">
                                    <div class="col-sm-2 mrg" >
                                        Brutto (&euro;)
                                    </div>
                                    <div class="col-sm-2" >
                                        <div class="form-group">
                                            <input type="text" readonly class="form-control readonly" id="motopartbruttoeurprice" name="motopartbruttoeurprice"/>
                                        </div>                     
                                    </div>
                                </div>    


                                <div class="row">
                                    <div class="col-sm-2 mrg" >
                                        Netto (Ft.)
                                    </div>
                                    <div class="col-sm-2" >
                                        <div class="form-group">
                                            <input type="text" readonly class="form-control readonly" id="motopartnettoprice" name="motopartnettoprice"/>
                                        </div>                     
                                    </div>
                                </div>    

                                <div class="row">
                                    <div class="col-sm-2 mrg" >
                                        Áfa
                                    </div>
                                    <div class="col-sm-2" >
                                        <div class="form-group">
                                            <input type="text" readonly class="form-control readonly" id="motopartvat" name="motopartvat"/> 
                                        </div>                     
                                    </div>
                                    <div class="col-sm-2" >
                                        <div class="form-group mrg">%</div>                     
                                    </div>

                                </div>    




                                <div class="row">
                                    <div class="col-sm-2 mrg" >
                                        Kedvezmény
                                    </div>
                                    <div class="col-sm-2" >
                                        <div class="form-group">
                                            <input type="text" readonly class="form-control readonly" id="motopartdiscount" name="motopartdiscount"/>
                                        </div>                     
                                    </div>
                                    <div class="col-sm-2" >
                                        <div class="form-group mrg">%</div>                     
                                    </div>
                                </div>    

                                <div class="row">
                                    <div class="col-sm-2 mrg" >
                                        Raktáron
                                    </div>
                                    <div class="col-sm-2" >
                                        <div class="form-group">
                                            <input type="text" readonly class="form-control readonly" id="motopartquantity" name="motopartquantity"/>
                                        </div>                     
                                    </div>
                                    <div class="col-sm-2" >
                                        <div class="form-group">
                                            <input type="text" readonly class="form-control readonly" id="motopartquantityunit" name="motopartquantityunit"/>
                                        </div>                     
                                    </div>
                                </div>    


                                <div class="row">
                                    <div class="col-sm-2 mrg" >
                                        Információ
                                    </div>
                                    <div class="col-sm-6" >
                                        <div class="form-group">
                                            <input type="text" readonly class="form-control readonly" id="motopartinfo" name="motopartinfo"/>
                                        </div>                     
                                    </div>
                                </div>    

                            </div>

                        </div>                        


                        <div class="row">
                            <div class="col-sm-2 mrg" >Rendelési mennyiség</div>
                            <div class="col-sm-1">
                                <div class="form-group">
                                    <input type="number" 
                                        class="form-control" 
                                        min="1" 
                                        max="10" 
                                        disabled 
                                        id="quantity" 
                                        name="quantity" 
                                        value="1" 
                                        style="width:80px"
                                        onkeydown="disText(event)" 
                                        onchange="setCost(this)">
                                </div>                     
                            </div>
                            <div class="col-sm-2 mrg" id="mee" >Rendelési mennyiség</div>
                            <div class="col-sm-3 mrg" id="meeDiv" ></div>
                        </div>



                        <div class="row">
                            <div class="col-sm-2 mrg" >Fizetendő</div>
                            <div class="col-sm-1">
                                <div class="form-group">
                                    <input type="text" 
                                        class="form-control" 
                                        disabled 
                                        readonly
                                        id="totalcost" 
                                        style="width:80px">
                                </div>                     
                            </div>
                            <div class="col-sm-2 mrg">Ft.</div>
                        </div>



                        <div class="row">
                            <div class="col-sm-2 mrg" ></div>
                            <div class="col-sm-2 mrg">
                                <button type="submit" id="intoShoppingCart" class="btn btn-success" disabled>
                                    <span class="glyphicon glyphicon-shopping-cart"></span> 
                                    Kosárba
                                </button>
                            </div>

                        </div>


                    </form>                
                </div>



                <div id="filters" class="container tab-pane tabcontain"><br>
                    <h3>Szűrőkereső</h3>
                    <p>.</p>
                </div>



                <div id="secondhand" class="container tab-pane tabcontain"><br>
                    <h3>Használt alkatrészek</h3>
                    <p>.</p>
                </div>
            </div>




        </div>


