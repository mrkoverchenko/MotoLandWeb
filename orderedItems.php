<?php
    session_start();
    if (empty($_SESSION['userid'])) { 
    	header("Location: index.php");
        exit();
    }
    include "connect.php";

?>
        <style>
            .ordereditemsbody {
                margin-top:60px; 
                color:gray;
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


        <div class="ordereditemsbody">



            <div class="row">
                <div class="col-sm-2 mrg">
                    <H3><strong> Rendeléseim </strong></H3>
                </div>
            </div>


            <div class="row">
                <div class="col-sm-2 mrg">
                     Rendelési tételek
                </div>
                <div class="col-sm-5" >
                    <div class="form-group">
                        <select class="form-control" id="myorderslist" onchange="getMyOrders()">
                            <option></option>
                            <?php
                                $sql = "SELECT 
                                            OrdersID_MSTR,
                                            OrdersDateTime_MSTR,
                                            OrdersFullCost_MSTR,
                                            OrderStatusStatus_MSTR              
                                        FROM 
                                            orders_mstr, orderstatus_mstr
                                        WHERE 
                                            OrdersUserID_MSTR = '$userID' AND
                                            OrdersStatusStatusID_MSTR = OrderStatusID_MSTR 
                                        GROUP BY
                                            OrdersDateTime_MSTR DESC";
                                $result = mysqli_query($connect, $sql);

                                while ($row = mysqli_fetch_assoc($result)) {                                    
                                    $id = $row["OrdersID_MSTR"];
                                    $date = $row["OrdersDateTime_MSTR"];
                                    $cost = $row["OrdersFullCost_MSTR"];
                                    $status = $row["OrderStatusStatus_MSTR"];
                                    echo "<option value='$id'>$date - $status ($cost.- Ft.)</option>";
                                }
                                mysqli_close($connect);
                            ?>

                        </select>
                    </div>                     
                </div>
            </div>



            <div class="row">
                <div class='row brdr'>
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

                        <tbody id="orderedTBody">
                               
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


