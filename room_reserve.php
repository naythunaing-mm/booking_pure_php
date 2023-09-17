<?php
    require('require/common.php');
    require('require/connect.php');
    require('require/setting.php');
    require('require/include_function.php');

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
          // $checkin_date_sql = "SELECT count(id) AS checkin_count FROM `reservation` WHERE checkin < '$checkin' AND checkout <= '$checkin' AND status = '1' AND room_id = '$room_id'AND deleted_at IS NULL ";
          $checkin_date_sql = "SELECT COUNT(id) AS checkin_count FROM `reservation` WHERE room_id = '$room_id' AND status = '1' AND deleted_at IS NULL AND (
                                (checkin <= '$checkin' AND checkout > '$checkin') OR
                                (checkin < '$checkout' AND checkout >= '$checkout') OR
                                (checkin >= '$checkin' AND checkout <= '$checkout')
                              )";
          
          $result_checkin   = $mysqli->query($checkin_date_sql);
          
          while($checkin_row = $result_checkin->fetch_assoc()){
            $check_checkin = $checkin_row['checkin_count'];
          }

          // $checkout_date_sql  = "SELECT count(id) AS checkout_count FROM `reservation` WHERE checkin < '$checkout' AND status = '1' AND room_id = '$room_id' AND deleted_at IS NULL ";
          $checkout_date_sql = "SELECT COUNT(id) AS checkout_count FROM `reservation` WHERE room_id = '$room_id' AND status = '1' AND deleted_at IS NULL AND (
                                    (checkin <= '$checkin' AND checkout > '$checkin') OR
                                    (checkin < '$checkout' AND checkout >= '$checkout') OR
                                    (checkin >= '$checkin' AND checkout <= '$checkout')
                                )";
          
          $result_checkout    = $mysqli->query($checkin_date_sql);
          while($checkout_row = $result_checkout->fetch_assoc()){
            $check_checkout   = $checkout_row['checkin_count'];
          }
          
          if($check_checkin > 0 || $check_checkout > 0){
            $error         = true;
            $process_error = true;
            $err_msg       = "This room is already taken. please choose other date or room";
          } else {   
          
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
          
     }
      
  } else {
      $room_id          = (isset($_GET['id'])) ? $_GET['id'] : "";
      $room_id          = $mysqli->real_escape_string($room_id);
      $name             = "";
      $email            = "";
      $phone            = "";
      $checkin          = "";
      $checkout         = "";
      $extra_bed_select = 0;
      
  }  
  
    $table        = "room";
    $room_col     = ["id","room_name","room_price_per_day","extra_bed_price_per_day"];
    $order_by     = ["id" => "ASC"];
    $where        = ["id" => $room_id];
    $room_res     = selectQuery($room_col,$table,$mysqli,$order_by,$where);
    $room_res_row = $room_res->num_rows;

    if($room_res_row >= 1){
      $room_row        = $room_res->fetch_assoc();
      $room_name       = (isset($room_row['room_name']) ? $room_row['room_name'] : "");
      $room_price      = (isset($room_row['room_price_per_day']) ? $room_row['room_price_per_day'] : "");
      $extra_bed_price = (isset($room_row['extra_bed_price_per_day']) ? $room_row['extra_bed_price_per_day'] : "");
      $total_price     = (int)($room_price) + (int)($extra_bed_price);
    } else {
      $error = true;
      $form  = true;
      $err_msg = "This id does not exit in database.";
    }
    $gallery_table   = "room_gallery";
    $gallery_col     = ["image"];
    $order_by        = ["id" => "ASC"];
    $where           = ["room_id" => $room_id];
    $gallery_res     = selectQuery($gallery_col,$gallery_table,$mysqli,$order_by,$where);
    $gallery_res_row = $gallery_res->num_rows;
    $title = "Booking";
    require('./template/header.php');

