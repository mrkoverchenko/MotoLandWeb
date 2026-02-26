<?php
	session_start();
    include("connect.php");
?>

        <style>

            .servicebookingbody {
                margin:10px;
                margin-top:60px; 

                color:gray;
                display: inline-block;
                width: 100%;
            }

            .mrg {
                margin-top:55px; 
            }

            .reservedday {
                background-color: gray;
                color: white;
            }

            .pastday {
                background-color: lightgray;
                color: white;
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
            .minDayRect {
                display:inline-block;
                width:20px;
                height:20px;
                padding-top:5px;
                border: 0.5px solid gray;
                margin-right: 5px;
                text-align: center;
                font-size:7px;
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

        <div class="servicebookingbody">

            <div class="row">
                <div class="col-sm-2 mrg">
                    <H3><strong> Időpontfoglalás </strong></H3>
                </div>
            </div>


            <div class="row">
                <div class="col-sm-10 mrg">
                     Az alábbi táblázatban található szabad munkanapokra ( <div class="minDayRect workday">18</div>) tud időpontot foglalni, egyeztetni.
                </div>

                <div class="col-sm-10" style="margin-top:10px;">
                    <div class="minDayRect holiday">16</div><div style="vertical-align: bottom; display:inline; margin-right:15px; ">Ünnepnap-munkaszüneti nap</div>
                    <div class="minDayRect weekend">10</div><div style="vertical-align: bottom; display:inline; margin-right:15px;">Hétvége-munkaszüneti nap</div>
                    <div class="minDayRect workday">18</div><div style="vertical-align: bottom; display:inline; margin-right:15px;">Munkanap</div>
                    <div class="minDayRect reservedday">13</div><div style="vertical-align: bottom; display:inline; margin-right:15px;">Foglalt munkanap</div>
                    <div class="minDayRect pastday">07</div><div style="vertical-align: bottom; display:inline; margin-right:15px;">Múlt</div>
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


                    $str = "SELECT DATE(BookingDateTime_MSTR) AS bookingDate ".
                           "FROM booking_mstr ".
                           "ORDER BY BookingDateTime_MSTR ASC";
                    $result = mysqli_query($connect, $str);
                    $reservedday = array();
                    while ($row = mysqli_fetch_assoc($result)) {                    
                        array_push($reservedday, $row["bookingDate"]);
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
                                                (($dayIC < 10) ? "0".$dayIC : $dayIC);


                                    $dayName = $dNames[date('w', strtotime($rectDate))];
                                    $dayType = "workday";
                                    $detail = "$rectDate, $dayName\nMunkanap";


                                    if ( date("Y-m-d") > date($rectDate)) {
                                        //PAST DAY
                                        $dayType = "pastday";
                                        $detail = "$rectDate, $dayName";
                                        $optional = false;

                                    } else {

                                        //WEEKEND
                                        //$rectDayName = date("l", strtotime($rectDate));
                                        if (date('N', strtotime($rectDate)) >= 6) {
                                            $dayType = "weekend";
                                            $detail = "$rectDate, $dayName\nHétvége";
                                            $optional = false;
                                        }


                                        //RESERVED DAY
                                        for ($ic = 0; $ic < count($reservedday); $ic++) {
                                            if ($reservedday[$ic] === $rectDate) {
                                                $dayType = "reservedday";
                                                $detail = "$rectDate, $dayName\nMár foglalt munkanap";
                                                $optional = false;
                                                break;
                                            }
                                        }



                                        //HOLIDAY
                                        for ($ic = 0; $ic < count($holiday); $ic++) {
                                            $holi = explode("ß", $holiday[$ic]);
                                            $hDate = $holi[0];
                                            $hDetail = $holi[1];
                                            if ($hDate === $rectDate) {
                                                $dayType = "holiday";
                                                $detail = "$rectDate, $dayName\n".$hDetail."\nÜnnepnap";
                                                $optional = false;
                                                break;
                                            }
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







