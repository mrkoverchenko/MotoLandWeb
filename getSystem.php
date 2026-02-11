<?php
    session_start(); 

    include "connect.php";
    $ret = "";
    if (isset($_POST["field"])) {

        $fieldNumber = $_POST["field"];
        
        $sql = "SELECT * FROM motosystem_mstr WHERE MotoSystemID_MSTR = '1'";

        $result = mysqli_query($connect, $sql);
        while ($row = mysqli_fetch_row($result)) {                                    
            $ret = $row[$fieldNumber];
        }
    }
    mysqli_close($connect);
    echo $ret;
?>
