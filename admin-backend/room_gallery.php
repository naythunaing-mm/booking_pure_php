<?php
  session_start();
    require('../require/common.php');
    require('../require/connect.php');
    require('../require/check_authentication.php');
    require('../require/include_function.php');
    
    $error       = false;
    $err_msg     = "";

    $table = "room_gallery";
    if(isset($_POST['form-sub']) && $_POST['form-sub'] == 1){
        $process_err = false; 
        $room_id     = (int)($_POST['id']);
        $file        = $_FILES['file'];
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
                    $insert_data = [
                        'room_id'            => "'$room_id'",
                        'image'              => "'$unique_name'",
                        'created_at'         => "'$today_date'",
                        'updated_at'         => "'$today_date'",
                        'created_by'         => "'$user_id'",
                        'updated_by'         => "'$user_id'"
    
                    ];
                    $insert      = insertQuery($insert_data,$table,$mysqli);
                    // $url = $cp_base_url . "room_gallery.php?id=" . $inserted_id;
                    // header("Refresh: 0; url=$url");
                    // exit();
                }
                $upload_process = true;
            } else {
                $process_err = true;
                $error       = true;
                $err_msg     .= "Please Upload valid image <br />";
                    
            }
        }
    } else {
        $room_id = (int)($_GET['id']);
    }
    $table = "room_gallery";
    $select_column = ["id","room_id","image"];
    $order_by = ['id' => 'ASC'];
    $where = ['room_id' => $room_id];
    $result = selectQuery($select_column,$table,$mysqli,$order_by,$where);
    $res_rows = $result->num_rows;
    $title = "Room Gallery";

    require('templates/cp_template_header.php');
    require('templates/cp_template_sidebar.php');
    require('templates/cp_template_topnav.php');
?>
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Hotel Room Gallery</h3>
            </div>
        </div>
        <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="x_panel">
                        <div class="x_content">
                        <?php if($res_rows >= 1) { ?>
                            <div class="row">
                                <?php while($row = $result->fetch_assoc()) {
                                    $gallery_id = (int)($row['id']);
                                    $r_id = (int)($row['room_id']);
                                    $r_image = htmlspecialchars($row['image']);
                                    $img_path = $base_url. "assets/upload-img/" . $r_id ."/" . $r_image; 
                                    $edit_url = $cp_base_url . "room_gallery_edit.php?id=" . $gallery_id . "&room-id=" . $r_id;
                                    $delete_url = $cp_base_url . "room_gallery_delete.php?id=" . $gallery_id . "&room-id=" . $r_id;
                                    ?>
                                    <div class="col-md-3">
                                    <div class="img-wrapper">
                                        <img src="<?php echo $img_path; ?>" alt="" style="width:100%;">
                                    </div>
                                    <div class="btn-wrapper">
                                        <a href="<?php echo $edit_url; ?>" class="btn btn-info btn-xs"><i class="fa fa-pencil"></i> Edit</a>
                                        <a href="<?php echo $delete_url; ?>" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> Delete</a>
                                    </div>
                                </div>
                                <?php } ?>

                            </div>
                            <?php } ?>
                            <div style="height:20px;"></div>
                            <form action="<?php echo $cp_base_url; ?>room_gallery.php" method="POST" id="form-create" enctype="multipart/form-data" >
                                <span class="section">Room Gallery</span>
                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align">Upload Image<span class="required">*</span></label>
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

                                <div class="">
                                    <div class="form-group">
                                        <div class="col-md-6 offset-md-3">
                                            <button type='submit' class="btn btn-primary" id="submit-btn">Upload</button>
                                            <button type='reset' class="btn btn-success" id="reset">Reset</button>
                                            <input type="hidden" name="form-sub" value="1" />
                                            <input type="hidden" name="id" value="<?php echo $room_id; ?>">
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