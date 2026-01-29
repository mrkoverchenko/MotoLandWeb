<?php
	session_start();
	
//	if(isset($_SESSION['usernickname'])){
	unset($_SESSION['userid']);  
	unset($_SESSION['usernickname']);
	unset($_SESSION['lastusing']);
	$_SESSION = array();
	session_unset();
	session_destroy();         

	/*if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
			$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]);
	}*/

	
	header("Location: index.php?logged=out");
	exit;
?>