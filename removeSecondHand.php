<?php
    session_start(); 

    include "connect.php";

    $ret = "";
    $fls = "";
    if (isset($_POST["id"]) && !empty($_POST["id"])) {

        $id = $_POST["id"];

        $sql = "SELECT 
                    SecondHandImageFileNames_MSTR 
                FROM 
                    secondhand_mstr 
                WHERE 
                    SecondHandID_MSTR = '$id'";

        $result = mysqli_query($connect, $sql);
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            $fls = explode(',', $row["SecondHandImageFileNames_MSTR"]);

            for ($ic = 0; $ic < count($fls); $ic++) {

                $f = $_SERVER['DOCUMENT_ROOT']."/MotoLandWeb/".$fls[$ic];

                //if (file_exists($f])) {

                    if (unlink($f) ) {
                        $ret = "OK";

                        $sqlRemove = "DELETE FROM secondhand_mstr WHERE SecondHandID_MSTR = '$id'";
                        mysqli_query($connect, $sqlRemove);

                    } else {
                        $ret = "Hiba a file törlésnél!";
                    }

                /*} else {
                    $ret = "Nincs ilyen file: $f";
                }*/
            }
        }
    }
    mysqli_close($connect);
    echo $ret;
?>
