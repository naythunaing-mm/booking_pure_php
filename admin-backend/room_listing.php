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

    $table       = 'room';
    $selectQuery = "SELECT
                    T01.id,
                    T01.room_name,
                    T01.occupancy,
                    T01.room_size,
                    T01.room_price_per_day,
                    T01.extra_bed_price_per_day,
                    T02.view_name AS view_name,
                    T03.bed_name AS bed_name
                    FROM `room` T01
                    LEFT JOIN `view` T02 
                    ON T01.view_id = T02.id
                    LEFT JOIN `bed` T03 
                    ON T01.bed_id = T03.id
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
                    <h2>Room Listing: <small>Room</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <div class="row">
                      <div class="col-sm-12">
                        <div class="card-box table-responsive">
                          <p class="text-muted font-13 m-b-30">
                            The following data are showing for room.
                          </p>

                          <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                            <thead>
                              <tr>
                                <th>ID</th>
                                <th>Room</th>
                                <th>Occupancy</th>
                                <th>Bed</th>
                                <th>Size</th>
                                <th>View</th>
                                <th>Price</th>
                                <th>Extra Bed</th>
                                <th>Action</th>
                              </tr>
                            </thead>
                            <tbody>
                            <?php
                                if($res_rows >= 1){
                                    while($row = $result->fetch_assoc()){
                                        $room_id    = (int)$row['id'];
                                        $name       = htmlspecialchars($row['room_name']);
                                        $occupancy  = htmlspecialchars($row['occupancy']);
                                        $bed        = htmlspecialchars($row['bed_name']);
                                        $size       = htmlspecialchars($row['room_size']);
                                        $view       = htmlspecialchars($row['view_name']);
                                        $price      = htmlspecialchars($row['room_price_per_day']);
                                        $extra      = htmlspecialchars($row['extra_bed_price_per_day']);
                                        $gallery_url = $cp_base_url."room_gallery.php?id=" . $room_id;
                                        $edit_url   = $cp_base_url."room_edit.php?id=" . $room_id;
                                        $delete_url = $cp_base_url."room_delete.php?id=" . $room_id;
                                        $view_url   = $cp_base_url."room_detail.php?id=" . $room_id;
                                    ?> 
                                        <tr>
                                        <td><?php echo $room_id; ?></td>
                                        <td><?php echo $name; ?></td>
                                        <td><?php echo $occupancy; ?></td>
                                        <td><?php echo $bed; ?></td>
                                        <td><?php echo $size; ?></td>
                                        <td><?php echo $view; ?></td>
                                        <td><?php echo $price . $setting_col['price_unit']; ?></td>
                                        <td><?php echo $extra . $setting_col['price_unit']; ?></td>
                                        <td>
                                        <a href="<?php echo $gallery_url; ?>" class="btn btn-success btn-xs"><small><i class="fa fa-photo"> Gallery</i></small></a>
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