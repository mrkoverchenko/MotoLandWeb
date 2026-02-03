<?php
    session_start(); 
    include "connect.php";
    $ret = array();
    if (isset($_POST["typeID"]) && isset($_POST["manID"]) && isset($_POST["catID"]) && isset($_POST["partID"])) {

        $manID = $_POST["manID"];
        $typeID = $_POST["typeID"];
        $catID = $_POST["catID"];
        $partID = $_POST["partID"];
        
        $sql = "SELECT 
                    *
                FROM 
                    motoparts_mstr, quantityunit_mstr
                WHERE 
                    MotoPartsManID_MSTR = '$manID' AND
                    MotoPartsTypeID_MSTR  = '$typeID' AND 
                    MotoPartsCategoryID_MSTR  = '$catID' AND
                    MotoPartsID_MSTR = '$partID' AND 
                    QuantityUnitID_MSTR = MotoPartsQuantityUnitID_MSTR";

        $result = mysqli_query($connect, $sql);
        while ($row = mysqli_fetch_object($result)) {
            array_push($ret, $row);
        }
    
    }

    mysqli_close($connect);
    echo json_encode($ret);
    
?>
    