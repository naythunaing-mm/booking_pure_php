
<footer class="ftco-footer ftco-section img" style="background-image: url(assets/frontend/images/bg_4.jpg);">
    	<div class="overlay"></div>
      <div class="container">
        <div class="row mb-5">
          <div class="col-md">
            <div class="ftco-footer-widget mb-4">
              <h2 class="ftco-heading-2">
                <?php if(isset($setting_col['name'])) {
                  echo $setting_col['name'];
                } else {
                  echo "";
                }
                ?></h2>
              <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.</p>
              <ul class="ftco-footer-social list-unstyled float-md-left float-lft mt-5">
                <li class="ftco-animate"><a href="#"><span class="icon-twitter"></span></a></li>
                <li class="ftco-animate"><a href="#"><span class="icon-facebook"></span></a></li>
                <li class="ftco-animate"><a href="#"><span class="icon-instagram"></span></a></li>
              </ul>
            </div>
          </div>
          <div class="col-md">
            <div class="ftco-footer-widget mb-4 ml-md-5">
              <h2 class="ftco-heading-2">Useful Links</h2>
              <ul class="list-unstyled">
                <li><a href="rooms" class="py-2 d-block">Rooms</a></li>
                <li><a href="rooms" class="py-2 d-block">Amenities</a></li>
                <li><a href="javascript:void(0)" class="py-2 d-block">Gift Card</a></li>
              </ul>
            </div>
          </div>
          <div class="col-md">
             <div class="ftco-footer-widget mb-4">
              <h2 class="ftco-heading-2">Privacy</h2>
              <ul class="list-unstyled">
                <li><a href="<?php echo $base_url;?>about" class="py-2 d-block">About Us</a></li>
                <li><a href="<?php echo $base_url;?>contact" class="py-2 d-block">Contact Us</a></li>
                <li><a href="<?php echo $base_url;?>service" class="py-2 d-block">Services</a></li>
              </ul>
            </div>
          </div>
          <div class="col-md">
            <div class="ftco-footer-widget mb-4">
            	<h2 class="ftco-heading-2">Have a Questions?</h2>
            	<div class="block-23 mb-3">
	              <ul>
	                <li><span class="icon icon-map-marker"></span><span class="text">
                    <?php if(isset($setting_col['address'])){
                      echo $setting_col['address'];
                    } else {
                      echo "";
                    }
                     ?>
                  </span></li>
	                <li><a href="javascript:void(0)"><span class="icon icon-phone"></span><span class="text">
                    <?php
                      if(isset($setting_col['online_phone'])){
                        echo $setting_col['online_phone'];
                      } else{
                        echo "";
                      }
                    ?>
                  </span></a></li>
	                <li><a href="javascript:void(0)"><span class="icon icon-envelope"></span><span class="text">
                  <?php if(isset($setting_col['email'])){
                      echo $setting_col['email'];
                    } else {
                      echo "";
                    }
                     ?>
                  </span></a></li>
	              </ul>
	            </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 text-center" style="color:white;">
            Copyright &copy;
            <?php if(isset($setting_col['name'])){
                                echo $setting_col['name'];
                              } else {
                                echo "";
                              }
                              ?>
            All rights reserved.
          </div>
        </div>
      </div>
    </footer>
    
  

  <!-- loader -->
  <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>


  <script src="<?php echo $base_url;?>assets/frontend/js/jquery.min.js"></script>
  <script src="<?php echo $base_url;?>assets/frontend/js/jquery-migrate-3.0.1.min.js"></script>
  <script src="<?php echo $base_url;?>assets/frontend/js/popper.min.js"></script>
  <script src="<?php echo $base_url;?>assets/frontend/js/bootstrap.min.js"></script>
  <script src="<?php echo $base_url;?>assets/frontend/js/jquery.easing.1.3.js"></script>
  <script src="<?php echo $base_url;?>assets/frontend/js/jquery.waypoints.min.js"></script>
  <script src="<?php echo $base_url;?>assets/frontend/js/jquery.stellar.min.js"></script>
  <script src="<?php echo $base_url;?>assets/frontend/js/owl.carousel.min.js"></script>
  <script src="<?php echo $base_url;?>assets/frontend/js/jquery.magnific-popup.min.js"></script>
  <script src="<?php echo $base_url;?>assets/frontend/js/aos.js"></script>
  <script src="<?php echo $base_url;?>assets/frontend/js/jquery.animateNumber.min.js"></script>
  <script src="<?php echo $base_url;?>assets/frontend/js/scrollax.min.js"></script>
  <script src="<?php echo $base_url;?>assets/frontend/js/main.js"></script>
  
  <!-- datepicker  -->
  <script src="<?php echo $base_url;?>template/Datepicker/jquery.js"></script>
  <script src="<?php echo $base_url;?>template/Datepicker/jquery-ui.js"></script>
  
  </body>
</html>