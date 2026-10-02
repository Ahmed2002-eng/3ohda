<?php

function return_dir2_ID($Dir2_NM, $Dir1_ID) {

    $dir2_sql = "select Dir2_ID from dir2_output where Dir2_NM = '$Dir2_NM' AND Dir1_ID=$Dir1_ID ";
    $dir2_result = mysql_query($dir2_sql);
    if (!$dir2_result) {
        die("Database access failed: " . mysql_error());
    }
    $dir2_row_data = mysql_fetch_row($dir2_result);
    $Dir2_ID = $dir2_row_data[0];
    return $Dir2_ID;
}

function return_Model_ID($Model_NM, $Type_ID) {

    $Model_ID_sql = "select Model_ID from device_model where Model_NM = '$Model_NM' AND Type_ID=$Type_ID ";
    $Model_ID_result = mysql_query($Model_ID_sql);
    if (!$Model_ID_result) {
        die("Database access failed: " . mysql_error());
    }
    $Model_ID_data = mysql_fetch_row($Model_ID_result);
    $Model_ID = $Model_ID_data[0];
    return $Model_ID;
}

function selectstate() {
    echo "<select name='state'>";

    echo "<option value ='admin'>admin</option>";
    echo "<option value ='dataEntry'>dataEntry</option>";
    echo "<option value ='globalView'>globalView</option>";

    echo "</select>";
}

function generateRandomString() {
    return date("Y_m_d_H_i_s"); 
}


function displayTableBody($sql, $table_name, $index = 0) {
    $total_count = 0;
    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $row_count = mysql_num_rows($result);
    echo '<tbody>';
    for ($i = 0; $i < $row_count; $i++) {
        $row_data = mysql_fetch_row($result);
        echo"<tr>";
        if ($index == 0) {
            for ($j = 0; $j < count($row_data); $j++) {
                echo"<td>" . $row_data[$j] . "</td>";
            }
        }
        else {
            for ($j = 0; $j < count($row_data); $j++) {
                if ($j == $index) {
                    echo"<td>" . getNameFromID($table_name, $row_data[$j]) . "</td>";
                } else {
                    echo"<td>" . $row_data[$j] . "</td>";
                }
            }
        }
        $total_count = $total_count + $row_data[1];
        echo"</tr>";
    }
  
    
}

function getNameFromID($table, $keyValue) {

    if ($table == "device_model") {
        $returnAttr = "Model_NM";
        $keyAttr = "Model_ID";
    } elseif ($table == "device_type") {
        $returnAttr = "Type_NM";
        $keyAttr = "Type_ID";
    } elseif ($table == "dir1_output") {
        $returnAttr = "Dir1_NM";
        $keyAttr = "Dir1_ID";
    } elseif ($table == "dir2_output") {
        $returnAttr = "Dir2_NM";
        $keyAttr = "Dir2_ID";
    } elseif ($table == "input_dir") {
        $returnAttr = "Dir_NM";
        $keyAttr = "Dir_ID";
    }

    $sql = "select $returnAttr from $table where $keyAttr = '$keyValue' ";
    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $row_data = mysql_fetch_row($result);
    return $row_data[0];
}

function getName($table, $returnAttr, $keyAttr, $keyValue) {
    $sql = "select $returnAttr from $table where $keyAttr = '$keyValue' ";
    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $row_data = mysql_fetch_row($result);
    return $row_data[0];
}

function printName($table, $returnAttr, $keyAttr, $keyValue) {
    $sql = "select $returnAttr from $table where $keyAttr = '$keyValue' ";
    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $row_data = mysql_fetch_row($result);
    echo $row_data[0];
}

function getID($table, $returnAttr, $keyAttr, $keyValue) {
    $keyValue = '"' . $keyValue . '"';
    $sql = "select $returnAttr from $table where $keyAttr = $keyValue ";

    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $row_data = mysql_fetch_row($result);

    return $row_data[0];
}

//================================================================
function selectView($table, $attribute, $ID = 0, $out_sql = "", $dev_state = 'false') {

    // condition selected depend on the table and ID
    if ($ID != 0 && $table == "device_model") {
        $Type_ID = $ID;
        $condition = "Type_ID =" . $Type_ID;
        //  if state discount should select from rubish only
    } elseif ($ID != 0 && $table == "dir2_output") {
        $Dir1_ID = $ID;
        $condition = "Dir1_ID =" . $Dir1_ID;
    } elseif ($ID != 0 && $table == "device") {
        $Model_ID = $ID;
        if ($dev_state == 'out') {
            $condition = "Model_ID =" . $Model_ID . " AND state=0";
        } elseif ($dev_state == 'discount') {
            $condition = "Model_ID =" . $Model_ID . " AND state=2";
        } elseif ($dev_state == 'Rubish') {
            $condition = "Model_ID =" . $Model_ID . " AND state=1";
        }
    } elseif ($table == "device") {
        $condition = "0";
    } else {
        $condition = "true";
    }
    //=========================

    $sql = "select $attribute from $table where $condition ORDER BY $attribute ASC  ";

    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $row_count = mysql_num_rows($result);


    if (($dev_state == 'out' || $dev_state == 'discount' || $dev_state == 'Rubish') && $table != "device") {
        echo "<select name='Model_ID' id='Model_ID' onclick='Select_SerialNo()' >";
    } else {

        echo "<select name='$attribute' id='$attribute' >";
    }
    echo "<option value =''></option>";

    if (($dev_state == 'out' || $dev_state == 'discount' || $dev_state == 'Rubish') && $table != "device") {
        for ($i = 0; $i < $row_count; $i++) {
            $Model_NM = mysql_result($result, $i, 0);
            $Model_ID = return_Model_ID($Model_NM, $Type_ID);
            echo "<option value ='$Model_ID'> $Model_NM </option>";
        }
    } else {
        for ($i = 0; $i < $row_count; $i++) {
            $Item = mysql_result($result, $i, 0);
            echo "<option value ='$Item'> $Item </option>";
        }
    }
    echo "</select>";
}

//==========================================================================================
function selectView_onChange($table, $attribute) {

    if ($table == "device_type") {
        $sql = "select $attribute from device_type";
    } elseif ($table == "dir1_output") {
        $sql = "select $attribute from dir1_output WHERE Dir1_ID!=20 and Dir1_ID!=21"; // to not display the rubish or discount as a diraction
    }

    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $row_count = mysql_num_rows($result);

    if ($table == "device_type") {
        echo "<select name='Type_ID' id='Type_ID' onclick='SelectModel()'>";
    } elseif ($table == "dir1_output") {
        echo "<select name='Dir1_ID' id='Dir1_ID' onclick='SelectDir2()'>";
    }

    echo "<option value =''></option>";

    for ($i = 0; $i < $row_count; $i++) {
        $Item = mysql_result($result, $i, 0);

        if ($table == "device_type") {
            $ID = getID('device_type', 'Type_ID', 'Type_NM', $Item);
        } elseif ($table == "dir1_output") {
            $ID = getID('dir1_output', 'Dir1_ID', 'Dir1_NM', $Item);
        }
        echo "<option value ='$ID' > $Item </option>";
    }
    echo "</select>";
}

