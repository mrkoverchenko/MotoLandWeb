<?php

    require_once('PHPMailer/src/PHPMailer.php');
    require_once('PHPMailer/src/Exception.php');
    require_once('PHPMailer/src/SMTP.php');

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    if (!empty($_SESSION["cartid"])) {

            $url = $_SERVER['REQUEST_URI'];
            $orderid = $lastid;
            $reg = $reg;

            // GET MAIL TEXT
            $welcometext = "Üres üdvözlő!";
            $sql = "SELECT MotoSystemMailText_MSTR FROM motosystem_mstr WHERE MotoSystemID_MSTR = '1'";
            $res = mysqli_query($connect, $sql);
            $row = mysqli_fetch_assoc($res);
            $welcometext = $row["MotoSystemMailText_MSTR"];

            if ($reg) { 
                //REGISTRATION TRUE
                $sql = "SELECT 
                            OrdersID_MSTR AS orderid,
                            OrdersDateTime_MSTR AS orderdate,
                            OrdersFullCost_MSTR AS fullcost,
                            OrdersPartsNumber_DET AS partnumber,
                            OrdersPartsName_DET AS partname,
                            OrdersNettoPrice_DET AS netto,
                            OrdersVAT_DET AS vat,
                            OrdersDiscount_DET AS disc,
                            OrdersBruttoPrice_DET AS brutto,
                            OrdersBruttoEURPrice_DET AS bruttoEUR,
                            OrdersQuantity_DET AS qua,
                            OrdersQuantityUnit_DET AS mee,
                            CountriesCountry_MSTR AS country,
                            UserMail_MSTR AS mail,
                            CONCAT(UserFirstName_DET, ' ', UserMiddleName_DET, ' ', UserLastName_DET) AS username,
                            UserPhone_DET AS phone,
                            UserPostCode_DET AS postcode,
                            UserCity_DET AS city,
                            UserStreet_DET AS street,
                            UserAddress_DET AS address
                        FROM 
                            orders_mstr, orders_det, user_mstr, user_det, countries_mstr
                        WHERE 
                            OrdersID_MSTR = '$orderid' AND
                            OrdersMSTRID_DET = '$orderid' AND 
                            OrdersUserID_MSTR = UserID_MSTR AND
                            UserMSTRID_DET = UserID_MSTR AND 
                            UserCountryID_DET = CountriesID_MSTR";
            } else {    
                //REGISTRATION FALSE
                $sql = "SELECT 
                            OrdersID_MSTR AS orderid,
                            OrdersDateTime_MSTR AS orderdate,
                            OrdersFullCost_MSTR AS fullcost,
                            OrdersPartsNumber_DET AS partnumber,
                            OrdersPartsName_DET AS partname,
                            OrdersNettoPrice_DET AS netto,
                            OrdersVAT_DET AS vat,
                            OrdersDiscount_DET AS disc,
                            OrdersBruttoPrice_DET AS brutto,
                            OrdersBruttoEURPrice_DET AS bruttoEUR,
                            OrdersQuantity_DET AS qua,
                            OrdersQuantityUnit_DET AS mee,
                            OrdersUserUserName_MSTR AS username,
                            OrdersUserCountry_MSTR AS country,
                            OrdersUserPostCode_MSTR AS postcode,
                            OrdersUserCity_MSTR AS city,
                            OrdersUserStreet_MSTR AS street,
                            OrdersUserAddress_MSTR AS address,
                            OrdersUserPhone_MSTR AS phone,
                            OrdersUserEmail_MSTR AS mail
                        FROM 
                            orders_mstr, orders_det, ordersuser_MSTR
                        WHERE 
                            OrdersID_MSTR = '$orderid' AND 
                            OrdersMSTRID_DET = '$orderid' AND 
                            OrdersUserOrdersMSTRID_MSTR = '$orderid'";
            }

            $result = mysqli_query($connect, $sql);

            $ic = 0;
            $rowCount = mysqli_num_rows($result);
            while ($row = mysqli_fetch_assoc($result)) {                                    
                $ic++;
                $username = $row["username"];
                $usermail = $row["mail"];
                $orderid = $row["orderid"];
            }
                


            $mailbody = "<div style='margin-top:10px'>
                            <span style='color:gray'>$welcometext</span>
                            <div style='margin-top:20px; width:100%; height:40px; background-color:lightgray; padding:2px 10px; border-radius:3px;'>
                                <h4><strong>Rendelésed részletei</strong></h4>
                            </div>

                            <div style='color: gray;'>
                                <div style='margin-top:20px;'>Rendelési szám: <b><u>#######</u></b></div>

                                <div style='margin-top:0px;'>Rendelés időpontja: <b>2026. 05. 05.</b></div>

                                <div style='margin-top:0px;'>Szállítás: <b>futárovics</b></div>

                                <div style='margin-top:0px;'>Fizetés: <b>sosem fizetek</b></div>

                                <div style='margin-top:20px;'>E-mail cím: <b><a href='mailto:katymaty@katymaty.hu'>katymaty@katymaty.hu</a></b></div>

                                <div style='margin-top:0px;'>Telefon: <b>+36 30 333 53333</b></div>
                            </div>


                            <div style='margin:5px;'>
                                <div class='row brdr'>
                                    <table class='table table-hover' style='color: gray;'>
                                        <thead>
                                            <tr>
                                                <th scope='col'>No.</th>
                                                <th scope='col'>Cikkszám</th>
                                                <th scope='col'>Terméknév</th>
                                                <th scope='col'>Netto</th>
                                                <th scope='col'>Áfa</th>
                                                <th scope='col'>Kedv.</th>
                                                <th scope='col'>Egységár</th>
                                                <th scope='col'>&euro;</th>
                                                <th scope='col'>Menny.</th>
                                                <th scope='col'>Összesen</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div style='float: left; width: 40%; margin : 2px; margin-top: 20px;'>
                                <div style='height:40px; background-color:lightgray; padding:3px 10px; border-radius:3px;'>
                                    <h5><strong>Szállítási adatok</strong></h5>
                                </div>

                                <div style='color: gray; margin-left:30px;'>
                                    <div style='margin-top:20px;'><b>Lövei István</b></div>

                                    <div>3533 Miskolc,</div>
                                    
                                    <div>Nádastó 10. sz.</div>

                                    <div><b><u>Magyarország</u></b></div>
                                </div>
                            </div>

                            <div style='float: left; width: 40%; margin : 2px; margin-top: 20px;'>
                                <div style='height:40px; background-color:lightgray; padding:3px 10px; border-radius:3px;'>
                                    <h5><strong>Számlázás</strong></h5>
                                </div>

                                <div style='color: gray; margin-left:30px;'>
                                    <div style='margin-top:20px;'><b>Lövei István</b></div>

                                    <div>3533 Miskolc,</div>
                                    
                                    <div>Nádastó 10. sz.</div>

                                    <div><b><u>Magyarország</u></b></div>
                                </div>
                            </div>



                            <div style='float: left; width: 100%; margin : 2px; margin-top: 20px;'>
                                <div style='height:40px; background-color:lightgray; padding:3px 10px; border-radius:3px;'>
                                    <h5><strong>Megjegyzés</strong></h5>
                                </div>

                                <div style='color: gray; margin-left:30px;'>
                                    <div style='margin-top:20px;'></div>
                                </div>
                            </div>



                            <div style='float: left; width: 100%; margin : 2px; margin-top: 20px;'>
                                <div style='height:40px; background-color:lightgray; padding:3px 10px; border-radius:3px;'>
                                    <h5><a href='http://localhost/MotoLandWeb/docs/aszf_minta.pdf' target='_new'>Általános Szerződési Feltételek</a></strong></h5>
                                </div>
                                
                            </div>



                            <div style='float:left; width: 95%; margin:auto; margin-top:30px;'>
                                <div style='height:40px; background-color:lightgray; color: gray; padding:3px 10px; border-radius:3px; text-align: center'>
                                    <h6><strong>© 2025-".Date("Y")." MotoLand szervíz és webshop</strong></h6>
                                </div>
                            </div>


                        </div>";






            //$rname = "megrendelo"; //$_POST['rname'];
            //$testaddr = "mrkoverchenko@gmail.com"; //$_POST['ilovei@rfmlib.hu'];*/


            try {
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->CharSet = "UTF-8";
                //$mail->SMTPDebug = 1;
                $mail->Host = "smtp.mail.yahoo.com";
                $mail->SMTPAuth = true;
                $mail->Username = "istvan.lovei@yahoo.com";
                $mail->Password = "zopxdyyvskobaqjz";
                $mail->SMTPSecure = "ssl"; //465 tls-587
                $mail->Port = 465; //587; 465; imap:993
                $mail->setFrom("istvan.lovei@yahoo.com", "Lövei István");
                $mail->addAddress($usermail, $username);

                $mail->isHTML(true);
                $mail->Subject = "Megrendelés: ";
                $mail->Body    = $mailbody;
                if(!$mail->send()){
                    echo 'Nincs küldés, szarakodás van: ' . $mail->ErrorInfo;
                } else {
                    echo 'Üzenet elküldve!';
                }
            } catch (Exception $ex) {
                echo "'Ha nincs net, nincs probléma': ".$ex;
            }

    }
?>
