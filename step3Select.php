<?php

require_once "connect.php";

//========================================================================
//
//
// list view of dir2
if (isset($_GET["Dir1_ID"])) {
    $Dir1_ID = $_GET["Dir1_ID"];
    $condition = "Dir1_ID =" . $Dir1_ID;
    $sql = "select Dir2_NM,Dir2_ID from dir2_output where $condition ORDER BY Dir2_NM DESC ";
//    echo $sql."<br>";
    $result = mysql_query($sql);
    if (!$result)
        die();
    $row_count = mysql_num_rows($result);

    echo "<select name='Dir2_ID' id='Dir2_ID' onclick='SelectType()'";
    echo "<option value =''></option>";
    for ($i = 0; $i < $row_count; $i++) {
        $Dir2_NM = mysql_result($result, $i, 0);
        $Dir2_ID = mysql_result($result, $i, 1);
        echo "<option value ='$Dir2_ID'> $Dir2_NM </option>";
    }
    echo "</select>";
}

// list view of device_models
elseif (isset($_GET["Type_ID"])) {

    $Type_ID = $_GET["Type_ID"];
    $Dir2_ID = $_GET["Dir2_ID"];

    $condition = "Type_ID =" . $Type_ID . " AND Dir2_ID=" . $Dir2_ID;

    $sql = "select DISTINCT(Model_NM),Model_ID from device natural join device_model where $condition ORDER BY Model_NM DESC ";

    $result = mysql_query($sql);
    if ($result) {
        $row_count = mysql_num_rows($result);
        echo "<select name='Model_ID' id='Model_ID' onclick='Select_SerialNo()'";
        echo "<option value =''></option>";
        for ($i = 0; $i < $row_count; $i++) {
            $Model_NM = mysql_result($result, $i, 0);
            $Model_ID = mysql_result($result, $i, 1);
            echo "<option value ='$Model_ID'> $Model_NM </option>";
        }
        echo "</select>";
    }
}
// list view of device_serials
elseif (isset($_GET["Model_ID"])) {

    $Model_ID = $_GET["Model_ID"];
    $Dir2_ID = $_GET["Dir2_ID"];

    $condition = "Model_ID =" . $Model_ID . " AND Dir2_ID=" . $Dir2_ID;

    $sql = "select Serial_NO from device device_model where $condition ORDER BY Serial_NO DESC ";

//    echo $sql;
    $result = mysql_query($sql);
    if (!$result)
        die();
    $row_count = mysql_num_rows($result);

    echo "<select name='Serial_NO' id='Serial_NO' ";

    echo "<option value =''></option>";
    for ($i = 0; $i < $row_count; $i++) {
        $Serial_NO = mysql_result($result, $i, 0);
        echo "<option value ='$Serial_NO'> $Serial_NO </option>";
    }
    echo "</select>";
}

// list view of device_types
elseif (isset($_GET["Dir2_ID"])) {

    $Dir2_ID = $_GET["Dir2_ID"];
  
    $condition = "Dir2_ID =" . $Dir2_ID;

    $sql = "select DISTINCT(Type_NM),Type_ID from device natural join device_type where $condition ORDER BY Type_NM DESC ";
//    echo $sql;
    $result = mysql_query($sql);
    if (!$result)
        die();
    $row_count = mysql_num_rows($result);

    echo "<select name='Type_ID' id='Type_ID' onclick='SelectModel()'";
    echo "<option value =''></option>";
    for ($i = 0; $i < $row_count; $i++) {
        $Type_NM = mysql_result($result, $i, 0);
        $Type_ID = mysql_result($result, $i, 1);
        echo "<option value ='$Type_ID'> $Type_NM </option>";
    }
    echo "</select>";
}
