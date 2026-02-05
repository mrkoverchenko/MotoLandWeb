<?php 
    session_start(); 

    include "connect.php";

    if (isset($_SESSION['userid'])) {
        $isUser = true;



    }
    


	if (isset
        
    $_POST["formName"] == "orderingForm") {
        
		function validate($data){
			$data = trim($data);
			$data = stripslashes($data);
			$data = htmlspecialchars($data); 
			return $data;
		}
		 
		$uname = strtolower(validate($_POST['loginUserName']));          //User Mail address or Nick name
		$passwordFromInput = validate($_POST['loginPassword']);
			 
        $sql = "SELECT 
                    UserID_MSTR,
                    UserMail_MSTR,
                    UserNickName_MSTR,
                    UserTypeID_MSTR,
                    UserFlagID_MSTR,
                    PasswordSalt_MSTR,
                    PasswordPassword_MSTR,
                    CONCAT(UserFirstName_DET,' ',UserMiddleName_DET,' ',UserLastName_DET) AS UserFullName

                FROM 
                    user_mstr, user_det, password_mstr
                WHERE 
                    LOWER(UserNickName_MSTR) = '$uname' AND 
                    UserMSTRID_DET = UserID_MSTR AND 
                    UserID_MSTR = PasswordUserID_MSTR AND 
                    UserTypeID_MSTR <> '6'";

        $isUser = false;
        $result = mysqli_query($connect, $sql);
        if (mysqli_num_rows($result) === 1) {
            $row = mysqli_fetch_assoc($result);
            
            $saltFromDb = $row["PasswordSalt_MSTR"];
            $passwordFromDb = $row["PasswordPassword_MSTR"];

            if (hash('sha256', $saltFromDb.$passwordFromInput) === $passwordFromDb) {
                $isUser = true;

                $_SESSION['usernickname'] = $row['UserNickName_MSTR'];
                $_SESSION['userid'] = $row['UserID_MSTR'];
                $_SESSION['userfullname'] = $row['UserFullName'];
                $_SESSION['lastusing'] = time();

            } else {
                $isUser = false;
            }
            $_POST = array();
            unset($_POST);
		}
        mysqli_close($connect);



        $hideTime = 10000;
        $systemIsMessage = true;
        if ($isUser) {
            $alertType = "alert-dismissible";
            $systemMessage = "<b>Sikeres bejelentkezés!</b>";
        } else {
            $alertType = "alert-danger";
            $systemMessage = "<b>Sikertelen bejelentkezés!</b";
        }

    }





?>





