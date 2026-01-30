<?php
	session_start();
/*	if (isset($_SESSION['userid']) && isset($_SESSION['usernickname'])) {

    } else {
        header('Location: index.php?logged=out');
        exit;
    }*/

    echo "<style>
            .cardsetup {
                background-color: lightgreen;
                padding:10px;
                margin: 5px;
                border-radius:5px;
            }
            .cardbody {
                margin-top:55px; 
                color:red;
                display: inline-block;
                width: 100%;
                background-color: gray;
            }
          </style>

            <div class='cardbody'> 

                <div class='card cardsetup' style='width: 18rem;'>
                    <img src='imgs/motorbike.png' class='card-img-top' alt=''>
                    <div class='card-body'>
                        <p class='card-text' style='text-align: center'>
                            Gyári alkatrészek listája
                        </p>
                    </div>
                </div>
                
                <div class='card cardsetup' style='width: 18rem;'>
                    <img src='imgs/motorbike.png' class='card-img-top' alt=''>
                    <div class='card-body'>
                        <p class='card-text' style='text-align: center'>
                            Gyári alkatrészek listája
                        </p>
                    </div>
                </div>    
                

            </div>";

	
?>