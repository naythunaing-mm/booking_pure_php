<?php 
    $setting_col = [];
    $setting_sql = "SELECT * FROM `hotel_setting`";
    $setting_result = $mysqli->query($setting_sql);
    $setting_res_rows = $setting_result->num_rows;
    if($setting_res_rows > 0) {
        $setting_row = $setting_result->fetch_assoc();
        $setting_col = $setting_row;
    }
    
?>