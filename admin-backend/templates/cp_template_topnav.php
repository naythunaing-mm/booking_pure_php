
<div class="top_nav">
          <div class="nav_menu">
              <div class="nav toggle">
                <a id="menu_toggle"><i class="fa fa-bars"></i></a>
              </div>
              <nav class="nav navbar-nav">
              <ul class=" navbar-right">
                <li class="nav-item dropdown open" style="padding-left: 15px;">
                  <a href="javascript:;" class="user-profile dropdown-toggle" aria-haspopup="true" id="navbarDropdown" data-toggle="dropdown" aria-expanded="false">
                    <img src="images/img.jpg" alt=""><?php 
                    if(isset($_SESSION['user_name'])){
                      echo $_SESSION['user_name'];
                    }else{
                      echo $_COOKIE['user_name'];
                    }
                     ?>
                  </a>
                  <div class="dropdown-menu dropdown-usermenu pull-right" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item"  href="<?php echo $cp_base_url; ?>logout.php"><i class="fa fa-sign-out pull-right"></i> Logout</a>
                  </div>
                </li>

                  </ul>
                </li>
              </ul>
            </nav>
          </div>
        </div>