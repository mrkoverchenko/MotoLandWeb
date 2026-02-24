<?php
	session_start();
    include("connect.php");
?>

        <style>
            .minmrgn {
                margin:3px;
            }

            .mrg {
                margin-top:55px; 
            }

            .holiday {
                background-color: red;
                color: white;
            }

            .workday {
                background-color: lightgreen;
            }

            .weekend {
                background-color: orange;
            }


            .dayRect {
                display:inline-block;
                width:30px;
                height:30px;
                padding:5px;
                border: 0.5px solid gray;
                text-align: center;
                cursor: pointer;
            }
            .monthRect {
                display:inline-block;
                background-color:#d9f0b4;
                width:80px;
                height:30px;
                padding:5px;
                border: 0.5px solid gray;
            }
        </style>

        <div class="ordereditemsbody">

            <div class="row">
                <div class="col-sm-2 mrg">
                    <H3><strong> Időpontfoglalás </strong></H3>
                </div>
            </div>


            <div class="row">
                <div class="col-sm-10 mrg">
                     Az alábbi táblázatban található szabad munkanapokra tud időpontot foglalni, egyeztetni.
                </div>

                <div class="col-sm-10" style="margin-top:10px;">
                    <div><div class="dayRect holiday minmrgn">16</div> Ünnepnap - munkaszüneti nap</div>
                    <div><div class="dayRect weekend minmrgn">04</div> Hétvége - munkaszüneti nap</div>
                    <div><div class="dayRect workday minmrgn">10</div> Munkanap</div>
                </div>

                <div class="col-sm-5" >
                    <div class="form-group">
                    </div>                     
                </div>
            </div>

            <div style="margin:10px; ">

                <?php
                    $dNames = ['vasárnap', 'hétfő', 'kedd', 'szerda', 'csütörtök', 'péntek', 'szombat'];
                    $yr = "2026";
                    $d = "";
                    $str = "SELECT HolidayDate_MSTR, HolidayDetail_MSTR ".
                           "FROM holidays_mstr ".
                           "ORDER BY HolidayDate_MSTR ASC";
                    $result = mysqli_query($connect, $str);
                    $holiday = array();
                    while ($row = mysqli_fetch_assoc($result)) {                    
                        array_push($holiday, $row["HolidayDate_MSTR"]."ß".$row["HolidayDetail_MSTR"]);
                        //$holiday[] = ['date' => $row["HolidayDate_MSTR"], 'detail' => $row["HolidayDetail_MSTR"] ];                        
                    }

                    for ($monthIC = 1; $monthIC <= 12; $monthIC++) {
                        $date =  $yr."-".$monthIC."-01";
                        $daysCount = cal_days_in_month(CAL_GREGORIAN, $monthIC, $yr); 

                        for ($dayIC = 0; $dayIC <= $daysCount; $dayIC++) {
                            $optional = true;

                            if ($dayIC === 0) {

                                $monthName = date('F', strtotime($date));
                                $d .= "<div class='monthRect'>$monthName</div>";

                            } else {

                                if ($dayIC > 0) {
                                    $rectDate = $yr."-".
                                                (($monthIC < 9) ? "0".$monthIC : $monthIC)."-".
                                                (($dayIC < 9) ? "0".$dayIC : $dayIC);


                                    $dayName = $dNames[date('w', strtotime($rectDate))];
                                    $dayType = "workday";
                                    $detail = "$rectDate, $dayName\nMunkanap";



                                    //WEEKEND
                                    //$rectDayName = date("l", strtotime($rectDate));
                                    if (date('N', strtotime($rectDate)) >= 6) {
                                        $dayType = "weekend";
                                        $detail = "$rectDate, $dayName\nHétvége";
                                        $optional = false;
                                    }


                                    for ($ic = 0; $ic < count($holiday); $ic++) {
                                        $holi = explode("ß", $holiday[$ic]);
                                        $hDate = $holi[0];
                                        $hDetail = $holi[1];
                                        

                                        //HOLIDAY
                                        if ($hDate === $rectDate) {
                                            $dayType = "holiday";
                                            $detail = "$rectDate, $dayName\n".$hDetail."\nMunkaszüneti nap";
                                            $optional = false;
                                            break;
                                        }

                                       
                                    }

                                    if ($optional)
                                        $d .= "<a onclick='setBooking(event)' href='#bookingService' data-toggle='modal'><div id='bookingrect' class='dayRect $dayType' title='$detail'>$dayIC</div></a>";
                                    else
                                        $d .= "<div class='dayRect $dayType' title='$detail'>$dayIC</div>";

                                }

                            }


                        }
                        $d .= "</br>"; 
                    }
                    echo $d;
                ?>

            </div>

        </div>

        <!-- BOOKING -->
            <!--if (isset($_SESSION["usertype"])) {-->







