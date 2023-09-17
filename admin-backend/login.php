 <?php
    session_start();
    require('../require/common.php');
    require('../require/connect.php');
   
    $title     = "Hotel Booking::Admin Login";
    $user_name = "";
    $remember  = 0;
    $error     = false;
    $err_msg   = "";
    
    if(isset($_POST['form-sub']) && $_POST['form-sub'] == 1){
      $user_name     = $_POST['user-name'];
      $user_password = $_POST['user-password'];
      $encrypt_pass  = md5(md5($user_password) . $site_config['key']);
      $remember      = (isset($_POST['remember'])) ? $_POST['remember'] : 0 ;

      if($remember == 0){
        $sql     = "SELECT * FROM `user` WHERE user_name= '$user_name' OR user_email= '$user_name'";
        $result  = $mysqli->query($sql);
        $res_row = $result->num_rows;

        if($res_row >=1 ){
          while($row = $result->fetch_assoc()){
            $user_id     = (int)$row['user_id'];
            $user_name   = htmlspecialchars($row['user_name']);
            $user_email  = htmlspecialchars($row['user_email']);
            $db_password = $row['user_password'];

            if($db_password == $encrypt_pass){
              $_SESSION['user_name']  = $user_name;
              $_SESSION['user_id']    = $user_id;
              $_SESSION['user_email'] = $user_email;

              $url = $cp_base_url . "index.php";
              header("Refresh: 0; url=$url");
              exit();
            }else{
              $error   = true;
              $err_msg = "Wrong Username";
            }
          }
        }else{
          $error   = true;
          $err_msg = "Wrong Password";
        }
       }else{
        $sql      = "SELECT * FROM `user` WHERE user_name= '$user_name' OR user_email = '$user_name'";
        $result   = $mysqli->query($sql);
        $res_row  = $result->num_rows;

        if($res_row >= 1){
          while($row = $result->fetch_assoc()){
            $user_id     = (int)($row['user_id']);
            $user_name   = htmlspecialchars($row['user_name']);
            $user_email  = htmlspecialchars($row['user_email']);
            $db_password = $row['user_password'];

            if($db_password == $encrypt_pass){
              $cookie_name  = "user_name";
              $cookie_value = $user_name;
              setcookie($cookie_name, $cookie_value, time()+ (86400 * 30), "/");

              $cookie_name  = "user_id";
              $cookie_value = $user_id;
              setcookie($cookie_name, $cookie_value, time()+ (86400 * 30), "/");

              $cookie_name  = "user_email";
              $cookie_value = $user_email;
              setcookie($cookie_name, $cookie_value, time()+ (86400 * 30), "/");

              $url = $cp_base_url . "index.php";
              header("Refresh: 0; url=$url");
              exit();
          } else {
            $error   = true;
            $err_msg = "Wrong Password";
          }
        }
        }
      }
    }
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <!-- Meta, title, CSS, favicons, etc. -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?php echo $title ?> </title>

    <!-- Bootstrap -->
    <link href="<?php echo $base_url; ?>assets/backend/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <!-- <link href="../vendors/font-awesome/css/font-awesome.min.css" rel="stylesheet"> -->
    <link href="<?php echo $base_url; ?>assets/backend/css/font-awesome/font-awesome.min.css" rel="stylesheet">

    <!-- Animate.css -->
    <link href="<?php echo $base_url; ?>assets/backend/css/animate/animate.min.css" rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="<?php echo $base_url; ?>assets/backend/css/custom.min.css" rel="stylesheet">

     <!-- PNotify -->
     <link href="<?php echo $base_url; ?>assets/backend/css/pnotify/pnotify.css" rel="stylesheet">
    <link href="<?php echo $base_url; ?>assets/backend/css/pnotify/pnotify.buttons.css" rel="stylesheet">
    <link href="<?php echo $base_url; ?>assets/backend/css/pnotify/pnotify.nonblock.css" rel="stylesheet">

  </head>
  <body class="login">
    <div>
      <a class="hiddenanchor" id="signup"></a>
      <a class="hiddenanchor" id="signin"></a>

      <div class="login_wrapper">
        <div class="animate form login_form">
          <section class="login_content">
            <form action="<?php echo $cp_base_url;?>login.php" method="post" />
              <h1>Login Form</h1>
              <div>
                <input type="text" class="form-control" placeholder="Username" value="<?php echo $user_name;?>" required="" name="user-name" />
              </div>
              <div>
                <input type="password" class="form-control" placeholder="Password" required="" name="user-password" />
              </div>
              <div class="checkbox">
                <label for="remember">
                  <input type="checkbox" name="remember" id="remember" value="1" <?php if($remember == '1'){echo "checked";}?>> Remember Me ?
                </label>
              </div>
              <div>
                <button type="submit" class="btn btn-secondary" name="submit" >Login</button>
                <input type="hidden" value="1" name="form-sub" />
              </div>

              <div class="clearfix"></div>

              <div class="separator">
                <p class="change_link">New to site?
                  <a href="javascript:void(0);" class="to_register"> Create Account </a>
                </p>

                <div class="clearfix"></div>
                <br />

                <div>
                  <h1><i class="fa fa-paw"></i> <?php echo $site_config['name'];?></h1>
                  <p>©2016 All Rights Reserved. <?php echo $site_config['name'];?>! is a Bootstrap 4 template. Privacy and Terms</p>
                </div>
              </div>
            </form>
          </section>
        </div>

      </div>
    </div>
    <!-- jQuery -->
    <script src="<?php echo $base_url; ?>assets/backend/jquery/jquery.min.js"></scripjavascript:void>
       
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
  </body>
</html>