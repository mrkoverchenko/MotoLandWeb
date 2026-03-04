<?php 
    require_once('PHPMailer/src/PHPMailer.php');
    require_once('PHPMailer/src/Exception.php');
    require_once('PHPMailer/src/SMTP.php');

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    if (session_status() != PHP_SESSION_ACTIVE) {
        session_start();
        $_SESSION["systemPath"] = "http://localhost/mrkoverchenko/MotoLandWeb/";
    }

    include "connect.php";






    /***************************************************************
     * SESSION DEADLINE SETTING
     */
    //if (empty($_SESSION["sessionDeadline"])) {    
        $sql = "SELECT MotoSystemSessionDeadline_MSTR, MotoSystemWebPath_MSTR FROM motosystem_mstr WHERE MotoSystemID_MSTR = '1'";
        $result = mysqli_query($connect, $sql);
        $row = mysqli_fetch_assoc($result);
        $_SESSION["sessionDeadline"] = $row["MotoSystemSessionDeadline_MSTR"];
        $_SESSION["systemPath"] = $row["MotoSystemWebPath_MSTR"];
    //}

    $cartid = "";
    $activePage = "sales";
    $shoppingcartqua = 0;

    if (isset($_SESSION["cartid"])) {
        $cartid = $_SESSION["cartid"];
    }

    /*****************************************************************
     * SESSION CHECKING
    */
    if (!empty($_SESSION['cartdeadline']) && $_SESSION['cartdeadline'] < time() - $_SESSION["sessionDeadline"]) {
        unset($_SESSION['cartdeadline']);
        unset($_SESSION['cartid']);
        session_unset();
        session_destroy();
        session_start();
    }


    $_SESSION['cartdeadline'] = time();

    $systemIsMessage = false;
    $systemMessage = "";
    $cartIs = false;
    $ok = 0;
    $isUser = false;
    
    if (isset($_SESSION['userid'])) {
        $isUser = true;
    }

    if (isset($_GET["session"]) && $_GET["session"] == "out") {
        $hideTime = 10000;
        $systemIsMessage = true;
        $cartIs = false;
        $alertType = "alert-danger";
        $systemMessage = "<b>Lejárt a munkamenet!</b";
    }
    

    /**************************************************************
     * LOGGED OUT IS SUCCESSFUL MESSAGE
     */
    if (isset($_GET["logged"]) && $_GET["logged"] == "out") {
        $_POST["userName"] = "";
        $_POST["password"] = "";
        $_POST["formName"] = "";
        $_POST = array();
        unset($_POST);
        $isUser = false;

        $sql = "SELECT MotoSystemSessionDeadline_MSTR, MotoSystemWebPath_MSTR FROM motosystem_mstr WHERE MotoSystemID_MSTR = '1'";
        $result = mysqli_query($connect, $sql);
        $row = mysqli_fetch_assoc($result);
        $_SESSION["sessionDeadline"] = $row["MotoSystemSessionDeadline_MSTR"];
        $_SESSION["systemPath"] = $row["MotoSystemWebPath_MSTR"];

       
        $systemIsMessage = true;
        $systemMessage = "";
        $hideTime = 5000;
        $alertType = "alert-dismissible";
        $systemMessage = "<b>Sikeres kijelentkezés!</b>";

    }
    


    if (isset($_SESSION["sessionDeadline"])) {
        /**********************************************************
         * SELECT EXPIRED SESSION RECORDS
         */
        //$c = 0;
        $dl = $_SESSION["sessionDeadline"];
        $sql = "SELECT * FROM lockedquantity_mstr
                WHERE LockedQuantityDateTime_MSTR < (NOW() - $dl)";
        $result = mysqli_query($connect, $sql);
        //$c = mysqli_num_rows($result);
        $dt = "";
        $sessionIDArray = array();
        while ($row = mysqli_fetch_assoc($result)) {    
            /**********************************************
             * RESERVED PARTS QUANTITY ROLLBACING
             */
            $sessionID = $row["LockedQuantitySessionID_MSTR"];
            array_push($sessionIDArray, $sessionID);
            $qua = $row["LockedQuantityQuantity_MSTR"];
            $partID = $row["LockedQuantityPartsID_MSTR"];

            // ROLLING BACK QUANTITY ROWS...
            $sql = "UPDATE motoparts_mstr 
                    SET MotoPartsQuantity_MSTR = MotoPartsQuantity_MSTR + $qua  
                    WHERE MotoPartsID_MSTR = '$partID'";
            mysqli_query($connect, $sql);

        }

        // ...THEN REMOVE ROWS FROM TEMPORARY TABLE...BY SESSION ID, ...
        $sql = "DELETE FROM lockedquantity_mstr 
                WHERE LockedQuantityDateTime_MSTR < (NOW() - $dl) ";
        mysqli_query($connect, $sql);

        for ($ic =0; $ic < count($sessionIDArray); $ic++) {

            // ... DELETE FROM SHOPPINGCART_DET TABLE BY SESSION ID AND ...
            $sql = "DELETE FROM shoppingcart_det WHERE ShoppingCartSessionID_DET = '$sessionIDArray[$ic]'";
            mysqli_query($connect, $sql);

            // ... DELETE FROM SHOPPINGCART_MSTR TABLE BY SESSION ID
            $sql = "DELETE FROM shoppingcart_mstr WHERE ShoppingCartSessionID_MSTR = '$sessionIDArray[$ic]'";
            mysqli_query($connect, $sql);
        }

    }


    /*******************************************************
     * ROLLBACK SHOPPING CART
    */
    //if (isset($_POST["formName"]) && ($_POST["formName"] == "shoppingCartRollbackForm")) {

	if (isset($_POST["idDET"], 
            $_POST["idMSTR"], 
                $_POST["lockedID"],
                    $_POST["sessionID"]) &&
                        $_POST["formName"] === "shoppingCartRollbackForm") {
            


    	$idDET = $_POST["idDET"];
        $idMSTR = $_POST["idMSTR"];
        $lockedID = $_POST["lockedID"];
        $sessionID = $_POST["sessionID"];

        /*************************************************
         * ROLLBACK TO PARTS TABLE
         * SELECT TEMPORARY TABLE, QUANTITY FIELD,
         * BY SESSION ID
         */
        $sqlstring = "SELECT
                        LockedQuantityPartsID_MSTR,
                        LockedQuantityQuantity_MSTR 
                      FROM
                        lockedquantity_mstr 
                      WHERE
                        LockedQuantitySessionID_MSTR = '$sessionID'";

        $result = mysqli_query($connect, $sqlstring);

        while ($row = mysqli_fetch_assoc($result)) {    

            $partID = $row["LockedQuantityPartsID_MSTR"];
            $partQua = $row["LockedQuantityQuantity_MSTR"];
            // ROLLING BACK QUANTITY ROWS...
            $sql = "UPDATE motoparts_mstr 
                    SET MotoPartsQuantity_MSTR = MotoPartsQuantity_MSTR + $partQua 
                    WHERE MotoPartsID_MSTR = $partID";
            mysqli_query($connect, $sql);
        }
        // ...THEN REMOVE ROWS FROM TEMPORARY TABLE...BY SESSION ID
        $sql = "DELETE FROM lockedquantity_mstr WHERE LockedQuantitySessionID_MSTR = '$sessionID'";
        mysqli_query($connect, $sql);

        // ...THEN REMOVE ROWS FROM SHOPPINGCART_DET TABLE...BY SESSION ID
        $sql = "DELETE FROM shoppingcart_det WHERE ShoppingCartSessionID_DET = '$sessionID'";
        mysqli_query($connect, $sql);

        // ...THEN REMOVE ROWS FROM SHOPPINGCART_MSTR TABLE...BY SESSION ID
        $sql = "DELETE FROM shoppingcart_mstr WHERE ShoppingCartSessionID_MSTR = '$sessionID'";
        mysqli_query($connect, $sql);

        //mysqli_close($connect);
        //unset($_POST);
        header("Location: index.php?shoppingcart=cleared&page=sales");  
        
    }


    /**************************************************************
     * SHOPPINGCART PARTS QUANTITY
     */
    if (isset($_SESSION["cartid"])) {

        $cartid = $_SESSION["cartid"];
        $sql = "SELECT ShoppingCartID_DET
                FROM shoppingcart_mstr, shoppingcart_det
                WHERE 
                    ShoppingCartID_MSTR = ShoppingCartMSTRID_DET AND 
                    ShoppingCartSessionID_MSTR = '$cartid'";

        $result = mysqli_query($connect, $sql);
        $shoppingcartqua = mysqli_num_rows($result);
        //mysqli_close($connect);
    }





    /**************************************************************
     * ADDED TO SHOPPING CART IS SUCCESSFUL MESSAGE OR NOT
     */
    if (isset($_GET["shoppingcart"])) { 
        $hideTime = 5000;
        $systemIsMessage = true;

        if ($_GET["shoppingcart"] === "added") {

            $cartIs = true;
            $alertType = "alert-dismissible";
            $systemMessage = "<b>Sikeresen a kosárba raktad!</b></br>Folytathatod a vásárlást vagy megtekintheted <a href='#shoppingCart' data-toggle='modal'>kosarad</a> tartalmát.";
        } else {
            $cartIs = false;
            $alertType = "alert-danger";
            $systemMessage = "<b>Probléma a kosárbahelyezésnél!</b";
        }

        if ($_GET["shoppingcart"] === "cleared") {
            unset($_SESSION['cartdeadline']);
            unset($_SESSION["cartid"]);
            //unset($_SESSION['cartid']);
            $shoppingcartqua = 0;

            $alertType = "alert-danger";
            $systemMessage = "<b>Kosár kiürítve!</b";
        }
    }

    if (isset($_GET["page"])) {
        $activePage = $_GET["page"];
    }

    

    function validate($data){
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data); 
        return $data;
    }

        function generateSalt($length = 32) {
            return bin2hex(random_bytes($length));
        }

      
        function createPasswordHashReg($password, $salt) {
            return hash('sha256', $salt . $password);
        }


    /*******************************************************
     * LOGIN
     */
	if (isset($_POST["loginUserName"]) && 
            isset($_POST["loginPassword"]) && 
                ($_POST["formName"] == "logForm" || $_POST["formName"] == "shoppingCartLoginForm" || $_POST["formName"] == "secondhandLoginForm" )) {

        

		$activePage = ($_POST["formName"] == "shoppingCartLoginForm") 
                        ? "payments" 
                        : (($_POST["formName"] == "secondhandLoginForm") 
                            ? "secondhand" 
                            : "sales");



		$uname = strtolower(validate($_POST['loginUserName']));          
		$passwordFromInput = validate($_POST['loginPassword']);
			 
        $sql = "SELECT 
                    UserID_MSTR,
                    UserMail_MSTR,
                    UserNickName_MSTR,
                    UserTypeType_MSTR,
                    UserFlagID_MSTR,
                    PasswordSalt_MSTR,
                    PasswordPassword_MSTR,
                    CONCAT(UserFirstName_DET,' ',UserMiddleName_DET,' ',UserLastName_DET) AS UserFullName,
                    UserPhone_DET

                FROM 
                    user_mstr, user_det, password_mstr, usertype_mstr
                WHERE 
                    LOWER(UserNickName_MSTR) = '$uname' AND 
                    user_mstr.UserTypeID_MSTR = usertype_mstr.UserTypeID_MSTR AND 
                    UserMSTRID_DET = UserID_MSTR AND 
                    UserID_MSTR = PasswordUserID_MSTR AND 
                    usertype_mstr.UserTypeType_MSTR <> 'törölve'";

        $isUser = false;
        $result = mysqli_query($connect, $sql);
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            
            $saltFromDb = $row["PasswordSalt_MSTR"];
            $passwordFromDb = $row["PasswordPassword_MSTR"];

            if (hash('sha256', $saltFromDb.$passwordFromInput) === $passwordFromDb) {
                $isUser = true;

                $_SESSION['usernickname'] = $row['UserNickName_MSTR'];
                $_SESSION['usertype'] = strtolower($row['UserTypeType_MSTR']);
                $_SESSION['userid'] = $row['UserID_MSTR'];
                $_SESSION['userfullname'] = $row['UserFullName'];
                $_SESSION['usermail'] = $row['UserMail_MSTR'];
                $_SESSION['userphone'] = $row['UserPhone_DET'];

            } else {
                $isUser = false;
            }
            $_POST = array();
            unset($_POST);
		}

        $hideTime = 5000;
        $systemIsMessage = true;
        if ($isUser) {
            $alertType = "alert-dismissible";
            $systemMessage = "<b>Sikeres bejelentkezés!</b>";
        } else {
            $alertType = "alert-danger";
            $systemMessage = "<b>Sikertelen bejelentkezés!</b";
        }

    }




    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        if (!empty($_POST['formName']) && $_POST['formName'] === 'shoppingCartLoginForm') {




        } else if (!empty($_POST["formName"]) && $_POST["formName"] === "securityForm") {

            /************************************************
             * CHANGE PASSWORD
             */

            $oldPasswordFromInput = validate($_POST['oldPassword']);
            $newPasswordFromInput = validate($_POST['newPassword']);
            $userID = $_SESSION['userid'];
                
            $sql = "SELECT PasswordSalt_MSTR, PasswordPassword_MSTR
                    FROM password_mstr
                    WHERE PasswordUserID_MSTR = '$userID'";
            $passwordIsOK = false;        
            $result = mysqli_query($connect, $sql);
            if (mysqli_num_rows($result) === 1) {
                $row = mysqli_fetch_assoc($result);
                
                $saltFromDb = $row["PasswordSalt_MSTR"];
                $passwordFromDb = $row["PasswordPassword_MSTR"];
                // OLDA PASSWORD CHECKING
                if (hash('sha256', $saltFromDb.$oldPasswordFromInput) === $passwordFromDb) {
                    $passwordIsOK = true;        
                }
                $_POST = array();
                unset($_POST);
            }


            // CHANGE PASSWORD
            if ($passwordIsOK) {
                $salt = generateSalt();
                $passHASH = createPasswordHashReg($newPasswordFromInput, $salt);
                $sqlString = "UPDATE password_mstr
                              SET PasswordPassword_MSTR = '$passHASH', 
                                  PasswordSalt_MSTR ='$salt'
                              WHERE PasswordUserID_MSTR = '$userID'";
                mysqli_query($connect, $sqlString);
            }








            $hideTime = 5000;
            $systemIsMessage = true;
            if ($passwordIsOK) {
                $alertType = "alert-dismissible";
                $systemMessage = "<b>Sikeres jelszócsere!</b>";
            } else {
                $alertType = "alert-danger";
                $systemMessage = "<b>Hibás jelszó!</b";
            }








        } else if (!empty($_POST["formName"]) && $_POST["formName"] === "bookingForm") {


            /************************************************
             * SENDING BOOKING DATA
             */
            $bookingDate = $_POST["bookingDate"] ?? "";
            $bookingDay = $_POST["bookingDay"] ?? "";
	        $bookingFullName = $_POST["bookingFullName"] ?? "";
            $bookingPhone = $_POST["bookingPhone"] ?? "";
            $bookingMail = $_POST["bookingMail"] ?? "";
            $bookingRegDateTime = Date("Y-m-d H:i:s");

            $sql = "INSERT INTO 
                        prebooking_mstr (
                            PreBookingID_MSTR, 
                            PreBookingDate_MSTR, 
                            PreBookingFullName_MSTR, 
                            PreBookingPhone_MSTR, 
                            PreBookingMail_MSTR,
                            PreBookingRegDateTime_MSTR                            
                    ) VALUES (
                        NULL,
                        '$bookingDate',
                        '$bookingFullName',
                        '$bookingPhone',
                        '$bookingMail',
                        '$bookingRegDateTime'
                    )";
            mysqli_query($connect, $sql);
            //$lastid = mysqli_insert_id($connect);

            $mailbody = "<pre style='color:gray; font-family: Helvetica Neue,Helvetica,Arial,sans-serif; font-size: 14px; line-height: 1.42857143;'>
    Üdv!
    Ezt az üzenetet azért kaptad, mert időpontot foglaltál a MotoLand szervíz és webshop rendszerében.

    Lefoglalt időpontod: <b><u>$bookingDate ($bookingDay)</u></b>
    Név : $bookingFullName
    E-mail cím: $bookingMail
    Telefonszám : $bookingPhone
    Rögzítés időpontja: $bookingRegDateTime

    A végleges egyeztetés miatt kollégánk a legrövidebb időn belül felkeres az általad megadott telefonszámon.

    Ez egy automatikus üzenet, kérlek ne válaszolj rá!
    Kérdés esetén keress bátran a weboldalunkon megtalálható információs telefonszámon, vagy e-mail címünkön.

                        </pre>";


            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->CharSet = "UTF-8";
                //$mail->SMTPDebug = 1;
                $mail->Host = "smtp.mail.yahoo.com";
                $mail->SMTPAuth = true;
                $mail->Username = "istvan.lovei@yahoo.com";
                $mail->Password = "zopxdyyvskobaqjz";
                $mail->SMTPSecure = "ssl"; 
                $mail->Port = 465; 
                $mail->setFrom("istvan.lovei@yahoo.com", "MotoLand");
                $mail->addAddress($bookingMail, $bookingFullName);

                $mail->isHTML(true);
                $mail->Subject = "MotoLand időpontfoglalás: $bookingDate - no reply";
                $mail->Body    = $mailbody;
                if(!$mail->send()){
                    echo 'Nincs küldés, szarakodás van: ' . $mail->ErrorInfo;
                }
            } catch (Exception $ex) {
                echo "'Ha nincs net, nincs probléma': ".$ex;
            }




            $hideTime = 20000;
            $alertType = "alert-dismissible";
            $systemIsMessage = true;
            $systemMessage = "<b>Az időpontfoglalás sikeresen rögzítve!</br>".
                             "Kollégánk a megadott számon keresni fogja Önt ".
                             "további egyeztetés céljából, a foglalás ".
                             "részleteit pedig e-mail címére küldtük!";



        } else if (!empty($_POST["formName"]) && $_POST["formName"] === "shoppingCartSendOrderForm") {





            /************************************************
             * SENDING ORDERED ITEMS
             */

            $sessionID = $_POST["shoppingCartSessionID"] ?? "";
            $userID = $_POST["shoppingCartUserID"] ?? "";
	        $email = $_POST["shoppingCartEMail"] ?? "";
            $firstName = $_POST["shoppingCartFirstName"] ?? "";
            $middleName = $_POST["shoppingCartMiddleName"] ?? "";
            $lastName = $_POST["shoppingCartLastName"] ?? ""; 
            $countryID = $_POST["shoppingCartCountryID"] ?? "";
            $country = $_POST["shoppingCartUserCountry"] ?? "";
            $postCode = $_POST["shoppingCartPostCode"] ?? "";
            $city = $_POST["shoppingCartCity"] ?? "";
            $street = $_POST["shoppingCartStreet"] ?? "";
            $address = $_POST["shoppingCartAddress"] ?? "";
            $phone = $_POST["shoppingCartPhone"] ?? "";
            $total = $_POST["shoppingCartPartTotal"] ?? "0"; 
            $supplierID = $_POST["shoppingCartSupplier"] ?? "0"; 
            $paymentTypeID = $_POST["shoppingCartPaymentType"] ?? "0"; 

            $shoppingCartNote = $_POST["shoppingCartNote"] ?? ""; 
            
            $usertype = ($userID === "") ? 1 : 2;
               
            $orderDateTime = date("Y-m-d H:i:s");
            //ORDERS_MSTR TABLE HANDLING
            $orderSQL = "INSERT INTO 
                            orders_mstr (
                                OrdersID_MSTR,
                                OrdersUserID_MSTR, 
                                OrdersDateTime_MSTR, 
                                OrdersFullCost_MSTR, 
                                OrdersStatusStatusID_MSTR, 
                                OrdersUserTypeID_MSTR, 
                                OrdersNote_MSTR,
                                OrdersASZFIsOK_MSTR 
                            ) VALUES (NULL, '$userID', '$orderDateTime', '$total', 1, $usertype, '$shoppingCartNote', 1)";

            mysqli_query($connect, $orderSQL);
            $lastid = mysqli_insert_id($connect);

            //GET PARTS DATAS
            $ordersql = "SELECT 
                            MotoPartsNumber_MSTR,
                            MotoPartsName_MSTR,
                            ROUND(MotoPartsBruttoEURPrice_MSTR,2) * ShoppingCartQuantity_DET AS bruttoEUR,
                            ROUND(MotoPartsNettoPrice_MSTR, 0) AS netto,
                            ROUND(MotoPartsVAT_MSTR * 100, 0) AS vat,
                            ROUND(MotoPartsDiscount_MSTR * 100, 0) AS disc,
                            ROUND(((MotoPartsNettoPrice_MSTR * MotoPartsVAT_MSTR) + MotoPartsNettoPrice_MSTR) - (((MotoPartsNettoPrice_MSTR * MotoPartsVAT_MSTR) + MotoPartsNettoPrice_MSTR) * MotoPartsDiscount_MSTR)) AS brutto,
                            QuantityUnitUnit_MSTR AS mee,
                            ShoppingCartQuantity_DET AS qua,
                            ROUND((ROUND(MotoPartsNettoPrice_MSTR, 0) * MotoPartsVAT_MSTR) + ROUND(MotoPartsNettoPrice_MSTR, 0)) * ShoppingCartQuantity_DET AS subtotal
                        FROM 
                            shoppingcart_mstr, shoppingcart_det, motoparts_mstr, quantityunit_mstr
                        WHERE 
                            ShoppingCartSessionID_MSTR = '$sessionID' AND 
                            ShoppingCartID_MSTR = ShoppingCartMSTRID_DET AND 
                            ShoppingCartMotoPartsID_DET = MotoPartsID_MSTR AND
                            MotoPartsQuantityUnitID_MSTR = QuantityUnitID_MSTR";

            $res = mysqli_query($connect, $ordersql);
            $c = mysqli_num_rows($res);
            while ($row = mysqli_fetch_assoc($res)) {
                $partNumber = $row["MotoPartsNumber_MSTR"];
                $partName = $row["MotoPartsName_MSTR"];
                $partNetto = $row["netto"];
                $partVAT =  $row["vat"];
                $partDISC =  $row["disc"];
                $partEUR = $row["bruttoEUR"];
                $partBrutto = $row["brutto"];
                $partMee = $row["mee"];
                $partQua = $row["qua"];
                $partSubtotal = $row["subtotal"];
                $partTotal = $partTotal + $partSubtotal;

                $orderSQL_B = "INSERT INTO 
                                    orders_det (
                                        OrdersID_DET, 
                                        OrdersMSTRID_DET, 
                                        OrdersPartsNumber_DET, 
                                        OrdersPartsName_DET, 
                                        OrdersNettoPrice_DET, 
                                        OrdersVAT_DET, 
                                        OrdersDiscount_DET,
                                        OrdersBruttoPrice_DET, 
                                        OrdersBruttoEURPrice_DET, 
                                        OrdersQuantity_DET, 
                                        OrdersQuantityUnit_DET,
                                        OrdersSupplierID_MSTR,
                                        OrdersPaymentTypeID_MSTR
                                    ) VALUES (
                                        NULL, 
                                        '$lastid', 
                                        '$partNumber', 
                                        '$partName', 
                                        '$partNetto', 
                                        '$partVAT', 
                                        '$partDISC',
                                        '$partBrutto', 
                                        '$partEUR', 
                                        '$partQua', 
                                        '$partMee',
                                        '$supplierID',
                                        '$paymentTypeID')";
                mysqli_query($connect, $orderSQL_B);
            }

            $reg = true;
            // NO REGISTRATED USER, USER DATA SAVING
            if ($userID === "") {
                $reg = false;
                $orderSQL = "INSERT INTO 
                                ordersuser_mstr ( 
                                    OrdersUserID_MSTR, 
                                    OrdersUserOrdersMSTRID_MSTR,	
                                    OrdersUserUserName_MSTR, 
                                    OrdersUserCountry_MSTR,     
                                    OrdersUserPostCode_MSTR,
                                    OrdersUserCity_MSTR, 
                                    OrdersUserStreet_MSTR,
                                    OrdersUserAddress_MSTR,
                                    OrdersUserPhone_MSTR, 
                                    OrdersUserEmail_MSTR,
                                    OrdersUserRegDateTime_MSTR
                                ) VALUES (
                                    NULL,
                                    '$lastid',
                                    '$firstName $middleName $lastName',
                                    '$country',
                                    '$postCode',
                                    '$city',
                                    '$street',
                                    '$address',
                                    '$phone',
	                                '$email',
                                    '$orderDateTime')";
                mysqli_query($connect, $orderSQL);

            }

            //MAIL SENDING TO CUSTOMER
            include "sendmail.php";

            unset($_SESSION['cartdeadline']);
            unset($_SESSION["cartid"]);
            $cartid = "";
            $activePage = "sales";
            $shoppingcartqua = 0;
            
            $hideTime = 5000;
            $alertType = "alert-dismissible";
            $systemIsMessage = true;
            $systemMessage = "<b>A megrendelés sikeresen rögzítve!</br>".
                             "A részleteket regisztrált e-mail címére küldtük!</br>".
                             "Időpont: $orderDateTime</br>".
                             "Rendelési azonosító: $lastid".
                             "</br>$c tétel </b>";
            
        }

    }


    /*********************************************************
     * REGISTRATION
     */
	if (isset($_POST["regUserName"]) && 
            isset($_POST["regPassword"]) && 
                isset($_POST["regFirstName"]) && 
                    isset($_POST["regMiddleName"]) && 
                        isset($_POST["regLastName"]) && 
                            isset($_POST["regCountryID"]) && 
                                isset($_POST["regPostCode"]) && 
                                    isset($_POST["regCity"]) && 
                                        isset($_POST["regStreet"]) && 
                                            isset($_POST["regAddress"]) && 
                                                isset($_POST["regPhone"]) && 
                                                    isset($_POST["regEmail"]) && 
                                                        isset($_POST["formName"]) && 
                                                            $_POST["formName"] == "regForm"  ) {

	    $username = $_POST["regUserName"];
        $password = $_POST["regPassword"];
        $firstname = $_POST["regFirstName"];
        $middlename = $_POST["regMiddleName"];
        $lastname = $_POST["regLastName"];
        $countryid = $_POST["regCountryID"];
        $postcode = $_POST["regPostCode"];
        $city = $_POST["regCity"];
        $street = $_POST["regStreet"];
        $address = $_POST["regAddress"];
        $phone = $_POST["regPhone"];
        $email = $_POST["regEmail"];

        $_POST = array();
        unset($_POST);

		$uname = validate($username);
		$pass = validate($password);

        $lastID = "";
        $lastDETID = "";
        $lastPWID = "";
        $usermstr = false;
        $userdet = false;
        $passwordmstr = false;

        $sqlstring = "INSERT INTO 
                        user_mstr (
                            UserNickName_MSTR, 
                            UserMail_MSTR, 
                            UserTypeID_MSTR, 
                            UserFlagID_MSTR, 
                            UserNote_MSTR
                        ) VALUES (
                            '$uname',
                            '$email',
                            '2',
                            '1',
                            '')";

        mysqli_query($connect, $sqlstring);

        $lastID = mysqli_insert_id($connect);
        $usermstr = ($lastID != "") ? true : false;

        $dateNow = date("Y-m-d H:i:s");

        $sqlstring = "INSERT INTO 
                        user_det (
                            UserMSTRID_DET, 
                            UserFirstName_DET, 
                            UserMiddleName_DET, 
                            UserLastName_DET, 
                            UserGenderID_DET,
                            UserPhone_DET, 
                            UserCountryID_DET, 
                            UserPostCode_DET,
                            UserCity_DET, 
                            UserStreet_DET,
                            UserAddress_DET,
                            UserRegDate_DET,
                            UserMotherName_DET,
                            UserBirthPlace_DET,
                            UserBirthDate_DET,
                            UserLastModifiedDate_DET
                    ) VALUES (
                            '$lastID', 
                            '$firstname', 
                            '$middlename',
                            '$lastname', 
                            '3',
                            '$phone',
                            '$countryid', 
                            '$postcode', 
                            '$city', 
                            '$street', 
                            '$address', 
                            '$dateNow', 
                            '',
                            '',
                            '1900-01-01',
                            '$dateNow'
                    )";
        mysqli_query($connect, $sqlstring);
        $lastDETID = mysqli_insert_id($connect);
        $userdet = ($lastDETID != "") ? true : false;



        $salt = generateSalt();
        $passHASH = createPasswordHashReg($pass, $salt);
        $sqlstring = "INSERT INTO 
                        password_mstr (
                            PasswordUserID_MSTR, 
                            PasswordPassword_MSTR, 
                            PasswordSalt_MSTR, 
                            PasswordStatusID_MSTR 
                    ) VALUES (
                        '$lastID', 
                        '$passHASH', 
                        '$salt',
                        '1'
                    )";
        mysqli_query($connect, $sqlstring);
        $lastPWID = mysqli_insert_id($connect);
        $passwordmstr = ($lastPWID != "") ? true : false;


        //mysqli_close($connect);

        if ($usermstr && $userdet && $passwordmstr) { 
            $hideTime = 5000;
            $alertType = "alert-dismissible";
            $systemIsMessage = true;
            $systemMessage = "<b>Sikeres regisztráció!</b>".
                                ($isUser)
                                    ? "Bejelentkezéshez előbb lépj ki a jelenlegi accountod-ból!"
                                    : "</br>Kérlek <a href='#loginForm' data-toggle='modal' title='Bejelentkezés'>jelentkezz be</a> a felhasználóneveddel és jelszavaddal.";
        } else {
            $hideTime = 5000;
            $alertType = "alert-danger";
            $systemIsMessage = true;
            $systemMessage = "<b>Sikertelen regisztráció!</b></br>".
                                "Hiba a rekordok létrehozása közben!";
        }
	}




    /******************************************************
     * SYSTEM
     */
	if (isset($_POST["systemPath"], $_POST["systemSessionDeadline"]) && 
        isset($_POST["formName"]) && $_POST["formName"] == "systemForm") {

	    $systemPath = $_POST["systemPath"];
	    $systemSessionDeadline = $_POST["systemSessionDeadline"];

        $sqlstring = "UPDATE
                        motosystem_mstr
                      SET  
                        MotoSystemWebPath_MSTR = '$systemPath',
                        MotoSystemSessionDeadline_MSTR = '$systemSessionDeadline'
                      WHERE  
                        MotoSystemID_MSTR = '1'";
        mysqli_query($connect, $sqlstring);

        $_SESSION["sessionDeadline"] = $systemSessionDeadline;


        $hideTime = 5000;
        $alertType = "alert-dismissible";
        $systemIsMessage = true;
        $systemMessage = "<b>Sikeres mentés!</b>";
	}




    /******************************************************
     * PROFILE FORM
     */
	if (isset($_POST["profileFirstName"]) && 
            isset($_POST["profileMiddleName"]) && 
                isset($_POST["profileLastName"]) && 
                    isset($_POST["profileCountryID"]) && 
                        isset($_POST["profilePostCode"]) && 
                            isset($_POST["profileCity"]) && 
                                isset($_POST["profileStreet"]) && 
                                    isset($_POST["profileAddress"]) && 
                                        isset($_POST["profilePhone"]) && 
                                            isset($_POST["profileEmail"]) && 
                                                isset($_POST["formName"]) && 
                                                    $_POST["formName"] == "profileForm"  ) {
        $userid = $_SESSION["userid"];
        $firstname = $_POST["profileFirstName"];
        $middlename = $_POST["profileMiddleName"];
        $lastname = $_POST["profileLastName"];
        $countryid = $_POST["profileCountryID"];
        $postcode = $_POST["profilePostCode"];
        $city = $_POST["profileCity"];
        $street = $_POST["profileStreet"];
        $address = $_POST["profileAddress"];
        $phone = $_POST["profilePhone"];
        $email = $_POST["profileEmail"];


        $sqlstring = "UPDATE
                        user_mstr 
                      SET
                        UserMail_MSTR = '$email'
                      WHERE
                        UserID_MSTR = '$userid'";
        mysqli_query($connect, $sqlstring);
        $dateNow = date("Y-m-d H:i:s");

        $sqlstring = "UPDATE
                        user_det 
                      SET
                        UserFirstName_DET = '$firstname', 
                        UserMiddleName_DET = '$middlename', 
                        UserLastName_DET = '$lastname', 
                        UserPhone_DET = '$phone', 
                        UserCountryID_DET = '$countryid', 
                        UserPostCode_DET = '$postcode',
                        UserCity_DET = '$city', 
                        UserStreet_DET = '$street',
                        UserAddress_DET = '$address',
                        UserLastModifiedDate_DET = '$dateNow' 
                      WHERE
                        UserMSTRID_DET = '$userid'";

        mysqli_query($connect, $sqlstring);
        //mysqli_close($connect);
        $_SESSION['userfullname'] = $firstname." ".$middlename." ".$lastname;

        //$_POST = array();
        //unset($_POST);

        $hideTime = 5000;
        $alertType = "alert-dismissible";
        $systemIsMessage = true;
        $systemMessage = "<b>Sikeres profil módosítás!</b>";
	}

