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
                        <li class="active"><a href="#">Kezdõlap</a></li>
                        <li><a href="#">Eladás</a></li>
                        <li><a href="#">Kapcsolat</a></li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Szolgáltatások<span class="caret"></span></a>
                            <ul class="dropdown-menu">
                                <li><a href="#">Szervíz idõpontfoglalás</a></li>
                                <li><a href="#">Alkatrészrendelés</a></li>
                                <li role="separator" class="divider"></li>
                                <li class="dropdown-header">Akció</li>
                                <li><a href="#">Törött motorok</a></li>
                            </ul>
                        </li>
                    </ul>


                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="#" title="Regisztráció" ><span class="glyphicon glyphicon-user"></span></a></li>
                        <li><a href="#" title="Bejelentkezés"><span class="glyphicon glyphicon-log-in"></span></a></li>
                        <li><a href="#" title="Bevásárlókosár"><span class="glyphicon glyphicon-shopping-cart"></span>   </a></li>
                    </ul>

                </div>
            </div>
        </nav>

  
        <div class="container" style="height:1000px">
            echo '<form action="'.$_SERVER["PHP_SELF"].'" method="post" onsubmit="return fv()" name="loginform">

                <div class="login-head"><span>lv426</span></div>

                <div class="login-field">
                    <div class="login-field-img login-field-img-user"></div>	
                    <input type="text" name="username" placeholder="Felhasználónév" autofocus>
                </div>

                <div class="login-field">
                    <div class="login-field-img login-field-img-pw"></div>	
                    <input type="password" name="password" placeholder="Jelszó" autofocus>
                </div>
                <div class="login-submit">
                    <input type="submit" class="button" value="Bejelentkezés">
                </div>
                <div class="login-as-guest">
                <!--<a href="#">Bejelentkezés vendégként</a>-->
                </div>
            </form>';

    </div>
    

    </body>

</html>
