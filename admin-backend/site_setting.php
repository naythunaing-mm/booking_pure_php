<?php
    session_start();
    require('../require/common.php');
    require('../require/connect.php');
    require('../require/check_authentication.php');
    require('../require/include_function.php');

    $error       = false;
    $err_msg     = "";
    $process_err = false; 
    $table       = 'hotel_setting';
    $setting_img = "";
    if(isset($_POST['form-sub']) && $_POST['form-sub'] == 1){
        
        $process_err   = false;
        $name          = $mysqli->real_escape_string($_POST['name']);
        $email         = $mysqli->real_escape_string($_POST['email']);
        $address       = $mysqli->real_escape_string($_POST['address']);
        $online_phone  = $mysqli->real_escape_string($_POST['online_phone']);
        $outline_phone = $mysqli->real_escape_string($_POST['outline_phone']);
        $size_unit     = $mysqli->real_escape_string($_POST['size_unit']);
        $occupancy     = $mysqli->real_escape_string($_POST['occupancy']);
        $price_unit    = $mysqli->real_escape_string($_POST['price_unit']);
        $checkin       = $mysqli->real_escape_string($_POST['checkin']);
        $checkout      = $mysqli->real_escape_string($_POST['checkout']);
        $file          = $_FILES['file'];

        $sql    = "SELECT count(id) AS total FROM `hotel_setting`";
        $result = $mysqli->query($sql);
        $row    = $result->fetch_assoc();
        $total  = $row['total'];

    if($total > 0 ) { 
            $id = $_POST['id'];
            $upload_process = false;
            if($file['name'] != '')   {
                $file_name       = $file['name'];
                $tmp_name        = $file['tmp_name'];
                $check_extension = checkImageExtexsion($file_name,$tmp_name);
                
                if($check_extension['error'] == false){
                    $unique_name = date('Ymd_His') . "_" . uniqid() . "." . $check_extension['extension'];
                    $upload_process = true;
                } else {
                    $process_err = true;
                    $error       = true;
                    $err_msg     .= "Please Upload valid image <br />";
                        
                }
            } 
        if($process_err == false) {
            if($upload_process == false) {
                $update_data = array(
                    'name'               => $name,
                    'email'              => $email,
                    'address'            => $address,
                    'checkin_time'       => $checkin,
                    'checkout_time'      => $checkout,
                    'online_phone'       => $online_phone,
                    'outline_phone'      => $outline_phone,
                    'room_size_unit'     => $size_unit,
                    'occupancy'          => $occupancy,
                    'price_unit'         => $price_unit,

                );
            } else {
                $update_data = array(
                    'name'               => $name,
                    'email'              => $email,
                    'address'            => $address,
                    'checkin_time'       => $checkin,
                    'checkout_time'      => $checkout,
                    'online_phone'       => $online_phone,
                    'outline_phone'      => $outline_phone,
                    'room_size_unit'     => $size_unit,
                    'occupancy'          => $occupancy,
                    'price_unit'         => $price_unit,
                    'logo_path'          => $unique_name,
                );
            }
            
            $update = updateQuery($update_data,$table,$id,$mysqli);
            if($update){
                if($upload_process == true) {
                    $upload_path = "../assets/upload-img/";
                    if(!file_exists($upload_path)){
                        mkdir($upload_path,0777,true);
                    }
                    if(move_uploaded_file($file['tmp_name'],$upload_path . $unique_name)){
                        $sourceImagepath = $upload_path . $unique_name;
                        $destinationImagePath = $upload_path . $unique_name;
                        $watermark = "../assets/watermark/sg.jpg";
                        cropAndResizeImage($sourceImagepath,$destinationImagePath,$logo_height,$logo_width);
                        $url = $cp_base_url . "site_setting.php?";
                        header("Refresh: 0; url=$url");
                    
                    }
                }
            }
        }
    } else {
        if($file['name'] != '')   {
            $file_name       = $file['name'];
            $tmp_name        = $file['tmp_name'];
            $check_extension = checkImageExtexsion($file_name,$tmp_name);
            
            if($check_extension['error'] == false){
                $unique_name = date('Ymd_His') . "_" . uniqid() . "." . $check_extension['extension'];
                $upload_process = true;
                } else {
                    $process_err = true;
                    $error       = true;
                    $err_msg     .= "Please Upload valid image <br />";
                        
                }
            }
            if($process_err == false) {
                $insert_data = [
                    'name'               => "'$name'",
                    'email'              => "'$email'",
                    'address'            => "'$address'",
                    'checkin_time'       => "'$checkin'",
                    'checkout_time'      => "'$checkout'",
                    'online_phone'       => "'$online_phone'",
                    'outline_phone'      => "'$outline_phone'",
                    'room_size_unit'     => "'$size_unit'",
                    'occupancy'          => "'$occupancy'",
                    'price_unit'         => "'$price_unit'",
                    'logo_path'          => "'$unique_name'",
                ];
                $insert = insertQuery($insert_data,$table,$mysqli);
           
            if($insert)
            {
                $upload_path = "../assets/upload-img/";
                if(!file_exists($upload_path)){
                    mkdir($upload_path,0777,true);
                }
                if(move_uploaded_file($file['tmp_name'],$upload_path . $unique_name)){
                    $sourceImagepath = $upload_path . $unique_name;
                    $destinationImagePath = $upload_path . $unique_name;
                    $watermark = "../assets/watermark/sg.jpg";
                    cropAndResizeImage($sourceImagepath,$destinationImagePath,$logo_height,$logo_width);
                    $url = $cp_base_url . "site_setting.php?";
                    header("Refresh: 0; url=$url");
                    exit();   
                }
            }
            
        }
      
    } 
}else {
        $name          = "";
        $email         = "";
        $address       = "";
        $checkin       = "";
        $checkout      = "";
        $online_phone  = "";
        $outline_phone = "";
        $size_unit     = "";
        $occupancy     = "";
        $price_unit    = "";
        $unique_name   = "";
        $select_column = [
            "id", "name", "email", "address", "checkin_time", "checkout_time",
            "online_phone", "outline_phone", "room_size_unit", "occupancy",
            "price_unit", "logo_path"
        ];
        $result = selectQuery($select_column,$table,$mysqli);
        $res_rows = $result->num_rows;
        if($res_rows > 0 ) {
            $row = $result->fetch_assoc();
            $id            = $row['id'];
            $name          = $row['name'];
            $email         = $row['email'];
            $address       = nl2br($row['address']);
            $checkin       = $row['checkin_time'];
            $checkout      = $row['checkout_time'];
            $online_phone  = $row['online_phone'];
            $outline_phone = $row['outline_phone'];
            $size_unit     = $row['room_size_unit'];
            $occupancy     = $row['occupancy'];
            $price_unit    = $row['price_unit'];
            $unique_name   = $row['logo_path'];
            $setting_img   = $base_url . "assets/upload-img/" . $unique_name;
        } 
    }

    $title    = "Site Setting";

    require('templates/cp_template_header.php');
    require('templates/cp_template_sidebar.php');
    require('templates/cp_template_topnav.php');