?>













<!DOCTYPE html>
<html lang="hu"> 
    <head>
        <title>MotoLand</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>      
		<link rel="Icon" type="image/png" href="imgs/motorbike.png">   
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="boogie.js"></script>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

        <style>
            .carousel-inner > .item > img,
            .carousel-inner > .item > a > img {
                width: 100%;
                margin: auto;
            }   

            .ellipsis {
                overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
            }

            .footer {
                position: fixed;
                left: 0;
                bottom: 0;
                width: 100%;
                background-color: black;
                color: gray;
                text-align: center;
            }
            .footer-icon {
                width: 20px;
                margin: 5px 5px 15px 15px;
            }
            .txtarea {
                overflow-y: auto; 
                resize: none;
            }
            .navbar-background {
                background-color: black;
            }
            .circle {
                width:20px; 
                height:20px; 
                margin:3px; 
                margin-left:10px; 
                /*border:1px solid lightgray; */
                border-radius: 50%;
                background-color:red;
            }

            .bg {
                background-color: rgba(0, 0, 0, 0.1);
                border-radius: 5px;
            }

            body {
                background-image: url('imgs/bg.png');
                background-repeat: repeat;
                /*background-attachment: fixed;
                /*background-size: cover;*/
            }
        </style>

        <script>
            window.onload = (event) => {
                startItem("<?php echo $activePage;?>");
            };
        </script>

    </head>

    <body>





        <!-- NAVIGATION -->
        <nav class="navbar navbar-inverse navbar-fixed-top">

            <div class="container">

                <div class="navbar-header">
                    <button type="button" 
                        class="navbar-toggle collapsed" 
                        data-toggle="collapse" 
                        data-target="#navbar" 
                        aria-expanded="false" 
                        aria-controls="navbar">

                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <a class="navbar-brand" href="#" onclick="startItem('sales')" style="padding:5px">
                        <div style="display: inline;">
                            <img src="imgs/motorbike.png" 
                                style="background-color:white; padding:4px; border-radius:6px;">
                            MotoLand
                        </div>                            
                    </a>                    

                </div>


                <div id="navbar" class="navbar-collapse collapse"> 

                    <ul class="nav navbar-nav">

                        <li class="dropdown">
                            <a href="#" 
                                class="dropdown-toggle" 
                                data-toggle="dropdown" 
                                role="button" 
                                aria-haspopup="true" 
                                aria-expanded="false">
                                Szolgáltatások 
                                <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" onclick="startItem('servicebooking')">
                                        <span class="glyphicon glyphicon-calendar" style="margin-right:20px;"></span>
                                        Szervíz időpontfoglalás
                                    </a>
                                </li>

                                <li>
                                    <a href="#" onclick="startItem('ordering')">
                                        <span class="glyphicon glyphicon-euro" style="margin-right:20px;"></span>
                                        Alkatrészrendelés
                                    </a>
                                </li>

                            </ul>

                        </li>
                        <li><a href='#' onclick="startItem('secondhand')">Hirdetés feladás</a></li>
                        <li><a href="#">Kapcsolat</a></li>
                    </ul>


                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <a href="#registrationForm" data-toggle="modal" title="Regisztráció" >
                                <span class="glyphicon glyphicon-lock"></span>
                            </a>
                        </li>


                        <?php 
                            if ($isUser) {
                                echo "<li class='dropdown'>
                                        <a href='#' class='dropdown-toggle' data-toggle='dropdown' role='button' aria-haspopup='true' aria-expanded='false'>
                                            <span class='glyphicon glyphicon-user' ></span>
                                        </a>
                                        <ul class='dropdown-menu'>
                                            <li class='dropdown-header'>".
                                                $_SESSION["userfullname"].
                                                "<img src='imgs/user.png' style='width:40px; margin-left: 30px;'>
                                            </li>
                                            <li role='separator' class='divider'></li>
                                            <li><a href='#myProfileForm' data-toggle='modal' ><span class='glyphicon glyphicon-user' style='margin-right:20px;'></span>Profil</a></li>
                                            <li><a href='#mySecurityForm' data-toggle='modal' ><span class='glyphicon glyphicon-lock' style='margin-right:20px;'></span>Biztonság</a></li>
                                            <li role='separator' class='divider'></li>

                                            <li><a href='#' onclick='startItem(\"bookedItems\")'><span class='glyphicon glyphicon-calendar' style='margin-right:20px;'></span>Időpontfoglalásaim</a></li>

                                            <li><a href='#' onclick='startItem(\"orderedItems\")'><span class='glyphicon glyphicon-euro' style='margin-right:20px;'></span>Rendeléseim</a></li>";

                                            if (isset($_SESSION['usertype']) && ($_SESSION['usertype'] == "admin" || $_SESSION["usertype"] == "root")) {
                                                echo "<li role='separator' class='divider'></li>
                                                        <li class='dropdown-header'>Admin</li>
                                                        <li>
                                                            <a href='#systemRows' data-toggle='modal'>
                                                                <span class='glyphicon glyphicon-cog' style='margin-right:20px;'></span>
                                                                Rendszerbeállítások
                                                            </a>
                                                        </li>";
                                            }

                                            echo "<li role='separator' class='divider'></li>
                                            <li><a href='logout.php'><span class='glyphicon glyphicon-log-out' style='margin-right:20px;'></span>Kijelentkezés</a></li>
                                        </ul>
                                    </li>";
                            } else {
                                echo "<li>
                                        <a href='#loginForm' data-toggle='modal' title='Bejelentkezés'>
                                            <span class='glyphicon glyphicon-log-in'></span>   
                                        </a>
                                     </li>";
                            };

                        ?>
                        <li>
                            <?php 
                                if ($shoppingcartqua > 0) 
                                    echo "<a href='#shoppingCart' data-toggle='modal'>
                                            <span class='glyphicon glyphicon-shopping-cart'></span>
                                            <span role='img' class='badge gh-badge'>$shoppingcartqua</span>
                                         </a>";
                                else
                                    echo "<a href='#' title='Még üres a kosarad!\nCsak mondom!'>
                                            <span class='glyphicon glyphicon-shopping-cart'></span>
                                            <span role='img' class='badge gh-badge'>$shoppingcartqua</span>
                                         </a>";
                            ?>
                        </li>";



                    </ul>

                </div>
            </div>
        </nav>






        
        <div class='modal fade' data-backdrop='static' data-keyboard='false' id='bookingService' role='dialog'>

            <div class='modal-dialog'>

                <div class='modal-content'>

                    <div class='modal-header' style='padding:5px 50px;'>
                        <button type='button' class='close' style='margin-top:13px' data-dismiss='modal' style='margin-top:3px'>&times;</button>
                        <h4><span class='glyphicon glyphicon-calendar'></span> Időpontfoglalás</h4>
                    </div>

                    <div class='modal-body' style='padding:10px 50px;'>

                        <div class='row' style='margin-bottom:10px'>
                            <label class='col-sm-12 col-form-label' style='margin-top:5px'>
                                Időpontfoglaláshoz kérlek töltsd ki az alábbi űrlapot, hogy egy későbbi időpontban keresni tudjunk egyeztetés céljából.
                            </label>
                        </div>
                                        

                        <form action='index.php' method='POST'>
                            <input type='hidden' name='formName' value='bookingForm'>


                            <div class='form-group row'>
                                <label for='bookingDate' class='col-sm-4 col-form-label' style='margin-top:5px'> Dátum</label>
                                <div class='col-sm-6'>
                                    <input type='text' 
                                            readonly
                                            class='form-control-plaintext' 
                                            id='bookingDate' 
                                            name='bookingDate' 
                                            style='width:100px'>

                                    <input type='text' 
                                            readonly
                                            class='form-control-plaintext' 
                                            id='bookingDay' 
                                            name='bookingDay' 
                                            style='width:100px'>
                                </div>
                            </div>




                            <div class='form-group row'>
                                <label for='bookingFullName' class='col-sm-4 col-form-label' style='margin-top:5px'> Név *</label>
                                <div class='col-sm-6'>
                                    <input type='text' 
                                            required
                                            <?php
                                                echo (($isUser)
                                                        ? " value = '".$_SESSION["userfullname"]."' "
                                                        : "");
                                            ?>
                                            class='form-control-plaintext' 
                                            id='bookingFullName' 
                                            name='bookingFullName' 
                                            placeholder='vezetéknév keresztnév'
                                            style='width:300px'>
                                </div>
                            </div>


                            <div class='form-group row'>
                                <label for='bookingPhone' class='col-sm-4 col-form-label'> Telefonszám *</label>
                                <div class='col-sm-6'>
                                    <input type='text' 
                                            required
                                            <?php
                                                echo (($isUser) 
                                                        ? " value = '".$_SESSION["userphone"]."' "
                                                        : "");
                                            ?>                                                            
                                            class='form-control-plaintext' 
                                            id='bookingPhone' 
                                            name='bookingPhone' 
                                            placeholder='+xx xx xxxxxxx'
                                            style='width:300px'
                                            maxlength='30'
                                            onkeypress='return onlyPhone(event)'>
                                </div>
                            </div>

                            <div class='form-group row'>
                                <label for='bookingMail' class='col-sm-4 col-form-label'> E-mail cím *</label>
                                <div class='col-sm-6'>
                                    <input type='email' 
                                            required
                                            <?php
                                                echo (($isUser) 
                                                        ? " value = '".$_SESSION["usermail"]."' "
                                                        : "");
                                            ?>
                                            class='form-control-plaintext' 
                                            id='bookingMail' 
                                            name='bookingMail' 
                                            placeholder='e-mail cím'
                                            style='width:300px'
                                            maxlength='64'>
                                </div>
                            </div>

                            <button type='submit' class='btn btn-success'>
                                <span class='glyphicon glyphicon-ok'></span> Mentés
                            </button>

                            <button type='reset' class='btn btn-primary' data-dismiss='modal' >
                                <span class='glyphicon glyphicon-remove'></span> Mégsem
                            </button>

                            <div class='modal-footer' style='text-align:left '>
                                <span>A *-al jelszett mezők kitöltése kötelező!</span>
                            </div>

                        </form>

                    </div>

                    <script>
                        // ONCLOSE
                        $('#bookingService').on('hidden.bs.modal', function (e) {
                        });

                        // BEFORE ON SHOW
                        $('#bookingService').on('show.bs.modal', function (e) {
                            document.getElementById('bookingDate').value = bookingDate;
                            document.getElementById('bookingDay').value = bookingDay;
                            document.getElementById('bookingFullName').value = "";
                            document.getElementById('bookingPhone').value = "";
                            document.getElementById('bookingMail').value = "";
                        })

                    </script>
                                    
                </div>
                    
            </div>

        </div>















        <!-- LOGIN -->
        <div class="modal fade" data-backdrop="static" data-keyboard="false" id="loginForm" role="dialog">
            <div class="modal-dialog">
    
                <div class="modal-content">

                    <div class="modal-header" style="padding:5px 50px;">
                        <button type="button" class="close" style="margin-top:13px" data-dismiss="modal">&times;</button>
                        <h4><span class="glyphicon glyphicon-user"></span> Bejelentkezés</h4>
                    </div>

                    <div class="modal-body" style="padding:40px 50px;">

                        <form id="logForm" action="index.php" method="POST">

                            <div class="form-group">
                                <input type="hidden" name="formName" value="logForm">

                                <label for="loginUserName"><span class="glyphicon glyphicon-user"></span> Felhasználónév</label>
                                <input type="text" 
                                        class="form-control" 
                                        required 
                                        value="istvan.lovei@yahoo.com"
                                        id="loginUserName" 
                                        name="loginUserName" 
                                        placeholder="e-mail">
                            </div>

                            <div class="form-group">
                                <label for="loginpassword"><span class="glyphicon glyphicon-eye-open"></span> Jelszó</label>
                                <input type="password" class="form-control" value="Gatya101" required id="loginPassword" name="loginPassword" placeholder="jelszó">
                            </div>

                            <button type="submit" class="btn btn-success ">
                                <span class="glyphicon glyphicon-off"></span> 
                                Bejelentkezés
                            </button>
                            <button class="btn btn-primary" data-dismiss="modal" >
                                <span class="glyphicon glyphicon-remove"></span> 
                                Bezárás
                            </button>

                        </form>

                    </div>

                    <div class="modal-footer">
                        <p>Nem vagy még tag? <a href="#registrationForm" class="close" data-dismiss="modal" data-toggle="modal" style="font-size:14px; color: black; margin:3px 10px;"> Regisztráció</a></p>
                    </div>

                    <script>
                        // ONCLOSE
                        $('#loginForm').on('hidden.bs.modal', function () {
                        });

                        // BEFORE ON SHOW
                        $('#loginForm').on('show.bs.modal', function (e) {
                        })

                    </script>


                </div>
            </div>
        </div> 







        <?php 
            /************************************************
             * SECURITY 
             */
            if ($isUser) {
                echo "  <div class='modal fade' data-backdrop='static' data-keyboard='false' id='mySecurityForm' role='dialog'>
                            <div class='modal-dialog'>
                    
                                <div class='modal-content'>

                                    <div class='modal-header' style='padding:5px 50px;'>
                                        <button type='button' class='close' style='margin-top:13px' data-dismiss='modal'>&times;</button>
                                        <h4><span class='glyphicon glyphicon-lock'></span> Jelszómódosítás</h4>
                                    </div>

                                    <div class='modal-body' style='padding:40px 50px;'>

                                        <form id='securityForm' action='index.php' method='POST' onsubmit='return chkPSWRD()'>

                                            <div class='form-group'>
                                                <input type='hidden' name='formName' value='securityForm'>
                                                <label for='oldPassword'>Régi jelszó</label>
                                                <input type='password' class='form-control' value='' required id='oldPassword' name='oldPassword' placeholder='jelenlegi jelszó'>
                                            </div>

                                            <div class='form-group'>
                                                <label for='newPassword'>Új jelszó</label>
                                                <input type='password' class='form-control' value='' required id='newPassword' name='newPassword' placeholder='új jelszó'>
                                            </div>

                                            <div class='form-group'>
                                                <label for='newPasswordC'>Ismétlés</label>
                                                <input type='password' class='form-control' value='' required id='newPasswordC' placeholder='ismétlés'>
                                            </div>

                                            <button type='submit' class='btn btn-success '>
                                                <span class='glyphicon glyphicon-off'></span> 
                                                Jelszócsere
                                            </button>

                                            <button class='btn btn-primary' data-dismiss='modal' >
                                                <span class='glyphicon glyphicon-remove'></span> 
                                                Bezárás
                                            </button>

                                        </form>

                                    </div>


                                    <script>
                                        // ONCLOSE
                                        $('#mySecurityForm').on('hidden.bs.modal', function () {
                                        });

                                        // BEFORE ON SHOW
                                        $('#mySecurityForm').on('show.bs.modal', function (e) {
                                        document.getElementById('oldPassword').focus;
                                        })

                                    </script>


                                </div>
                            </div>
                        </div>";
            }
        ?>

















        <!-- SYSTEM -->
        <?php 
            if (isset($_SESSION['usertype']) && ($_SESSION['usertype'] == "admin" || $_SESSION["usertype"] == "root")) {
                echo "  <div class='modal fade' data-backdrop='static' data-keyboard='false' id='systemRows' role='dialog'>
                            <div class='modal-dialog'>
    
                                <div class='modal-content'>

                                    <div class='modal-header' style='padding:5px 50px;'>
                                        <button type='button' class='close' data-dismiss='modal'>&times;</button>
                                        <h4><span class='glyphicon glyphicon-cog'></span> Rendszerbeállítások</h4>
                                    </div>

                                    <div class='modal-body' style='padding:40px 50px;'>

                                        <form id='systemForm' action='index.php' method='POST'>

                                            <div class='form-group'>
                                                <input type='hidden' name='formName' value='systemForm'>

                                                <label for='systemPath'>Rendszer útvonal</label>
                                                <input type='text' 
                                                        class='form-control' 
                                                        required 
                                                        id='systemPath' 
                                                        name='systemPath' 
                                                        placeholder='web cím'>

                                                <label for='systemSessionDeadline'>Session határidő</label>
                                                <div>
                                                    <input type='number' 
                                                            min='60'
                                                            class='form-control' 
                                                            required 
                                                            style='width:100px;display:inline-block;'
                                                            id='systemSessionDeadline' 
                                                            name='systemSessionDeadline' 
                                                            placeholder='session'>
                                                    sec.
                                                </div>
                                            </div>




                                            <button type='submit' class='btn btn-success '>
                                                <span class='glyphicon glyphicon-floppy-disk'></span> 
                                                Mentés
                                            </button>

                                            <button type='reset' class='btn btn-primary' data-dismiss='modal' >
                                                <span class='glyphicon glyphicon-remove'></span> 
                                                Mégsem
                                            </button>

                                        </form>

                                    </div>

                                    <script>
                                        // ONCLOSE
                                        $('#systemRows').on('hidden.bs.modal', function () {
                                        });

                                        // BEFORE ON SHOW
                                        $('#systemRows').on('show.bs.modal', function (e) {
                                            initFields('systemPath');
                                            initFields('systemSessionDeadline');
                                        })
                                    </script>

                                </div>

                            </div>

                        </div> ";
            }
        ?>






        <!-- SHOPPING CART -->
        <div class="modal fade" data-backdrop="static" data-keyboard="false" id="shoppingCart" role="dialog">
            <div class="modal-dialog">
    
                <div class="modal-content">

                    <div class="modal-header" style="padding:5px 30px;">
                        <button type="button" class="close" style="margin-top:13px" data-dismiss="modal">&times;</button>
                        <h4><span class="glyphicon glyphicon-shopping-cart"></span> Bevásárlókosár</h4>
                    </div>


                    <div class="modal-body" style="padding:5px 30px;">

                        <form id="rollbackForm" action="index.php" method="POST" onsubmit="return que();">
                            <input type="hidden" name="formName" value="shoppingCartRollbackForm">

                            <div id="cartBody" style="max-height:400px; overflow-x: auto; overflow-y: none; margin-bottom:10px;"></div>

                            <button type="button" class="btn btn-success" onclick="startItem('payments');" data-dismiss="modal">
                                <span class="glyphicon glyphicon-piggy-bank"></span> 
                                Fizetés
                            </button>
                                
                            <button type="submit" class="btn btn-primary" >
                                <span class="glyphicon glyphicon-trash"></span> 
                                Kosár törlés
                            </button>

                            <button class="btn btn-primary" data-dismiss="modal" >
                                <span class="glyphicon glyphicon-remove"></span> 
                                Bezárás
                            </button>

                        
                        </form>    
                        
                    </div>



                    <script>
                        // ONCLOSE
                        $('#shoppingCart').on('hidden.bs.modal', function () {
                        });

                        // BEFORE ON SHOW
                        $('#shoppingCart').on('show.bs.modal', function (e) {
                            initFields("cartBody");
                        })
                    </script>

                </div>

            </div>

        </div> 




    
        <!-- REGISTRATION -->
        <div class="modal fade" data-backdrop="static" data-keyboard="false" id="registrationForm" role="dialog">
            <div class="modal-dialog">
    
                <div class="modal-content">

                    <div class="modal-header" style="padding:5px 50px;">
                        <button type="button" class="close" style="margin-top:13px" data-dismiss="modal" style="margin-top:3px">&times;</button>
                        <h4><span class="glyphicon glyphicon-lock"></span> Regisztráció</h4>
                    </div>

                    <div class="modal-body" style="padding:10px 50px;">



                        <form id="regForm" 
                                action="index.php"
                                method="POST" 
                                onsubmit="return checkForms(this)" >

                            <div class="form-group row">
                                <label for="regUserName" class="col-sm-4 col-form-label" style="margin-top:5px">
                                    <span class="glyphicon glyphicon-user"></span> Felhasználónév *
                                </label>
                                <div class="col-sm-6">

                                    <input type="hidden" name="formName" value="regForm">

                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regUserName" 
                                            name="regUserName" 
                                            placeholder="email cím vagy felhasználónév"  
                                            style="width:300px"
                                            onfocusout="checkUserName(this)">
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="regPassword" class="col-sm-4 col-form-label" style="margin-top:5px">
                                    <span class="glyphicon glyphicon-eye-open"></span> Jelszó *
                                </label>

                                <div class="col-sm-6">
                                    <input type="password" 
                                            pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                                            required
                                            class="form-control-plaintext" 
                                            id="regPassword" 
                                            name="regPassword" 
                                            placeholder="jelszó"
                                            style="width:270px"
                                            onkeyup="chkp()"
                                            onfocusout="checkPW()">
                                </div>
                                <div class="col-sm-2">
                                    <div id="passwordValidator" class="circle"></div>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password1Check" class="col-sm-4 col-form-label" style="margin-top:5px">
                                    <span class="glyphicon glyphicon-eye-open"></span> Ellenőrzés *
                                </label>

                                <div class="col-sm-6">
                                    <input type="password" 
                                            required
                                            class="form-control-plaintext" 
                                            id="passwordCheck" 
                                            placeholder="jelszó ellenőrzés"
                                            style="width:270px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regFirstName" class="col-sm-4 col-form-label" style="margin-top:5px"> Vezetéknév *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regFirstName" 
                                            name="regFirstName" 
                                            placeholder="vezetéknév"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regMiddleName" class="col-sm-4 col-form-label" style="margin-top:5px" > Keresztnév *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regMiddleName" 
                                            name="regMiddleName" 
                                            placeholder="keresztnév"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regLastName" class="col-sm-4 col-form-label" style="margin-top:5px"> Keresztnév</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            alt="noChecking"
                                            class="form-control-plaintext" 
                                            id="regLastName" 
                                            name="regLastName" 
                                            placeholder="keresztnév"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regCountryID" class="col-sm-4 col-form-label" style="margin-top:5px"> Ország *</label>
                                <div class="col-sm-6">
                                    <select class="form-select form-select-sm" id="regCountryID" name="regCountryID" aria-label=".form-select-sm example"></select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regPostCode" class="col-sm-4 col-form-label"> Irányítószám *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required 
                                            class="form-control-plaintext" 
                                            id="regPostCode" 
                                            name="regPostCode" 
                                            placeholder="irányítószám" 
                                            style="width:300px" 
                                            onkeypress="return onlyNumber(event)" 
                                            maxlength="8">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regCity" class="col-sm-4 col-form-label"> Város *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regCity" 
                                            name="regCity" 
                                            placeholder="város"
                                            style="width:300px"
                                            maxlength="30">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regStreet" class="col-sm-4 col-form-label"> Utca *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regStreet" 
                                            name="regStreet" 
                                            placeholder="út/utca/tér ...stb"
                                            style="width:300px"
                                            maxlength="30">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regAddress" class="col-sm-4 col-form-label"> Házszám/emelet *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regAddress" 
                                            name="regAddress" 
                                            placeholder="házszám/emelet/ajtó...stb"
                                            style="width:300px"
                                            maxlength="50">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regPhone" class="col-sm-4 col-form-label"> Telefonszám *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regPhone" 
                                            name="regPhone" 
                                            placeholder="telefonszám"
                                            style="width:300px"
                                            maxlength="30"
                                            onkeypress="return onlyPhone(event)">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="regEmail" class="col-sm-4 col-form-label"> E-mail cím *</label>
                                <div class="col-sm-6">
                                    <input type="email" 
                                            required
                                            class="form-control-plaintext" 
                                            id="regEmail" 
                                            name="regEmail" 
                                            placeholder="e-mail cím"
                                            style="width:300px"
                                            maxlength="64">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success">
                                <span class="glyphicon glyphicon-ok"></span> Regisztráció
                            </button>

                            <button type="button" class="btn btn-primary" data-dismiss="modal" >
                                <span class="glyphicon glyphicon-remove"></span> Mégsem
                            </button>

                            <div class="modal-footer" style="text-align:left ">
                                <span id="regMessage">A *-al jelszett mezők kitöltése kötelező!</span>
                            </div>

                        </form>

                    </div>

                </div>
      
            </div>

            <script>
                // ONCLOSE
                $('#registrationForm').on('hidden.bs.modal', function () {
                    clearForm("regForm");
                });

                // BEFORE ON SHOW
                $('#registrationForm').on('show.bs.modal', function (e) {
                    initFields("regCountryID");
                })

            </script>

        </div> 










        <!-- MY PROFILE -->
        <?php 
            if (isset($_SESSION["usertype"])) {

                echo "  <div class='modal fade' data-backdrop='static' data-keyboard='false' id='myProfileForm' role='dialog'>
                            <div class='modal-dialog'>
    
                                <div class='modal-content'>

                                    <div class='modal-header' style='padding:5px 50px;'>
                                        <button type='button' class='close' style='margin-top:13px' data-dismiss='modal' style='margin-top:3px'>&times;</button>
                                        <h4><span class='glyphicon glyphicon-lock'></span> Profil</h4>
                                    </div>

                                    <div class='modal-body' style='padding:10px 50px;'>



                                        <form id='profileForm' 
                                                action='index.php'
                                                method='POST' 
                                                onsubmit='return checkForms(this)' >

                                            <div class='form-group row'>
                                                <label for='profileUserName' class='col-sm-4 col-form-label' style='margin-top:5px'>
                                                    <span class='glyphicon glyphicon-user'></span> Felhasználónév *
                                                </label>
                                                <div class='col-sm-6'>

                                                    <input type='hidden' name='formName' value='profileForm'>

                                                    <input type='text' 
                                                            readonly 
                                                            disabled 
                                                            class='form-control-plaintext' 
                                                            id='profileUserName' 
                                                            name='profileUserName' 
                                                            placeholder='email cím vagy felhasználónév'  
                                                            style='width:300px'>
                                                </div>
                                            </div>



                                            <div class='form-group row'>
                                                <label for='profileFirstName' class='col-sm-4 col-form-label' style='margin-top:5px'> Vezetéknév *</label>
                                                <div class='col-sm-6'>
                                                    <input type='text' 
                                                            required
                                                            class='form-control-plaintext' 
                                                            id='profileFirstName' 
                                                            name='profileFirstName' 
                                                            placeholder='vezetéknév'
                                                            style='width:300px'>
                                                </div>
                                            </div>

                                            <div class='form-group row'>
                                                <label for='profileMiddleName' class='col-sm-4 col-form-label' style='margin-top:5px' > Keresztnév *</label>
                                                <div class='col-sm-6'>
                                                    <input type='text' 
                                                            required
                                                            class='form-control-plaintext' 
                                                            id='profileMiddleName' 
                                                            name='profileMiddleName' 
                                                            placeholder='keresztnév'
                                                            style='width:300px'>
                                                </div>
                                            </div>

                                            <div class='form-group row'>
                                                <label for='profileLastName' class='col-sm-4 col-form-label' style='margin-top:5px'> Keresztnév</label>
                                                <div class='col-sm-6'>
                                                    <input type='text' 
                                                            alt='noChecking'
                                                            class='form-control-plaintext' 
                                                            id='profileLastName' 
                                                            name='profileLastName' 
                                                            placeholder='keresztnév'
                                                            style='width:300px'>
                                                </div>
                                            </div>

                                            <div class='form-group row'>
                                                <label for='profileCountryID' class='col-sm-4 col-form-label' style='margin-top:5px'> Ország *</label>
                                                <div class='col-sm-6'>
                                                    <select class='form-select form-select-sm' id='profileCountryID' name='profileCountryID' aria-label='.form-select-sm example'></select>
                                                </div>
                                            </div>

                                            <div class='form-group row'>
                                                <label for='profilePostCode' class='col-sm-4 col-form-label'> Irányítószám *</label>
                                                <div class='col-sm-6'>
                                                    <input type='text' 
                                                            required 
                                                            class='form-control-plaintext' 
                                                            id='profilePostCode' 
                                                            name='profilePostCode' 
                                                            placeholder='irányítószám' 
                                                            style='width:300px' 
                                                            onkeypress='return onlyNumber(event)' 
                                                            maxlength='8'>
                                                </div>
                                            </div>

                                            <div class='form-group row'>
                                                <label for='profileCity' class='col-sm-4 col-form-label'> Város *</label>
                                                <div class='col-sm-6'>
                                                    <input type='text' 
                                                            required
                                                            class='form-control-plaintext' 
                                                            id='profileCity' 
                                                            name='profileCity' 
                                                            placeholder='város'
                                                            style='width:300px'
                                                            maxlength='30'>
                                                </div>
                                            </div>

                                            <div class='form-group row'>
                                                <label for='profileStreet' class='col-sm-4 col-form-label'> Utca *</label>
                                                <div class='col-sm-6'>
                                                    <input type='text' 
                                                            required
                                                            class='form-control-plaintext' 
                                                            id='profileStreet' 
                                                            name='profileStreet' 
                                                            placeholder='út/utca/tér ...stb'
                                                            style='width:300px'
                                                            maxlength='30'>
                                                </div>
                                            </div>

                                            <div class='form-group row'>
                                                <label for='profileAddress' class='col-sm-4 col-form-label'> Házszám/emelet *</label>
                                                <div class='col-sm-6'>
                                                    <input type='text' 
                                                            required
                                                            class='form-control-plaintext' 
                                                            id='profileAddress' 
                                                            name='profileAddress' 
                                                            placeholder='házszám/emelet/ajtó...stb'
                                                            style='width:300px'
                                                            maxlength='50'>
                                                </div>
                                            </div>

                                            <div class='form-group row'>
                                                <label for='profilePhone' class='col-sm-4 col-form-label'> Telefonszám *</label>
                                                <div class='col-sm-6'>
                                                    <input type='text' 
                                                            required
                                                            class='form-control-plaintext' 
                                                            id='profilePhone' 
                                                            name='profilePhone' 
                                                            placeholder='telefonszám'
                                                            style='width:300px'
                                                            maxlength='30'
                                                            onkeypress='return onlyPhone(event)'>
                                                </div>
                                            </div>

                                            <div class='form-group row'>
                                                <label for='profileEmail' class='col-sm-4 col-form-label'> E-mail cím *</label>
                                                <div class='col-sm-6'>
                                                    <input type='email' 
                                                            required
                                                            class='form-control-plaintext' 
                                                            id='profileEmail' 
                                                            name='profileEmail' 
                                                            placeholder='e-mail cím'
                                                            style='width:300px'
                                                            maxlength='64'>
                                                </div>
                                            </div>

                                            <button type='submit' class='btn btn-success'>
                                                <span class='glyphicon glyphicon-ok'></span> Mentés
                                            </button>

                                            <button type='reset' class='btn btn-primary' data-dismiss='modal' >
                                                <span class='glyphicon glyphicon-remove'></span> Mégsem
                                            </button>

                                            <div class='modal-footer' style='text-align:left '>
                                                <span id='profileMessage'>A *-al jelszett mezők kitöltése kötelező!</span>
                                            </div>

                                        </form>

                                    </div>

                                </div>
      
                            </div>

                            <script>
                                // ONCLOSE
                                $('#myProfileForm').on('hidden.bs.modal', function () {
                                    //clearForm('regForm');
                                });

                                // BEFORE ON SHOW
                                $('#myProfileForm').on('show.bs.modal', function (e) {
                                    initFields('profileCountryID');
                                    initProfileEditor('".$_SESSION["userid"]."');
                                })

                            </script>

                        </div>";
            }
        ?>







        <!-- SECONDHAND -->
        <div class='modal fade' data-backdrop='static' data-keyboard='false' id='editsecondhand' role='dialog'>

            <div class='modal-dialog'>

                <div class='modal-content'>

                    <div class='modal-header' style='padding:5px 50px;'>
                        <button type='button' class='close' style='margin-top:13px' data-dismiss='modal' style='margin-top:3px'>&times;</button>
                        <H4>Részletek</h4>
                    </div>

                    <div class='modal-body' style='padding:10px 50px;' id='secondHandDetails'></div>

                    <script>
                        // ONCLOSE
                        $('#editsecondhand').on('hidden.bs.modal', function (e) {
                        });

                        // BEFORE ON SHOW
                        $('#editsecondhand').on('show.bs.modal', function (e) {
                            setSecondhandDetails(document.getElementById("detailID").value);
                            
                        })

                    </script>
                                    
                </div>
                    
            </div>

        </div>












        <div class="container" style="padding:0px;" id="container">


            <div id="subcontainer" class="bg">

                <div w3-include-html="homeMotos.html"></div>

            </div>

            <!-- SYSTEM MESSAGE -->
            <?php
                if ($systemIsMessage) {
                    $systemIsMessage = false;
                    echo "<div class='alert alert-success $alertType' style='position:absolute; top:100px; width:500px;' id='messagediv'>
                                <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                                $systemMessage
                            </div>
                            <script>
                                let nt = setInterval(clrInterval, $hideTime);
                                function clrInterval() {
                                    clearInterval(nt);
                                    let parent = document.getElementById('messagediv').parentNode;
                                    parent.removeChild(document.getElementById('messagediv'));
                                }		
                            </script>";
                    $systemMessage = "";
                }
            ?>





        </div>
    
        <div class="footer">
            <div style="margin-top:10px;">
                <p>&copy; 2025-<?php echo Date("Y"); ?> MotoLand, Inc. All rights reserved.</p>
                <a href="https://facebook.com" target="new"><img class="footer-icon" src="imgs/facebook.png" title="FaceBook"></a>
                <a href="https://instagram.com" target="new"><img class="footer-icon" src="imgs/instagram.png" title="Instagram"></a>
                <a href="https://x.com" target="new"><img class="footer-icon" src="imgs/twitter.png" title="Twitter"></a>
            </div>
        </div>


    </body>


</html>


<?php
    mysqli_close($connect);
?>