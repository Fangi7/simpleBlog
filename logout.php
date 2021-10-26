<?php
    header("refresh:5; url=/hw4/login.php ");
    echo "<h1>You will be signed out after five seconds.</h1>";
    echo "<h3><a href='/hw4/login.php'>If you not successfully logged out, please click here.</a></h3>";
    session_start();
    // set cookie
    setcookie("logged", false);
    setcookie("loginName", "");
    setcookie("pwd", "");
    unset($_SESSION['username']);   //將「指定」的session清除

    session_destroy();      	   //清除使用中的session
?>