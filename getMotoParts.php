<?php
    session_start(); 

    include "connect.php";
    $ret = "";
    if (isset($_POST["typeID"]) && isset($_POST["manID"]) && isset($_POST["catID"])) {

        $manID = $_POST["manID"];
        $typeID = $_POST["typeID"];
        $catID = $_POST["catID"];
        
        $sql = "SELECT 
                    *
                FROM 
                    motoparts_mstr
                WHERE 
                    MotoPartsManID_MSTR = '$manID' AND
                    MotoPartsTypeID_MSTR  = '$typeID' AND 
                    MotoPartsCategoryID_MSTR  = '$catID'";

        $result = mysqli_query($connect, $sql);
        $ret = "<option></option>";
        while ($row = mysqli_fetch_assoc($result)) {                                    
            $id = $row["MotoPartsID_MSTR"];
            $num = $row["MotoPartsNumber_MSTR"];
            $ret .= "<option value='$id'>$num</option>"; 
        }
    
    }
    mysqli_close($connect);
    echo $ret;
?>
