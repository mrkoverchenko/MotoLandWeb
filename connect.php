<?php
    if (!isset($_SESSION)) {
		session_start();
	}
	$connect = mysqli_connect("localhost","root","","motoland");
	if (mysqli_connect_errno()) {
		echo "<div style='background-color:red;color:yellow;'>Connection error: " . mysqli_connect_error() . "</div";
		mysqli_close($connect);  
		exit();
	}
?>