?>
  
    <section class="ftco-section contact-section bg-light">
      <div class="container">
        
       <h1 style="text-align:center;font-size:23px;" class="mb-4"><?php echo $room_name; ?></h1>
        <div class="row block-9"> 
          <div class="col-md-6 order-md-last d-flex">
            <form  class="bg-white p-5 contact-form" action="<?php echo $base_url; ?>room_reserve.php?id=<?php echo $room_id; ?>" method="POST">
              <div class="form-group d-flex" >
                <label class="col-form-label col-md-4 col-sm-4" for="price">Price<span class="required">*</span></label>
                <input type="text" class="form-control col-md-8 price" id="price"  name="checkin" value="<?php echo $room_price . $setting_col['price_unit']; ?>" disabled readonly/>
              </div>

              <div class="form-group d-flex">
                <label class="col-form-label col-md-4 col-sm-4 " for="extra_bed" >Extra Bed<span class="required">*</span></label>
                <input type="text" class="form-control col-md-8 extra_bed" id="extra_bed" name="extra_bed" value="<?php echo $extra_bed_price . $setting_col['price_unit']; ?>" disabled readonly />
              </div>
          
              <div class="form-group d-flex">
                <label class="col-form-label col-md-4 col-sm-4 " for="total_price">Total Price<span class="required">*</span></label>
                <input type="text" class="form-control col-md-8 total_price" id="total_price"  name="total_price" value="<?php echo $total_price . $setting_col['price_unit']; ?>" disabled readonly>
              </div>

              <div class="form-group d-flex" >
                <label class="col-form-label col-md-4 col-sm-4  " for="extra_bed_select">Extra Bed<span class="required">*</span></label>
                <input type="checkbox" class="form-control col-md-1 extra_bed_select" id="extra_bed_select" value="1" name="extra_bed_select" <?php if($extra_bed_select == '1'){echo "checked";} ?> /> <small style="padding:8px 0px;" id="extra_bed">&nbsp; If you went to take extra bed please Click.</small>
              </div>

              <div class="form-group d-flex">
                <label class="col-form-label col-md-4 col-sm-4 " for="checkin">Checkin Date<span class="required">*</span></label>
                <input type="text" class="form-control col-md-8 checkin" id="checkin" placeholder="Checkin Date" name="checkin" value="<?php echo $checkin; ?>" require />
              </div>

              <div class="form-group d-flex">
              <label class="col-form-label col-md-4 col-sm-4 " for="checkout">Checkout Date<span class="required">*</span></label>
                <input type="text" class="form-control col-md-8 checkout" disabled id="checkout" placeholder="Checkout Date" name="checkout" value="<?php echo $checkout; ?>" require />
              </div>

              <div class="form-group d-flex">
                <label class="col-form-label col-md-4 col-sm-4 " for="name">Name<span class="required">*</span></label>
                <input type="text" class="form-control col-md-8" placeholder="Your Name" name="name" id="name" value="<?php echo $name; ?>" require />
              </div>

              <div class="form-group d-flex">
              <label class="col-form-label col-md-4 col-sm-4 " for="email">Email<span class="required">*</span></label>
                <input type="text" class="form-control col-md-8" placeholder="yourmail@email.com" pattern="[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}" name="email" id="email" value="<?php echo $email; ?>" require />
              </div>

              <div class="form-group d-flex">
              <label class="col-form-label col-md-4 col-sm-4 " for="checkin">Phone<span class="required">*</span></label>
                <input type="number" class="form-control com-md-8" placeholder="+95 XX XXXX XXXX" name="phone" value="<?php echo $phone; ?>" require />
              </div>

              <div class="form-group offset-md-4">
                <input type="submit" value="Booking" class="btn btn-primary py-3 px-5">
                <input type="hidden" name="form-sub" value="1" />
                <input type="hidden" name="room_id" value="<?php echo $room_id; ?>">
              </div>
            </form>
          </div>

          <div class="col-md-6 d-flex">
          <?php if($gallery_res_row >= 1){ ?>
          		<div class="col-md-12 ftco-animate">
          			<div class="single-slider owl-carousel">
                  <?php while($gallery_row = $gallery_res->fetch_assoc()){
                    $image_name = $gallery_row['image'];
                    $gallery_path = $base_url . "assets/upload-img/" . $room_id . "/" . $image_name;
                  ?>
                    <div class="item">
          					<div class="room-img" style="background-image: url(<?php echo $gallery_path ?>);"></div>
          				</div>
                  <?php } ?>
          </div>
          </div>
          <?php } ?>      
          </div>
        </div>
      </div>
    </section>
    
<?php require('./template/footer.php'); ?>

    <script>
      $(document).ready(function() {
          $("#checkin").datepicker({
              minDate: 0,
              onSelect: function(selectedDate) {
                var minDate = new Date(selectedDate);
                minDate.setDate(minDate.getDate()+1);
                  $("#checkout").datepicker("option", "minDate", minDate);
                  $("#checkout").prop("disabled",false);
              }
          });
          
          $("#checkout").datepicker({
              minDate: 0
          });
      });
    </script>

    <script>
      $(document).ready(function() {
        var roomPrice = <?php echo $room_price; ?>;
        var extraBedPrice = <?php echo $extra_bed_price; ?>;
        
        function updatePriceDisplay() {
          var checkinDate    = new Date($("#checkin").val());
          var checkoutDate   = new Date($("#checkout").val());
          var daysDifference = Math.floor((checkoutDate - checkinDate) / (1000 * 60 * 60 * 24)); // Adding 1 to include the last day
          var totalBasePrice = roomPrice * daysDifference; 
          
          if ($(".extra_bed_select").is(":checked")) {
            totalBasePrice += extraBedPrice * daysDifference;
          }

          $(".total_price").val(formatPrice(totalBasePrice));
        }

        function formatPrice(price) {
          return price.toFixed(2) + "" + '<?php echo $setting_col["price_unit"]; ?>';
        }
        
        updatePriceDisplay();
        $(".extra_bed_select, .checkin, .checkout").change(function() {
          updatePriceDisplay();
        });
      });
    </script>
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

        if($success == true){
          echo "
          <script>
                new PNotify({
                title: 'Success',
                text: '$success_msg',
                type: 'success',
                hide: false, 
                styling: 'bootstrap3'
            }); 
          </script>";
        }
    ?>
  </body>
</html>

