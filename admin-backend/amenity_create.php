<?php
  session_start();
    require('../require/common.php');
    require('../require/connect.php');
    require('../require/check_authentication.php');
    require('../require/include_function.php');

    $error        = false;
    $err_msg      = "";
    $process_err  = false; 
    $table        = 'amenity';
    $amenity_type = "";
    
    if(isset($_POST['form-sub']) == 1){
        $amenity_name = $mysqli->real_escape_string($_POST['amenity_name']);
        $amenity_type = (int)($_POST['amenity_type']);

        if(trim($amenity_name)  == ''){
            $error       = true;
            $process_err = true;
            $err_msg     = "Please Fill Amenity Name";
        }
        if($amenity_type == ''){
            $error       = true;
            $process_err = true;
            $err_msg     = "Please Choose Amenity Type";
        }
        $check_column = array(
                                'amenity_name' => $amenity_name
                             );
        $check_unique = checkUniqueValue($check_column, $table, $mysqli);

        if($check_unique >= 1){
            $error       = true;
            $process_err = true;
            $err_msg     = "Amenity name is already exist.";
        }

        if($process_err == false){
            $today_date  = date('Y-m-d H:i:s');
            $user_id     = (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : $_COOKIE['user_id'];
            $insert_data = array(
                                    'amenity_name' => "'$amenity_name'",
                                    'amenity_type' => "'$amenity_type'",
                                    'created_at'   => "'$today_date'",
                                    'updated_at'   => "'$today_date'",
                                    'created_by'   => "'$user_id'",
                                    'updated_by'   => "'$user_id'",
                                );
            $insert = insertQuery($insert_data, $table, $mysqli);
            if($insert){
                $url = $cp_base_url . "amenity_listing.php?msg=success";
                header("Refresh: 0; url=$url");
                exit();
            }
        }
    
    }
    $title = "Amenity";
    require('templates/cp_template_header.php');
    require('templates/cp_template_sidebar.php');
    require('templates/cp_template_topnav.php');
?>
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Amenity</h3>
            </div>
        </div>
        <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="x_panel">
                        <div class="x_content">
                            <form action="<?php echo $cp_base_url; ?>amenity_create.php" method="POST" id="form-create" >
                                <span class="section">Amenity Create</span>
                                    <div class="field item form-group">
                                        <label class="col-form-label col-md-3 col-sm-3  label-align" for="amenity_name">Amenity Name<span class="required">*</span></label>
                                            <div class="col-md-6 col-sm-6">
                                                <input class="form-control amenity_name" name="amenity_name" placeholder="ex. Swimming Pool" type="text" id="amenity_name"/>
                                            </div>
                                        <label class="col-form-label col-md-3 col-sm-3  label-error hide" id="amenity-name-error"><span class="name-error-msg"></span></label>
                                    </div>
                                
                                <div class="form-group row">
                                    <label class="control-label col-md-3 col-sm-3 label-align">Amenity Type</label>
                                        <div class="col-md-6 col-sm-6 ">
                                            <select class="form-control amenity_type"  name="amenity_type">
                                                <option value="" disabled <?php if($amenity_type==""){echo "selected";}?>>-Choose Amenity Type-</option>
                                                <?php foreach($amenities AS $key => $value) {?>
                                                <option name="amenity_type" value="<?php echo $key; ?>"<?php if($key == $value) echo "selected" ?>>
                                                <?php echo $value;?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    <label class="col-form-label col-md-3 col-sm-3  type-label-error hide" id="type-error-msg"><span class="type-error-msg" style="color:red;"></span></label>
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
                let error                 = false
                const amenity_name        = $('.amenity_name').val();
                const amenity_name_length = amenity_name.length;
                const amenity_type        = $('.amenity_type').find(":selected").val();
                if(amenity_name  === ''){
                    $('.name-error-msg').text('')
                    $('.name-error-msg').text('Please fill amenity name')
                    $('.label-error').show()
                    error     = true
                } else {
                    $('.label-error').hide()
                    error     = false
                }
                if(amenity_type === ''){
                    $('.type-error-msg').text('')
                    $('.type-error-msg').text('Please choose amenity type')
                    $('.type-label-error').show()
                    error       = true
                } else {
                    $('.type-label-error').hide()
                }
                if(amenity_name != '' && amenity_name_length <3){
                    $('.name-error-msg').text('')
                    $('.name-error-msg').text('Name length is at least must be greather then three ')
                    $('.label-error').show()
                    error     = true
                }
                if(amenity_name != '' && amenity_name_length > 30){
                    $('.name-error-msg').text('')
                    $('.name-error-msg').text('Name length is must be less then 30')
                    $('.label-error').show()
                    error     = true
                }
            
                if(error==false){
                    $('#form-create').submit();
                }
            })
            
            $('#reset').click(function(){
                    $('.label-error').hide();
                    $('.type-label-error').hide();
            }) 
        })

    </script>
         <!-- pnotify -->
    <script src="<?php echo $base_url; ?>assets/backend/css/pnotify/pnotify.js"></script>
    <script src="<?php echo $base_url; ?>assets/backend/css/pnotify/pnotify.buttons.js"></script>
    <script src="<?php echo $base_url; ?>assets/backend/css/pnotify/pnotify.nonblock.js"></script>
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