//===============================================================================================
function displayAllDeviceTable($sql,$source="insert") {
    $parent_path="../../../";
    if ($source!="insert") {
        $parent_path="../../../../";
    }
    echo '<p align="center"> <FONT FACE="arial" SIZE="6" ><B></B></FONT></p>';
//    echo $sql;
    echo '<table border="3"><thead><tr><th>مسلسل</th><th>جهة التوريد</th><th>الفئة</th> <th>موديل الجهاز</th><th>رقم الجهاز</th><th>رقم الممارسة</th><th>تاريخ التوريد</th><th>رقم 1 مخازن</th><th>رقم 2 مخازن</th><th>صورة السند</th><th>رقم الشاشة</th><th>الشركة القائمة بالصيانة</th><th>الجهة الرئيسية</th><th>الجهة الفرعية</th></tr></thead>';
    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $row_count = mysql_num_rows($result);
    echo '<tbody>';
    for ($i = 0; $i < $row_count; $i++) {
        $row_data = mysql_fetch_row($result);
        echo"<tr>";
        for ($j = 0; $j < count($row_data); $j++) {
            if ($j == 0) {
                $count = $i + 1;
                echo"<td>" . $count . "</td>";
            } elseif ($j == 1) {
                $Dir_NM = getName("input_dir", "Dir_NM", "Dir_ID", $row_data[1]);
                echo"<td>" . $Dir_NM . "</td>";
            } elseif ($j == 2) {
                $Type_NM = getName("device_type", "Type_NM", "Type_ID", $row_data[2]);
                echo"<td>" . $Type_NM . "</td>";
            } elseif ($j == 3) {
                $Model_NM = getName("device_model", "Model_NM", "Model_ID", $row_data[3]);
                echo"<td>" . $Model_NM . "</td>";
            } elseif ($j == 9) {
                echo"<td><a href='" . $parent_path . $row_data[9] . "' target='_blank'>تفصيلى</a></td>"; //Input_PDF path
            } elseif ($j == 11) {
                $Maintainance_CO_NM = getName("maintainance_co", "Maintainance_CO_NM", "Maintainance_CO_ID", $row_data[11]);
                echo"<td>" . $Maintainance_CO_NM . "</td>";
            } elseif ($j == 12) {
                $Device_State_NM = getName("device_state", "Device_State_NM", "Device_State_ID", $row_data[12]);
                // echo"<td>" . $Device_State_NM . "</td>";
            } elseif ($j == 13) {
                $Dir1_NM = getName("dir1_output", "Dir1_NM", "Dir1_ID", $row_data[13]);
                echo"<td>" . $Dir1_NM . "</td>";
            } elseif ($j == 14) {
                $Dir2_NM = getName("dir2_output", "Dir2_NM", "Dir2_ID", $row_data[14]);
                echo"<td>" . $Dir2_NM . "</td>";
            } else {
                echo"<td>" . $row_data[$j] . "</td>";
            }
        }
        echo"</tr>";
    }
    echo '</tbody>';
    echo '</table><br>';
}

function display_All_Consumed_Table($sql) {
    echo '<p align="center"> <FONT FACE="arial" SIZE="6" ><B></B></FONT></p>';
//    echo $sql;
    echo '<form><table border="3" width=100%><thead><tr><th>مسلسل</th><th>جهة التوريد</th><th>الفئة</th> <th>موديل الجهاز</th><th>رقم الممارسة</th><th>تاريخ التوريد</th><th>رقم 1 مخازن</th><th>رقم 2 مخازن</th><th>صورة السند</th><th>الشركة القائمة بالصيانة</th><th>حالة الصرف</th><th>الكمية</th><th>الوحدة</th></tr></thead>';

    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $row_count = mysql_num_rows($result);
    echo '<tbody>';
    for ($i = 0; $i < $row_count; $i++) {
        $row_data = mysql_fetch_row($result);

        echo"<tr>";
        for ($j = 0; $j < count($row_data); $j++) {




            if ($j == 0) {
                $count = $i + 1;
                echo"<td>" . $count . "</td>";
            } elseif ($j == 1) {
                $Dir_NM = getName("input_dir", "Dir_NM", "Dir_ID", $row_data[1]);
                echo"<td>" . $Dir_NM . "</td>";
            } elseif ($j == 2) {
                $Type_NM = getName("device_type", "Type_NM", "Type_ID", $row_data[2]);
                echo"<td>" . $Type_NM . "</td>";
            } elseif ($j == 3) {
                $Model_NM = getName("device_model", "Model_NM", "Model_ID", $row_data[3]);
                echo"<td>" . $Model_NM . "</td>";
            } elseif ($j == 8) {
                echo"<td><a href='" . "../../../" . $row_data[8] . "' target='_blank'>تفصيلى</a></td>"; //Input_PDF path
            } elseif ($j == 9) {
                $Maintainance_CO_NM = getName("maintainance_co", "Maintainance_CO_NM", "Maintainance_CO_ID", $row_data[9]);
                echo"<td>" . $Maintainance_CO_NM . "</td>";
            } else {
                echo"<td>" . $row_data[$j] . "</td>";
            }
        }
        echo"</tr>";
    }

    echo '</tbody>';
    echo '</table></form><br>';
}

function displayALL($sql, $table_header, $attributes) {

    echo '<table border="3" width=100%><thead><tr>' . $table_header . '</tr></thead>';
    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $row_count = mysql_num_rows($result);
    echo '<tbody>';
    for ($i = 0; $i < $row_count; $i++) {
        $row_data = mysql_fetch_row($result);

        echo"<tr>";

        $count_row_data = count($row_data);
        for ($j = 0; $j < $count_row_data; $j++) {

            if ($j == 0) {
                $count = $i + 1;
                echo"<td>" . $count . "</td>";
            } elseif ($j == array_search("Dir1_ID", $attributes)) {
                $Dir1_ID = $row_data[$j];
                $Dir1_NM = getName("dir1_output", "Dir1_NM", "Dir1_ID", $Dir1_ID);
                echo"<td>" . $Dir1_NM . "</td>";
            } elseif ($j == array_search("Dir2_ID", $attributes)) {
                $Dir2_ID = $row_data[$j];
                $Dir2_NM = getName("dir2_output", "Dir2_NM", "Dir2_ID", $Dir2_ID);
                echo"<td>" . $Dir2_NM . "</td>";
            } elseif ($j == array_search("Input_PDF", $attributes)) {
                echo"<td><a href='" . "../../../" . $row_data[$j] . "' target='_blank'>تفصيلى</a></td>"; //Input_PDF path
            } elseif ($j == array_search("Output_PDF", $attributes)) {
                echo"<td><a href='" . "../../../" . $row_data[$j] . "' target='_blank'>تفصيلى</a></td>"; //Input_PDF path
            } elseif ($j == array_search("Dir_ID", $attributes)) {
                echo "<form action='../../update/edit_device/edit_device_output/submit_edit_device_output.php' method='POST'><td><input type='submit' name='update_device' value='تعديل' style='text-align: center;'><input type='hidden' name='Device_ID' value=$row_data[0]></td></form>";
            } else {
                echo"<td>" . $row_data[$j] . "</td>";
            }
        }



        echo"</tr>";
    }
    echo '</tbody>';
    echo '</table>';
}

