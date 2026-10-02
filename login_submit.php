<html>
    <head>
        <meta charset="UTF-8">
        <title>Login</title>
        <link href="../config/_css/edit_code.css" rel="stylesheet" media="screen">  
    </head>

    <body dir="rtl">

        <header id="mainHeader">
            <img src="_images/لوجو ألوان جديد.png" alt="شعار الأمن المركزي" class="main-logo">
            <p style=" font-size : 25pt; color : #2c56ba "><b>قطاع الأمن المركزى</b></p>
            <p style=" font-size : 15pt; padding-right: 20px">الادارة العامة للأمانة القطاع</p>
            <p style=" font-size : 15pt; padding-right: 20px"> ادارة تكنولوجيا المعلومات</p>
        </header>


        <?php
        session_start();
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            require_once "../config/connect.php";

            $username = filter_var($_POST["username"], FILTER_SANITIZE_STRING);
            $password = filter_var($_POST["password"], FILTER_SANITIZE_STRING);

            $sql = "select User_ID, UserName, state from user where UserName = '$username' AND Password= '$password'";

            $result = mysql_query($sql);
            if (!$result)
                die("Database access failed: " . mysql_error());




            $row_data = mysql_fetch_row($result);

            if (!empty($row_data[0])) {
                echo '<h1 align="center">تم تسجيل الدخول بنجاح سيتم تحويلك تلقائي  </h1>' . '<h1 align="center"><a href="../views/main_page.php">الصفحة الرئيسية</a></h1>';
                $_SESSION['3ohda_userstate'] = $row_data[2];
                $_SESSION['3ohda_userid'] = $row_data[0];
                $_SESSION['3ohda_username'] = $row_data[1];
                header('REFRESH:1;URL=../views/main_page.php');
            } else {
                echo '<h2 >خطأ فى اسم المستخدم او كلمة المرور</h2>';
                echo '<h1 align="center"><a href="../index.php">المحاولة مرة اخرى</a></h1>';
            }
        } else {
            echo '<h2>لا يمكن الدخول الى هذه الصفحة مباشرة</h2>';
        }
        ?>


        <footer id="pageFooter">


            <p  align="center" >جميع الحقوق محفوظة لادارة تكنولوجيا المعلومات بقطاع الامن المركزى  2025 &copy;</p> 

        </footer>

    </body>
</html>