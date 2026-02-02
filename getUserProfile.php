<?php
    session_start(); 
    header("Content-Type: application/json; charset=UTF-8");

    include "connect.php";

    $ret = array();
    if (isset($_POST["userID"])) { 

        $userID = $_POST["userID"];

        $sql = "SELECT * FROM user_mstr, user_det WHERE UserID_MSTR = '$userID' AND UserMSTRID_DET = '$userID'";

        $result = mysqli_query($connect, $sql);
        while ($row = mysqli_fetch_object($result)) {
            array_push($ret, $row);
        }
    }
    mysqli_close($connect);
    echo json_encode($ret);
?>
