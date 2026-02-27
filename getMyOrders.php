<?php
    session_start(); 

    include "connect.php";

    $ret = ""; //array();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (isset($_POST["orderID"])) {

            $orderID = $_POST["orderID"];

            $sql = "SELECT 
                        *,
                        SupplierName_MSTR AS supplier,
                        SupplierCost_MSTR AS suppliercost,
                        SupplierCash_MSTR AS suppliercash,
                        PaymentTypeName_MSTR AS payment,
                        PaymentTypeCost_MSTR AS paymentcost
                    FROM 
                        orders_mstr, orders_det, suppliers_mstr, paymenttype_mstr                    
                    WHERE 
                        OrdersID_MSTR = '$orderID' AND 
                        OrdersMSTRID_DET = '$orderID' AND 
                        OrdersSupplierID_MSTR = SupplierID_MSTR AND 
                        OrdersPaymentTypeID_MSTR = PaymentTypeID_MSTR";
            $supplierName = "";
            $supplierCost = "";
            $supplierCash = "";
            $result = mysqli_query($connect, $sql);
            $fullTotal = 0;
            $ic = 0;
            while ($row = mysqli_fetch_assoc($result)) {
                //array_push($ret, $row);
                $partNumber = $row["OrdersPartsNumber_DET"];
                $partName = $row["OrdersPartsName_DET"];
                $partNetto = number_format($row["OrdersNettoPrice_DET"], 0,","," ");
                $partVAT = round($row["OrdersVAT_DET"], 0);
                $partDISC = round($row["OrdersDiscount_DET"], 0);

                $partEUR = number_format($row["OrdersBruttoEURPrice_DET"], 2, ",", " ");
                $partQua = round($row["OrdersQuantity_DET"], 1);
                $partQuaUnit = $row["OrdersQuantityUnit_DET"];

                $partBrutto = round($row["OrdersBruttoPrice_DET"], 0);
                $subtotal = $partBrutto; // ($partBrutto - ($partBrutto * $partDISC)) * $partQua;

                $paymentName = $row["payment"];
                $paymentCost = $row["paymentcost"];
                $supplierName = $row["supplier"];
                $supplierCost = $row["suppliercost"];
                $supplierCash = $row["suppliercash"];

                $ic++;
                $ret .= "<tr style='color: green'>
                            <td>$ic</td>
                            <td>$partNumber</td>
                            <td>$partName</td>
                            <td>$partNetto.- Ft.</td>
                            <td>$partVAT%</td>
                            <td>$partDISC%</td>
                            <td>".number_format($partBrutto, 0, ",", " ").".- Ft.</td>
                            <td>$partEUR.- &euro;</td>
                            <td>$partQua $partQuaUnit</td>
                            <td style='text-align: right'><b><u>".number_format($subtotal, 0,","," ").".- Ft.</u></b></td>
                       </tr>";
                $fullTotal += ($partBrutto * $partQua);
            }

            $fullTotal = number_format($fullTotal, 0, ",", " ");
            //$fmt = new NumberFormatter( 'hu_HU', NumberFormatter::DECIMAL );
            //$fullTotal = $fmt->format($fullTotal);


            $ret .= "<tfoot>

                        <tr>
                            <td colspan='6'></td>
                            <td colspan='3' style='text-align: right'>Szállítási díj: $supplierName</td>
                            <td style='text-align: right; color: green'><b><u>".number_format($supplierCost, 0, ",", " ").".- Ft.</u></b></td>
                        </tr>".

                        (($supplierCash > 0) 
                            ? "<tr>
                                <td colspan='7'></td>
                                <td colspan='2' style='text-align: right'>Készpénzes fizetés:</td>
                                <td style='text-align: right; color: green'><b><u>".number_format($supplierCash, 0, ",", " ").".- Ft.</u></b></td>
                                </tr>"
                            : "").

                        "<tr>
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
