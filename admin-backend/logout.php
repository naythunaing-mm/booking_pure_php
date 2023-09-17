<?php
    session_start();
    require('../require/common.php');
    session_unset();
    session_destroy();
    setcookie("user_name", "", time() - 3600, "/");
    setcookie("user_id", "", time() - 3600, "/");
    setcookie("user_emai", "", time() - 3600, "/");

    $url = $cp_base_url . "login.php";
    header("Refresh:0 ; url=$url");
    exit();
?>