?>
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Hotel Room View</h3>
            </div>
        </div>
        <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="x_panel">
                        <div class="x_content">
                            <form action="<?php echo $cp_base_url; ?>site_setting.php" method="POST" id="form-create" enctype="multipart/form-data">
                                <span class="section">Site Setting</span>

                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align">Logo<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <div id="preview-wrapper">
                                            <div class="vertical-center" style="<?php if($setting_img == '')   {echo'display:block';} else { echo 'display:none';} ?>">
                                                <label class="file-choose" onclick="chooseFile()">Choose File</label>
                                            </div>
                                            <div class="" id="preview-img" style="<?php if($setting_img == '') {echo'display:none';} else { echo 'display:block';} ?>" >
                                            <label for="" class="change-img" onclick="changePhoto()">Change Photo</label>
                                                <img src="<?php echo $setting_img; ?>" alt="" id="upload-img">
                                            </div>
                                            <input type="file" id="thumb-file" name="file" style="display:none" onchange="uploadPhoto()">
                                         </div> 
                                    </div>
                                </div>

                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align" for="name">Web Site Name<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control name" name="name" placeholder="Please Fill Name" type="text" id="name" value="<?php echo $name;?>" />
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  label-error hide" id="name-error"><span class="error-msg"></span></label>
                                </div>  

                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align" for="email">Email<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control email" name="email" placeholder="example@gmail.com" type="email" id="email" value="<?php echo $email;?>"/>
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  label-error hide" id="email-error"><span class="error-msg"></span></label>
                                </div>

                                <div class="form-group row">
                                    <label class="control-label col-md-3 col-sm-3 label-align" for="address">Address<span class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 ">
                                        <textarea class="form-control address" rows="2" name="address" id="address" placeholder="Please Fill Address"><?php echo $address;?></textarea>
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  address-label-error hide" id="address-name-error"><span class="address-error-msg" style="color:red;"></span></label>
                                </div>

                                <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3 label-align" for="checkin">Checkin Time<span class="required">*</span>
                                    </label>
                                    <div class='input-group date col-md-6 col-sm-6' id='checkin'>
                                        <input type='text' class="form-control" name="checkin" value="<?php echo $checkin;?>" />
                                        <span class="input-group-addon">
                                        <span class="fa fa-calendar"></span>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group row">
                                <label class="control-label col-md-3 col-sm-3 label-align" for="checkin">Checkout Time<span class="required">*</span>
                                    </label>
                                    <div class='input-group date col-md-6 col-sm-6' id='checkout'>
                                        <input type='text' class="form-control" name="checkout" value="<?php echo $checkin;?>" />
                                        <span class="input-group-addon">
                                        <span class="fa fa-calendar"></span>
                                        </span>
                                    </div>
                                </div>

                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align" for="online">Online Phone<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control name" name="online_phone" placeholder="+95 XXXX XXX XXXX" type="text" id="online" value="<?php echo $online_phone;?>" />
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  label-error hide" id="online-error"><span class="online-error-msg"></span></label>
                                </div>  

                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align" for="outline">Outline Phone<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control name" name="outline_phone" placeholder="+1 XXX XXXX" type="text" id="name" value="<?php echo $outline_phone;?>" />
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  label-error hide" id="outline-error"><span class="outline-error-msg"></span></label>
                                </div>  
                                
                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align" for="size-unit">Room Size Unlit<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control unit" name="size_unit" placeholder="ex. mm²" type="text" id="size-unit"value="<?php echo $size_unit;?>"/>
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  label-error hide" id="unit-error"><span class="unit-error-msg"></span></label>
                                </div>  
                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align" for="people">Occupancy<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control name" name="occupancy" placeholder="ex. people" type="text" id="people" value="<?php echo $occupancy;?>"/>
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  label-error hide" id="people-error"><span class="people-error-msg"></span></label>
                                </div>  
                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align" for="price-unit">Price Unit<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control price_unit" name="price_unit" placeholder="ex. mmk" type="text" id="price-unit" value="<?php echo $price_unit;?>"/>
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  label-error hide" id="price-unit-error"><span class="price-uniterror-msg"></span></label>
                                </div>  

                                <div class="">
                                    <div class="form-group">
                                        <div class="col-md-6 offset-md-3">
                                            <button type='submit' class="btn btn-primary" id="submit-btn">Submit</button>
                                            <button type='reset' class="btn btn-success" id="reset">Reset</button>
                                            <input type="hidden" name="form-sub" value="1" />
                                            <?php 
                                                if(isset($id)){
                                                    ?>
                                                    <input type="hidden" name="id" value="<?php echo $id; ?>" />
                                                <?php }
                                             ?>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <!-- /page content -->