function display_dir_3ohda_Table($sql) {


    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $row_count = mysql_num_rows($result);
    echo '<tbody>';
    for ($i = 0; $i < $row_count; $i++) {
        $row_data = mysql_fetch_row($result);

        echo"<tr>";


        for ($j = 0; $j < count($row_data); $j++) {

            if ($j == 0) {
                $count = $i + 1;
                echo"<td>" . $count . "</td>";
            } else {
                echo"<td>" . $row_data[$j] . "</td>";
            }
        }
        echo"</tr>";
    }
    echo '</tbody>';
    echo '</table></form>';
    echo '<br><table border="3"><thead><tr><th>الاجمالى العام</th></tr></thead><tr><td>' . $row_count . '</td></tr></table>';
}

function display_num_store($sql, $condition) {
    $counter = 0;
    echo '<p align="center"> <FONT FACE="arial" SIZE="6" ><B>يومية رصيد مخزن ادارة تكنولوجيا المعلومات</B></FONT></p>';

    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $total = 0;
    $row_count = mysql_num_rows($result);
    for ($i = 0; $i < $row_count; $i++) {
        $row_data = mysql_fetch_row($result);
        echo '<table border="3" width=100%><thead><tr><th>مسلسل</th><th>' . $row_data[1] . '</th><th>' . $row_data[2] . '</th></tr></thead>';
        $total = $total + $row_data[2];
        $second_sql = "SELECT Model_NM,COUNT(device.Model_ID) FROM `device` join device_model on device.Model_ID=device_model.Model_ID WHERE device.Type_ID=$row_data[0] AND $condition GROUP by device.Model_ID";
        $second_result = mysql_query($second_sql);
        if (!$second_result) {
            die("Database access failed: " . mysql_error());
        }
        $second_row_count = mysql_num_rows($second_result);
        for ($j = 0; $j < $second_row_count; $j++) {
            $counter++;
            $second_row_data = mysql_fetch_row($second_result);
            echo '<tbody><tr><td>' . $counter . '</td><td>' . $second_row_data[0] . '</td><td>' . $second_row_data[1] . '</td></tr></tbody>';
        }

        echo '</table><br><br>';
    }
    echo '<br><table border="3"><thead><tr><th>الاجمالى العام</th></tr></thead><tr><td>' . $total . '</td></tr></table>';
}

function display_Device_num_3ohda_Table($sql, $Dir1_ID) {
    $Dir1_NM = getName("dir1_output", "Dir1_NM", "Dir1_ID", $Dir1_ID);
    echo '<p align="center"> <FONT FACE="arial" SIZE="6" ><B> عهدة الجهة الرئيسية ' . $Dir1_NM . '</B></FONT></p>';

    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $total = 0;
    $row_count = mysql_num_rows($result);
    for ($i = 0; $i < $row_count; $i++) {
        $row_data = mysql_fetch_row($result);
        echo '<table border="1" width=100%><thead><tr><th>' . $row_data[1] . '</th><th>' . 'الرصيد المخزنى' . '</th></tr></thead>';
        $Dir2_ID = $row_data[0];
        $total = $total + $row_data[2];
        $second_sql = "SELECT `Type_NM`,COUNT(Device_ID) FROM `device` NATURAL JOIN device_type  WHERE Dir2_ID =$Dir2_ID GROUP by `Type_NM`";
        $second_result = mysql_query($second_sql);
        if (!$second_result) {
            die("Database access failed: " . mysql_error());
        }
        $second_row_count = mysql_num_rows($second_result);
        for ($j = 0; $j < $second_row_count; $j++) {
            $second_row_data = mysql_fetch_row($second_result);
            echo '<tbody><tr><td>' . $second_row_data[0] . '</td><td>' . $second_row_data[1] . '</td></tr>';
        }
        echo '<tr><td><b>' . 'الاجمالى' . '</td><td><b>' . $row_data[2] . '</td></tr></tbody>';
        echo '</table><br>';
    }
    $sql = "select Type_NM,COUNT(Type_NM) from output  NATURAL JOIN device NATURAL JOIN device_type WHERE Dir1_ID =$Dir1_ID  GROUP by Type_NM order by Output_Date ASC ";
    displayTotalTable($sql);
//    echo '<br><table border="3"><thead><tr><th>الاجمالى العام</th></tr></thead><tr><td>' . $total . '</td></tr></table>';
}

function display_Device_details_3ohda_Table($sql, $Dir1_ID) {
    $Dir1_NM = getName("dir1_output", "Dir1_NM", "Dir1_ID", $Dir1_ID);
    echo '<p align="center"> <FONT FACE="arial" SIZE="6" ><B> عهدة الجهة الرئيسية ' . $Dir1_NM . '</B></FONT></p>';

    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $row_count = mysql_num_rows($result);
    for ($i = 0; $i < $row_count; $i++) {
        $row_data = mysql_fetch_row($result);

        $Dir2_ID = $row_data[0];
        $Dir2_NM = $row_data[1];
        $count = $i + 1;
        echo '<h3 style="position : relative ; left : 0px">' . $count . '-' . $Dir2_NM . '</h2> ';
        echo '<table border="3" width=100%><thead>';
        echo '<tr><th>المسلسل</th><th>تاريخ الصرف</th><th> الفئة</th><th>الماركة والموديل</th><th>رقم المسلسل</th></tr></thead>';
        $second_sql = "SELECT Output_Date,`Type_NM`,Model_NM,Serial_NO FROM output NATURAL JOIN device  NATURAL JOIN device_type join device_model on device.Model_ID=device_model.Model_ID  WHERE Dir2_ID =$Dir2_ID order by Output_Date ASC";
        $second_result = mysql_query($second_sql);
        if (!$second_result) {
            die("Database access failed: " . mysql_error());
        }
        $second_row_count = mysql_num_rows($second_result);
        for ($j = 0; $j < $second_row_count; $j++) {
            $second_row_data = mysql_fetch_row($second_result);
            $count = $j + 1;
            echo '<tbody><tr><td>' . $count . '</td><td>' . $second_row_data[0] . '</td><td>' . $second_row_data[1] . '</td><td>' . $second_row_data[2] . '</td><td>' . $second_row_data[3] . '</td></tr></tbody>';
        }
        echo '</table><br><br>';
    }
    $sql = "select Type_NM,COUNT(Type_NM) from output  NATURAL JOIN device NATURAL JOIN device_type WHERE Dir1_ID =$Dir1_ID  GROUP by Type_NM order by Output_Date ASC ";
    displayTotalTable($sql);
}

function display_Device_type_balance_Table($sql) {
    echo '<p align="center"> <FONT FACE="arial" SIZE="6" ><B> تقرير تفصيلي برصيد صنف</B></FONT></p>';
    echo '<form><table border="3" width=100%><thead><tr><th>مسلسل</th><th>رقم الجهاز</th><th>رقم الشاشة</th> <th>الجهة الرئيسية</th><th>الجهة الفرعية</th></tr></thead>';

    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $row_count = mysql_num_rows($result);
    echo '<tbody>';
    for ($i = 0; $i < $row_count; $i++) {
        $row_data = mysql_fetch_row($result);
        if ($i % 2 != 0) {
            echo"<tr BGCOLOR='#808080'>";
        } else {
            echo"<tr>";
        }

        for ($j = 0; $j < count($row_data); $j++) {
            if ($j == 0) {
                $count = $i + 1;
                echo"<td>" . $count . "</td>";
            } else {
                echo"<td>" . $row_data[$j] . "</td>";
            }
        }
        echo"</tr>";
    }
    echo '</tbody>';
    echo '</table></form>';
}

