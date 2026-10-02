<?php

require_once "connect.php";
require_once "used_functions.php";

if ((isset($_GET["devices_num"]) || isset($_GET["Type_ID"])) && isset($_GET["state"])) {
    
    if ($_GET["state"] == "Model" ) {

        $Type_ID = $_GET["Type_ID"];
        selectView("device_model", "Model_NM", $Type_ID,"","out");
    }elseif ($_GET["state"] == "rub") {
        $Type_ID = $_GET["Type_ID"];
        selectView("device_model", "Model_NM", $Type_ID,"","Rubish");
    } 
    else {
        $devices_num = $_GET["devices_num"];
        $Type_ID = $_GET["Type_ID"];

        if ($Type_ID == 5) {

            for ($i = 1; $i <= $devices_num; $i++) {
                echo ' <tr><td><b>رقم مسلسل' . $i . '</td> <td><input type="text"  name="Serial_NO' . $i . '" ></td> ';
                echo ' <td><b> مسلسل الشاشة' . $i . '</td> <td><input type="text"  name="Screen_NO' . $i . '" ></td> ';
                echo '</tr>';
            }
        } else {

            for ($i = 1; $i <= $devices_num; $i++) {
                echo ' <tr><td><b>رقم مسلسل' . $i . '</td> <td><input type="text"  name="Serial_NO' . $i . '" ></td> ';
                echo '</tr>';
            }
        }
    }
}

if (isset($_GET["Dir1_ID"])&& !isset($_GET["state"])) {

    $Dir1_ID = $_GET["Dir1_ID"];
    selectView("dir2_output", "Dir2_NM", $Dir1_ID);
}

if (isset($_GET["Model_ID"]) && !isset($_GET["state"])) {
    $Model_ID = $_GET["Model_ID"];
    
    if (isset($_GET["Rubish"])) {
        $sql = "SELECT Serial_NO 
                FROM device 
                WHERE Model_ID = '$Model_ID' 
                AND state NOT IN (2,3) 
                ORDER BY Serial_NO";
                
        $result = mysql_query($sql);
        if (!$result) die("Database access failed: " . mysql_error());
        
        echo '<select name="Serial_NO" id="Serial_NO">';
        while ($row = mysql_fetch_array($result)) {
            echo '<option value="' . $row['Serial_NO'] . '">' . $row['Serial_NO'] . '</option>';
        }
        echo '</select>';
    } elseif (isset($_GET["discount"])) {
        // كود خاص بالخصم 
                $sql = "SELECT Serial_NO 
                FROM device 
                WHERE Model_ID = '$Model_ID' 
                AND state NOT IN (3) 
                ORDER BY Serial_NO";
                
        $result = mysql_query($sql);
        if (!$result) die("Database access failed: " . mysql_error());
        
        echo '<select name="Serial_NO" id="Serial_NO">';
        while ($row = mysql_fetch_array($result)) {
            echo '<option value="' . $row['Serial_NO'] . '">' . $row['Serial_NO'] . '</option>';
        }
        echo '</select>';
    } else {
        selectView("device", "Serial_NO", $Model_ID,"","out");
    }
}

if (isset($_GET["Type_ID"])&& !isset($_GET["state"])) {
    $Type_ID = $_GET["Type_ID"];
    selectView("device_model", "Model_NM", $Type_ID,"", "out");
}





