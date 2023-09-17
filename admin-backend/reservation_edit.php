<?php
    session_start();
    require('../require/common.php');
    require('../require/connect.php');
    require('../require/setting.php');
    require('../require/check_authentication.php');
    require('../require/include_function.php');

    $err_msg          = "";
    $success_msg      = "";
    $success          = false;
    $form             = false;
    $error            = false;
    $process_error    = false;

    if(isset($_POST['form-sub']) && $_POST['form-sub'] == 1) {
      $room_id          = (int)($_POST['room_id']);
      $name             = $mysqli->real_escape_string($_POST['name']);
      $email            = $mysqli->real_escape_string($_POST['email']);
      $phone            = $mysqli->real_escape_string($_POST['phone']);
      $checkin          = date("Y-m-d", strtotime($_POST['checkin']));
      $checkout         = date("Y-m-d", strtotime($_POST['checkout']));
      $extra_bed_select = (isset($_POST['extra_bed_select']) ? $_POST['extra_bed_select'] : 0 );
      
      if($name == '' || $email == '' || $phone == '' || $checkin == '' || $checkout == ''){
        $error         = true;
        $process_error = true;
        $err_msg       = "Please fill form data complete!";
      }

      if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error         = true;
        $process_error = true;
        $err_msg       = "Please choose valid email!";
      } 
      if($checkout < $checkin){
        $error         = true;
        $err_msg       = "Checkout date is grather than the chcekin date.";
        $process_error = true;
      }

      if($process_error == false) {
        $customer_table = "customer";
        $check_data     = [
                            "customer_name"  => $name,
                            "customer_email" => $email, 
                            "customer_phone" => $phone
                          ];
        $check_sql = checkUniqueValue($check_data,$customer_table,$mysqli);
        if($check_sql <= 0){
          $today_date  = date('Y-m-d H:i:s');
          $insert_data = array(
                                'customer_name'  => "'$name'",
                                'customer_email' => "'$email'",
                                'customer_phone' => "'$phone'",
                                'created_at'     => "'$today_date'"
                              );
          $insert = insertQuery($insert_data, $customer_table, $mysqli);
          $customer_id = $mysqli->insert_id;
          } else {
            $slect_data = ["id"];
            $where      = [
                            "customer_name"   => $name,
                            "customer_email"  => $email,
                            "customer_phone"  => $phone
                          ];
            $select_sql = selectQuery($slect_data,$customer_table,$mysqli,$order=null,$where);
            while($customer_row = $select_sql->fetch_assoc()){
              $customer_id = (int)($customer_row['id']);
            }
          } 

          // check date 
          $checkin_date_sql = "SELECT count(id) AS checkin_count FROM `reservation` WHERE checkin < '$checkin' AND checkout <= '$checkin' AND status = '1' AND room_id = '$room_id'AND deleted_at IS NULL ";
          $result_checkin   = $mysqli->query($checkin_date_sql);

          while($checkin_row = $result_checkin->fetch_assoc()){
            $check_checkin = $checkin_row['checkin_count'];
          }

          $checkout_date_sql  = "SELECT count(id) AS checkout_count FROM `reservation` WHERE checkin < '$checkout' AND status = '1' AND room_id = '$room_id' AND deleted_at IS NULL ";
          $result_checkout    = $mysqli->query($checkin_date_sql);
          while($checkout_row = $result_checkout->fetch_assoc()){
            $check_checkout   = $checkout_row['checkin_count'];
          }
          
          if($check_checkin > 0 || $check_checkout > 0){
            $error         = true;
            $process_error = true;
            $err_msg       = "This room is already taken. please choose other date or room";
          }
         
          
          
          $room_table = "room";
          $today_date = date("Y-m-d H:i:s");
        // $insert_checkin_date  = convertYmdFormat($checkin);
        // $insert_checkout_date = convertYmdFormat($checkout);
          $checkin_date  = new DateTime($checkin);
          $checkout_date = new DateTime($checkout);
          $interval_date = $checkin_date->diff($checkout_date);
          $day_different = $interval_date->days;
          
          $select_room = ["room_price_per_day","extra_bed_price_per_day"];
          $room_res    = selectQueryById($select_room,$room_table,$room_id,$mysqli);
          $room_row    = $room_res->fetch_assoc();
          $room_price  = $room_row["room_price_per_day"];
          $extra_bed   = $room_row["extra_bed_price_per_day"];
          
          if($extra_bed_select == 0) {
            $final_price = $room_price * $day_different;
          } else {
            $final_price = ($room_price + $extra_bed) * $day_different;
          }

          $reserve_table = "reservation";
          $today_date = date("Y-m-d H:i:s");
          $insert_data = array(
            'checkin'     => "'$checkin'", 
            'checkout'    => "'$checkout'",
            'room_id'     => "'$room_id'",
            'extra_bed'   => "'$extra_bed_select'",
            'total_price' => "'$final_price'",
            'customer_id' => "'$customer_id'",
            'created_at'  => "'$today_date'",
          );

          $insert_reserve = insertQuery($insert_data , $reserve_table, $mysqli);
          if($insert_reserve){
            $success          = true;
            $success_msg      = "Please wait Admin confirm.";
            $name             = "";
            $email            = "";
            $phone            = "";
            $checkin          = "";
            $checkout         = "";
            $extra_bed_select = 0;
          }
          
     }
      
  } else {
        $id          = (isset($_GET['id'])) ? $_GET['id'] : "";
        $id          = $mysqli->real_escape_string($id);
        $reserve_table = "reservation";
        $reservation_column = [
                                "id",
                                "checkin",
                                "checkout",
                                "room_id",
                                "extra_bed",
                                "total_price",
                                "customer_id",
                                "status"
                            ];
        $reservation_res = selectQueryById($reservation_column,$reserve_table,$id,$mysqli);
        $reservation_res_row = $reservation_res->num_rows;
        $row = $reservation_res->fetch_assoc();
        $checkin = htmlspecialchars($row['checkin']);
        $checkout = htmlspecialchars($row['checkout']);
        $extra_bed = htmlspecialchars($row['extra_bed']);
        $total_price = htmlspecialchars($row['total_price']);
        $status      = htmlspecialchars($row['status']);
       
      
  }  
  
    $table        = "room";
    $room_col     = ["id","room_name","room_price_per_day","extra_bed_price_per_day","room_thumbnail"];
    $order_by     = ["id" => "ASC"];
    $where        = ["id" => $id];
    $room_res     = selectQuery($room_col,$table,$mysqli,$order_by,$where);
    $room_res_row = $room_res->num_rows;

    if($room_res_row >= 1){
      $room_row        = $room_res->fetch_assoc();
      $room_name       = (isset($room_row['room_name']) ? $room_row['room_name'] : "");
      $room_price      = (isset($room_row['room_price_per_day']) ? $room_row['room_price_per_day'] : "");
      $extra_bed_price = (isset($room_row['extra_bed_price_per_day']) ? $room_row['extra_bed_price_per_day'] : "");
    
    } 
   
    $title = "Reservation";

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
                            <form action="<?php echo $cp_base_url; ?>reservation_edit.php" method="POST" id="form-create" enctype="multipart/form-data" >
                                <span class="section">Room Create</span>

                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align" for="name">Customer Name<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control room_name" name="customer_name" placeholder="Please fill customer name" type="text" id="name" value="<?php echo $customer_name; ?>" />
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  label-error hide" id="room-name-error"><span class="name-error-msg"></span></label>
                                </div>
                                
                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align " for="occupancy"> Customer Phone<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control room_occupancy" type="text"  name="room_occupancy" id="phone" placeholder="+95-XXXX-XXX-XXXX" value="<?php echo $phone; ?>">
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  occupancy-label-error hide" id="room-occupancy-error"><span class="occupancy-error-msg" style="color:red;"></span></label>
                                </div>

                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align " for="email"> Customer Email<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control room_occupancy" type="text"  name="customer_email" id="email" placeholder="youremail@gmial.com" value="<?php echo $email; ?>">
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  occupancy-label-error hide" id="room-occupancy-error"><span class="occupancy-error-msg" style="color:red;"></span></label>
                                </div> <hr>

                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align " for="roomname"> Room Name<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control room_occupancy" type="text"  name="room_name" id="roomname" placeholder="ex. Please fill room name" value="<?php echo $room_name; ?>">
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  occupancy-label-error hide" id="roomname-occupancy-error"><span class="roomname-error-msg" style="color:red;"></span></label>
                                </div>
                                
                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align " for="checkin"> Checkin Date<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control room_occupancy" type="text"  name="checkin" id="checkin" placeholder="ex. 2 or 3" value="<?php echo $checkin; ?>">
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  occupancy-label-error hide" id="room-occupancy-error"><span class="occupancy-error-msg" style="color:red;"></span></label>
                                </div>

                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align " for="checkout"> Checkout Date<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control room_occupancy" type="text"  name="checkout" id="checkout" placeholder="ex. 2 or 3" value="<?php echo $checkout; ?>">
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  occupancy-label-error hide" id="room-occupancy-error"><span class="occupancy-error-msg" style="color:red;"></span></label>
                                </div>

                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align " for="extrabed"> Extra Bed<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control room_occupancy" type="text"  name="extra_bed" id="extrabed" placeholder="ex. Please fill extra bed" value="<?php echo $common_extra_bed[$extra_bed]; ?>">
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  occupancy-label-error hide" id="extrabed-occupancy-error"><span class="extrabed-error-msg" style="color:red;"></span></label>
                                </div> <hr>

                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align " for="total_price"> Total Price<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control room_occupancy" type="text"  name="total_price" id="total_price" placeholder="Please fill total price" value="<?php echo $total_price . $setting_col['price_unit']; ?>">
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  occupancy-label-error hide" id="room-occupancy-error"><span class="occupancy-error-msg" style="color:red;"></span></label>
                                </div>

                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align " for="status"> Status<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control "   name="status" id="status" type="text" value="<?php echo $common_status[$status]; ?>">
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  status-label-error hide" id="room-status-error"><span class="status-error-msg" style="color:red;"></span></label>
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
            let error              = false
            const room_name        = $('.room_name').val();
            const room_occupancy   = $('.room_occupancy').val();
            const room_bed         = $('.room_bed').val();
            const room_size        = $('.room_size').val();
            const room_view        = $('.room_view').val();
            const room_price       = $('.room_price').val();
            const room_extra       = $('.room_extra').val();
            const room_feature     = $('.room_feature').val();
            const room_amenity     = $('.room_amenity').val();
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
            if(room_feature  == ''){
                $('.feature-error-msg').text('')
                $('.feature-error-msg').text('Please fill Special Feature')
                $('.feature-label-error').show()
                error = true
            } else {
                $('.feature-label-error').hide()
            }
            if(room_amenity  == ''){
                $('.amenty-error-msg').text('')
                $('.amenity-error-msg').text('Please fill Room Amenity')
                $('.amenity-label-error').show()
                error = true
            } else {
                $('.amenity-label-error').hide()
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