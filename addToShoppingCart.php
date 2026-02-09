<?php 

    if (session_status() != PHP_SESSION_ACTIVE) {
        session_start();
    }

    include "connect.php";

    /************************************************
     * CHECK SESSION DEADLINE
     */   
    if (isset($_SESSION['cartdeadline']) && $_SESSION['cartdeadline'] < time() - 1200) {
        session_unset();
        session_destroy();
        session_start();

        header('Location: index.php?session=out');
    }
    
    if (!isset($_SESSION["cartid"]) && !isset($_SESSION["cartdeadline"])) {
        $date = new DateTime();
        $milliseconds = (int) $date->format('Uv');
        $_SESSION["cartid"] = $milliseconds; 
        $_SESSION['cartdeadline'] = time();
    }



	if (isset($_POST["motoparts"]) && 
            isset($_POST["quantity"]) && 
                $_POST["formName"] == "orderingForm") {


	    $partId = $_POST["motoparts"];          // SELECTED PART ID
        $partQuantity = $_POST["quantity"];     // SELECTED QUANTITY
        $dateNow = date("Y-m-d H:i:s");
        $cartID = $_SESSION["cartid"];


        $lastID = 0;
        $sql = "SELECT ShoppingCartID_MSTR FROM shoppingcart_mstr WHERE ShoppingCartSessionID_MSTR = '$cartID'";
        $result = mysqli_query($connect, $sql);
        while ($row = mysqli_fetch_assoc($result)) {                                    
            $lastID = $row["ShoppingCartID_MSTR"];
        }



        /************************************************
         * INSERT TO SHOPPINGCART_MSTR IF NOT EXIST
         * ID(AUTOINC), STATUS, DATETIME
        */
        if ($lastID == 0) {
            $sql = "INSERT INTO 
                        shoppingcart_mstr (
                            ShoppingCartStatusID_MSTR, 
                            ShoppingCartDateTime_MSTR,
                            ShoppingCartSessionID_MSTR
                    ) VALUES (
                          '0',
                          '$dateNow',
                          '$cartID'
                    )";
            mysqli_query($connect, $sql);
            $lastID = mysqli_insert_id($connect);
        }

         /*****************************************************
          * FUNCTIONAL BUT NOT USED
          */
         /*$sql = "INSERT INTO 
                    shoppingcart_mstr (
                        ShoppingCartStatusID_MSTR, 
                        ShoppingCartDateTime_MSTR,
                        ShoppingCartSessionID_MSTR
                    )
                    SELECT '0', '$dateNow', '$cartID' 
                    WHERE NOT EXISTS (
                        SELECT 
                            '$cartID' 
                        FROM 
                            shoppingcart_mstr 
                        WHERE ShoppingCartSessionID_MSTR = '$cartID')";*/






        /************************************************
         * INSERT TO SHOPPINGCART_DET
         * ID(AUTOINC), MSTR ID, PART ID, QUANTITY
        */
        $sql = "INSERT INTO 
                    shoppingcart_det (
                        ShoppingCartMSTRID_DET, 
                        ShoppingCartMotoPartsID_DET, 
                        ShoppingCartQuantity_DET
                    ) VALUES (
                        '$lastID',
                        '$partId',
                        '$partQuantity'
                    )";
        mysqli_query($connect, $sql);


        /************************************************
         * INSERT QUANTITY TO LOCKEDQUANTITY_MSTR 
         * ID(AUTOINC), LockedQuantitySessionID_MSTR,
         * LockedQuantityQuantity_MSTR,
         * LockedQuantityDateTime_MSTR,
        */
        $sql = "INSERT INTO 
                    lockedquantity_mstr (
                        LockedQuantitySessionID_MSTR, 
                        LockedQuantityQuantity_MSTR, 
                        LockedQuantityDateTime_MSTR,
                        LockedQuantityPartsID_MSTR 
                    ) VALUES (
                        '$cartID',
                        '$partQuantity',
                        '$dateNow',
                        '$partId'
                    )";
        mysqli_query($connect, $sql);


        /************************************************************
         * PARTS MSTR TABLE - QUANTITY VALUE DECREMENT IF NOT ZERO
        */
        $sql = "UPDATE 
                    motoparts_mstr
                SET 
                    MotoPartsQuantity_MSTR = (MotoPartsQuantity_MSTR - $partQuantity) 
                WHERE 
                    MotoPartsID_MSTR = '$partId' AND
                    MotoPartsQuantity_MSTR > (MotoPartsQuantity_MSTR - $partQuantity)";
        mysqli_query($connect, $sql);

        mysqli_close($connect);


        header("Location: index.php?shoppingcart=added&page=ordering");
    }

?>





