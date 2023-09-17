<?php 
	require('require/common.php');
	require('require/connect.php');
	require('require/setting.php');
	require('require/include_function.php');
	
    $checkin          = $_GET['checkin'];
    $checkout         = $_GET['checkout'];
	$checkin 	      = date('Y-m-d', strtotime($checkin));
	$checkout         = date('Y-m-d', strtotime($checkout));
	$select_room_data = "SELECT id, room_name, room_price_per_day, room_thumbnail 
                     	 FROM `room` 
                     	 WHERE id NOT IN (
                         SELECT DISTINCT room_id 
                         FROM `reservation` 
                         WHERE 
						 (
                             (checkin < '$checkout' AND checkout > '$checkin') OR
                             (checkin < '$checkout' AND checkout > '$checkout') OR
                             ('$checkin' BETWEEN checkin AND checkout)
                         ) 
                         AND status = '1' 
                         AND deleted_at IS NULL
                     	 ) 
                     	 AND deleted_at IS NULL";
	$result_room_data = $mysqli->query($select_room_data);
	$result_row		  = $result_room_data->num_rows;

	$title    = "Avaliable Rooms";
	require('./template/header.php');
?>

    <section class="ftco-section ftco-no-pb ftco-room">
    	<div class="container-fluid px-0">
    		<div class="row no-gutters justify-content-center mb-5 pb-3">
          <div class="col-md-7 heading-section text-center ftco-animate">
          	<span class="subheading"><?php echo $title; ?></span>
            <h2 class="mb-4">Hotel Master's Rooms</h2>
          </div>
        </div>  
    		<div class="row no-gutters">
			<?php 	
				if($result_row >= 1 ){
					$room_count = 0;
					$room_line = 1;
					while($row = $result_room_data->fetch_assoc()){
						$room_count++;
						
						if(($room_line%2) == 0){
							$class1 = "order-md-last";
							$class2 = "right-arrow";
						} else {
							$class1 = "";
							$class2 = "left-arrow";
						}
						if($room_count == 2){
							$room_line++;
							$room_count = 0;
						}
						
						$room_id	 = (int)($row['id']);
						$detail_link = $base_url . "room/detail/" . $room_id;
						$room_name   = htmlspecialchars($row['room_name']);
						$thumbnail   = htmlspecialchars($row['room_thumbnail']);
						$price       = htmlspecialchars($row['room_price_per_day']); ?>
						<div class="col-lg-6">
    					<div class="room-wrap d-md-flex ftco-animate">
						<img src="<?php echo getuploadImage($thumbnail,$room_id);?>" alt="" class="<?php echo $class1; ?>">
    					<div class="half  <?php echo $class2; ?> d-flex align-items-center">
    						<div class="text p-4 text-center">
    							<p class="star mb-0"><span class="ion-ios-star"></span><span class="ion-ios-star"></span><span class="ion-ios-star"></span><span class="ion-ios-star"></span><span class="ion-ios-star"></span></p>
    							<p class="mb-0"><span class="price mr-1"><?php echo $price.$setting_col['price_unit']; ?></span> <span class="per">per night</span></p>
	    						<h3 class="mb-3"><a href="<?php echo $base_url; ?>"><?php echo $room_name; ?></a></h3>
	    						<p class="pt-1"><a href="<?php echo $detail_link; ?>" class="btn-custom px-3 py-2 rounded">View Details <span class="icon-long-arrow-right"></span></a></p>
    						</div>
    					</div>
    				</div>
    			</div>
				<?php	}

				}
			?>
    			
    		</div>
    	</div>
    </section>

<?php require('./template/footer.php');?>
