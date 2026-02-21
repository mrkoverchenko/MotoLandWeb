<?php

    include "connect.php";



            $serverName = "http://".$_SERVER['SERVER_NAME'];
            $url = $_SERVER['REQUEST_URI'];
            $req = explode("/", $url);
            for ($i = 0; $i < count($req)-1; $i++) {
                $p .= $req[$i]."/";
            }
            $aszf = $serverName.$p."docs/aszf_minta.pdf";

            $welcometext = "Üres üdvözlő!";
            $sql = "SELECT MotoSystemMailText_MSTR FROM motosystem_mstr WHERE MotoSystemID_MSTR = '1'";
            $res = mysqli_query($connect, $sql);
            $row = mysqli_fetch_assoc($res);
            $welcometext = $row["MotoSystemMailText_MSTR"];
            $mailbody = "<div style='margin-top:80px'>";

            $mailbody .= "  <span style='color:gray'>$welcometext</span>";
            $mailbody .=    "<div style='margin-top:20px; width:100%; height:40px; background-color:lightgray; padding:2px 10px; border-radius:3px;'>
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
                                    <h5><a href='$aszf' target='_new'>Általános Szerződési Feltételek</a></strong></h5>
                                </div>
                            </div>



                            <div style='float:left; width: 100%; margin:auto; margin-top:30px;'>
                                <div style='height:40px; background-color:lightgray; color: gray; padding:3px 10px; border-radius:3px; text-align: center'>
                                    <h6><strong>© 2025-".Date("Y")." MotoLand alkatrészkereskedés</strong></h6>
                                </div>
                            </div>


                        </div>";
        echo $mailbody;
?>