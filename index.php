<?php 
    if (!isset($_SESSION)) {
        session_start(); 
    }
    include "connect.php";
  

    if (isset($_SESSION["usernickname"]) && isset($_SESSION["userid"])) { 
		loadMenu($mmac);
	} else if (isset($_POST['username']) && isset($_POST['password'])) {
			 
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
		 
		 if (empty($uname)) {
			  header("Location: index.php?error=unir");
		 } else if(empty($pass)) {
			  header("Location: index.php?error=pir");
		 } else {
			 
			 
			 
			 //echo $verify;
			  $sql = "SELECT 
							* 
						FROM 
							User_MSTR, 
							Passwords_MSTR 
						WHERE 
							LOWER(UserNickName_MSTR) = '$verifyuname' AND	
							PasswordsPassword_MSTR = '$verifypass' AND
							PasswordsUserID_MSTR = UserID_MSTR";
			  
			  $result = mysqli_query($connect, $sql);
			  if (mysqli_num_rows($result) === 1) {
					$row = mysqli_fetch_assoc($result);
						
					$_SESSION['usernickname'] = $row['UserNickName_MSTR'];
					$_SESSION['userid'] = $row['UserID_MSTR'];
					$_SESSION['lastusing'] = time();
					//$_SESSION['parentpage'] = "timeline.php";

					user_log("member¤SuccessfulLogin¤$_POST[username]");
					loadMenu($mmac);
			  } else {
                    $wrongLoginCount = (checkLog($_POST["username"]) + 1);
					user_log("member¤UnsuccessfulLogin¤".$wrongLoginCount."¤".$_POST["username"]."¤".$_POST["password"]);
                    
					header("Location: index.php?error=nopass&cnt=".$wrongLoginCount);
					exit();
			  }

		}
		
	} else {
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

        <style>
            .carousel-inner > .item > img,
            .carousel-inner > .item > a > img {
                width: 100%;
                margin: auto;
            }   
        </style>

    </head>

    <body>
        <nav class="navbar navbar-default navbar-fixed-top">
            <div class="container">

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
                        <li><a href="#">Eladás</a></li>
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
                        <li><a href="#registrationForm" data-toggle="modal" title="Regisztráció" ><span class="glyphicon glyphicon-user"></span></a></li>


                        <li><a href="#loginForm" data-toggle="modal" title="Bejelentkezés"><span class="glyphicon glyphicon-log-in"></span></a></li>



                        <li><a href="#" title="Bevásárlókosár"><span class="glyphicon glyphicon-shopping-cart"></span>   </a></li>
                    </ul>

                </div>
            </div>
        </nav>




        <!-- LOGIN -->
        <div class="modal fade" id="loginForm" role="dialog">
            <div class="modal-dialog">
    
                <div class="modal-content">

                    <div class="modal-header" style="padding:35px 50px;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4><span class="glyphicon glyphicon-lock"></span> Bejelentkezés</h4>
                    </div>

                    <div class="modal-body" style="padding:40px 50px;">

                        <form role="form" action="<?php $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return fv()" >

                            <div class="form-group">
                                <label for="usrname"><span class="glyphicon glyphicon-user"></span> Felhasználónév</label>
                                <input type="text" class="form-control" id="usrname" placeholder="email">
                            </div>

                            <div class="form-group">
                                <label for="psw"><span class="glyphicon glyphicon-eye-open"></span> Jelszó</label>
                                <input type="password" class="form-control" id="psw" placeholder="jelszó">
                            </div>

                            <div class="checkbox">
                                <label><input type="checkbox" value="" checked>Remember me</label>
                            </div>

                            <button type="submit" class="btn btn-success "><span class="glyphicon glyphicon-off"></span> Bejelentkezés</button>

                        </form>

                    </div>

                    <div class="modal-footer">
                        <p>Nem vagy még tag? <a href="#registrationForm" class="close" data-dismiss="modal" data-toggle="modal" style="font-size:14px; color: black; margin:3px 10px;"> Regisztráció</a></p>
                    </div>

                </div>
      
            </div>
        </div> 





        <!-- REGISTRATION -->
        <div class="modal fade" id="registrationForm" role="dialog">
            <div class="modal-dialog">
    
                <div class="modal-content">

                    <div class="modal-header" style="">

                        <button type="button" class="close" data-dismiss="modal" style="margin-top:3px">&times;</button>

                        <h4><span class="glyphicon glyphicon-lock"></span> Regisztráció</h4>
                    </div>

                    <div class="modal-body" style="padding:10px 50px;">

                        <form role="form" action="<?php $_SERVER['PHP_SELF'] ?>" method="post" onsubmit="return fv()" >

                            <div class="form-group">
                                <label for="username"><span class="glyphicon glyphicon-user"></span> Felhasználónév *</label>
                                <input class="form-control form-control-sm" type="text" id="username" placeholder="emaild vagy felhasználónév">
                            </div>

                            <div class="form-group">
                                <label for="psw"><span class="glyphicon glyphicon-eye-open"></span> Jelszó *</label>
                                <input type="password" class="form-control form-control-sm" id="psw" placeholder="jelszó">
                                <input type="password" class="form-control form-control-sm" id="psw1" placeholder="jelszó ismét" style="margin-top:5px">
                            </div>

                            <div class="form-group">
                                <label for="fname"><span class=""></span> Teljes név *</label>
                                <input type="text" class="form-control form-control-sm" id="fname" placeholder="vezetéknév">
                                <input type="text" class="form-control form-control-sm" id="mname" placeholder="keresztnév" style="margin-top:5px">
                                <input type="text" class="form-control form-control-sm" id="lname" placeholder="keresztnév" style="margin-top:5px">
                            </div>

                            <div class="form-group">
                                <label for="country"><span class=""></span> Cím *</label>
                                <select class="form-select form-select-sm" id="country" aria-label=".form-select-sm example">
                                    <option selected>Magyarország</option>
                                    <option value="1">Lengyelország</option>
                                    <option value="2">Ausztria</option>
                                    <option value="3">Anglia</option>
                                </select>

                                <input type="text" class="form-control form-control-sm" id="postcode" placeholder="irányítószám" style="margin-top:5px">
                                <input type="text" class="form-control form-control-sm" id="city" placeholder="város" style="margin-top:5px">
                                <input type="text" class="form-control form-control-sm" id="street" placeholder="utca" style="margin-top:5px">
                                <input type="text" class="form-control form-control-sm" id="address" placeholder="házszám/emelet/ajtó" style="margin-top:5px">
                            </div>


                            <div class="form-group">
                                <label for="tel"><span class=""></span> Kapcsolat *</label>
                                <input type="text" class="form-control form-control-sm" id="tel" placeholder="telefon">
                                <input type="email" class="form-control form-control-sm" id="email" placeholder="e-mail cím" style="margin-top:5px">
                            </div>




                            <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-ok"></span> Regisztráció</button>
                            <button type="button" class="btn btn-primary" class="close" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span> Mégsem</button>


                            <div class="modal-footer" style="height: 10px">
                                <p>Az összes mező kitöltése kötelező</p>
                            </div>


                        </form>

                    </div>

                </div>
      
            </div>
        </div> 






        <div class="container">

            <div id="myCarousel" class="carousel slide" data-ride="carousel">

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
            <a class="left carousel-control" href="#myCarousel" role="button" data-slide="prev">
                <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="right carousel-control" href="#myCarousel" role="button" data-slide="next">
                <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
            </div>



        </div>
    

    </body>

</html>