function display_dir2_3ohda_detail_able($sql) {

    echo '<form><table border="3" width=100%><thead><tr><th>مسلسل</th><th>تاريخ الصرف</th><th>نوع الجهاز</th><th>الماركة والموديل</th> <th>رقم الجهاز</th></tr></thead>';

    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $row_count = mysql_num_rows($result);
    echo '<tbody>';
    for ($i = 0; $i < $row_count; $i++) {
        $row_data = mysql_fetch_row($result);

        echo"<tr>";


        for ($j = 0; $j < count($row_data); $j++) {
            if ($j == 0) {
                $count = $i + 1;
                echo"<td>" . $count . "</td>";
            } else {
                echo"<td>" . $row_data[$j] . "</td>";
            }
        }
        echo"</tr>";
    }
    echo '</tbody>';
    echo '</table></form>';
}

//===================================================================================

function display_officer_3ohda_table($sql) {
    $userState = $_SESSION['3ohda_userstate'];
    $edit = "";
    if ($userState == "admin") {
        $edit = "<th>نقل عهدة</th><th>تعديل</th>";
    }

    echo '<table border="3" width=100%><thead><tr><th>مسلسل</th><th>نوع الجهاز</th><th>الماركة والموديل</th> <th>رقم الجهاز</th><th>تاريخ الاستلام</th><th>تفصيلي</th>' . $edit . '</tr></thead>';
    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $row_count = mysql_num_rows($result);
    echo '<tbody>';
    for ($i = 0; $i < $row_count; $i++) {
        $row_data = mysql_fetch_row($result);

        $row_count1 = count($row_data);
        if ($userState == "admin") {
            $row_count1 = count($row_data) + 2;
        }

        echo"<tr>";
        for ($j = 0; $j < $row_count1; $j++) {
            if ($j == 0) {
                $count = $i + 1;
                echo"<td>" . $count . "</td>";
            } elseif ($j == 5) {
                echo"<td><a href='" . "../../../" . $row_data[$j] . "' target='_blank'>تفصيلى</a></td>"; //Discount_PDF path
            } elseif ($j == 6) {
                echo "<form action='upadteOut2.php' method='POST'><td><input type='submit' name='upadteOut2_device' value='نقل' style='text-align: center;'><input type='hidden' name='Serial_NO' value=$row_data[3]></td></form>";
            } elseif ($j == 7) {
                echo "<form action='../../update/edit_device/edit_output2/submit_edit_ouput2.php' method='POST'><td><input type='submit' name='upadte_output_2' value='تعديل' style='text-align: center;'><input type='hidden' name='Serial_NO' value=$row_data[3]></td></form>";
            } else {
                echo"<td>" . $row_data[$j] . "</td>";
            }
        }
        echo"</tr>";
    }
    echo '</tbody>';
    echo '</table>';
}
function display_officer_3ohda_table_with_name_of_receiver($sql) {
    $userState = $_SESSION['3ohda_userstate'];
    $edit = "";
    

    echo '<table border="3" width=100%><thead><tr><th>مسلسل</th><th>نوع الجهاز</th><th>الماركة والموديل</th> <th>رقم الجهاز</th><th>تاريخ الاستلام</th><th>تفصيلي</th><th>اسم المستلم</th></tr></thead>';
    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $row_count = mysql_num_rows($result);
    echo '<tbody>';
    for ($i = 0; $i < $row_count; $i++) {
        $row_data = mysql_fetch_row($result);

        $row_count1 = count($row_data);
        if ($userState == "admin") {
            $row_count1 = count($row_data) + 2;
        }

        echo"<tr>";
        for ($j = 0; $j < $row_count1; $j++) {
            if ($j == 0) {
                $count = $i + 1;
                echo"<td>" . $count . "</td>";
            } elseif ($j == 5) {
                echo"<td><a href='" . "../../../" . $row_data[$j] . "' target='_blank'>تفصيلى</a></td>";
            } elseif ($j == 6) {
                echo"<td>" . htmlspecialchars($row_data[$j]) . "</td>";
            } elseif ($j == 7) {
            } elseif ($j == 8) {
                echo "<form action='../../update/edit_device/edit_output/submit_edit_output.php' method='POST'><td><input type='submit' name='upadte_output' value='نقل عهدة' style='text-align: center;'><input type='hidden' name='Device_ID' value=$row_data[0]></td></form>";
            } else {
                echo"<td>" . $row_data[$j] . "</td>";
            }
        }
        echo"</tr>";
    }
    echo '</tbody>';
    echo '</table>';
}
function display_consumed_3ohda_table($sql) {
    $userState = $_SESSION['3ohda_userstate'];
    $edit = "";
    if ($userState == "admin") {
        $edit = "<th>تعديل</th>";
    }
    echo '<table border="3" width=100%><thead><tr><th>مسلسل</th><th>نوع الجهاز</th><th>الماركة والموديل</th> <th>الكمية</th><th>تاريخ الاستلام</th><th>تفصيلي</th>' . $edit . '</tr></thead>';
    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $row_count = mysql_num_rows($result);
    echo '<tbody>';
    for ($i = 0; $i < $row_count; $i++) {
        $row_data = mysql_fetch_row($result);
        $row_count1 = count($row_data);
        if ($userState == "admin") {
            $row_count1 = count($row_data) + 1;
        }
        echo"<tr>";
        for ($j = 0; $j < $row_count1; $j++) {
            if ($j == 0) {
                $count = $i + 1;
                echo"<td>" . $count . "</td>";
            } elseif ($j == 5) {
                echo"<td><a href='" . "../../../" . $row_data[$j] . "' target='_blank'>تفصيلى</a></td>"; //Discount_PDF path
            } elseif ($j == 6) {
                echo "<form action='upadteOut2.php' method='POST'><td><input type='submit' name='deleteOut2_consumed_device' value='حذف وارجاع فى عهدة الجهة' style='text-align: center;'><input type='hidden' name='output_2_ID' value=$row_data[0]></td></form>";
            } else {
                echo"<td>" . $row_data[$j] . "</td>";
            }
        }
        echo"</tr>";
    }
    echo '</tbody>';
    echo '</table>';
}

