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
                <div id="types" class="container tab-pane active tabcontain"><br>
                    <h3>Keresés típus szerint</h3>
                    <form action="index.php">

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
                                    <a data-bs-toggle="collapse" class="icon-link" href="#explodedView" role="button" aria-expanded="false" aria-controls="explodedView">
                                        Robbantott nézet &#11167;
                                    </a>                            
                                 </p>
                            </div>

                        </div>


                        <div class="collapse" id="explodedView">
                            <div class="row">
                                <div class="col-sm-2 mrg" >
                                    Robbantott nézet
                                </div>
                                <div class="col-sm-6" >
                                    <div class="card card-body">
                                        <img id="explodedViewIMG" src="" style="width:100px; height:100px; margin-bottom:20px;">
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
                        </div>


                        <div class="row">
                            <div class="col-sm-2 mrg" >
                                Brutto (Ft.)
                            </div>
                            <div class="col-sm-2" >
                                <div class="form-group">
                                    <input type="text" class="form-control" id="motopartbruttoprice" name="motopartbruttoprice"/>
                                </div>                     
                            </div>
                        </div>    


                        <div class="row">
                            <div class="col-sm-2 mrg" >
                                Brutto (&euro;)
                            </div>
                            <div class="col-sm-2" >
                                <div class="form-group">
                                    <input type="text" class="form-control" id="motopartbruttoeurprice" name="motopartbruttoeurprice"/>
                                </div>                     
                            </div>
                            <div class="col-sm-2 mrg">
                                <p class="d-inline-flex gap-1">
                                    <a data-bs-toggle="collapse" class="icon-link" href="#showDetails" role="button" aria-expanded="false" aria-controls="showDetails">
                                        Részletek &#11167;
                                    </a>                            
                                 </p>
                            </div>
                        </div>    


                        <div class="collapse" id="showDetails">
                            <div class="card card-body">

                                <div class="row">
                                    <div class="col-sm-2 mrg" >
                                        Netto (Ft.)
                                    </div>
                                    <div class="col-sm-2" >
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="motopartnettoprice" name="motopartnettoprice"/>
                                        </div>                     
                                    </div>
                                </div>    

                                <div class="row">
                                    <div class="col-sm-2 mrg" >
                                        Áfa
                                    </div>
                                    <div class="col-sm-2" >
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="motopartvat" name="motopartvat"/> 
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
                                            <input type="text" class="form-control" id="motopartdiscount" name="motopartdiscount"/>
                                        </div>                     
                                    </div>
                                </div>    

                                <div class="row">
                                    <div class="col-sm-2 mrg" >
                                        Raktáron
                                    </div>
                                    <div class="col-sm-2" >
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="motopartquantity" name="motopartquantity"/>
                                        </div>                     
                                    </div>
                                    <div class="col-sm-2" >
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="motopartquantityunit" name="motopartquantityunit"/>
                                        </div>                     
                                    </div>
                                </div>    


                                <div class="row">
                                    <div class="col-sm-2 mrg" >
                                        Információ
                                    </div>
                                    <div class="col-sm-6" >
                                        <div class="form-group">
                                            <input type="text" class="form-control" id="motopartinfo" name="motopartinfo"/>
                                        </div>                     
                                    </div>
                                </div>    



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


<?php
/*



            <div class="tab-content" id="myTabContent">

                <div class="tab-pane fade" id="types" role="tabpanel" aria-labelledby="types-tab">
                    <h2>Típus keresés</h2>
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


*/
?>