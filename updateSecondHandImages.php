<?php
    //session_start(); 

    include "connect.php";

    $ret = "";

    if (isset($_POST["id"]) && isset($_POST["filenames"]) && isset($_POST["rem"]) 
            && !empty($_POST["id"]) && !empty($_POST["filenames"]) && !empty($_POST["rem"])) { 

        $id = $_POST["id"];
        $filenames = $_POST["filenames"];
        $rdate = Date("Y-m-d H:i:s");
        $rem = $_POST["rem"];

        $sqlString = "UPDATE 
                            secondhand_mstr
                        SET 
                            SecondHandLastRegDateTime_MSTR = '$rdate',
                            SecondHandImageFileNames_MSTR = '$filenames'  
                        WHERE 
                            SecondHandID_MSTR = $id";
        $ret = mysqli_query($connect, $sqlString);

            $f = $_SERVER['DOCUMENT_ROOT']."/MotoLandWeb/".$rem;
            if (file_exists($f)) {
                if (unlink($f) ) {
                    $ret = "OK";
                } else {
                    $ret = "Hiba a file törlésnél!";
                }
            } else {
                $ret = "Nincs ilyen file: $f";
            }
    }
    mysqli_close($connect);
    echo $ret;
?>
