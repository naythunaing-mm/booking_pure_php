<?php
  session_start();
    require('../require/common.php');
    require('../require/connect.php');
    require('../require/setting.php');
    require('../require/check_authentication.php');
    require('../require/include_function.php');
    
    $error       = false;
    $process_err = false; 
    $err_msg     = "";

    $table            = "room";
    $amenities        = [];
    $special_feature  = [];
    
    $sf_table         = "special_feature";
    $sf_select_column = ["id","special_feature_name"];
    $sf_order_by      = ["id" => "DESC"];
    $sf_result        = selectQuery($sf_select_column,$sf_table,$mysqli,$sf_order_by);
    $sf_res_rows      = $sf_result->num_rows;

    $bed_table         = "bed";
    $bed_select_column = ["id","bed_name"];
    $bed_order_by      = ["id"  => "DESC"];
    $bed_result        = selectQuery($bed_select_column,$bed_table,$mysqli,$bed_order_by);
    $bed_res_rows      = $bed_result->num_rows;

    $view_table         = "view";
    $view_select_column = ["id","view_name"];
    $view_order_by      = ["id"  => "DESC"];
    $view_result        = selectQuery($view_select_column,$view_table,$mysqli,$view_order_by);
    $view_res_rows      = $view_result->num_rows;
    
    $amenity_table = "amenity";
    $select_column = ["id","amenity_name"];
    $order_by      = ["id"   => "DESC"];
    $result        = selectQuery($select_column,$amenity_table,$mysqli,$order_by);
    $res_rows      = $result->num_rows;

    if(isset($_POST['form-sub']) && $_POST['form-sub'] == 1){
        
        $room_name            = $mysqli->real_escape_string($_POST['room_name']);
        $room_occupancy       = $mysqli->real_escape_string($_POST['room_occupancy']);
        $room_bed             = $mysqli->real_escape_string($_POST['room_bed']);
        $room_size            = $mysqli->real_escape_string($_POST['room_size']);
        $room_view            = $mysqli->real_escape_string($_POST['room_view']);
        $room_price           = $mysqli->real_escape_string($_POST['room_price']);
        $room_extra_bed_price = $mysqli->real_escape_string($_POST['room_extra_bed_price']);
        $room_description     = $mysqli->real_escape_string($_POST['room_description']);
        $room_detail          = $mysqli->real_escape_string($_POST['room_detail']);
        $amenities            = $_POST['amenity'];
        $special_feature      = $_POST['special_feature'];
        $file                 = $_FILES['file'];

        if($room_name == '') {
            $error       = true;
            $process_err = true; 
            $err_msg     = "Please fill Room Name";
        }
        if($room_occupancy == '') {
            $error       = true;
            $process_err = true; 
            $err_msg     = "Please fill Room Occupancy";
        }
        if($room_bed == '') {
            $error       = true;
            $process_err = true; 
            $err_msg     = "Please fill Room Bed";
        }
       if($room_size == '') {
            $error       = true;
            $process_err = true; 
            $err_msg     = "Please fill Room Size";
       }
       if($room_view == '') {
            $error       = true;
            $process_err = true; 
            $err_msg     = "Please fill Room View";
       }
       if($room_price == '') {
            $error       = true;
            $process_err = true; 
            $err_msg     = "Please fill Room Price";
       }
       if($room_extra_bed_price == '') {
            $error       = true;
            $process_err = true; 
            $err_msg     = "Please fill Room Extra Bed Price";
       }
       if($room_description == '') {
            $error       = true;
            $process_err = true; 
            $err_msg     = "Please fill Room Description";
       }
       if($room_detail == '') {
            $error       = true;
            $process_err = true; 
            $err_msg     = "Please fill Room Detail";
       }
       if($amenities == '') {
            $error       = true;
            $process_err = true; 
            $err_msg     = "Please fill Room Amenity";
       }
       if($special_feature == '') {
            $error       = true;
            $process_err = true; 
            $err_msg     = "Please fill Room Special Feature";
       }
        // for image upload 
        if($process_err == false) {
            $upload_process = false;
            if($file['name'] != '')   {
                $name            = $file['name'];
                $tmp_name        = $file['tmp_name'];
                $check_extension = checkImageExtexsion($name,$tmp_name);
                
                if($check_extension['error'] == false){
                    $unique_name = date('Ymd_His') . "_" . uniqid() . "." . $check_extension['extension'];
                    $upload_process = true;
                } else {
                    $process_err = true;
                    $error       = true;
                    $err_msg     .= "Please Upload valid image <br />";
                        
                }
                

            $today_date  = date('Y-m-d H:i:s');
            $user_id     = (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : $_COOKIE['user_id'];
            if($upload_process == true){
                $insert_data = array(
                    'room_name'               => "'$room_name'",
                    'occupancy'               => "'$room_occupancy'",
                    'bed_id'                  => "'$room_bed'",
                    'room_size'               => "'$room_size'",
                    'view_id'                 => "'$room_view'",
                    'room_description'        => "'$room_description'",
                    'room_detail'             => "'$room_detail'",
                    'room_price_per_day'      => "'$room_price'",
                    'extra_bed_price_per_day' => "'$room_extra_bed_price'",
                    'room_thumbnail'          => "'$unique_name'",
                    'created_at'              => "'$today_date'",
                    'updated_at'              => "'$today_date'",
                    'created_by'              => "'$user_id'",
                    'updated_by'              => "'$user_id'"
                );
            } else {
              $insert_data = array(
                'room_name'               => "'$room_name'",
                'occupancy'               => "'$room_occupancy'",
                'bed_id'                  => "'$room_bed'",
                'room_size'               => "'$room_size'",
                'view_id'                 => "'$room_view'",
                'room_description'        => "'$room_description'",
                'room_detail'             => "'$room_detail'",
                'room_price_per_day'      => "'$room_price'",
                'extra_bed_price_per_day' => "'$room_extra_bed_price'",
                'created_at'              => "'$today_date'",
                'updated_at'              => "'$today_date'",
                'created_by'              => "'$user_id'",
                'updated_by'              => "'$user_id'"
             );
            }

            $insert      = insertQuery($insert_data,$table,$mysqli);
            $inserted_id = $mysqli->insert_id;

            foreach($amenities as $amenity) {
                $table       = "room_amenity";
                $insert_data = [
                    'room_id'    => "'$inserted_id'",
                    'amenity_id' => "'$amenity'",
                    'created_at' => "'$today_date'",
                    'updated_at' => "'$today_date'",
                    'created_by' => "'$user_id'",
                    'updated_by' => "'$user_id'"

                ];
                $insert = insertQuery($insert_data,$table,$mysqli);
            }

            foreach($special_feature as $feature) {
                $table = "room_special_feature";
                $insert_data = [
                    'room_id'            => "'$inserted_id'",
                    'special_feature_id' => "'$feature'",
                    'created_at'         => "'$today_date'",
                    'updated_at'         => "'$today_date'",
                    'created_by'         => "'$user_id'",
                    'updated_by'         => "'$user_id'"

                ];
                $insert = insertQuery($insert_data,$table,$mysqli);
            }
            if($upload_process == true){
                $upload_path           = "../assets/upload-img/" . $inserted_id . "/thumb/";
                if(!file_exists($upload_path)){
                    mkdir($upload_path,0777,true);
                }
                if(move_uploaded_file($file['tmp_name'],$upload_path . $unique_name)){
                    $sourceImagepath = $upload_path . $unique_name;
                    $destinationImagePath = $upload_path . $unique_name;
                    $watermark = "../assets/watermark/sg.jpg";
                    cropAndResizeImage($sourceImagepath,$destinationImagePath,$target_height,$target_width);
                    addWatermarkToImage($destinationImagePath,$watermark,$destinationImagePath);
                    $url = $cp_base_url . "room_gallery.php?id=" . $inserted_id;
                    header("Refresh: 0; url=$url");
                    exit();
                }
            }
        }  
    }  
    }
    $title = "Room room";

    require('templates/cp_template_header.php');
    require('templates/cp_template_sidebar.php');
    require('templates/cp_template_topnav.php');
?>
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Hotel Room room</h3>
            </div>
        </div>
        <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="x_panel">
                        <div class="x_content">
                            <form action="<?php echo $cp_base_url; ?>room_create.php" method="POST" id="form-create" enctype="multipart/form-data" >
                                <span class="section">Room Create</span>
                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align">Image<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <div id="preview-wrapper">
                                            <div class="vertical-center" >
                                                <label class="file-choose" onclick="chooseFile()">Choose File</label>
                                            </div>
                                            <div class="" id="preview-img" style="display:none;" >
                                            <label for="" class="change-img" onclick="changePhoto()">Change Photo</label>
                                                <img src="" alt="" id="upload-img">
                                            </div>
                                            <input type="file" id="thumb-file" name="file" style="display:none" onchange="uploadPhoto()">
                                         </div> 
                                    </div>
                                </div>

                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align" for="name">Name<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control room_name" name="room_name" placeholder="ex. Depluex" type="text" id="name"/>
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  label-error hide" id="room-name-error"><span class="name-error-msg"></span></label>
                                </div>
                                
                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align " for="occupancy">Occupancy <span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control room_occupancy" type="number"  name="room_occupancy" id="occupancy" placeholder="ex. 2 or 3">
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  occupancy-label-error hide" id="room-occupancy-error"><span class="occupancy-error-msg" style="color:red;"></span></label>
                                </div>

                                <div class=" field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align " for="bed">Bed <span calss="required">*</span> </label>
                                    <div class="col-md-6 col-sm-6">
                                        <select class="select2_group form-control room_bed" id="bed" name="room_bed">
                                        <option value=""> Choose Bed Type </option>
                                                <?php 
                                                    if($bed_res_rows >= 1){
                                                        while($bed_row = $bed_result->fetch_assoc()){
                                                            $bed_id    = (int)($bed_row['id']);
                                                            $bed_name  = htmlspecialchars($bed_row['bed_name']);
                                                ?>
                                                <option value="<?php echo $bed_id; ?>" ><?php echo $bed_name;?></option>
                                                <?php
                                                        }
                                                    }
                                                 ?>
                                        </select>
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  bed-label-error hide"><span class="bed-error-msg" style="color:red;"></span></label>
								</div>

                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align" for="room_size">Room Size <span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control room_size" type="number" name="room_size" id="room_size" data-validate-minmax="10,100" required='required'  placeholder="ex. 10'">
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3 size-label-error hide"><span class="size-error-msg" style="color:red;"></span></label>
                                </div>

                                <div class=" field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align " for="view">View <span calss="required">*</span> </label>
                                    <div class="col-md-6 col-sm-6">
                                        <select class="select2_group form-control room_view"  name="room_view">
                                                <option value=""> Choose View </option>
                                                <?php 
                                                    if($view_res_rows >= 1){
                                                        while($view_row = $view_result->fetch_assoc()){
                                                            $view_id   = (int)($view_row['id']);
                                                            $view_name = htmlspecialchars($view_row['view_name']);
                                                ?>
                                                <option value="<?php echo $view_id; ?>" ><?php echo $view_name;?></option>
                                                <?php
                                                        }
                                                    }
                                                 ?>
                                        </select>
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  view-label-error hide" id="room-view-error"><span class="view-error-msg" style="color:red;"></span></label>
								</div>


                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align" for="price_per_day">Price Per Day <small>($)</small> <span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control room_price" type="number"  name="room_price" data-validate-minmax="10,100" required='required' id="price_per_day" placeholder="ex. 30$">
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  price-label-error hide" id="room-price-error"><span class="price-error-msg" style="color:red;"></span></label>
                                </div>

                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align" for="extra_price">Extra Bed Price Per Day <small>($)</small> <span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control room_extra" type="number"  name="room_extra_bed_price" data-validate-minmax="10,100" required='required' id="extra_price" placeholder="ex. 1.8$">
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  extra-label-error hide" id="room-extra-error"><span class="extra-error-msg" style="color:red;"></span></label>
                                </div>

                                <div class="form-group row">
											<label class="col-md-3 col-sm-3  label-align">Choose Special Feature <span class="required">*</span></label>
                                            <div class="col-md-6 col-sm-6 " >
                                            <div class="row">
                                            <?php if($sf_res_rows >= 1) {
                                                while($row = $sf_result->fetch_assoc()){
                                                    $special_feature_id   = (int)($row['id']);
                                                    $special_feature_name = htmlspecialchars($row['special_feature_name']);
                                                 ?> 
                                                    <div class="col-md-6">
												    <div class="checkbox">
													<label>
														<input type="checkbox" class="room_feature"  name="special_feature[]" value="<?php echo $special_feature_id;?>" <?php if(in_array($special_feature_id,$special_feature)){echo "checked";} ?>><?php echo $special_feature_name;?>
													</label> 
												    </div>
											        </div>
                                               <?php }
                                            }
                                             ?>
                                            </div>
									</div>
                                    <label class="col-form-label col-md-3 col-sm-3  feature-label-error hide" id="feature-name-error"><span class="feature-error-msg" style="color:red;"></span></label>
                                </div>

                                <div class="form-group row">
											<label class="col-md-3 col-sm-3  label-align">Choose Amenities <span class="required">*</span></label>
                                            <div class="col-md-6 col-sm-6 ">
                                            <div class="row">
                                            <?php if($res_rows >= 1) {
                                                while($row = $result->fetch_assoc()){
                                                    $amenity_id   = (int)($row['id']);
                                                    $amenity_name = htmlspecialchars($row['amenity_name']);
                                                 ?> 
                                                    <div class="col-md-6">
												    <div class="checkbox">
													<label>
														<input type="checkbox" class="room_amenity" value="<?php echo $amenity_id;?>" name="amenity[]" <?php if(in_array($amenity_id,$amenities)){echo "checked";}?>><?php echo $amenity_name;?>
													</label> 
												    </div>
											        </div>
                                               <?php }
                                            }
                                             ?>
                                            </div>
									</div>
                                    <label class="col-form-label col-md-3 col-sm-3  amenity-label-error hide" id="room-amenity-error"><span class="amenity-error-msg" style="color:red;"></span></label>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-md-3 col-sm-3 label-align" for="room_detail">Room Details<span class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 ">
                                        <textarea class="form-control room_detail" rows="2" name="room_detail" id="room_detail" placeholder="ex. Room Details"></textarea>
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  detail-label-error hide" id="detail-name-error"><span class="detail-error-msg" style="color:red;"></span></label>
                                </div>

                                <div class="form-group row">
                                    <label class="control-label col-md-3 col-sm-3 label-align" for="description">Room Description<span class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 ">
                                        <textarea class="form-control room_des" rows="3" name="room_description" id="description" placeholder="ex. Room Description"></textarea>
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  des-label-error hide" id="des-name-error"><span class="des-error-msg" style="color:red;"></span></label>
                                </div>

                                <div class="">
                                    <div class="form-group">
                                        <div class="col-md-6 offset-md-3">
                                            <button type='button' class="btn btn-primary" id="submit-btn">Submit</button>
                                            <button type='reset' class="btn btn-success" id="reset">Reset</button>
                                            <input type="hidden" name="form-sub" value="1" />
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
    $(document).ready(function(){
        $('#submit-btn').click(function(){
            let error              = false
            const room_name        = $('.room_name').val();
            const room_occupancy   = $('.room_occupancy').val();
            const room_bed         = $('.room_bed').val();
            const room_size        = $('.room_size').val();
            const room_view        = $('.room_view').val();
            const room_price       = $('.room_price').val();
            const room_extra       = $('.room_extra').val();
            const room_amenities = $('input[name="amenity[]"]:checked').length;
            const room_features = $('input[name="special_feature[]"]:checked').length;
            const room_detail      = $('.room_detail').val();
            const room_description = $('.room_des').val();
            const room_name_length = room_name.length;

            if(room_name  === ''){
                $('.name-error-msg').text('')
                $('.name-error-msg').text('Please fill Hotel Room name')
                $('.label-error').show()
                error = true
            } else {
                $('.label-error').hide()
            }
            if(room_occupancy  === ''){
                $('.occupancy-error-msg').text('')
                $('.occupancy-error-msg').text('Please fill Room Occupancy')
                $('.occupancy-label-error').show()
                error = true
            } else {
                $('.occupancy-label-error').hide()
            }
            if(room_bed  == ''){
                $('.bed-error-msg').text('')
                $('.bed-error-msg').text('Please fill Room Bed')
                $('.bed-label-error').show()
                error = true
            } else {
                $('.bed-label-error').hide()
            }
            if(room_size  == ''){
                $('.size-error-msg').text('')
                $('.size-error-msg').text('Please fill Room Size')
                $('.size-label-error').show()
                error = true
            } else {
                $('.size-label-error').hide()
            }
            if(room_view  == ''){
                $('.view-error-msg').text('')
                $('.view-error-msg').text('Please fill Room View')
                $('.view-label-error').show()
                error = true
            } else {
                $('.view-label-error').hide()
            }
            if(room_price  == ''){
                $('.price-error-msg').text('')
                $('.price-error-msg').text('Please fill Room Price Per Day')
                $('.price-label-error').show()
                error = true
            } else {
                $('.price-label-error').hide()
            }
            if(room_extra  == ''){
                $('.extra-error-msg').text('')
                $('.extra-error-msg').text('Please fill Extra Price Per Day')
                $('.extra-label-error').show()
                error = true
            } else {
                $('.extra-label-error').hide()
            }
            if (room_amenities === 0) {
                $('.amenity-error-msg').text('Please select at least one Room Amenity.');
                $('.amenity-label-error').show();
                error = true;
            } else {
                $('.amenity-label-error').hide();
            }

            if (room_features === 0) {
                $('.feature-error-msg').text('Please select at least one Special Feature.');
                $('.feature-label-error').show();
                error = true;
            } else {
                $('.feature-label-error').hide();
            }
            if(room_detail  == ''){
                $('.deatil-error-msg').text('')
                $('.detail-error-msg').text('Please fill Room Detail')
                $('.detail-label-error').show()
                error = true
            } else {
                $('.detail-label-error').hide()
            }
            if(room_description  == ''){
                $('.des-error-msg').text('')
                $('.des-error-msg').text('Please fill Room Description')
                $('.des-label-error').show()
                error = true
            } else{
                $('.des-label-error').hide()
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