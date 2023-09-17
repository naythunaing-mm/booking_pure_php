<?php
    session_start();
    require('../require/connect.php');
    require('../require/check_authentication.php');
    require('../require/include_function.php');
    require('../require/common.php');

    $table = 'view';

    if(!isset($_GET['id'])){
        $url = $cp_base_url . "view_listing.php?error";
        header("Refresh: 0; url=$url");
        exit();
    }
    $id          = (int)($_GET['id']);
    $id          = $mysqli->real_escape_string($id);
    $today_date  = date('Y-m-d H:i:s');
    $user_id     = (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : $_COOKIE['user_id'] ;
    $update_data = [
        'deleted_at' => $today_date,
        'deleted_by' => $user_id
    ];

    $result = updateQuery($update_data,$table,$id,$mysqli);

    if($result){
        $url = $cp_base_url . "view_listing.php?msg=delete";
        header("Refresh: 0; url=$url");
        exit();
    }
    while($row = $result->fetch_assoc()){
        $view_name = htmlspecialchars($row['view_name']);
    }
?>