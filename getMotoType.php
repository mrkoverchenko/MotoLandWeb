<?php
    session_start(); 

    include "connect.php";
    $ret = "";
    if (isset($_POST["manufacturerID"])) {

        $manID = $_POST["manufacturerID"];
        
        $sql = "SELECT 
                    MotoTypeID_MSTR,
                    MotoTypeType_MSTR, 
                    MotoTypeCode_MSTR
                FROM 
                    mototype_mstr
                WHERE 
                    MotoTypeManID_MSTR = '$manID' 
                ORDER BY
                    MotoTypeType_MSTR ASC";

        $result = mysqli_query($connect, $sql);
        $ret = "<option></option>";
        while ($row = mysqli_fetch_assoc($result)) {                                    
            $id = $row["MotoTypeID_MSTR"];
            $type = $row["MotoTypeType_MSTR"]. " ". $row["MotoTypeCode_MSTR"];
            
            $ret .= "<option value = '$id'>$type</option>"; 
        }
    
    }
    mysqli_close($connect);
    echo $ret;
?>
