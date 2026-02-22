<?php

    require_once('PHPMailer/src/PHPMailer.php');
    require_once('PHPMailer/src/Exception.php');
    require_once('PHPMailer/src/SMTP.php');

    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    if (!empty($_SESSION["cartid"])) {
            $p = "";
            $serverName = "http://".$_SERVER['SERVER_NAME'];
            $url = $_SERVER['REQUEST_URI'];
            $req = explode("/", $url);
            for ($i = 0; $i < count($req)-1; $i++) {
                $p .= $req[$i]."/";
            }
            $aszf = $serverName.$p."docs/aszf_minta.pdf";

            $orderid = $lastid;

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
                            OrdersNote_MSTR AS note,
                            CONCAT(UserFirstName_DET, ' ', UserMiddleName_DET, ' ', UserLastName_DET) AS username,
                            UserPhone_DET AS phone,
                            UserPostCode_DET AS postcode,
                            UserCity_DET AS city,
                            UserStreet_DET AS street,
                            UserAddress_DET AS address,
                            SupplierName_MSTR AS supplier,
                            SupplierCost_MSTR AS suppliercost,
                            SupplierCash_MSTR AS suppliercash,
                            PaymentTypeName_MSTR AS payment,
                            PaymentTypeCost_MSTR AS paymentcost
                        FROM 
                            orders_mstr, orders_det, user_mstr, user_det, countries_mstr, suppliers_mstr, paymenttype_mstr
                        WHERE 
                            OrdersID_MSTR = '$orderid' AND
                            OrdersMSTRID_DET = '$orderid' AND 
                            OrdersUserID_MSTR = UserID_MSTR AND
                            UserMSTRID_DET = UserID_MSTR AND 
                            UserCountryID_DET = CountriesID_MSTR AND 
                            OrdersSupplierID_MSTR = SupplierID_MSTR AND 
                            OrdersPaymentTypeID_MSTR = PaymentTypeID_MSTR";
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
                            OrdersUserEmail_MSTR AS mail,
                            OrdersNote_MSTR AS note,
                            SupplierName_MSTR AS supplier,
                            SupplierCost_MSTR AS suppliercost,
                            SupplierCash_MSTR AS suppliercash,
                            PaymentTypeName_MSTR AS payment,
                            PaymentTypeCost_MSTR AS paymentcost
                        FROM 
                            orders_mstr, orders_det, ordersuser_MSTR, suppliers_mstr, paymenttype_mstr
                        WHERE 
                            OrdersID_MSTR = '$orderid' AND 
                            OrdersMSTRID_DET = '$orderid' AND 
                            OrdersUserOrdersMSTRID_MSTR = '$orderid' AND 
                            OrdersSupplierID_MSTR = SupplierID_MSTR AND 
                            OrdersPaymentTypeID_MSTR = PaymentTypeID_MSTR";
            }

            $result = mysqli_query($connect, $sql);
            $parts = "";
            $ic = 0;
            $fulltotal = 0;
            $rowCount = mysqli_num_rows($result);
            while ($row = mysqli_fetch_assoc($result)) {                                    
                $ic++;
                $orderid = $row["orderid"];
                $orderdate = $row["orderdate"];
                $username = $row["username"];
                $usermail = $row["mail"];
                $userphone = $row["phone"];
                $postcode = $row["postcode"];
                $city = $row["city"];
                $street = $row["street"];
                $address = $row["address"];
                $country = $row["country"];
                $note = $row["note"];

                $partnumber = $row["partnumber"];
                $partname = $row["partname"];
                $netto = round($row["netto"], 0);
                $vat = round($row["vat"], 0);
                $disc = round($row["disc"], 0);
                $brutto = round($row["brutto"], 0);
                $bruttoEUR = round($row["bruttoEUR"], 2);
                $qua = round($row["qua"], 1);
                $mee = $row["mee"];

                $paymentName = $row["payment"];
                $paymentCost = $row["paymentcost"];
                $supplierName = $row["supplier"];
                $supplierCost = $row["suppliercost"];
                $supplierCash = $row["suppliercash"];

                $subtotal = $brutto * $qua;

                $parts .= " <tr>
                                <td style='text-align: center; margin:3px'>$ic.</td>
                                <td style='text-align: center; margin:3px'>$partnumber</td>
                                <td style='text-align: center; margin:3px'>$partname</td>
                                <td style='text-align: center; margin:3px'>$netto.-</td>
                                <td style='text-align: center; margin:3px'>$vat%</td>
                                <td style='text-align: center; margin:3px'>$disc%</td>
                                <td style='text-align: center; margin:3px'>$brutto.-</td>
                                <td style='text-align: center; margin:3px'>$bruttoEUR &euro;</td>
                                <td style='text-align: center; margin:3px'>$qua $mee</td>
                                <td style='text-align: right; margin:3px'><b><u>".$subtotal."</u></b>.-</td>
                            </tr>";
                $fulltotal += $subtotal;

            }

            $fulltotal += ($supplierCost > 0)
                            ? $supplierCost
                            : 0;

            $fulltotal += ($supplierCash > 0)
                            ? $supplierCash
                            : 0;

            $mailbody = "<div style='margin-top:10px; width:95%'>

                            <pre style='color:gray; font-family: Helvetica Neue,Helvetica,Arial,sans-serif; font-size: 14px; line-height: 1.42857143;'>
                                $welcometext
                            </pre>

                            <div style='margin-top:20px; width:100%; height:40px; background-color:lightgray; padding:2px 10px; border-radius:3px;'>
                                <h4><strong>Rendelésed részletei</strong></h4>
                            </div>

                            <div style='color: gray;'>
                                <div style='margin-top:20px;'>Rendelési szám: <b><u>#$orderid</u></b></div>

                                <div style='margin-top:0px;'>Rendelés időpontja: <b>$orderdate</b></div>

                                <div style='margin-top:0px;'>
                                    Szállítás: <b>$supplierName</b>
                                </div>

                                <div style='margin-top:0px;'>
                                    Fizetés: <b>$paymentName</b>".
                                        (($supplierCash > 0) 
                                            ? " ($supplierCash.- Ft.)" 
                                            : "").
                                "</div>

                                <div style='margin-top:20px;'>E-mail cím: <b><a href='mailto:$usermail'>$usermail</a></b></div>

                                <div style='margin-top:0px;'>Telefon: <b>$userphone</b></div>
                            </div>



                            <div style='margin-top: 20px; margin-bottom: 10px; height:40px; background-color:lightgray; padding:3px 10px; border-radius:3px;'>
                                <h5><strong>Termékek</strong></h5>
                            </div>

                            <table style='color: gray;'>
                                <thead>
                                    <tr style='border-bottom: 1px solid red;'>
                                        <th style='margin:3px; width:30px; text-align: center;'>No.</th>
                                        <th style='margin:3px; width:100px; text-align: center;'>Cikkszám</th>
                                        <th style='margin:3px; width:250px; text-align: center;'>Megnevezés</th>
                                        <th style='margin:3px; width:90px; text-align: center;'>Netto</th>
                                        <th style='margin:3px; width:50px; text-align: center;'>Áfa</th>
                                        <th style='margin:3px; width:50px; text-align: center;'>Kedv.</th>
                                        <th style='margin:3px; width:90px; text-align: center;'>Brutto</th>
                                        <th style='margin:3px; width:90px; text-align: center;'>EUR;</th>
                                        <th style='margin:3px; width:80px; text-align: center;'>Mennyiség</th>
                                        <th style='margin:3px; width:100px; text-align: right;'>Összesen</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    $parts
                                </tbody>

                                <tfoot>

                                    <tr>
                                        <td colspan='6'></td>
                                        <td colspan='3' style='text-align: right'>Szállítási díj: $supplierName</td>
                                        <td style='text-align: right'><b><u>$supplierCost.-</u></b></td>
                                    </tr>".

                                        (($supplierCash > 0) 
                                            ? "<tr>
                                                <td colspan='7'></td>
                                                <td colspan='2' style='text-align: right'>Készpénzes fizetés:</td>
                                                <td style='text-align: right'><b><u>$supplierCash.-</u></b></td>
                                              </tr>"
                                            : "").

                                    "<tr>
                                        <td colspan='8'></td>
                                        <td style='text-align: right'>Fizetendő: </td>
                                        <td style='text-align: right'><b><u>$fulltotal.-</u></b></td>
                                    </tr>
                                </tfoot>

                            </table>

                            <hr>

                            <div style='float: left; width: 40%; margin : 2px; margin-top: 20px;'>
                                <div style='height:40px; background-color:lightgray; padding:3px 10px; border-radius:3px;'>
                                    <h5><strong>Szállítási adatok</strong></h5>
                                </div>

                                <div style='color: gray; margin-left:30px;'>
                                    <div style='margin-top:20px;'><b>$username</b></div>

                                    <div>$postcode $city,</div>
                                    
                                    <div>$street $address</div>

                                    <div><b><u>$country</u></b></div>
                                </div>
                            </div>

                            <div style='float: left; width: 40%; margin : 2px; margin-top: 20px;'>
                                <div style='height:40px; background-color:lightgray; padding:3px 10px; border-radius:3px;'>
                                    <h5><strong>Számlázás</strong></h5>
                                </div>

                                <div style='color: gray; margin-left:30px;'>
                                    <div style='margin-top:20px;'><b>$username</b></div>

                                    <div>$postcode $city,</div>
                                    
                                    <div>$street $address</div>

                                    <div><b><u>$country</u></b></div>
                                </div>
                            </div>



                            <div style='float: left; width: 100%; margin : 2px; margin-top: 20px;'>
                                <div style='height:40px; background-color:lightgray; padding:3px 10px; border-radius:3px;'>
                                    <h5><strong>Megjegyzés</strong></h5>
                                </div>

                                <div style='color: gray; margin-left:30px;'>
                                    <div style='margin-top:20px;'>$note</div>
                                </div>
                            </div>



                            <div style='float: left; width: 100%; margin : 2px; margin-top: 20px;'>
                                <div style='height:40px; background-color:lightgray; padding:3px 10px; border-radius:3px;'>
                                    <h5><a href='$aszf' target='_new'>Általános Szerződési Feltételek</a></strong></h5>
                                </div>
                                
                            </div>



                            <div style='float:left; width: 100%; margin:auto; margin-top:30px;'>
                                <div style='height:30px; background-color:lightgray; color: gray; padding:3px 10px; border-radius:3px; text-align: center'>
                                    <h6><strong>© 2025-".Date("Y")." MotoLand szervíz és webshop</strong></h6>
                                </div>
                            </div>


                        </div>";

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
                $mail->setFrom("istvan.lovei@yahoo.com", "MotoLand");
                $mail->addAddress($usermail, $username);

                $mail->isHTML(true);
                $mail->Subject = "MotoLand rendelés: $orderid";
                $mail->Body    = $mailbody;
                if(!$mail->send()){
                    echo 'Nincs küldés, szarakodás van: ' . $mail->ErrorInfo;
                } else {
                    //echo 'Üzenet elküldve!';
                }
            } catch (Exception $ex) {
                echo "'Ha nincs net, nincs probléma': ".$ex;
            }

    }
?>