function display_discount_table($sql) {
    $userState = $_SESSION['3ohda_userstate'];
    $edit = "";
    if ($userState == "admin") {
        $edit = "<th>تعديل</th>";
    }

    echo '<table border="3" width=100%><thead><tr><th>مسلسل</th><th>رقم الاذن</th><th>تاريخ الخصم</th><th>نوع الجهاز</th><th>الماركة والموديل</th> <th>رقم الجهاز</th><th>تفصيلي</th>' . $edit . '</tr></thead>';
    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $row_count = mysql_num_rows($result);
    echo '<tbody>';
    for ($i = 0; $i < $row_count; $i++) {
        $row_data = mysql_fetch_row($result);

        echo"<tr>";

        $count_row_data = count($row_data);
        if ($userState == "admin") {
            $count_row_data = count($row_data) + 1;
        }

        for ($j = 0; $j < $count_row_data; $j++) {
            if ($j == 0) {
                $count = $i + 1;
                echo"<td>" . $count . "</td>";
            } elseif ($j == 6) {
                echo"<td><a href='" . "../../../" . $row_data[$j] . "' target='_blank'>تفصيلى</a></td>"; //Discount_PDF path
            } elseif ($j == 7) {
                echo "<form action='../../update/edit_device/edit_discount/submit_edit_discount.php' method='POST'><td><input type='submit' name='upadte_discount' value='تعديل' style='text-align: center;'><input type='hidden' name='Device_ID' value=$row_data[0]></td></form>";
            } else {
                echo"<td>" . $row_data[$j] . "</td>";
            }
        }
        echo"</tr>";
    }
    echo '</tbody>';
    echo '</table>';
    echo '<br><table border="3"><thead><tr><th>الاجمالى</th></tr></thead><tr><td>' . $row_count . '</td></tr></table>';
}

function display_rubish_table($sql) {
    $userState = $_SESSION['3ohda_userstate'];
    $edit = "";
    if ($userState == "admin") {
        $edit = "<th>تعديل</th>";
    }

    echo '<table border="3" width=100%><thead><tr><th>مسلسل</th><th>نوع الجهاز</th><th>الماركة والموديل</th> <th>رقم الجهاز</th><th>تفصيلي</th>' . $edit . '</tr></thead>';
    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $row_count = mysql_num_rows($result);
    echo '<tbody>';
    for ($i = 0; $i < $row_count; $i++) {
        $row_data = mysql_fetch_row($result);

        echo"<tr>";

        $count_row_data = count($row_data);

        if ($userState == "admin") {
            $count_row_data = count($row_data) + 1;
        }

        for ($j = 0; $j < $count_row_data; $j++) {
            if ($j == 0) {
                $count = $i + 1;
                echo"<td>" . $count . "</td>";
            } elseif ($j == 4) {
                echo"<td><a href='" . "../../../" . $row_data[$j] . " ' target='_blank'>تفصيلى</a></td>"; //Discount_PDF path
            } elseif ($j == 5) {
                echo "<form action='../../update/edit_device/edit_rubish/submit_edit_rubish.php' method='POST'><td><input type='submit' name='upadte_rubish' value='تعديل' style='text-align: center;'><input type='hidden' name='Device_ID' value=$row_data[0]></td></form>";
            } else {
                echo"<td>" . $row_data[$j] . "</td>";
            }
        }
        echo"</tr>";
    }
    echo '</tbody>';
    echo '</table>';

//    echo '<br><table border="3"><thead><tr><th>الاجمالى</th></tr></thead><tr><td>' . $row_count . '</td></tr></table>';
}

function display_rubish_table2($sql, $Condition) {
    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $row_count = mysql_num_rows($result);

    for ($i = 0; $i < $row_count; $i++) {
        echo '<table border="3" width=50%><thead><tr><th>تاريخ التخريد</th><th>رقم الاذن</th></tr></thead>';
        echo '<tbody>';
        $row_data = mysql_fetch_row($result);
        $Rubish_Date = $row_data[0];
        $NO_8_Store = $row_data[1];
        echo"<tr>";
        for ($j = 0; $j < count($row_data); $j++) {

            echo"<td>" . $row_data[$j] . "</td>";
        }
        echo"</tr>";
        echo '</tbody></table>';

        $sql = "select Device_ID,Type_NM,Model_NM,Serial_NO,Rubish_PDF from device NATURAL JOIN rubish NATURAL JOIN device_type join device_model on device.Model_ID=device_model.Model_ID WHERE state=2 AND Rubish_Date='$Rubish_Date' AND NO_8_Store=$NO_8_Store AND $Condition";

        display_rubish_table($sql);
        echo '<br>';
        echo '<br>';
        echo '<br>';
    }




//    echo '<br><table border="3"><thead><tr><th>الاجمالى</th></tr></thead><tr><td>' . $row_count . '</td></tr></table>';
}

function display_past_out_dir_table($sql) {
    $userState = $_SESSION['3ohda_userstate'];
    $edit = "";
    if ($userState == "admin") {
        $edit = "<th>تعديل</th>";
    }
    echo '<table border="3" width=100%><thead><tr><th>مسلسل</th><th>نوع الجهاز</th><th>الماركة والموديل</th> <th>رقم الجهاز</th><th>الجهة الرئيسية</th><th>الجهة الفرعية</th><th>رقم 1مخازن</th><th>رقم 2 مخازن</th><th>تاريخ الصرف</th><th>تفصيلي</th>' . $edit . '</tr></thead>';
    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $row_count = mysql_num_rows($result);
    echo '<tbody>';
    for ($i = 0; $i < $row_count; $i++) {
        $row_data = mysql_fetch_row($result);

        $count_row_data = count($row_data);
        if ($userState == "admin") {
            $count_row_data = count($row_data) + 1;
        }
        echo"<tr>";
        for ($j = 0; $j < $count_row_data; $j++) {
            if ($j == 0) {
                $count = $i + 1;
                echo"<td>" . $count . "</td>";
            } elseif ($j == 4) {
                $Dir1_ID = $row_data[$j];
                $Dir1_NM = getName("dir1_output", "Dir1_NM", "Dir1_ID", $Dir1_ID);
                echo"<td>" . $Dir1_NM . "</td>";
            } elseif ($j == 5) {
                $Dir2_ID = $row_data[$j];
                $Dir2_NM = getName("dir2_output", "Dir2_NM", "Dir2_ID", $Dir2_ID);
                echo"<td>" . $Dir2_NM . "</td>";
            } elseif ($j == 9) {
                echo"<td><a href='" . "../../../" . $row_data[$j] . "' target='_blank'>تفصيلى</a></td>"; //Discount_PDF path
            } elseif ($j == 10) {
                echo "<form action='../../update/edit_device/edit_past_out_dir/submit_edit_past_out_dir.php' method='POST'><td><input type='submit' name='upadte_Past_out_dir' value='تعديل' style='text-align: center;'><input type='hidden' name='Past_out_dir_ID' value=$row_data[0]></td></form>";
            } else {
                echo"<td>" . $row_data[$j] . "</td>";
            }
        }
        echo"</tr>";
    }
    echo '</tbody>';
    echo '</table>';
    echo '<br><table border="3"><thead><tr><th>الاجمالى</th></tr></thead><tr><td>' . $row_count . '</td></tr></table>';
}

