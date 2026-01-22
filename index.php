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
                        <li><a href="#" title="Regisztráció" ><span class="glyphicon glyphicon-user"></span></a></li>


                        <li><a href="#loginForm" data-toggle="modal" title="Bejelentkezés"><span class="glyphicon glyphicon-log-in"></span></a></li>



                        <li><a href="#" title="Bevásárlókosár"><span class="glyphicon glyphicon-shopping-cart"></span>   </a></li>
                    </ul>

                </div>
            </div>
        </nav>

  
        <div class="modal fade" id="loginForm" role="dialog">
            <div class="modal-dialog">
    
                <!-- Modal content-->
                <div class="modal-content">

                    <div class="modal-header" style="padding:35px 50px;">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4><span class="glyphicon glyphicon-lock"></span> Bejelentkezés</h4>
                    </div>

                    <div class="modal-body" style="padding:40px 50px;">

                        <form role="form" action="<?php .$_SERVER["PHP_SELF"]. ?>" method="post" onsubmit="return fv()" >

                            <div class="form-group">
                                <label for="usrname"><span class="glyphicon glyphicon-user"></span> Felhasználónév</label>
                                <input type="text" class="form-control" id="usrname" placeholder="EMail">
                            </div>

                            <div class="form-group">
                                <label for="psw"><span class="glyphicon glyphicon-eye-open"></span> Jelszó</label>
                                <input type="text" class="form-control" id="psw" placeholder="Jelszó">
                            </div>

                            <div class="checkbox">
                                <label><input type="checkbox" value="" checked>Remember me</label>
                            </div>

                            <button type="submit" class="btn btn-success btn-block"><span class="glyphicon glyphicon-off"></span> Login</button>
                        </form>

                    </div>

                    <div class="modal-footer">
                        <p>Not a member? <a href="#">Sign Up</a></p>
                        <p>Forgot <a href="#">Password?</a></p>
                    </div>

                </div>
      
            </div>
        </div> 



        <div class="container" style="height:1000px">

        </div>
    

    </body>

</html>
