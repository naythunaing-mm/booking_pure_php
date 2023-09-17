<?php
  session_start();
    require('../require/common.php');
    require('../require/connect.php');
    require('../require/check_authentication.php');
    require('../require/include_function.php');

    $err_msg     = "";
    $success_msg = "";
    $success     = false;
    $error       = false;

    if(isset($_GET['msg'])){
      if($_GET['msg'] =='success'){
        $success     = true;
        $success_msg = "Insert Hotel Room View successful!";

      }else if($_GET['msg']=='update'){
        $success     = true;
        $success_msg = "Update Hotel Room View Data successful! ";

      }else if($_GET['msg'] == 'delete'){
        $success       = true;
        $success_msg   = "Delete Hotel Room View data  successful!";

      }else if($_GET['msg'] == 'delete'){
        $error   = true;
        $err_msg = "Something wrong!";

      }else{
        $error   = true;
        $err_msg = "Something wrong!";
      }
   }

    $table       = 'reservation';
    $selectQuery = "SELECT
                    T01.id,
                    T01.checkin,
                    T01.checkout,
                    T01.extra_bed,
                    T01.total_price,
                    T01.status,
                    T02.room_name AS room_name,
                    T03.customer_name,
                    T03.customer_phone,
                    T03.customer_email
                    FROM `reservation` T01
                    LEFT JOIN `room` T02 
                    ON T01.room_id = T02.id
                    LEFT JOIN `customer` T03 
                    ON T01.customer_id = T03.id
                    WHERE T01.deleted_at IS NULL
                    ORDER BY T01.id DESC";

    $result        = $mysqli->query($selectQuery);
    $res_rows      = $result->num_rows;
    if($res_rows >= 1){
        $row = $result->fetch_assoc();
        $reservation_id = (int)$row['id'];
        $room_name      = htmlspecialchars($row['room_name']);
        $customer_name  = htmlspecialchars($row['customer_name']);
        $phone          = htmlspecialchars($row['customer_phone']);
        $email          = htmlspecialchars($row['customer_email']);
        $checkin        = htmlspecialchars($row['checkin']);
        $checkout       = htmlspecialchars($row['checkout']);
        $extra_bed      = htmlspecialchars($row['extra_bed']);
        $total_price    = htmlspecialchars($row['total_price']);
        $status         = htmlspecialchars($row['status']);
        $edit_url       = $cp_base_url."reservation_edit.php?id=" . $reservation_id;
        $delete_url     = $cp_base_url."reservation_delete.php?id=" . $reservation_id;
        $view_url       = $cp_base_url."reservation_detail.php?id=" . $reservation_id;
    } else {
        $error   = true;
        $err_msg = "Something wrong.";
    }

    $title         = "Room Listing";

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
                                <span class="section">Reservation</span>
                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align " for="occupancy">Customer Name <span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control room_occupancy" type="text"  name="room_occupancy" id="occupancy" placeholder="ex. 2 or 3" value="<?php echo $customer_name; ?>">
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  occupancy-label-error hide" id="room-occupancy-error"><span class="occupancy-error-msg" style="color:red;"></span></label>
                                </div>

                                <div class=" field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align " for="bed">Customer Phone <span calss="required">*</span> </label>
                                    <div class="col-md-6 col-sm-6">
                                    <input class="form-control room_occupancy" type="text"  name="room_occupancy" id="occupancy" placeholder="ex. 2 or 3" value="<?php echo $phone; ?>">

                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  bed-label-error hide"><span class="bed-error-msg" style="color:red;"></span></label>
								</div>

                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align" for="room_size">Customer Email<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                    <input class="form-control room_occupancy" type="text"  name="room_occupancy" id="occupancy" placeholder="ex. 2 or 3" value="<?php echo $email; ?>">
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3 size-label-error hide"><span class="size-error-msg" style="color:red;"></span></label>
                                </div> <hr>

                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align" for="name">Room Name<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control room_name" name="room_name" placeholder="ex. Depluex" type="text" id="name" value="<?php echo $room_name; ?>" />
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  label-error hide" id="room-name-error"><span class="name-error-msg"></span></label>
                                </div>

                                <div class=" field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align " for="view">Checkin Date<span calss="required">*</span> </label>
                                    <div class="col-md-6 col-sm-6">
                                    <input class="form-control room_occupancy" type="text"  name="room_occupancy" id="occupancy" placeholder="ex. 2 or 3" value="<?php echo $checkin; ?>">

                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  view-label-error hide" id="room-view-error"><span class="view-error-msg" style="color:red;"></span></label>
								</div>
                                <div class=" field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align " for="view">Checkout Date<span calss="required">*</span> </label>
                                    <div class="col-md-6 col-sm-6">
                                    <input class="form-control room_occupancy" type="text"  name="room_occupancy" id="occupancy" placeholder="ex. 2 or 3" value="<?php echo $checkout; ?>">
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  view-label-error hide" id="room-view-error"><span class="view-error-msg" style="color:red;"></span></label>
								</div>

                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align" for="price_per_day">Extra Bed <span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control room_price" type="text"  name="room_price" data-validate-minmax="10,100" required='required' id="price_per_day" placeholder="ex. 30$" value="<?php echo $extra_bed.$setting_col['price_unit']; ?>">
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  price-label-error hide" id="room-price-error"><span class="price-error-msg" style="color:red;"></span></label>
                                </div> <hr>

                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align" for="extra_price">Total Price <span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input class="form-control room_extra" type="text"  name="room_extra_bed_price" data-validate-minmax="10,100" required='required' id="extra_price" placeholder="ex. 1.8$"value="<?php echo $total_price.$setting_col['price_unit']; ?>" >
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  extra-label-error hide" id="room-extra-error"><span class="extra-error-msg" style="color:red;"></span></label>
                                </div>
                                
                                <div class="form-group row">
                                    <label class="control-label col-md-3 col-sm-3 label-align" for="description">Status<span class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 ">
                                    <input class="form-control room_extra" type="text"  name="room_extra_bed_price" data-validate-minmax="10,100" required='required' id="extra_price" placeholder="ex. 1.8$"value="<?php echo $common_status[$status]; ?>" >
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  des-label-error hide" id="des-name-error"><span class="des-error-msg" style="color:red;"></span></label>
                                </div>

                                <div class="">
                                    <div class="form-group">
                                        <div class="col-md-6 offset-md-3">
                                            <input type="hidden" name="form-sub" value="1" />
                                            <input type="hidden" name="id" value="<?php echo $id; ?>">
                                            <button onclick="printPage()" type="button" class="btn btn-success">Print</button>
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
        function printPage() {
            window.print();
        }
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