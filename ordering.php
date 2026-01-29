<?php
	session_start();
	if (isset($_SESSION['userid']) && isset($_SESSION['usernickname'])) {

    } else {
        header("Location: index.php?logged=out");
        exit;
    }



	
?>