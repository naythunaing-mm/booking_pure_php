<?php
    session_start();
    require('../require/connect.php');
    require('../require/check_authentication.php');
    require('../require/include_function.php');
    require('../require/common.php');

    $table = 'room_gallery';

    if(!isset($_GET['id'])){
        $url = $cp_base_url . "room_gallery.php?error";
        header("Refresh: 0; url=$url");
        exit();
    }
    $id          = (int)($_GET['id']);
    $room_id     = (int)($_GET['room-id']);
    $today_date  = date('Y-m-d H:i:s');
    $user_id     = (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : $_COOKIE['user_id'] ;
    $select_column = ["image"];
    $result = selectQueryById($select_column,$table,$id,$mysqli);
    $row = $result->fetch_assoc();
    $image_name = $row["image"];
    $update_data = [
        'deleted_at' => $today_date,
        'deleted_by' => $user_id,
    ];

    $update = updateQuery($update_data,$table,$id,$mysqli);

    if($update){
       
        $old_image_path = "../assets/upload-img/" . $room_id ."/" . $image_name; 
        unlink($old_image_path);
        $url = $cp_base_url . "room_gallery.php?id=" . $room_id;
        header("Refresh: 0; url=$url");
        exit();
    }
    
?>