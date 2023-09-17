<?php
    require('require/common.php');
    require('require/connect.php');
    require('require/setting.php');
    require('require/include_function.php');
    $form    = false;
    $error   = false;
    $err_msg = "";
    $room_id = (int)($_GET['id']);
    $sql     = "SELECT 
                T01.id,
                T01.room_name AS room_name,
                T01.occupancy AS room_occupancy,
                T01.room_size AS room_size,
                T01.room_description AS room_description,
                T01.room_detail AS room_detail,
                T01.room_price_per_day AS room_price_per_day,
                T01.extra_bed_price_per_day AS room_extra_bed_price,
                T02.view_name AS view_name,
                T03.bed_name AS bed_name
                FROM room T01 
                LEFT JOIN view T02
                ON T01.view_id = T02.id
                LEFT JOIN bed T03
                ON T01.bed_id = T03.id
                WHERE T01.id = '$room_id'
                AND T01.deleted_at IS NULL
                AND T02.deleted_at IS NULL
                AND T03.deleted_at IS NULL
                ";
    $room_res     = $mysqli->query($sql);
    $room_res_row = $room_res->num_rows;
    if($room_res_row >= 1) {
      $room_row             = $room_res->fetch_assoc(); 
      $room_name            = htmlspecialchars($room_row['room_name']);
      $room_occupancy       = (int)($room_row['room_occupancy']); 
      $room_size            = (int)($room_row['room_size']);
      $room_description     = htmlspecialchars($room_row['room_description']); 
      $room_detail          = htmlspecialchars($room_row['room_detail']); 
      $room_price_per_day   = (int)($room_row['room_price_per_day']); 
      $room_extra_bed_price = (int)($room_row['room_extra_bed_price']); 
      $view_name            = htmlspecialchars($room_row['view_name']); 
      $bed_name             = htmlspecialchars($room_row['bed_name']); 
     
    } else {
      $form    = true;
      $eror    = true;
      $err_msg = "This ROOM-ID does not exit.";
     
    }

    // start room gallery 
    $gallery_table        = "room_gallery";
    $gallery_col          = ["image"];
    $order_by             = ["id" => "ASC"];
    $where                = ["room_id" => $room_id];
    $gallery_res          = selectQuery($gallery_col,$gallery_table,$mysqli,$order_by,$where);
    $gallery_res_row      = $gallery_res->num_rows;
  // end room gallery 

  // start room amenities 
    $room_amenity_sql = "SELECT T02.amenity_name,T02.amenity_type
                         FROM room_amenity T01 
                         LEFT JOIN amenity T02
                         ON T01.amenity_id = T02.id
                         WHERE T01.room_id='$room_id'
                         AND T01.deleted_at IS NULL AND T02.deleted_at IS NULL ";
    $room_amenity_res = $mysqli->query($room_amenity_sql);
    $room_amenity_row = $room_amenity_res->num_rows; 
    // end room amenities

    // start rom special feature 
    $room_feature_sql = "SELECT T02.special_feature_name
                         FROM room_special_feature T01 
                         LEFT JOIN special_feature T02
                         ON T01.special_feature_id = T02.id
                         WHERE T01.room_id='$room_id'
                         AND T01.deleted_at IS NULL AND T02.deleted_at IS NULL ";
    $room_feature_res = $mysqli->query($room_feature_sql);
    $room_feature_row = $room_feature_res->num_rows; 
    $Booking_url = $base_url . "room/reserve/" . $room_id;
    $title = "ROOMS";
    require('./template/header.php');
    
?>
    <section class="ftco-section">
      <div class="container">
        <?php if($error == true){ ?>
          <div class="col-md-12" style="background-color:#f44336;color:white;text-align:center;">
          <strong><?php echo $err_msg; ?></strong>
        <?php } ?>
        <?php if($form == false){ ?>
        <div class="row">
          <div class="col-lg-8">
          	<div class="row">
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
          		<div class="col-md-12 room-single mt-4 mb-5 ftco-animate">
          			<h2 class="mb-4"><?php echo $room_name;?><span> - <?php echo $room_occupancy; ?> Person(s) Affordable</span></h2>
                <p><?php echo $room_detail; ?></p>
    						<div class="d-md-flex mt-5 mb-5">
    							<ul class="list">
	    							<li><span>Occupancy:</span> <?php echo $room_occupancy; ?></li>
	    							<li><span>Size:</span> <?php echo $room_size; ?>
                    <?php 
                      if(isset($setting_col['room_size_unit'])){
                        echo $setting_col['room_size_unit'];
                      } else {
                        echo "";
                      }
                    ?>
                    </li>
	    						</ul>
	    						<ul class="list ml-md-5">
	    							<li><span>View:</span> <?php echo $view_name; ?></li>
	    							<li><span>Bed:</span> <?php echo $bed_name; ?></li>
	    						</ul>
                  <ul class="list ml-md-5">
	    							<li><span>Price per Day:</span> <?php echo $room_price_per_day. $setting_col['price_unit']; ?></li>
	    							<li><span>Extra Bed Price:</span> <?php echo $room_extra_bed_price . $setting_col['price_unit']; ?></li>
	    						</ul>
    						</div>

                <p><?php echo $room_description; ?></p>
          		</div>
              <a href="<?php echo $Booking_url; ?>" class="btn btn-primary py-3 px-5">Booking</a>
          	</div>
          </div> <!-- .col-md-8 -->
          <div class="col-lg-4 sidebar ftco-animate pl-md-5">
            <div class="sidebar-box ftco-animate">
              <div class="categories">
                <h3>Amenities</h3>
                <?php if($room_amenity_row >= 1){
                while($amenity_row = $room_amenity_res->fetch_assoc()){ 
                  $amenity_name = htmlspecialchars($amenity_row['amenity_name']);
                  $amenity_type = htmlspecialchars($amenity_row['amenity_type']);
                ?>
                  <li><a href="javascript:void(0)"><?php echo $amenity_name ?><span>(<?php echo $amenities[$amenity_type]; ?>)</span></a></li>
                <?php }
              }
                 ?>   
              </div>
            </div>

            <div class="sidebar-box ftco-animate">
              <div class="categories">
                <h3>Facilities</h3>
                <?php if($room_feature_row >= 1){
                while($feature_row = $room_feature_res->fetch_assoc()){ 
                  $feature_name = htmlspecialchars($feature_row['special_feature_name']);
                 
                ?>
                  <li><a href="javascript:void(0)"><?php echo $feature_name; ?></a></li>
                <?php }
              }
                 ?>   
              </div>
            </div>
            </div>
          </div>
        </div>
        <?php } ?>
      </div>
    </section> <!-- .section -->

<?php 
    require('./template/footer.php');
?>