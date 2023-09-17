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

      }else if($_GET['msg'] == 'confirm'){
        $success     = true;
        $success_msg = "Confirm Reservation  successful!";

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
    $title         = "Room Listing";

    require('templates/cp_template_header.php');
    require('templates/cp_template_sidebar.php');
    require('templates/cp_template_topnav.php');

?>

<div class="right_col" role="main">
<div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>reservation Listing: <small>reservation</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <div class="row">
                      <div class="col-sm-12">
                        <div class="card-box table-responsive">
                          <p class="text-muted font-13 m-b-30">
                            The following data are showing for Reservation.
                          </p>

                          <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">                            <thead>
                              <tr>
                                <th>ID</th>
                                <th>Room Name</th>
                                <th>Customer Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Checkin</th>
                                <th>Checkout</th>
                                <th>ExtraBed</th>
                                <th>Total Price</th>
                                <th>Status</th>
                                <th>Action</th>
                              </tr>
                            </thead>
                            <tbody>
                            <?php
                                if($res_rows >= 1){
                                    while($row = $result->fetch_assoc()){
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
                                        $view_url       = $cp_base_url."reservation_view.php?id=" . $reservation_id;
                                        $confirm_url    = $cp_base_url."reservation_confirm.php?id=" . $reservation_id;
                                    ?> 
                                        <tr>
                                        <td><?php echo $reservation_id; ?></td>
                                        <td><?php echo $room_name; ?></td>
                                        <td><?php echo $customer_name; ?></td>
                                        <td><?php echo $phone; ?></td>
                                        <td><?php echo $email; ?></td>
                                        <td><?php echo $checkin; ?></td>
                                        <td><?php echo $checkout; ?></td>
                                        <td><?php echo $common_extra_bed[$extra_bed]; ?></td>
                                        <th><?php echo $total_price . $setting_col['price_unit']; ?></th>
                                        <th><?php echo $common_status[$status]; ?></th>
                                  
                                        
                                        <td>
                                        <a href="<?php echo $confirm_url; ?>" class="btn btn-primary btn-xs">Confirm</a>
                                        <a href="<?php echo $view_url; ?>" class="btn btn-primary btn-xs"><i class="fa fa-eye"> View</i></a>
                                        <a href="<?php echo $edit_url; ?>" class="btn btn-info btn-xs"><small><i class="fa fa-pencil"> Edit</i></small></a>
                                        <a href="<?php echo $delete_url; ?>" class="btn btn-danger btn-xs"><small><i class="fa fa-trash-o"> Delete</i></small></a>
                                        </td>
                                    </tr>
                            <?php   
                                 }
                                }  
                            ?>        
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
</div>

<?php require('templates/cp_template_footer.php'); ?>
         <!-- pnotify -->
    <script src="<?php echo $base_url; ?>assets/backend/css/pnotify/pnotify.js"></script>
    <script src="<?php echo $base_url; ?>assets/backend/css/pnotify/pnotify.buttons.js"></script>
    <script src="<?php echo $base_url; ?>assets/backend/css/pnotify/pnotify.nonblock.js"></script>
<?php
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