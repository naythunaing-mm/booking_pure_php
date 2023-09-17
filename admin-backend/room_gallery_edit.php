<?php
  session_start();
    require('../require/common.php');
    require('../require/connect.php');
    require('../require/check_authentication.php');
    require('../require/include_function.php');
    
    $error = false;
    $err_msg = "";
    $form = true;
    $table = "room_gallery";
    if(isset($_POST['form-sub']) && $_POST['form-sub'] == 1){

        $id          = (int)($_POST['id']);
        $process_err = false; 
        $room_id     = (int)($_POST['room-id']);
        $file        = $_FILES['file'];
        $select_column = ["image"];
        $result = selectQueryById($select_column,$table,$id,$mysqli);
        $res_rows = $result->num_rows;
        if($res_rows <= 0) {
            $form = false;
            $error = true;
            $process_err = true;
            $err_msg = "Image no found.!";
        } else {
            while($row = $result->fetch_assoc()){
                $old_image_name = htmlspecialchars($row['image']);
                $old_image_path = "../assets/upload-img/" . $room_id ."/" . $old_image_name;
            }
        }
        if($file == '') {
            $error       = true;
            $process_err = true;
            $err_msg     = "Please upload image.";

        } else {
            $name            = $file['name'];
            $tmp_name        = $file['tmp_name'];
            $check_extension = checkImageExtexsion($name,$tmp_name);

            if($check_extension['error'] == false){
                $unique_name = date('Ymd_His') . "_" . uniqid() . "." . $check_extension['extension'];
                $upload_path = "../assets/upload-img/" . $room_id . "/";
                if(!file_exists($upload_path)){
                    mkdir($upload_path,0777,true);
                }
                if(move_uploaded_file($file['tmp_name'],$upload_path . $unique_name)){
                    $destinationImagePath = $upload_path . $unique_name;
                    $watermark    = "../assets/watermark/sg.jpg";
                    cropAndResizeImage($destinationImagePath,$destinationImagePath,$upload_height,$upload_width);
                    addWatermarkToImage($destinationImagePath,$watermark,$destinationImagePath);
                    $today_date  = date('Y-m-d H:i:s');
                    $user_id     = (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : $_COOKIE['user_id'];
                    $update_data = [
                        'image'              => $unique_name,
                        'updated_at'         => $today_date,
                        'updated_by'         => $user_id,
    
                    ];
                    $update      = updateQuery($update_data,$table,$id,$mysqli);
                    if($update){
                        unlink($old_image_path);
                        $url = $cp_base_url . "room_gallery.php?id=" . $room_id;
                        header("Refresh: 0; url=$url");
                        exit();
                    }
                   
                }
                
            } else {
                $process_err = true;
                $error       = true;
                $err_msg     .= "Please Upload valid image <br />";
                    
            }
        }
    } else {
        $id      = (int)($_GET['id']);
        $room_id = (int)($_GET['room-id']);
        $table = 'room_gallery';
        $select_column = ["image"];
        $result = selectQueryById($select_column,$table,$id,$mysqli);
        $res_rows = $result->num_rows;
        if($res_rows <= 0) {
            $form = false;
            $error = true;
            $err_msg = "Image no found.!";
        } else {
            while($row = $result->fetch_assoc()){
                $image_name = htmlspecialchars($row['image']);
                $img_path = $base_url. "assets/upload-img/" . $room_id ."/" . $image_name;
            }
        }
    }
    $table = "room_gallery";
    $select_column = ["id","room_id","image"];
    $order_by = ['id' => 'DESC'];
    $result = selectQuery($select_column,$table,$mysqli,$order_by);
    $res_rows = $result->num_rows;
    $title = "Room Gallery Update";

    require('templates/cp_template_header.php');
    require('templates/cp_template_sidebar.php');
    require('templates/cp_template_topnav.php');
?>
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Hotel Room Gallery Update</h3>
            </div>
        </div>
        <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="x_panel">
                        <div class="x_content">
                            <?php if($form == true) { ?>
                            <form action="<?php echo $cp_base_url; ?>room_gallery_edit.php" method="POST" id="form-create" enctype="multipart/form-data" >
                                <span class="section">Room Gallery</span>
                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align">Upload Image<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <div id="preview-wrapper">
                                            
                                            <div class="" id="preview-img" >
                                            <label for="" class="change-img" onclick="changePhoto()">Change Photo</label>
                                                <img src="<?php echo $img_path; ?>" alt="" id="upload-img">
                                            </div>
                                            <input type="file" id="thumb-file" name="file" style="display:none" onchange="uploadPhoto()">
                                         </div> 
                                    </div>
                                </div>

                                <div class="">
                                    <div class="form-group">
                                        <div class="col-md-6 offset-md-3">
                                            <button type='submit' class="btn btn-primary" id="submit-btn">Upload</button>
                                            <button type='reset' class="btn btn-success" id="reset">Reset</button>
                                            <input type="hidden" name="form-sub" value="1" />
                                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                                            <input type="hidden" name="room-id" value="<?php echo $room_id; ?>">

                                        </div>
                                    </div>
                                </div>
                            </form>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
        <!-- /page content -->


<?php require('templates/cp_template_footer.php'); ?>

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