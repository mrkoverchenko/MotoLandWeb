<?php
    session_start(); 

    if (!empty($_SESSION['cartdeadline']) && $_SESSION['cartdeadline'] < time() - $_SESSION["sessionDeadline"]) {
        unset($_SESSION['cartdeadline']);
        unset($_SESSION['cartid']);
        session_unset();
        session_destroy();
        session_start();
        header("Location: index.php?self=ggg");  
        exit();
    }

    include "connect.php";

    $ret = false;
    if (isset($_POST["idDET"], $_POST["idMSTR"], $_POST["lockedID"], $_POST["sessionID"], $_POST["qua"], $_POST["partID"])) {


 //   if (isset($_POST["idDET"]) && isset($_POST["idMSTR"]) && isset($_POST["lockedID"]) && isset($_POST["sessionID"]) && isset($_POST["qua"]) && isset($_POST["partID"]) ) {

        $idDET = $_POST["idDET"];
        $idMSTR = $_POST["idMSTR"];
        $lockedID = $_POST["lockedID"];
        $sessionID = $_POST["sessionID"];
        $qua = $_POST["qua"];
        $partID = $_POST["partID"];


        // ROLLING BACK QUANTITY ROWS...
        $sql = "UPDATE motoparts_mstr 
                SET MotoPartsQuantity_MSTR = MotoPartsQuantity_MSTR + $qua 
                WHERE MotoPartsID_MSTR = '$partID'";
        mysqli_query($connect, $sql);

        // ...THEN REMOVE ROWS FROM TEMPORARY TABLE...BY SESSION ID AND...
        $sql = "DELETE FROM lockedquantity_mstr WHERE LockedQuantityShoppingCartDETID_MSTR = '$idDET' AND LockedQuantitySessionID_MSTR = '$sessionID'";
        mysqli_query($connect, $sql);

        // ... DELETE FROM SHOPPINGCART_DET TABLE
        $sql = "DELETE FROM shoppingcart_det WHERE ShoppingCartID_DET = '$idDET' AND ShoppingCartSessionID_DET = '$sessionID'";
        mysqli_query($connect, $sql);
        
        // CHECK SHOPPINGCART_MSTR' CHILDREN BY SESSION 
        // IF NO CHILD RECORDS THEN I REMOVE MSTR RECORD TOO
        $sql = "SELECT ShoppingCartMSTRID_DET 
                FROM shoppingcart_mstr, shoppingcart_det  
                WHERE ShoppingCartSessionID_MSTR = '$sessionID' AND ShoppingCartID_MSTR = ShoppingCartMSTRID_DET";
        $result = mysqli_query($connect, $sql);
        $c = 0;
        $c = mysqli_num_rows($result);
        if ($c === 0) {
            $sql = "DELETE FROM shoppingcart_mstr WHERE ShoppingCartID_MSTR = '$idMSTR' AND ShoppingCartSessionID_MSTR = '$sessionID'";
            mysqli_query($connect, $sql);
        }
        
        $ret = true;
    }
    mysqli_close($connect);
    echo $ret;
?>