function displayDeviceTable($sql) {
    echo '<form><table border="3" width=100%><thead><tr><th>مسلسل</th><th>جهة التوريد</th><th>الفئة</th> <th>موديل الجهاز</th><th>رقم الجهاز</th><th>رقم الممارسة</th><th>تاريخ التوريد</th><th>رقم 1 مخازن</th><th>رقم 2 مخازن</th><th>صورة السند</th></tr></thead>';
    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $row_count = mysql_num_rows($result);
    echo '<tbody>';
    for ($i = 0; $i < $row_count; $i++) {
        $row_data = mysql_fetch_row($result);

        echo"<tr>";

        for ($j = 0; $j < count($row_data); $j++) {
            if ($j == 9) {
                echo"<td><a href='" . "../../../" . $row_data[$j] . "' target='_blank'>تفصيلى</a></td>"; //Input_PDF path
            } elseif ($j == 0) {
                $count = $i + 1;
                echo"<td>" . $count . "</td>";
            } else {
                echo"<td>" . $row_data[$j] . "</td>";
            }
        }
        echo"</tr>";
    }
    echo '</tbody>';
    echo '</table></form>';
}

function display_CONSUMED_DEVICE_Table($sql) {
    $userState = $_SESSION['3ohda_userstate'];

    $edit = "";
    if ($userState == "admin") {
        $edit = "<th>تعديل</th>";
    }
    echo '<table border="3" width=100%><thead><tr><th>مسلسل</th><th>جهة التوريد</th><th>الفئة</th> <th>الموديل</th><th>الكمية</th><th>رقم الممارسة</th><th>تاريخ التوريد</th><th>رقم 1 مخازن</th><th>رقم 2 مخازن</th><th>صورة السند</th>' . $edit . '</tr></thead>';
    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $row_count = mysql_num_rows($result);
    echo '<tbody>';
    for ($i = 0; $i < $row_count; $i++) {
        $row_data = mysql_fetch_row($result);

        $count_row_data = count($row_data);
        if ($userState == "admin") {
            $count_row_data = count($row_data) + 1;
        }
        echo"<tr>";

        for ($j = 0; $j < $count_row_data; $j++) {
            if ($j == 0) {
                $count = $i + 1;
                echo"<td>" . $count . "</td>";
            } elseif ($j == 9) {
                echo"<td><a href='" . "../../../" . $row_data[$j] . "' target='_blank'>تفصيلى</a></td>"; //Input_PDF path
            } elseif ($j == 10) {
                echo "<form action='../../update/edit_device/edit_consumed_device/submit_edit_consumed_device.php' method='POST'><td><input type='submit' name='update_consumed' value='تعديل' style='text-align: center;'><input type='hidden' name='Device_ID' value=$row_data[0]></td></form>";
            } else {
                echo"<td>" . $row_data[$j] . "</td>";
            }
        }
        echo"</tr>";
    }
    echo '</tbody>';
    echo '</table>';
}

function display_CONSUMED_output_Table($sql) {
    $userState = $_SESSION['3ohda_userstate'];

    $edit = "";
    if ($userState == "admin") {
        $edit = "<th>تعديل</th>";
    }
    echo '<table border="3" width=100%><thead><tr><th>مسلسل</th><th>الجهة الرئيسية</th><th>الجهة الفرعية</th><th>الفئة</th> <th>الموديل</th><th>الكمية</th><th>تاريخ الصرف</th><th>رقم 1 مخازن</th><th>رقم 2 مخازن</th><th>صورة السند</th>' . $edit . '</tr></thead>';
    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $row_count = mysql_num_rows($result);
    echo '<tbody>';
    for ($i = 0; $i < $row_count; $i++) {
        $row_data = mysql_fetch_row($result);

        $count_row_data = count($row_data);
        if ($userState == "admin") {
            $count_row_data = count($row_data) + 1;
        }

        echo"<tr>";
        for ($j = 0; $j < $count_row_data; $j++) {
            if ($j == 0) {
                $count = $i + 1;
                echo"<td>" . $count . "</td>";
            } elseif ($j == 9) {
                echo"<td><a href='" . "../../../" . $row_data[$j] . "' target='_blank'>تفصيلى</a></td>"; //Input_PDF path
            } elseif ($j == 10) {
                echo "<form action='../../update/edit_device/edit_consumed_device_output/submit_edit_consumed_device_out.php' method='POST'><td><input type='submit' name='upadte_consumed_out' value='تعديل' style='text-align: center;'><input type='hidden' name='Output_ID' value=$row_data[0]></td></form>";
            } else {
                echo"<td>" . $row_data[$j] . "</td>";
            }
        }
        echo"</tr>";
    }
    echo '</tbody>';
    echo '</table>';
}

function displayTotalTable($sql) {
    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $total = 0;
    $row_count = mysql_num_rows($result);
    echo '<br><table border="3" width=20%><thead><tr><th>الفئة</th><th>الرصيد</th></tr>';

    for ($i = 0; $i < $row_count; $i++) {
        $row_data = mysql_fetch_row($result);
        echo"<tr>";
        for ($j = 0; $j < count($row_data); $j++) {
            echo"<td>" . $row_data[$j] . "</td>";
        }
        $total = $total + $row_data[1];
        echo"</tr>";
    }
    echo '<tr><th>الاجمالى</th><th>' . $total . '</th></tr></table>';
    echo '</table>';
}

function display_consumed_store_Table($sql) {
    //    echo $sql;
    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $total = 0;
    $row_count = mysql_num_rows($result);
    echo '<br><table border="3" width=100%><thead><tr><th>مسلسل</th><th>الفئة</th><th>الماركة والموديل</th><th>الكمية</th><th>الوحدة</th></tr>';

    for ($i = 0; $i < $row_count; $i++) {
        $row_data = mysql_fetch_row($result);
        echo"<tr>";
        for ($j = 0; $j < count($row_data); $j++) {

            if ($j == 0) {
                echo"<td>" . ($i + 1) . "</td>";
            } else {
                echo"<td>" . $row_data[$j] . "</td>";
            }
        }

        echo"</tr>";
    }

    echo '</table>';
}

function displayTotalTable2($table, $Condition) {

    $sql = "select Type_NM,COUNT(Type_NM) from $table WHERE $Condition  GROUP by Type_NM ";

    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $total = 0;
    $row_count = mysql_num_rows($result);


    for ($i = 0; $i < $row_count; $i++) {
        $row_data = mysql_fetch_row($result);
        $Type_NM = $row_data[0];
        echo '<br><table border="3" width=30%><thead><tr><th>الفئة</th><td>' . $Type_NM . ' </td></tr></thead></table>';

        $total = $total + $row_data[1];
        $total_of_type = $row_data[1];

        $sql2 = "SELECT Model_NM,COUNT(*) FROM $table WHERE $Condition AND Type_NM='$Type_NM' GROUP BY Model_NM";
        $result2 = mysql_query($sql2);
        if (!$result) {
            die("Database access failed: " . mysql_error());
        }
        $row_count2 = mysql_num_rows($result2);

        echo '<table border="3" width=100%><thead><tr><th>الموديل</th><th>الرصيد</th></tr></thead>';
        for ($i2 = 0; $i2 < $row_count2; $i2++) {
            $row_data2 = mysql_fetch_row($result2);
            echo "<tr>";
            for ($j = 0; $j < count($row_data2); $j++) {
                echo"<td>" . $row_data2[$j] . "</td>";
            }
            echo"</tr>";
        }
        echo "<tr><td><b>الاجمالى </td><td>" . $total_of_type . "</td></tr>";
        echo"</table>";


//echo '<tr><th>الاجمالى</th><th>' . $total . '</th></tr></table>';
//    echo '</table>';
    }
    echo '<br><table border="3" width=30%><thead><tr><th>الاجمالى العام</th><th>' . $total . '</th></tr></thead></table>';
}