<?php require('templates/cp_template_footer.php'); ?>
<script>
     $('#checkin').datetimepicker({
        format: 'hh:mm A'
     });

     $('#checkout').datetimepicker({
        format: 'hh:mm A'
     });

    $(document).ready(function(){
        $('#submit-btn').click(function(){
            let error              = false
            const view_name        = $('.view_name').val();
            const view_name_length = view_name.length;
            if(view_name  == ''){
                $('.name-error-msg').text('')
                $('.name-error-msg').text('Please fill hotel room view name')
                $('.label-error').show()
                error     = true
            } else {
                $('.label-error').hide()
            }
            if(view_name != '' && view_name_length <3){
                $('.name-error-msg').text('')
                $('.name-error-msg').text('Name length is at least must be greather then three ')
                $('.label-error').show()
                error     = true
            }
            if(view_name != '' && view_name_length > 30){
                $('.name-error-msg').text('')
                $('.name-error-msg').text('Name length is must be less then 30')
                $('.label-error').show()
                error     = true
            }
            if(error == false){
                $('#form-create').submit();
            }
        })
        
        $('#reset').click(function(){
                $('.label-error').hide()
        }) 
    })
</script>
         <!-- pnotify -->
     <script src="<?php echo $base_url; ?>assets/backend/css/pnotify/pnotify.js"></script>
    <script src="<?php echo $base_url; ?>assets/backend/css/pnotify/pnotify.buttons.js"></script>
    <script src="<?php echo $base_url; ?>assets/backend/css/pnotify/pnotify.nonblock.js"></script>
    <script src="<?php echo $base_url; ?>assets/backend/js/pages/upload_img.js?v=20230802"></script>
<?php
    if($error == true){
      echo "
      <script>
            new PNotify({
            title: 'Error',
            text: '$err_msg',
            type: 'error',
            hide: false,
            styling: 'bootstrap3'
        }); 
      </script>";
    }
?>
</html>