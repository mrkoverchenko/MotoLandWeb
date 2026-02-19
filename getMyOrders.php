<?php
    session_start(); 

    include "connect.php";

    $ret = ""; //array();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (isset($_POST["orderID"])) {

            $orderID = $_POST["orderID"];

            $sql = "SELECT * FROM orders_det WHERE OrdersMSTRID_DET = '$orderID'";

            $result = mysqli_query($connect, $sql);
            $ic = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                //array_push($ret, $row);
                $partNumber = $row["OrdersPartsNumber_DET"];
                $partName = $row["OrdersPartsName_DET"];
                $partNetto = round($row["OrdersNettoPrice_DET"], 0);
                $partVAT = round($row["OrdersVAT_DET"], 0);
                $partDISC = round($row["OrdersDiscount_DET"], 0);
                $partBrutto = round($row["OrdersBruttoPrice_DET"], 0);
                $partEUR = round($row["OrdersBruttoEURPrice_DET"], 2);
                $partQua = round($row["OrdersQuantity_DET"], 0);
                $partQuaUnit = $row["OrdersQuantityUnit_DET"];
                $ic++;
                $ret .= "<tr>
                            <td>$ic</td>
                            <td>$partNumber</td>
                            <td>$partName</td>
                            <td>$partNetto.- Ft.</td>
                            <td>$partVAT %</td>
                            <td>$partDISC %</td>
                            <td>$partBrutto.- Ft.</td>
                            <td>$partEUR &euro;</td>
                            <td>$partQua $partQuaUnit</td>
                            <td>".$partBrutto * $partQua.".- Ft.</td>
                       </tr>";
                       
            }

        }

    }

    mysqli_close($connect);
    echo $ret;
    //echo json_encode($ret);
?>
