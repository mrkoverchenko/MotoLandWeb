<?php
    session_start(); 

    include "connect.php";

    $ret = ""; 

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (isset($_POST["bookingID"])) {

            $bookingID = $_POST["bookingID"];

            $sql = "SELECT 
                        * 
                    FROM 
                        booking_mstr, 
                        motomanufacturer_mstr,
                        mototype_mstr
                    WHERE 
                        BookingID_MSTR = '$bookingID' AND 
                        MotoManufacturerID_MSTR = BookingMotoManID_MSTR AND 
                        MotoTypeID_MSTR = BookingMotoTypeID_MSTR";

            $result = mysqli_query($connect, $sql);
            $ic = 0;

            while ($row = mysqli_fetch_assoc($result)) {
                $motoRegNumber = $row["BookingMotoRegNumber_MSTR"];
                $motoMan = $row["MotoManufacturerManufacturer_MSTR"];
                $motoType = $row["MotoTypeType_MSTR"];
                $motoYear = $row["BookingMotoYear_MSTR"];
                $ic++;
                $ret .= "<tr>
                            <td>$ic</td>
                            <td>$motoRegNumber</td>
                            <td>$motoMan</td>
                            <td>$motoType</td>
                            <td>$motoYear</td>
                       </tr>";
                       
            }

        }

    }

    mysqli_close($connect);
    echo $ret;
?>
