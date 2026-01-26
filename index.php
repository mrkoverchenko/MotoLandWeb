<?php 
    if (!isset($_SESSION)) {
        session_start(); 
    }
    include "connect.php";
  
    $ok = 0;
    $isUser = false;



    // LOGIN
	if (isset($_POST["userName"]) && 
            isset($_POST["password"]) && 
                $_POST["formName"] == "loginForm") {
        
    
		function validate($data){
			$data = trim($data);
			$data = stripslashes($data);
			$data = htmlspecialchars($data); 
			return $data;
		}
        function generateSalt($length = 32) {
            return bin2hex(random_bytes($length));
        }
        function createPasswordHash($password, $salt) {
            return hash('sha256', $salt . $password);
        }
        function verifyPassword($inputPassword, $storedSalt, $storedHash) {
            return hash('sha256', $storedSalt . $inputPassword) === $storedHash;
        }
		 
		$uname = validate($_POST['userName']);          //User Mail address
		$passwordFromInput = validate($_POST['password']);
		$verifyuname = strtolower($uname); 
		//$verifypass = hash('sha512', $pass);
		 
			 
        $sql = "SELECT 
                    UserID_MSTR,
                    UserMail_MSTR,
                    UserNickName_MSTR,
                    UserTypeID_MSTR,
                    UserFlagID_MSTR,
                    PasswordSalt_MSTR,
                    PasswordPassword_MSTR
                FROM 
                    user_mstr, password_mstr
                WHERE 
                    UserMail_MSTR = '$uname' AND 
                    UserID_MSTR = PasswordUserID_MSTR AND 
                    UserTypeID_MSTR <> '6'";

        $result = mysqli_query($connect, $sql);
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
                
            $_SESSION['usernickname'] = $row['UserNickName_MSTR'];
            $_SESSION['userid'] = $row['UserID_MSTR'];
            $_SESSION['lastusing'] = time();
            
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

	
    }





	if (isset($_POST["userName"]) && 
            isset($_POST["password"]) && 
                isset($_POST["formName"]) && 
                    $_POST["formName"] == "regForm"  ) {

		$ok = 1;
        
        $_POST = array();
        unset($_POST);

		function validate($data){
			$data = trim($data);
			$data = stripslashes($data);
			$data = htmlspecialchars($data); 
			return $data;
		}
		 
		$uname = validate($_POST['username']);
		$pass = validate($_POST['password']);
		$verifyuname = strtolower($uname); 
		$verifypass = hash('sha512', $pass);
		 
			 
        $sql = "SELECT 
                    UserID_MSTR,
                    UserMail_MSTR,
                    UserNickName_MSTR,
                    UserTypeID_MSTR,
                    UserFlagID_MSTR,
                    PasswordSalt_MSTR,
                    PasswordPassword_MSTR
                FROM 
                    user_mstr, password_mstr
                WHERE 
                    UserMail_MSTR= @usermail AND 
                    UserID_MSTR = PasswordUserID_MSTR AND 
                    UserTypeID_MSTR <> '6'";

        $result = mysqli_query($connect, $sql);
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
                
            $_SESSION['usernickname'] = $row['UserNickName_MSTR'];
            $_SESSION['userid'] = $row['UserID_MSTR'];
            $_SESSION['lastusing'] = time();
            
            $saltFromDb = $row["PasswordSalt_MSTR"];
            $passwordFromDb = $row["PasswordPassword_MSTR"];
            $hashedPasswordFromDb = $verifypass;

		}
		
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

        <style>
            .carousel-inner > .item > img,
            .carousel-inner > .item > a > img {
                width: 100%;
                margin: auto;
            }   
        </style>


    </head>

    <body>

        <!-- NAVIGATION -->
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container">.  

                <div class="navbar-header">
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                        <span class="sr-only">Toggle navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <a class="navbar-brand" href="#">MotoLand</a>
                </div>


                <div id="navbar" class="navbar-collapse collapse"> 
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="#">Kezdőlap</a></li>
                        <li><a href="#">  
                            <?php
                                if ($ok== 1)
                                    echo "Regisztrácio";
                                else 
                                    echo "Eladás"; 
                            ?>
                        </a></li>
                        <li><a href="#">Kapcsolat</a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Szolgáltatások<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#">Szervíz időpontfoglalás</a></li>
                                <li><a href="#">Alkatrészrendelés</a></li>
                                <li role="separator" class="divider"></li>
                                <li class="dropdown-header">Akció</li>
                                <li><a href="#">Törött motorok</a></li>
                            </ul>
                        </li>
                    </ul>


                    <ul class="nav navbar-nav navbar-right">
                        <li>
                            <a href="#registrationForm" data-toggle="modal" title="Regisztráció" >
                                <span class="glyphicon glyphicon-lock"></span>
                            </a>
                        </li>


                        <li>
                            <a href="#loginForm" data-toggle="modal" title="Bejelentkezés">
                                
                                <span 
                                
                                    <?php
                                        if ($isUser)
                                            echo "class='glyphicon glyphicon-user'";
                                        else
                                            echo "class='glyphicon glyphicon-log-in'";
                                    ?> >
                                </span>
                            </a>
                        </li>



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
                                        id="userName" 
                                        name="userName" 
                                        placeholder="e-mail">
                            </div>

                            <div class="form-group">
                                <label for="password"><span class="glyphicon glyphicon-eye-open"></span> Jelszó</label>
                                <input type="password" class="form-control" required id="password" name="password" placeholder="jelszó">
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
                                            style="width:300px">
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
                                <label for="fName" class="col-sm-4 col-form-label" style="margin-top:5px"> Vezetéknév *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="fName" 
                                            placeholder="vezetéknév"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="mName" class="col-sm-4 col-form-label" style="margin-top:5px" > Keresztnév *</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            required
                                            class="form-control-plaintext" 
                                            id="mName" 
                                            placeholder="keresztnév"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="lName" class="col-sm-4 col-form-label" style="margin-top:5px"> Keresztnév</label>
                                <div class="col-sm-6">
                                    <input type="text" 
                                            alt="noChecking"
                                            class="form-control-plaintext" 
                                            id="lName" 
                                            placeholder="keresztnév"
                                            style="width:300px">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label for="country" class="col-sm-4 col-form-label" style="margin-top:5px"> Ország *</label>
                                <div class="col-sm-6">
                                    <select class="form-select form-select-sm" id="country" aria-label=".form-select-sm example">
                                        <option selected>Magyarország</option>
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
                                A *-al jelszett mezők kitöltése kötelező!
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



        </div>
    

    </body>

</html>
