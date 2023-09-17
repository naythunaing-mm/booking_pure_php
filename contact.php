<?php 
  require('require/common.php');
  require('require/connect.php');
  require('require/setting.php');
  require('require/include_function.php');
  
  $error        = false;
  $err_msg      = "";
  $process_err  = false; 
  $table        = 'customer_contact';
  $success      = false;
  $success_msg  = '';
  
  
  if(isset($_POST['form-sub']) && $_POST['form-sub'] == 1){
      $name    = $mysqli->real_escape_string($_POST['name']);
      $email   = $mysqli->real_escape_string($_POST['email']);
      $city    = $mysqli->real_escape_string($_POST['city']);
      $message = $mysqli->real_escape_string($_POST['message']);

     

      if($name  == ''){
          $error       = true;
          $process_err = true;
          $err_msg     = "Please Fill Name.";
      }
      if($email == ''){
          $error       = true;
          $process_err = true;
          $err_msg     = "Please fill Email Address.";
      }
      if($city == ''){
        $error       = true;
        $process_err = true;
        $err_msg     = "Please fill your City or Township.";
      }
      if($message == ''){
        $error       = true;
        $process_err = true;
        $err_msg     = "Please fill Message.";
      }
     if($process_err == false){
      $today_date  = date('Y-m-d H:i:s');
      $insert_data = array(
                              'name'         => "'$name'",
                              'email'        => "'$email'",
                              'city'         => "'$city'",
                              'message'      => "'$message'",
                              'created_at'   => "'$today_date'",
                  
                          );
      $insert = insertQuery($insert_data, $table, $mysqli);
      if($insert){
        $success      = true;
        $success_msg  = 'Thanks for your contact.';
      }
     }
      
  
  }
  $title    = "Contact";
  require('./template/header.php');
?>
    <section class="ftco-section contact-section bg-light">
      <div class="container">
        <div class="row d-flex mb-5 contact-info">
          <div class="col-md-12 mb-4">
            <h2 class="h3">Contact Information</h2>
          </div>
          <div class="w-100"></div>
          <div class="col-md-3 d-flex">
          	<div class="info rounded bg-white p-4">
	            <p><span>Address:</span> 
                <?php 
                    if(isset($setting_col['address'])){
                        echo $setting_col['address'];
                    } else {
                        echo "";
                    }
                ?>
                </p>
	          </div>
          </div>
          <div class="col-md-3 d-flex">
          	<div class="info rounded bg-white p-4">
	            <p><span>Online Phone</span> <a href="">
                <?php 
                    if(isset($setting_col['online_phone'])){
                        echo $setting_col['online_phone'];
                    } else {
                        echo "";
                    }
                ?>
                </a></p>
	          </div>
          </div>
          <div class="col-md-3 d-flex">
          	<div class="info rounded bg-white p-4">
	            <p><span>Phone:</span> <a href="javascript:void(0);">
                <?php 
                    if(isset($setting_col['outline_phone'])){
                        echo $setting_col['outline_phone'];
                    } else {
                        echo "";
                    }
                ?>
                </a></p>
	          </div>
          </div>
          <div class="col-md-3 d-flex">
          	<div class="info rounded bg-white p-4">
	            <p><span>Email:</span> <a href="mailto:info@yoursite.com">
                <?php 
                    if(isset($setting_col['email'])){
                        echo $setting_col['email'];
                    } else {
                        echo "";
                    }
                ?>
                </a></p>
	          </div>
          </div>
          
        </div>
        <div class="row block-9">
          <div class="col-md-6 order-md-last d-flex">
            <form action="contact.php" class="bg-white p-5 contact-form" method="post">
              <div class="form-group">
                <input type="text" class="form-control" placeholder="Your Name" name="name">
              </div>
              <div class="form-group">
                <input type="text" class="form-control" placeholder="Your Email" name="email">
              </div>
              <div class="form-group">
                <input type="text" class="form-control" placeholder="City" name="city">
              </div>
              <div class="form-group">
                <textarea name="message" id="" cols="30" rows="5" class="form-control" placeholder="Message" ></textarea>
              </div>
              <div class="form-group">
                <input type="submit" value="Send Message" class="btn btn-primary py-3 px-5">
                <input type="hidden" name="form-sub" value="1">
              </div>
            </form>
          
          </div>

          <div class="col-md-6 d-flex">
          	<div id="map" class="bg-white">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3818.920199614829!2d96.1271119340391!3d16.830314483320876!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x30c195280d8ece71%3A0xae58d36ddceb3e81!2sSoftGuide%20Hledan!5e0!3m2!1sen!2smm!4v1693315975524!5m2!1sen!2smm" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
          </div>
        </div>
      </div>
    </section>
<?php require('./template/footer.php'); ?>

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

    if($success== true){
      echo "
      <script>
            new PNotify({
            title: 'success',
            text: '$success_msg',
            type: 'success',
            hide: false,
            styling: 'bootstrap3'
        }); 
      </script>";
    }
?>