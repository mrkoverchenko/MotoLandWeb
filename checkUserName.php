<?php 
	session_start(); 
    include "connect.php";
	
    $found = "";
    if (isset($_POST["username"])) {
        $username = $_POST["username"];

        $querystring = "SELECT UserNickName_MSTR FROM user_mstr WHERE UserNickName_MSTR = '$username'";
        $result = mysqli_query($connect, $querystring);
        if ($result) {
            while ($row = mysqli_fetch_array($result)) {  
                $found = $row["UserNickName_MSTR"];
            }	
        }
    }
	mysqli_close($connect);
	echo $found;
?>		
