<?php 
    session_start(); 

    include "connect.php";

    if (!isset($_SESSION['userid'])) {
//        header("Location: http://lv426.giize.com");
    }
    
    $systemIsMessage = true;
    $systemMessage = "<b>Sikeres regisztráció!</b></br>".
                        "Kérlek <a href='#loginForm' data-toggle='modal' title='Bejelentkezés'>jelentkezz be</a> a felhasználóneveddel és jelszavaddal.";
    /// REGISTRATION IS COMPLET MESSAGE
    if (isset($_GET["reg"]) && $_GET["reg"] === "ok") {
        $systemIsMessage = true;
        $systemMessage = "<b>Sikeres regisztráció!</b></br>".
                            "Kérlek <a href='#loginForm' data-toggle='modal' title='Bejelentkezés'>jelentkezz be</a> a felhasználóneveddel és jelszavaddal.";
    }



    $ok = 0;
    
    $isUser = false;
    
    
    if (isset($_GET["logged"]) && $_GET["logged"] == "out") {
        $_POST["userName"] = "";
        $_POST["password"] = "";
        $_POST["formName"] = "";
        $_POST = array();
        unset($_POST);
        $isUser = false;
    }
    


    ////////////////////////////////////////////////////////
    // LOGIN
    ////////////////////////////////////////////////////////
	if (isset($_POST["userName"]) && 
    isset($_POST["password"]) && 
    $_POST["formName"] == "loginForm") {
        
        $isUser = false;

		function validate($data){
			$data = trim($data);
			$data = stripslashes($data);
			$data = htmlspecialchars($data); 
			return $data;
		}
        function createPasswordHash($password, $salt) {
            return hash('sha256', $salt . $password);
        }
        function verifyPassword($inputPassword, $storedSalt, $storedHash) {
            return hash('sha256', $storedSalt . $inputPassword) === $storedHash;
        }
		 
		$uname = strtolower(validate($_POST['userName']));          //User Mail address
		$passwordFromInput = validate($_POST['password']);
		//$verifyuname = strtolower($uname); 
		//$verifypass = hash('sha512', $pass);
		 
			 
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

        $result = mysqli_query($connect, $sql);
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
                
            $_SESSION['usernickname'] = $row['UserNickName_MSTR'];
            $_SESSION['userid'] = $row['UserID_MSTR'];
            $_SESSION['lastusing'] = time();
            $userFullName = $row['UserFullName'];
            
            $saltFromDb = $row["PasswordSalt_MSTR"];
            $passwordFromDb = $row["PasswordPassword_MSTR"];
            //$hashedPasswordFromDb = $verifypass;

            //$salt = generateSalt(); 
            $hash = createPasswordHash($passwordFromDb, $saltFromDb);

            if (verifyPassword($passwordFromInput, $saltFromDb, $hash)) {
                $isUser = true;
            } else {
                $isUser = false;
            }

            $_POST = array();
            unset($_POST);


            $isUser = true;
		}
        mysqli_close($connect);

    }




    ////////////////////////////////////////////////////////
    // REGISTRATION
    ////////////////////////////////////////////////////////
	if (isset($_POST["userName"]) && 
            isset($_POST["password"]) && 
                isset($_POST["firstName"]) && 
                    isset($_POST["middleName"]) && 
                        isset($_POST["lastName"]) && 
                            isset($_POST["countryID"]) && 
                                isset($_POST["postcode"]) && 
                                    isset($_POST["city"]) && 
                                        isset($_POST["street"]) && 
                                            isset($_POST["address"]) && 
                                                isset($_POST["phone"]) && 
                                                    isset($_POST["email"]) && 
                                                        isset($_POST["formName"]) && 
                                                            $_POST["formName"] == "regForm"  ) {

	    $username = $_POST["userName"];
        $password = $_POST["password"];
        $firstname = $_POST["firstName"];
        $middlename = $_POST["middleName"];
        $lastname = $_POST["lastName"];
        $countryid = $_POST["countryID"];
        $postcode = $_POST["postcode"];
        $city = $_POST["city"];
        $street = $_POST["street"];
        $address = $_POST["address"];
        $phone = $_POST["phone"];
        $email = $_POST["email"];

        $_POST = array();
        unset($_POST);

        function generateSalt($length = 32) {
            return bin2hex(random_bytes($length));
        }
        function createPasswordHash($password, $salt) {
            return hash('sha256', $salt . $password);
        }

		function validate($data){
			$data = trim($data);
			$data = stripslashes($data);
			$data = htmlspecialchars($data); 
			return $data;
		}
		 
		$uname = validate($username);
		$pass = validate($password);
		//$verifyuname = strtolower($uname); 
		//$verifypass = hash('sha512', $pass);

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



        $salt = generateSalt();
        $passHASH = createPasswordHash($pass, $salt);
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



			 
        /*$sql = "SELECT * FROM user_mstr WHERE UserNickName_MSTR = '$email'";
        $result = mysqli_query($connect, $sql);
        if (mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $row['UserNickName_MSTR'];
		}*/


        mysqli_close($connect);

        header("Location: index.php?reg=ok");
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

        <link rel="canonical" href="https://getbootstrap.com/docs/5.1/examples/footers/">

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

        </style>


    </head>

    <body>

        <!-- NAVIGATION -->
        <nav class="navbar navbar-inverse navbar-fixed-top">

            <div class="container">

                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <a class="navbar-brand" href="#">
                        MotoLand
                    </a>

                </div>


                <div id="navbar" class="navbar-collapse collapse"> 

                    <ul class="nav navbar-nav">
                        <li class="active"><a href="#">Kezdőlap</a></li>
                        <li><a href="#">Értékesítés</a></li>
                        <li><a href="#">Kapcsolat</a></li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Szolgáltatások<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#"><span class="glyphicon glyphicon-calendar" style="margin-right:20px;"></span>Szervíz időpontfoglalás</a></li>
                                <li><a href="#"><span class="glyphicon glyphicon-euro" style="margin-right:20px;"></span>Alkatrészrendelés</a></li>
                                <li role="separator" class="divider"></li>
                                <li class="dropdown-header">Akció</li>
                                <li><a href="#"><span class="glyphicon glyphicon-cog" style="margin-right:20px;"></span>Törött motorok</a></li>
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
                                            <li class='dropdown-header'>
                                                $userFullName
                                                <img src='imgs/user.png' style='width:40px; margin-left: 30px;'>
                                            </li>
                                            <li role='separator' class='divider'></li>
                                            <li><a href='#'><span class='glyphicon glyphicon-calendar' style='margin-right:20px;'></span>Időpontfoglalás</a></li>
                                            <li><a href='#'><span class='glyphicon glyphicon-euro' style='margin-right:20px;'></span>Rendelések</a></li>
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



                        <li><a href="#" title="Bevásárlókosár"><span class="glyphicon glyphicon-shopping-cart"></span>   </a></li>
                    </ul>

                </div>
            </div>
        </nav>








        <!-- LOGIN -->
        <div class="modal fade" data-backdrop="static" data-keyboard="false" id="loginForm" role="dialog">
            <div class="modal-dialog">
    
                <div class="modal-content">

                    <div class="modal-header" style="padding:5px 50px;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4><span class="glyphicon glyphicon-user"></span> Bejelentkezés</h4>
                    </div>

                    <div class="modal-body" style="padding:40px 50px;">

                        <form id="loginForm" action="index.php" method="POST">

                            <div class="form-group">
                                <input type="hidden" name="formName" value="loginForm">

                                <label for="userName"><span class="glyphicon glyphicon-user"></span> Felhasználónév</label>
                                <input type="text" 
                                        class="form-control" 
                                        required 
                                        value="istvan.lovei@yahoo.com"
                                        id="userName" 
                                        name="userName" 
                                        placeholder="e-mail">
                            </div>

                            <div class="form-group">
                                <label for="password"><span class="glyphicon glyphicon-eye-open"></span> Jelszó</label>
                                <input type="password" class="form-control" value="katymaty" required id="password" name="password" placeholder="jelszó">
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

                </div>
            </div>
        </div> 



        <!-- REGISTRATION -->
        <div class="modal fade" data-backdrop="static" data-keyboard="false" id="registrationForm" role="dialog">
            <div class="modal-dialog">
    
                <div class="modal-content">

                    <div class="modal-header" style="padding:5px 50px;">
                        <button type="button" class="close" data-dismiss="modal" style="margin-top:3px">&times;</button>
                        <h4><span class="glyphicon glyphicon-lock"></span> Regisztráció</h4>
                    </div>

                    <div class="modal-body" style="padding:10px 50px;">



                        <form id="regForm" 
                                action="index.php"
                                method="POST" 
                                onsubmit="return checkForms(this)" >

                            <div class="form-group row">
                                <label for="userName" class="col-sm-4 col-form-label" style="margin-top:5px">
                                    <span class="glyphicon glyphicon-user"></span> Felhasználónév *
                                </label>
                                <div class="col-sm-6">

                                    <input type="hidden" name="formName" value="regForm">

                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="userName" 
                                            name="userName" 
                                            placeholder="email cím vagy felhasználónév"  
                                            style="width:300px"
                                            onfocusout="checkUserName(this)">
                                </div>
                            </div>


                            <div class="form-group row">
                                <label for="password" class="col-sm-4 col-form-label" style="margin-top:5px">
                                    <span class="glyphicon glyphicon-eye-open"></span> Jelszó *
                                </label>

                                <div class="col-sm-6">
                                    <input type="password" 
                                            required
                                            class="form-control-plaintext" 
                                            id="password" 
                                            name="password" 
                                            placeholder="jelszó"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="password1" class="col-sm-4 col-form-label" style="margin-top:5px">
                                    <span class="glyphicon glyphicon-eye-open"></span> Ellenőrzés *
                                </label>

                                <div class="col-sm-6">
                                    <input type="password" 
                                            required
                                            class="form-control-plaintext" 
                                            id="password1" 
                                            placeholder="jelszó ellenőrzés"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="firstName" class="col-sm-4 col-form-label" style="margin-top:5px"> Vezetéknév *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="firstName" 
                                            name="firstName" 
                                            placeholder="vezetéknév"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="middleName" class="col-sm-4 col-form-label" style="margin-top:5px" > Keresztnév *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="middleName" 
                                            name="middleName" 
                                            placeholder="keresztnév"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="lastName" class="col-sm-4 col-form-label" style="margin-top:5px"> Keresztnév</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            alt="noChecking"
                                            class="form-control-plaintext" 
                                            id="lastName" 
                                            name="lastName" 
                                            placeholder="keresztnév"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="country" class="col-sm-4 col-form-label" style="margin-top:5px"> Ország *</label>
                                <div class="col-sm-6">
                                    <select class="form-select form-select-sm" id="country" name="countryID" aria-label=".form-select-sm example">
                                        <option value="0" selected>Magyarország</option>
                                        <option value="1">Lengyelország</option>
                                        <option value="2">Ausztria</option>
                                        <option value="3">Anglia</option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="postcode" class="col-sm-4 col-form-label"> Irányítószám *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="postcode" 
                                            name="postcode" 
                                            placeholder="irányítószám"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="city" class="col-sm-4 col-form-label"> Város *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="city" 
                                            name="city" 
                                            placeholder="város"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="street" class="col-sm-4 col-form-label"> Utca *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="street" 
                                            name="street" 
                                            placeholder="út/utca/tér ...stb"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="address" class="col-sm-4 col-form-label"> Házszám/emelet *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="address" 
                                            name="address" 
                                            placeholder="házszám/emelet/ajtó...stb"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="phone" class="col-sm-4 col-form-label"> Telefonszám *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="phone" 
                                            name="phone" 
                                            placeholder="telefonszám"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="email" class="col-sm-4 col-form-label"> E-mail cím *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="email" 
                                            name="email" 
                                            placeholder="e-mail cím"
                                            style="width:300px">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success">
                                <span class="glyphicon glyphicon-ok"></span> Regisztráció
                            </button>

                            <button type="reset" class="btn btn-primary" data-dismiss="modal" >
                                <span class="glyphicon glyphicon-remove"></span> Mégsem
                            </button>

                            <div class="modal-footer" style="text-align:left ">
                                <span id="message">A *-al jelszett mezők kitöltése kötelező!</span>
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
                })

            </script>




        </div> 





        <div class="container">


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

            <!-- SYSTEM MESSAGE -->
            <?php
                if ($systemIsMessage) {
                    $systemIsMessage = false;
                    echo "<div class='alert alert-success alert-dismissible' style='position:absolute; top:100px; width:500px; id=/'messagediv/''>
                              <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                              $systemMessage
                          </div>
                          <script>
                            let nt = setInterval(clrInterval, 5000);
                            function clrInterval() {
                                clearInterval(nt);
                                let parent = document.getElementById('messagediv').parent;
                                alert(parent);
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

</html>
