<?php
	session_start();
/*	if (isset($_SESSION['userid']) && isset($_SESSION['usernickname'])) {

    } else {
        header("Location: index.php?logged=out");
        exit;
    }*/

?>
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