function display_follow_model($sql, $Condition) {

    $standard = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
    $eastern_arabic_symbols = array("٠", "١", "٢", "٣", "٤", "٥", "٦", "٧", "٨", "٩");

    $total_input = 0;
    $total_output = 0;

    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $row_count = mysql_num_rows($result);

    $count_item = 0;

    for ($i = 0; $i < $row_count; $i++) {


        $count_item++;
        $row_data = mysql_fetch_row($result);
        $Input_Date = $row_data[0];
//        echo $sql;
        $Condition = $Condition . " AND state!=3";
        $sql2 = "SELECT Serial_NO,NO_1_Store,NO_2_Store FROM device WHERE  $Condition AND Input_Date='$Input_Date' ";
        $result2 = mysql_query($sql2);
        if (!$result2) {
            die("Database access failed: " . mysql_error());
        }
        $row_count2 = mysql_num_rows($result2);
        $row_data2 = mysql_fetch_row($result2);
        $no1_store = str_replace($standard, $eastern_arabic_symbols, $row_data2[1]);
        $no2_store = str_replace($standard, $eastern_arabic_symbols, $row_data2[2]);
        echo '<br><table border="3" width=30%><thead><tr><th>مسلسل</th><th>تاريخ التوريد</th><th>رقم 1 مخازن</th><th>رقم 2 مخازن</th><th>الاجمالى</th></tr></thead>'
        . '<tr style="color: red;"><td>' . str_replace($standard, $eastern_arabic_symbols, $count_item) . '</td><td>' . $Input_Date . '</td><td>' . $no1_store . '</td><td>' . $no2_store . '</td><td>' . str_replace($standard, $eastern_arabic_symbols, $row_count2) . '</td></tr></table>';
        $result2 = mysql_query($sql2);
        echo '<table border="3" width=100%><thead><tr><th>رقم الجهاز</th></tr></thead>';
        for ($i2 = 0; $i2 < $row_count2; $i2++) {
            $row_data2 = mysql_fetch_row($result2);
            echo "<tr>";
            echo"<td  style='color: red;'>" . $row_data2[0] . "</td>";
            echo"</tr>";
        }
        echo"</table>";
        $total_input = $total_input + $row_count2;


        $sql3 = "SELECT Output_Date FROM device NATURAL JOIN output WHERE  Input_Date='$Input_Date' AND  $Condition GROUP BY Output_Date";
        $result3 = mysql_query($sql3);

        if (!$result3) {
            die("Database access failed: " . mysql_error());
        }
        $row_count3 = mysql_num_rows($result3);
        for ($i3 = 0; $i3 < $row_count3; $i3++) {
//             echo $sql3;
            $row_data3 = mysql_fetch_row($result3);
            $Output_Date = $row_data3[0];
            echo '<br><table border="3" width=30%><thead><tr><th>تاريخ الصرف</th><td style="color: blue;">' . $Output_Date . ' </td></tr></thead></table>';
            $table_head = '<table border="3" width=100%><thead><tr><th>مسلسل</th><th>الجهة الرئيسية</th><th>الجهة الفرعية</th><th>رقم 1مخازن للصرف</th><th>رقم 2مخازن للصرف</th><th>رقم الجهاز</th></tr></thead>';
            $sql4 = "SELECT device.Device_ID,Dir1_ID,Dir2_ID,OUT_NO_1_Store,OUT_NO_2_Store,Serial_NO FROM device NATURAL JOIN output  WHERE Output_Date='$Output_Date' AND Input_Date='$Input_Date' AND $Condition  ";
//            echo $sql4;
            $result4 = mysql_query($sql4);
            if (!$result4) {
                die("Database access failed: " . mysql_error());
            }
            $row_count4 = mysql_num_rows($result4);
            for ($i4 = 0; $i4 < $row_count4; $i4++) {
                $row_data4 = mysql_fetch_row($result4);
                $count_item++;
                $Device_ID = $row_data4 [0];

                $query = "SELECT Past_Dir1_ID,Past_Dir2_ID from past_out_dir where Device_ID='$Device_ID'";
                $query_result = mysql_query($query);
                if (!$query_result) {
                    die("Database access failed: " . mysql_error());
                }
                $query_row_count = mysql_num_rows($query_result);



                for ($query_j = 0; $query_j < $query_row_count; $query_j++) {
                    $query_row_data = mysql_fetch_row($query_result);
                    $Dir1_ID = $query_row_data [0];
                    $Dir2_ID = $query_row_data [1];
                    $Dir1_NM = getName("dir1_output", "Dir1_NM", "Dir1_ID", $Dir1_ID);
                    $Dir2_NM = getName("dir2_output", "Dir2_NM", "Dir2_ID", $Dir2_ID);
                }



//                $query2 = "SELECT Rubish_ID from rubish where Device_ID='$Device_ID'";
//                $query_result2 = mysql_query($query2);
//                if (!$query_result2) {
//                    die("Database access failed: " . mysql_error());
//                }
//                $query_row_count2 = mysql_num_rows($query_result2);
//                if ($query_row_count2 != 0) { //the device is rubbished
//                    echo $table_head . "<tr style='color: darkorchid;'>";
//                }

                if ($query_row_count != 0) { // the device is trasnsported and not rubished
                    $table_head = '<table border="3" width=100%><thead><tr><th>مسلسل</th><th> الجهة الرئيسية الحالية</th><th>الجهة الفرعية الحالية</th><th>رقم 1مخازن للصرف</th><th>رقم 2مخازن للصرف</th><th>رقم الجهاز</th><th> الجهة الرئيسية السابقة</th><th>الجهة الفرعية السابقة</th></tr></thead>';
                    echo $table_head . "<tr style='color: green;'>";
                } else {
                    echo $table_head . "<tr style='color: blue;'>";
                }


                if ($query_row_count != 0) {

                    for ($j4 = 0; $j4 < 8; $j4++) {
                        echo "<td>";
                        if ($j4 == 0) {
                            echo str_replace($standard, $eastern_arabic_symbols, $count_item);
                        } elseif ($j4 == 1) {
                            echo getName("dir1_output", "Dir1_NM", "Dir1_ID", $row_data4 [1]);
                        } elseif ($j4 == 2) {
                            echo getName("dir2_output", "Dir2_NM", "Dir2_ID", $row_data4 [2]);
                        } elseif ($j4 == 5) {
                            echo $row_data4 [5];
                        } elseif ($j4 == 6) {
                            echo $Dir1_NM;
                        } elseif ($j4 == 7) {
                            echo $Dir2_NM;
                        } else {
                            echo str_replace($standard, $eastern_arabic_symbols, $row_data4 [$j4]);
                        }
                        echo "</td>";
                    }
                } else {
                    for ($j4 = 0; $j4 < 6; $j4++) {

                        echo "<td>";
                        if ($j4 == 0) {
                            echo str_replace($standard, $eastern_arabic_symbols, $count_item);
                        } elseif ($j4 == 1) {
                            echo getName("dir1_output", "Dir1_NM", "Dir1_ID", $row_data4 [1]);
                        } elseif ($j4 == 2) {
                            echo getName("dir2_output", "Dir2_NM", "Dir2_ID", $row_data4 [2]);
                        } elseif ($j4 == count($row_data4) - 1) {
                            echo $row_data4 [$j4];
                        } else {
                            echo str_replace($standard, $eastern_arabic_symbols, $row_data4 [$j4]);
                        }
                        echo "</td>";
                    }
                }

                echo"</tr>";
            }

            echo"</table>";
            $total_output = $total_output + $row_count4;
        }

        $sql3 = "SELECT Rubish_Date FROM device NATURAL JOIN rubish WHERE  Input_Date='$Input_Date' AND  $Condition GROUP BY Rubish_Date";
        $result3 = mysql_query($sql3);
        if (!$result3) {
            die("Database access failed: " . mysql_error());
        }
        $row_count3 = mysql_num_rows($result3);
        for ($i3 = 0; $i3 < $row_count3; $i3++) {
            $row_data3 = mysql_fetch_row($result3);
            $Rubish_Date = $row_data3[0];
            echo '<br><table border="3" width=30%><thead><tr><th>تاريخ التخريد</th><td style="color: peru ;">' . $Rubish_Date . ' </td></tr></thead></table>';
            $table_head = '<table border="3" width=100%><thead><tr><th>مسلسل</th><th>رقم 8 مخازن</th><th>رقم الجهاز</th></tr></thead>';
            $sql4 = "SELECT device.Device_ID,NO_8_Store,Serial_NO FROM device NATURAL JOIN rubish WHERE Rubish_Date='$Rubish_Date' AND Input_Date='$Input_Date' AND $Condition  ";

            $result4 = mysql_query($sql4);
            if (!$result4) {
                die("Database access failed: " . mysql_error());
            }
            $row_count4 = mysql_num_rows($result4);
            for ($i4 = 0; $i4 < $row_count4; $i4++) {
                $row_data4 = mysql_fetch_row($result4);
                $count_item++;
                $Device_ID = $row_data4 [0];


//                $query2 = "SELECT Rubish_ID from rubish where Device_ID='$Device_ID'";
//                $query_result2 = mysql_query($query2);
//                if (!$query_result2) {
//                    die("Database access failed: " . mysql_error());
//                }
//                $query_row_count2 = mysql_num_rows($query_result2);
//                if ($query_row_count2 != 0) { //the device is rubbished
//                    echo $table_head . "<tr style='color: darkorchid;'>";
//                }
//                if ($query_row_count != 0) { // the device is trasnsported and not rubished
//                    $table_head = '<table border="3" width=100%><thead><tr><th>مسلسل</th><th> الجهة الرئيسية الحالية</th><th>الجهة الفرعية الحالية</th><th>رقم 1مخازن للصرف</th><th>رقم 2مخازن للصرف</th><th>رقم الجهاز</th><th> الجهة الرئيسية السابقة</th><th>الجهة الفرعية السابقة</th></tr></thead>';
//                    echo $table_head . "<tr style='color: green;'>";
//                } else {
//                    echo $table_head . "<tr style='color: blue;'>";
//                }

                echo $table_head . "<tr style='color: peru;'>";


                for ($j4 = 0; $j4 < 3; $j4++) {
                    echo "<td>";
                    if ($j4 == 0) {
                        echo str_replace($standard, $eastern_arabic_symbols, $count_item);
                    } elseif ($j4 == 2) {
                        echo $row_data4 [$j4];
                    } else {
                        echo str_replace($standard, $eastern_arabic_symbols, $row_data4 [$j4]);
                    }
                    echo "</td>";
                }


                echo"</tr>";
            }

            echo"</table>";
            $total_output = $total_output + $row_count4;
        }

        echo '<br><br><br>';
        echo'<HR  size="5" WIDTH="100%"  style="background: #7f7f7f">';
    }

    $store = $total_input - $total_output;

    echo '<br><table border="3" width=30%><thead><tr><th>اجمالى الوارد</th><th>اجمالى المنصرف</th><th>رصيد المخزن</th></tr></thead>';
    echo '<tbody><tr><td>' . str_replace($standard, $eastern_arabic_symbols, $total_input) . '</td><td>' . str_replace($standard, $eastern_arabic_symbols, $total_output) . '</td><td>' . str_replace($standard, $eastern_arabic_symbols, $store) . '</td></tr></tbody>';
    echo '</table>';
}

