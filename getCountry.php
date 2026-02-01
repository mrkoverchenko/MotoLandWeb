<?php
    session_start(); 

    include "connect.php";
    $ret = "";
    if (isset($_POST["countryID"])) {

        $countryID = $_POST["countryID"];
        
        $where = "";
        if (strLen($countryID) > 0) {
            $where = "WHERE CountriesID_MSTR = '$countryID' ";
        }
        
        $sql = "SELECT 
                    CountriesID_MSTR,
                    CountriesCountry_MSTR
                FROM 
                    countries_mstr 
                    $where
                ORDER BY CountriesCountry_MSTR ASC";

        $result = mysqli_query($connect, $sql);
        while ($row = mysqli_fetch_assoc($result)) {                                    
            $id = $row["CountriesID_MSTR"];
            $country = $row["CountriesCountry_MSTR"];
            $ret .= "<option ".(($country == "Magyarország")?"selected":"")." value = '$id'>$country</option>"; 
        }
    }
    mysqli_close($connect);
    echo $ret;
?>
