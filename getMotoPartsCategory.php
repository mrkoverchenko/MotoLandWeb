<?php
    session_start(); 

    include "connect.php";
    $ret = "";
    if (isset($_POST["typeID"]) && isset($_POST["manID"])) {

        $manID = $_POST["manID"];
        $typeID = $_POST["typeID"];
        
        $sql = "SELECT 
                    PartsCategoryID_MSTR,
                    PartsCategoryCategory_MSTR, 
                    PartsCategoryCategoryImageFileName_MSTR
                FROM 
                    motopartscategory_mstr
                WHERE 
                    PartsCategoryManID_MSTR = '$manID' AND
                    PartsCategoryTypeID_MSTR  = '$typeID' ";

        $result = mysqli_query($connect, $sql);
        $ret = "<option></option>";
        while ($row = mysqli_fetch_assoc($result)) {                                    
            $id = $row["PartsCategoryID_MSTR"];
            $cat = $row["PartsCategoryCategory_MSTR"];
            $image = $row["PartsCategoryCategoryImageFileName_MSTR"];
            
            $ret .= "<option value='$id'>$cat</option>"; 
        }
    
    }
    mysqli_close($connect);
    echo $ret;
?>