function display_count_of_models($sql) {

    $result = mysql_query($sql);

    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $total = 0;
    $row_count = mysql_num_rows($result);
    echo '<br><table border="3" width=100%><thead><tr><th>المسلسل</th><th>الماركة والموديل</th><th>الرصيد</th></tr>';
    for ($i = 0; $i < $row_count; $i++) {
        $row_data = mysql_fetch_row($result);
        echo"<tr>";
        for ($j = 0; $j < count($row_data); $j++) {
            if ($j == 0) {
                echo"<td>" . ($i + 1) . "</td>";
            } else {
                echo"<td>" . $row_data[$j] . "</td>";
            }
        }
        $total = $total + $row_data[2];
        echo"</tr>";
    }
    echo '<tr><th COLSPAN=2>الاجمالى</th><th>' . $total . '</th></tr></table>';
    echo '</table>';
}

function display_back_process_table($sql) {
    $userState = $_SESSION['3ohda_userstate'];
    $edit = "";
    if ($userState == "admin") {
        $edit = "<th>تعديل</th>";
    }
    echo '<table border="3" width=100%><thead><tr><th>م</th><th>رقم 8 مخازن</th><th>تاريخ الارتجاع</th><th>نوع الجهاز</th><th>الماركة والموديل</th> <th>رقم الجهاز</th><th>الجهة الرئيسية</th><th>الجهة الفرعية</th><th>تفصيلي</th>' . $edit . '</tr></thead>';
    $result = mysql_query($sql);
    if (!$result) {
        die("Database access failed: " . mysql_error());
    }
    $row_count = mysql_num_rows($result);
    echo '<tbody>';
    for ($i = 0; $i < $row_count; $i++) {
        $row_data = mysql_fetch_row($result);
        echo"<tr>";
        $count_row_data = count($row_data);
        if ($userState == "admin") {
            $count_row_data = count($row_data) + 1;
        }
        for ($j = 0; $j < $count_row_data; $j++) {
            if ($j == 0) {
                $count = $i + 1;
                echo"<td>" . $count . "</td>";
            } elseif ($j == 8) {
                echo"<td><a href='" . "../../../" . $row_data[$j] . "' target='_blank'>تفصيلى</a></td>"; //Discount_PDF path
            } elseif ($j == 9) {
                echo "<form action='../../update/edit_device/edit_discount/submit_edit_discount.php' method='POST'><td><input type='submit' name='upadte_discount' value='تعديل' style='text-align: center;'><input type='hidden' name='Device_ID' value=$row_data[0]></td></form>";
            } else {
                echo"<td>" . $row_data[$j] . "</td>";
            }
        }
        echo"</tr>";
    }
    echo '</tbody>';
    echo '</table>';
    echo '<br><table border="3"><thead><tr><th>الاجمالى</th></tr></thead><tr><td>' . $row_count . '</td></tr></table>';
}