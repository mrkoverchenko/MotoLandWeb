<?php
    session_start(); 

    include "connect.php";

    $ret = "";
    if (isset($_POST["id"]) && !empty($_POST["id"])) {

        $id = $_SESSION["userid"];

        $sql = "SELECT * FROM secondhand_mstr, motomanufacturer_mstr
                WHERE 
                    SecondHandUserID_MSTR = '$id' AND
                    MotoManufacturerID_MSTR = SecondHandManufacturerID_MSTR 
                ORDER BY 
                    SecondHandRegDateTime_MSTR ASC";

        $ret = "<option value='-1'>Új hirdetés feladása</option>";
        $result = mysqli_query($connect, $sql);
        while ($row = mysqli_fetch_assoc($result)) {                                    
            $cntr = $row['SecondHandID_MSTR'];
            $type = $row['MotoManufacturerManufacturer_MSTR']." ".
                    $row['SecondHandType_MSTR']." [".$row['SecondHandYear_MSTR']."]";
            $ret .= "<option value='$cntr'>$cntr. $type</option>"; 
        }
    }
    mysqli_close($connect);
    echo $ret;
?>
