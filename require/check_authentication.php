<?php
    $authentication         = false;
    if(
        isset($_SESSION['user_id']) && 
        isset($_SESSION['user_name']) && 
        isset($_SESSION['user_email'])
    ) {
        $check_sql          = "SELECT count(user_id) AS total FROM `user` WHERE user_id= '" .$_SESSION['user_id'] ."'";
        $check_res          = $mysqli->query($check_sql);
        while($row  = $check_res->fetch_assoc()){
            $user_total     = $row['total'];
            if($user_total >= 1){
                $authentication = true;
            }
        }
    }
    if(
        isset($_COOKIE['user_id']) &&
        isset($_COOKIE['user_name']) && 
        isset($_COOKIE['user_email'])
    ) {
        $check_sql  = "SELECT count(user_id) AS total FROM `user` WHERE user_id = '" . $_COOKIE['user_id'] . "'";
        $check_res  = $mysqli->query($check_sql);
        while($row = $check_res->fetch_assoc()){
            $user_total = $row['total'];
            if($user_total >= 1){
                $authentication = true;
            }
        }
    }
    if($authentication == false){
        $url          = $cp_base_url . "logout.php";
        header("Refresh: 0; url=$url");
        exit();
    }
?>