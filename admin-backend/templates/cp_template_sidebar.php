<?php 
  require('../require/setting.php');
 ?>
      <div class="col-md-3 left_col">
          <div class="left_col scroll-view">
            <div class="navbar nav_title" style="border: 0;">
              <a href="<?php echo $cp_base_url; ?>index.php" class="site_title">
              <span>
                <?php 
                 if(count($setting_col) > 0 ) {
                  echo $setting_col['name'];
                 } else {
                  echo "";
                 }         
                ?></span></a>
            </div>

            <div class="clearfix"></div>

            <!-- menu profile quick info -->
            <div class="profile clearfix">
              <div class="profile_pic">
                <?php 
                  if(count($setting_col) > 0 ){ ?>
                    <img src="<?php echo $base_url; ?>assets/upload-img/<?php echo $setting_col['logo_path']?>
                    " alt="..." class="img-circle profile_img" style="width:50px;">
                  <?php } else { ?>
                    <img src="" alt="..." class="img-circle profile_img" style="width:50px;">
                  <?php }
                ?>
                
              </div>
              <div class="profile_info">
                <span>Welcome,</span>
                <h2>
                      <?php
                         if(isset($_SESSION['user_name'])){
                            echo $_SESSION['user_name'];
                         }else{
                          echo $_COOKIE['user_name'];
                         }
                      ?>
                </h2>
              </div>
            </div>
            <!-- /menu profile quick info -->

            <br />

            <!-- sidebar menu -->
            <div id="sidebar-menu" class="main_menu_side hidden-print main_menu">
              <div class="menu_section">
                <h3>General</h3>
                <ul class="nav side-menu">
                <li><a href="<?php echo $cp_base_url; ?>index.php"><i class="fa fa-home"></i>Home</a></li>
                   
                <li><a><i class="fa fa-laptop"></i>View<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="<?php echo $cp_base_url;?>view_create.php">Create</a></li>
                      <li><a href="<?php echo $cp_base_url;?>view_listing.php">Listing</a></li>
                    </ul>
                </li>

                <li><a><i class="fa fa-edit"></i>Bed<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="<?php echo $cp_base_url;?>bed_create.php">Create</a></li>
                      <li><a href="<?php echo $cp_base_url;?>bed_listing.php">Listing</a></li>
                    </ul>
                </li>

                <li><a><i class="fa fa-bank"></i> Amenities<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="<?php echo $cp_base_url;?>amenity_create.php">Create</a></li>
                      <li><a href="<?php echo $cp_base_url;?>amenity_listing.php">Listing</a></li>
                    </ul>
                  </li>

                <li><a><i class="fa fa-beer"></i>Special Feature<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="<?php echo $cp_base_url;?>special_feature_create.php">Create</a></li>
                      <li><a href="<?php echo $cp_base_url;?>special_feature_listing.php">Listing</a></li>
                    </ul>
                  </li>
                  
                <li><a><i class="fa fa-dashboard"></i> Room<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="<?php echo $cp_base_url;?>room_create.php">Create</a></li>
                      <li><a href="<?php echo $cp_base_url;?>room_listing.php">Listing</a></li>
                    </ul>
                </li>
                 
                <li><a><i class="fa fa-edit"></i>Reservation<span class="fa fa-chevron-down"></span></a>
                    <ul class="nav child_menu">
                      <li><a href="<?php echo $cp_base_url;?>reservation_listing.php">Listing</a></li>
                    </ul>
                  </li>
                
                <li><a href="<?php echo $cp_base_url; ?>site_setting.php"><i class="fa fa-home"></i>Site Setting</a></li>
                   
                 
                </ul>
              </div>
            </div>
            <!-- /sidebar menu -->
            <!-- /menu footer buttons -->
            <!-- /menu footer buttons -->
          </div>
      </div>