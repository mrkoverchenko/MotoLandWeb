<?php
    session_start();
    if (empty($_SESSION['userid'])) { 
    	header("Location: index.php");
        exit();
    }
    include "connect.php";
    $userID = $_SESSION["userid"];

?>
        <style>
            .mybookingbody {
                margin-top:60px; 
                color:gray;
                display: inline-block;
                width: 100%;
                background-color: transparent;
            }
            .mrg {
                margin-top: 5px;
            }
            .readonly {
                color: gray;
            } 
        </style>


        <div class="mybookingbody">



            <div class="row">
                <div class="col-sm-2 mrg">
                    <H3><strong> Időpontfoglalások </strong></H3>
                </div>
            </div>


            <div class="row">
                <div class="col-sm-2 mrg">
                     Előjegyzett időpontok
                </div>
                <div class="col-sm-5" >
                    <div class="form-group">
                        <select class="form-control" id="mybookinglist" onchange="getMyBooking(this)">
                            <option></option>
                            <?php
                                $sql = "SELECT 
                                            BookingID_MSTR,
                                            BookingDateTime_MSTR
                                        FROM 
                                            booking_mstr
                                        WHERE 
                                            BookingUserID_MSTR = '$userID' 
                                        ORDER BY
                                            BookingDateTime_MSTR DESC";
                                $result = mysqli_query($connect, $sql);

                                while ($row = mysqli_fetch_assoc($result)) {                                    
                                    $id = $row["BookingID_MSTR"];
                                    $date = $row["BookingDateTime_MSTR"];
                                    echo "<option value='$id'>$id - $date</option>";
                                }
                                mysqli_close($connect);
                            ?>

                        </select>
                    </div>                     
                </div>
            </div>



            <div class="row">
                <div class='row brdr'>
                    <table class='table table-hover' style='color: gray;'>
                        <thead>
                            <tr>
                                <th scope='col'>No.</th>
                                <th scope='col'>Rendszám</th>
                                <th scope='col'>Gyártó</th>
                                <th scope='col'>Típus</th>
                                <th scope='col'>Évjárat</th>
                            </tr>
                        </thead>

                        <tbody id="bookingTBody">
                               
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


