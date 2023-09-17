<?php
  session_start();
    require('../require/common.php');
    require('../require/connect.php');
    require('../require/check_authentication.php');
    require('../require/include_function.php');

    $error                = false;
    $err_msg              = "";
    $process_err          = false; 
    $table                = 'special_feature';
    $special_feature_name = "";

    if(isset($_POST['form-sub']) && $_POST['form-sub'] == 1){
        $id                   = (int)($_POST['id']);
        $id                   = $mysqli->real_escape_string($id);
        $special_feature_name = $mysqli->real_escape_string($_POST['special_feature_name']);

        if(trim($special_feature_name) == ''){
            $error       = true;
            $err_msg     = "Please fill special feature name";
            $process_err = true;
        }

        $check_column = array(
            'special_feature_name' => $special_feature_name,
        );

        $check_unique = checkUniqueValueUpdate($check_column, $table,$id,$mysqli);

        if($check_unique >= 1){
            $error       = true;
            $err_msg     = "Special Feature name is already exist.";
            $process_err = true;
        }

        if($process_err == false){
        $today_date  = date('Y-m-d H:i:s');
        $user_id     = (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : $_COOKIE['user_id'];
        $update_data = array(
                'special_feature_name' => $special_feature_name,
                'updated_at'           => $today_date,
                'updated_by'           => $user_id
        );

        $update = updateQuery($update_data,$table,$id,$mysqli);

        if($update){
            $url = $cp_base_url . "special_feature_listing.php?msg=update";
            header("Refresh: 0; url=$url");
            exit();
        }
    }
    }else{
        if(!isset($_GET['id']))
        {
            $url = $cp_base_url . "special_feature_listing.php?msg=error";
            header("Refresh: 0; url=$url");
            exit();
        }else{
            $id       = (int)($_GET['id']);
            $id       = $mysqli->real_escape_string($id);
            $column   = ['id','special_feature_name'];
            $result   = selectQueryById($column,$table,$id,$mysqli);
            $res_rows = $result->num_rows;

            if($res_rows <= 0){
                $url = $cp_base_url . "special_feature_listing.php?msg=error";
                header("Refresh: 0; url=$url");
                exit();
            } 
            while($row = $result->fetch_assoc()){
                $special_feature_name = htmlspecialchars($row['special_feature_name']);
            }
        }
    }
    $title = "Special Feature Update";

    require('templates/cp_template_header.php');
    require('templates/cp_template_sidebar.php');
    require('templates/cp_template_topnav.php');
?>
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Special Feature Update</h3>
            </div>
        </div>
        <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="x_panel">
                        <div class="x_content">
                            <form class="" action="special_feature_edit.php" method="POST" id="form-create">
                                <span class="section">Special Feature Update</span>
                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align">Name<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control special_feature_name" name="special_feature_name" value="<?php echo $special_feature_name; ?>" id="special_feature_name" />
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  label-error hide" id="special-feature-name-error"><span class="name-error-msg"></span></label>
                                </div>  
                                <div class="">
                                    <div class="form-group">
                                        <div class="col-md-6 offset-md-3">
                                            <button type='button' class="btn btn-primary" id="submit-btn">Submit</button>
                                            <button type='reset' class="btn btn-success" id="reset">Reset</button>
                                            <input type="hidden" name="form-sub" value="1" />
                                            <input type="hidden" name="id" value="<?php echo $id; ?>">
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
            let error = false
            const special_feature_name = $('.special_feature_name').val();
            const special_feature_name_length = special_feature_name.length;
            if(special_feature_name  == ''){
                $('.name-error-msg').text('')
                $('.name-error-msg').text('Please fill special feature name')
                $('.label-error').show()
                error     = true
            }
            if(special_feature_name != '' && special_feature_name_length <3){
                $('.name-error-msg').text('')
                $('.name-error-msg').text('Name length is at least must be greather then three ')
                $('.label-error').show()
                error = true
            }
            if(special_feature_name != '' && special_feature_name_length > 30){
                $('.name-error-msg').text('')
                $('.name-error-msg').text('Name length is must be less then 30')
                $('.label-error').show()
                error = true
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