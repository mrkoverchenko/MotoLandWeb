<?php
    session_start(); 

    include "connect.php";

    $ret = "";
    if (isset($_POST["id"]) && !empty($_POST["id"])) {
        $id = $_POST["id"];
        $sql = "DELETE FROM secondhand_mstr WHERE SecondHandID_MSTR = '$id'";
        mysqli_query($connect, $sql);
        $ret = "OK";
    }
    mysqli_close($connect);
    echo $ret;
?>
