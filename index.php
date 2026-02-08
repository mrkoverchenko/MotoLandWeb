<?php 

    if (session_status() != PHP_SESSION_ACTIVE) {
        session_start();
        $_SESSION["systemPath"] = "http://localhost/mrkoverchenko/MotoLandWeb/";
    }

    $cartid = "üres";
    $activePage = "sales";
    $shoppingcartqua = 0;


    if (isset($_SESSION["cartid"]))
        $cartid = $_SESSION["cartid"];


    /*****************************************************************
     * SESSION CHECKING
    */
    if (!empty($_SESSION['cartdeadline']) && $_SESSION['cartdeadline'] < time() - 3600) {
        session_unset();
        session_destroy();
        session_start();

        $hideTime = 10000;
        $systemIsMessage = true;
        $cartIs = false;
        $alertType = "alert-danger";
        $systemMessage = "<b>Lejárt a munkamenet!</b";
    }



    include "connect.php";

    $systemIsMessage = false;
    $systemMessage = "";
    $cartIs = false;
    $ok = 0;
    $isUser = false;
    
    if (isset($_SESSION['userid'])) {
        $isUser = true;
    }

    if (isset($_GET["session"]) && $_GET["session"] == "out") {
//        sessionOutMessage();
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

       
        $systemIsMessage = true;
        $systemMessage = "";
        $hideTime = 10000;
        $alertType = "alert-dismissible";
        $systemMessage = "<b>Sikeres kijelentkezés!</b>";

    }
    

    /**************************************************************
     * ADDED TO SHOPPING CART IS SUCCESSFUL MESSAGE OR NOT
     */
    if (isset($_GET["shoppingcart"])) { 
        $hideTime = 10000;
        $systemIsMessage = true;

        if ($_GET["shoppingcart"] === "added") {

            $shoppingcartqua = $_SESSION["shoppingcartquantity"] + 1;
            $_SESSION["shoppingcartquantity"] = $shoppingcartqua;

            $cartIs = true;
            $alertType = "alert-dismissible";
            $systemMessage = "<b>Sikeresen a kosárba raktad!</b></br>Folytathatod a vásárlást vagy megtekintheted <a href='#shoppingCart' data-toggle='modal'>kosarad</a> tartalmát.";
        } else {
            $cartIs = false;
            $alertType = "alert-danger";
            $systemMessage = "<b>Probléma a kosárbahelyezésnél!</b";
        }

        if ($_GET["shoppingcart"] === "cleared") {
            unset($_SESSION["shoppingcartquantity"]);
            $shoppingcartqua = 0;

            $alertType = "alert-danger";
            $systemMessage = "<b>Kosár kiürítve!</b";
        }


        if (isset($_GET["page"])) {
            $activePage = $_GET["page"];
        }

    }



    ////////////////////////////////////////////////////////
    // LOGIN
    ////////////////////////////////////////////////////////
	if (isset($_POST["loginUserName"]) && 
            isset($_POST["loginPassword"]) && 
                $_POST["formName"] == "logForm") {
        
		function validate($data){
			$data = trim($data);
			$data = stripslashes($data);
			$data = htmlspecialchars($data); 
			return $data;
		}
		 
		$uname = strtolower(validate($_POST['loginUserName']));          //User Mail address or Nick name
		$passwordFromInput = validate($_POST['loginPassword']);
			 
        $sql = "SELECT 
                    UserID_MSTR,
                    UserMail_MSTR,
                    UserNickName_MSTR,
                    UserTypeID_MSTR,
                    UserFlagID_MSTR,
                    PasswordSalt_MSTR,
                    PasswordPassword_MSTR,
                    CONCAT(UserFirstName_DET,' ',UserMiddleName_DET,' ',UserLastName_DET) AS UserFullName

                FROM 
                    user_mstr, user_det, password_mstr
                WHERE 
                    LOWER(UserNickName_MSTR) = '$uname' AND 
                    UserMSTRID_DET = UserID_MSTR AND 
                    UserID_MSTR = PasswordUserID_MSTR AND 
                    UserTypeID_MSTR <> '6'";

        $isUser = false;
        $result = mysqli_query($connect, $sql);
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            
            $saltFromDb = $row["PasswordSalt_MSTR"];
            $passwordFromDb = $row["PasswordPassword_MSTR"];

            if (hash('sha256', $saltFromDb.$passwordFromInput) === $passwordFromDb) {
                $isUser = true;

                $_SESSION['usernickname'] = $row['UserNickName_MSTR'];
                $_SESSION['userid'] = $row['UserID_MSTR'];
                $_SESSION['userfullname'] = $row['UserFullName'];
                $_SESSION['userdeadline'] = time();

            } else {
                $isUser = false;
            }
            $_POST = array();
            unset($_POST);
		}
        mysqli_close($connect);



        $hideTime = 10000;
        $systemIsMessage = true;
        if ($isUser) {
            $alertType = "alert-dismissible";
            $systemMessage = "<b>Sikeres bejelentkezés!</b>";
        } else {
            $alertType = "alert-danger";
            $systemMessage = "<b>Sikertelen bejelentkezés!</b";
        }

    }




    ////////////////////////////////////////////////////////
    // REGISTRATION
    ////////////////////////////////////////////////////////
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

		function validate($data){
			$data = trim($data);
			$data = stripslashes($data);
			$data = htmlspecialchars($data); 
			return $data;
		}
		 
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


        function generateSalt($length = 32) {
            return bin2hex(random_bytes($length));
        }

      
        function createPasswordHashReg($password, $salt) {
            return hash('sha256', $salt . $password);
            //return hash('sha256', $password);
        }

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


        mysqli_close($connect);

        if ($usermstr && $userdet && $passwordmstr) { 
            $hideTime = 10000;
            $alertType = "alert-dismissible";
            $systemIsMessage = true;
            $systemMessage = "<b>Sikeres regisztráció!</b>".
                                ($isUser)
                                    ? "Bejelentkezéshez előbb lépj ki a jelenlegi accountod-ból!"
                                    : "</br>Kérlek <a href='#loginForm' data-toggle='modal' title='Bejelentkezés'>jelentkezz be</a> a felhasználóneveddel és jelszavaddal.";
        } else {
            $hideTime = 10000;
            $alertType = "alert-danger";
            $systemIsMessage = true;
            $systemMessage = "<b>Sikertelen regisztráció!</b></br>".
                                "Hiba a rekordok létrehozása közben!";
        }
	}




    ////////////////////////////////////////////////////////
    // SYSTEM
    ////////////////////////////////////////////////////////
	if (isset($_POST["systemPath"]) && 
        isset($_POST["formName"]) && $_POST["formName"] == "systemForm") {

	    $systemPath = $_POST["systemPath"];

        $sqlstring = "UPDATE
                        motosystem_mstr
                      SET  
                        MotoSystemWebPath_MSTR = '$systemPath'
                      WHERE  
                        MotoSystemID_MSTR = '1'";
        mysqli_query($connect, $sqlstring);

        mysqli_close($connect);

        $hideTime = 10000;
        $alertType = "alert-dismissible";
        $systemIsMessage = true;
        $systemMessage = "<b>Sikeres mentés!</b>";
	}




    ////////////////////////////////////////////////////////
    // PROFILE FORM
    ////////////////////////////////////////////////////////
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
        mysqli_close($connect);
        $_SESSION['userfullname'] = $firstname." ".$middlename." ".$lastname;

        //$_POST = array();
        //unset($_POST);

        $hideTime = 10000;
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

        </style>


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
                        <li><a href="#">Értékesítés</a></li>
                        <li><a href="#">Kapcsolat</a></li>

                        <li class="dropdown">
                            <a href="#" 
                                class="dropdown-toggle" 
                                data-toggle="dropdown" 
                                role="button" 
                                aria-haspopup="true" 
                                aria-expanded="false">
                                Szolgáltatások <?php echo $cartid; ?>
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

                                <li role="separator" class="divider"></li>
                                <li class="dropdown-header">Egyéb</li>
                                <li>
                                    <a href="#systemRows" data-toggle="modal">
                                        <span class="glyphicon glyphicon-cog" style="margin-right:20px;"></span>
                                        Rendszerbeállítások
                                    </a>
                                </li>
                            </ul>

                        </li>
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
                                            <li><a href='#'><span class='glyphicon glyphicon-calendar' style='margin-right:20px;'></span>Időpontfoglalásaim</a></li>
                                            <li><a href='#'><span class='glyphicon glyphicon-euro' style='margin-right:20px;'></span>Rendeléseim</a></li>
                                            <li role='separator' class='divider'></li>
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
                                        value="LoIs"
                                        id="loginUserName" 
                                        name="loginUserName" 
                                        placeholder="e-mail">
                            </div>

                            <div class="form-group">
                                <label for="loginpassword"><span class="glyphicon glyphicon-eye-open"></span> Jelszó</label>
                                <input type="password" class="form-control" value="" required id="loginPassword" name="loginPassword" placeholder="jelszó">
                            </div>

                            <button type="submit" class="btn btn-success ">
                                <span class="glyphicon glyphicon-off"></span> 
                                Bejelentkezés
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



        <!-- SYSTEM -->
        <div class="modal fade" data-backdrop="static" data-keyboard="false" id="systemRows" role="dialog">
            <div class="modal-dialog">
    
                <div class="modal-content">

                    <div class="modal-header" style="padding:5px 50px;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4><span class="glyphicon glyphicon-cog"></span> Rendszerbeállítások</h4>
                    </div>

                    <div class="modal-body" style="padding:40px 50px;">

                        <form id="systemForm" action="index.php" method="POST">

                            <div class="form-group">
                                <input type="hidden" name="formName" value="systemForm">

                                <label for="systemPath">Rendszer útvonal</label>
                                <input type="text" 
                                        class="form-control" 
                                        required 
                                        id="systemPath" 
                                        name="systemPath" 
                                        placeholder="web cím">
                            </div>

                            <button type="submit" class="btn btn-success ">
                                <span class="glyphicon glyphicon-floppy-disk"></span> 
                                Mentés
                            </button>

                            <button type="reset" class="btn btn-primary" data-dismiss="modal" >
                                <span class="glyphicon glyphicon-remove"></span> 
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
                            initFields("systemPath");
                        })
                    </script>

                </div>

            </div>

        </div> 



        <!-- SHOPPING CART -->
        <div class="modal fade" data-backdrop="static" data-keyboard="false" id="shoppingCart" role="dialog">
            <div class="modal-dialog">
    
                <div class="modal-content">

                    <div class="modal-header" style="padding:5px 50px;">
                        <button type="button" class="close" style="margin-top:13px" data-dismiss="modal">&times;</button>
                        <h4><span class="glyphicon glyphicon-shopping-cart"></span> Bevásárlókosár</h4>
                    </div>

                    <div class="modal-body" style="padding:5px 20px;">
                        <div id="cartBody"></div>


                        <button type="submit" class="btn btn-success">
                            <span class="glyphicon glyphicon-piggy-bank"></span> 
                            Fizetés és szállítás
                        </button>

                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="clearSC()">
                            <span class="glyphicon glyphicon-trash"></span> 
                            Kosár törlés
                        </button>

                        <a href="#" class="btn btn-primary" data-dismiss="modal" >
                            <span class="glyphicon glyphicon-trash"></span> 
                            Bezárás
                        </button>


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
        <div class="modal fade" data-backdrop="static" data-keyboard="false" id="myProfileForm" role="dialog">
            <div class="modal-dialog">
    
                <div class="modal-content">

                    <div class="modal-header" style="padding:5px 50px;">
                        <button type="button" class="close" style="margin-top:13px" data-dismiss="modal" style="margin-top:3px">&times;</button>
                        <h4><span class="glyphicon glyphicon-lock"></span> Profil</h4>
                    </div>

                    <div class="modal-body" style="padding:10px 50px;">



                        <form id="profileForm" 
                                action="index.php"
                                method="POST" 
                                onsubmit="return checkForms(this)" >

                            <div class="form-group row">
                                <label for="profileUserName" class="col-sm-4 col-form-label" style="margin-top:5px">
                                    <span class="glyphicon glyphicon-user"></span> Felhasználónév *
                                </label>
                                <div class="col-sm-6">

                                    <input type="hidden" name="formName" value="profileForm">

                                    <input type="text" 
                                            readonly 
                                            disabled 
                                            class="form-control-plaintext" 
                                            id="profileUserName" 
                                            name="profileUserName" 
                                            placeholder="email cím vagy felhasználónév"  
                                            style="width:300px">
                                </div>
                            </div>



                            <div class="form-group row">
                                <label for="profileFirstName" class="col-sm-4 col-form-label" style="margin-top:5px"> Vezetéknév *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="profileFirstName" 
                                            name="profileFirstName" 
                                            placeholder="vezetéknév"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="profileMiddleName" class="col-sm-4 col-form-label" style="margin-top:5px" > Keresztnév *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="profileMiddleName" 
                                            name="profileMiddleName" 
                                            placeholder="keresztnév"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="profileLastName" class="col-sm-4 col-form-label" style="margin-top:5px"> Keresztnév</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            alt="noChecking"
                                            class="form-control-plaintext" 
                                            id="profileLastName" 
                                            name="profileLastName" 
                                            placeholder="keresztnév"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="profileCountryID" class="col-sm-4 col-form-label" style="margin-top:5px"> Ország *</label>
                                <div class="col-sm-6">
                                    <select class="form-select form-select-sm" id="profileCountryID" name="profileCountryID" aria-label=".form-select-sm example"></select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="profilePostCode" class="col-sm-4 col-form-label"> Irányítószám *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required 
                                            class="form-control-plaintext" 
                                            id="profilePostCode" 
                                            name="profilePostCode" 
                                            placeholder="irányítószám" 
                                            style="width:300px" 
                                            onkeypress="return onlyNumber(event)" 
                                            maxlength="8">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="profileCity" class="col-sm-4 col-form-label"> Város *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="profileCity" 
                                            name="profileCity" 
                                            placeholder="város"
                                            style="width:300px"
                                            maxlength="30">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="profileStreet" class="col-sm-4 col-form-label"> Utca *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="profileStreet" 
                                            name="profileStreet" 
                                            placeholder="út/utca/tér ...stb"
                                            style="width:300px"
                                            maxlength="30">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="profileAddress" class="col-sm-4 col-form-label"> Házszám/emelet *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="profileAddress" 
                                            name="profileAddress" 
                                            placeholder="házszám/emelet/ajtó...stb"
                                            style="width:300px"
                                            maxlength="50">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="profilePhone" class="col-sm-4 col-form-label"> Telefonszám *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="profilePhone" 
                                            name="profilePhone" 
                                            placeholder="telefonszám"
                                            style="width:300px"
                                            maxlength="30"
                                            onkeypress="return onlyPhone(event)">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="profileEmail" class="col-sm-4 col-form-label"> E-mail cím *</label>
                                <div class="col-sm-6">
                                    <input type="email" 
                                            required
                                            class="form-control-plaintext" 
                                            id="profileEmail" 
                                            name="profileEmail" 
                                            placeholder="e-mail cím"
                                            style="width:300px"
                                            maxlength="64">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success">
                                <span class="glyphicon glyphicon-ok"></span> Mentés
                            </button>

                            <button type="reset" class="btn btn-primary" data-dismiss="modal" >
                                <span class="glyphicon glyphicon-remove"></span> Mégsem
                            </button>

                            <div class="modal-footer" style="text-align:left ">
                                <span id="profileMessage">A *-al jelszett mezők kitöltése kötelező!</span>
                            </div>

                        </form>

                    </div>

                </div>
      
            </div>

            <script>
                // ONCLOSE
                $('#myProfileForm').on('hidden.bs.modal', function () {
                    //clearForm("regForm");
                });

                // BEFORE ON SHOW
                $('#myProfileForm').on('show.bs.modal', function (e) {
                    initFields("profileCountryID");
                    initProfileEditor(<?php echo $_SESSION['userid']; ?>);
                })

            </script>

        </div> 







        <div class="container" id="container">
            
            <div id="subcontainer">

                <div id="homeMotos" class="carousel slide" data-ride="carousel">

                    <ol class="carousel-indicators">
                        <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                        <li data-target="#myCarousel" data-slide-to="1"></li>
                        <li data-target="#myCarousel" data-slide-to="2"></li>
                        <li data-target="#myCarousel" data-slide-to="3"></li>
                        <li data-target="#myCarousel" data-slide-to="4"></li>
                        <li data-target="#myCarousel" data-slide-to="5"></li>
                        <li data-target="#myCarousel" data-slide-to="6"></li>
                    </ol>

                    <div class="carousel-inner" role="listbox">
                

                        <div class="item active">
                            <img src="motoimg/1776335-3000x1794-desktop-hd-kawasaki-1400gtr-wallpaper.jpg" alt="Kawasaki" width="460" height="345">
                            <div class="carousel-caption">
                                <h3>Kawasaki</h3>
                                <p>Kawasaki 1400GTR 2011</p>
                            </div>
                        </div>

                        <div class="item">
                            <img src="motoimg/bmw-s-1000-rr-sports-bikes-2023-5k-3840x2160-8696.jpg" alt="BMW S1000" width="460" height="345">
                            <div class="carousel-caption">
                                <h3>BMW</h3>
                                <p>BMW S1000 RR Sports bikes 2023</p>
                            </div>
                        </div>

                        <div class="item">
                            <img src="motoimg/harley-davidson-sportster-s-2021-3840x2160-6159.jpg" alt="Harley-Davidson" width="460" height="345">
                            <div class="carousel-caption">
                                <h3>Harley Davidson</h3>
                                <p>Harley Davidson Sportster S 2021</p>
                            </div>
                        </div>
        
                        <div class="item">
                            <img src="motoimg/honda-cb750-hornet-3840x2160-25085.jpg" alt="Honda" width="460" height="345">
                            <div class="carousel-caption">
                                <h3>Honda</h3>
                                <p>Honda CB750 Hornet</p>
                            </div>
                        </div>

                        <div class="item">
                            <img src="motoimg/kawasaki-ninja-zx-3840x2160-10204.jpg" alt="Kawasaki" width="460" height="345">
                            <div class="carousel-caption">
                                <h3>Kawasaki</h3>
                                <p>Kawasaki Ninja ZX</p>
                            </div>
                        </div>

                        <div class="item">
                            <img src="motoimg/kawasaki-z-h2-se-2021-sports-bikes-racing-bikes-race-track-3840x2160-3429.jpg" alt="Kawasaki" width="460" height="345">
                            <div class="carousel-caption">
                                <h3>Kawasaki</h3>
                                <p>Kawasaki Z H2 SE 2021</p>
                            </div>
                        </div>
                    
                        <div class="item">
                            <img src="motoimg/yamaha-yzf-r7-sports-bikes-5k-2022-3840x2160-5730.jpg" alt="Yamaha" width="460" height="345">
                            <div class="carousel-caption">
                                <h3>Yamaha</h3>
                                <p>Yamaha YZF R7 Sports bikes 2022</p>
                            </div>
                        </div>
    
                    </div>

                    <!-- Left and right controls -->
                    <a class="left carousel-control" href="#homeMotos" role="button" data-slide="prev">
                        <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="right carousel-control" href="#homeMotos" role="button" data-slide="next">
                        <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                </div>

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
                <p>&copy; 2026 Company, Inc. All rights reserved.</p>
                <a href="https://facebook.com" target="new"><img class="footer-icon" src="imgs/facebook.png" title="FaceBook"></a>
                <a href="https://instagram.com" target="new"><img class="footer-icon" src="imgs/instagram.png" title="Instagram"></a>
                <a href="https://x.com" target="new"><img class="footer-icon" src="imgs/twitter.png" title="Twitter"></a>
            </div>
        </div>










    </body>

    <script>
        window.onload = (event) => {
            startItem("<?php echo $activePage;?>");
        };
    </script>

</html>
