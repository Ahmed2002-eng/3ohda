<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">      
        <link href="config/_css/edit_code.css" rel="stylesheet" media="screen"> 
    </head>
    <body>
        <?php
        session_start();

        if (isset($_SESSION['3ohda_userstate'])) {
            header('location:views/main_page.php');
        } else {
            require_once "views/login.html";
        }
        ?>      
    </body>