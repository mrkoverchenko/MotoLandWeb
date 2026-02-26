<?php
    session_start(); 

    include "connect.php";

    $ret = ""; //array();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (isset($_POST["orderID"])) {

            $orderID = $_POST["orderID"];

            $sql = "SELECT * FROM orders_det WHERE OrdersMSTRID_DET = '$orderID'";

            $result = mysqli_query($connect, $sql);
            $fullTotal = 0;
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
                $ret .= "<tr style='color: green'>
                            <td>$ic</td>
                            <td>$partNumber</td>
                            <td>$partName</td>
                            <td>$partNetto.- Ft.</td>
                            <td>$partVAT %</td>
                            <td>$partDISC %</td>
                            <td>$partBrutto.- Ft.</td>
                            <td>$partEUR &euro;</td>
                            <td>$partQua $partQuaUnit</td>
                            <td style='text-align: right'><b><u>".$partBrutto * $partQua.".- Ft.</u></b></td>
                       </tr>";
                $fullTotal += ($partBrutto * $partQua);
            }
            $ret .= "<tfoot>
                        <tr>
                            <td colspan='10' style='text-align: right'></td>
                        </tr>
                        <tr>
                            <td colspan='9' style='text-align: right'><b>Fizetendő: </b></td>
                            <td style='text-align: right; color: green' colspan='1'><b><u>$fullTotal.- Ft.</u></b></td>
                        </tr>
                    </tfoot>";

        }

    }

    mysqli_close($connect);
    echo $ret;
    //echo json_encode($ret);
?>
