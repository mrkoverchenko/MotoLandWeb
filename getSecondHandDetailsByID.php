<?php
    session_start(); 

    include "connect.php";
    $ret = array();
    if (isset($_POST["id"]) && !empty($_POST["id"])) {

        $id = $_POST["id"];

        $sql = "SELECT 
                    SecondHandID_MSTR,
                    SecondHandManufacturerID_MSTR,
                    SecondHandType_MSTR,
                    SecondHandYear_MSTR,
                    SecondHandStateID_MSTR,
                    SecondHandPrice_MSTR,
                    SecondHandImages_MSTR,
                    SecondHandRegDateTime_MSTR,
                    SecondHandLastRegDateTime_MSTR,
                    SecondHandImageFileNames_MSTR 
                FROM 
                    secondhand_mstr
                WHERE 
                    SecondHandID_MSTR = '$id'";
        $result = mysqli_query($connect, $sql);
        while ($row = mysqli_fetch_object($result)) {
            array_push($ret, $row);
        }
    
    }
    mysqli_close($connect);
    echo json_encode($ret);

?>



