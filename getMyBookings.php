<?php
    session_start(); 

    include "connect.php";

    $ret = ""; 
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (isset($_POST["bookingID"])) {

            $bookingID = $_POST["bookingID"];

            $sql = "SELECT 
                        * ,
                        ROUND(BookingWorkCost_MSTR, 0) AS cost
                    FROM 
                        booking_mstr, 
                        motomanufacturer_mstr,
                        mototype_mstr,
                        orderstatus_mstr
                    WHERE 
                        BookingID_MSTR = '$bookingID' AND 
                        MotoManufacturerID_MSTR = BookingMotoManID_MSTR AND 
                        MotoTypeID_MSTR = BookingMotoTypeID_MSTR AND 
                        OrderStatusID_MSTR = BookingWorkStatusID_MSTR";

            $result = mysqli_query($connect, $sql);
            $ic = 0;

            while ($row = mysqli_fetch_assoc($result)) {
                $motoRegNumber = $row["BookingMotoRegNumber_MSTR"];
                $motoMan = $row["MotoManufacturerManufacturer_MSTR"];
                $motoType = $row["MotoTypeType_MSTR"];
                $motoYear = $row["BookingMotoYear_MSTR"];
                $checkList = $row["BookingCheckList_MSTR"];
                $checkMList = $row["BookingMechanicCheckList_MSTR"];
                $regDateTime = $row["BookingRegDateTime_MSTR"];
                $note = $row["BookingNote_MSTR"];
                $lastModifiy = $row["BookingLastModifiedDate_MSTR"];
                $workStatus = $row["OrderStatusStatus_MSTR"];
                $workBegin = $row["BookingWorkBegin_MSTR"];
                $workEnd = $row["BookingWorkEnd_MSTR"];
                $workHours = $row["BookingWorkHours_MSTR"];
                $workCost = number_format($row["cost"], 0,","," ");

                $ic++;
                $ret .= "<tr style='color: green; border: 0px solid red'>
                            <td>$ic.</td>
                            <td>$motoRegNumber</td>
                            <td>$motoMan</td>
                            <td>$motoType</td>
                            <td>$motoYear</td>
                            <td>$regDateTime</td>
                            <td>$lastModifiy</td>
                       </tr>";

                $ret .= "<tfoot>
                            <tr>
                                <td colspan='2' style='text-align: right'><b>Szervíz státusz: </b></td>
                                <td style='color: green' colspan='2'>$workStatus</td>
                            </tr>
                            <tr>
                                <td colspan='2' style='text-align: right'><b>Munka kezdés: </b></td>
                                <td style='color: green' colspan='2'>$workBegin</td>
                            </tr>
                            <tr>
                                <td colspan='2' style='text-align: right'><b>Munka befejezés: </b></td>
                                <td style='color: green' colspan='2'>$workEnd</td>
                            </tr>
                            <tr>
                                <td colspan='2' style='text-align: right'><b>Munkaórák száma: </b></td>
                                <td style='color: green' colspan='2'>$workHours</td>
                            </tr>
                            <tr>
                                <td colspan='2' style='text-align: right'><b>Fizetendő: </b></td>
                                <td style='color: green' colspan='2'><b><u>$workCost.- Ft.</u></b></td>
                            </tr>
                            <tr>
                                <td colspan='2' style='text-align: right'><b>Hibajelenségek, felvett problémák: </b></td>
                                <td style='color: green' colspan='2'><textarea class='txtarea' cols='40' rows='3' readonly>$checkList</textarea></td>
                            </tr>
                            <tr>
                                <td colspan='2' style='text-align: right'><b>Ellenőrizve, kijavítva: </b></td>
                                <td style='color: green' colspan='2'><textarea class='txtarea' cols='40' rows='3' readonly>$checkMList</textarea></td>
                            </tr>
                            <tr>
                                <td colspan='2' style='text-align: right'><b>Megjegyzés: </b></td>
                                <td style='color: green' colspan='2'><textarea class='txtarea' cols='40' rows='2' readonly>$note</textarea></td>
                            </tr>
                       </tfoot>";
                       
            }

        }

    }

    mysqli_close($connect);
    echo $ret;
?>
