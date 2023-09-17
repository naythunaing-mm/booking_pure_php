<?php
  session_start();
    require('../require/common.php');
    require('../require/connect.php');
    require('../require/check_authentication.php');
    require('../require/include_function.php');
    
    $success     = false;
    $success_msg = "";
    $error       = false;
    $err_msg     = "";

    if(isset($_GET['msg'])){
      if($_GET['msg']=='success'){
      $success     = true;
      $success_msg = "Insert Special Feature Data successful!";

      }else if($_GET['msg'] == 'update'){
      $success     = true;
      $success_msg = "Update Special Feature Data successful! ";

     }else if($_GET['msg'] =='delete'){
      $success     = true;
      $success_msg = "Delete Special Feature data  successful!";

    }else if($_GET['msg'] == 'delete'){
      $error   = true;
      $err_msg = "Something wrong!";
    }else{
      $error   = true;
      $err_msg = "Something wrong!";
    }
  }

    $table         = 'special_feature';
    $select_column = ["id","special_feature_name"];
    $order_by      = [
      'id'         => 'DESC'
     ];
    $result        = selectQuery($select_column,$table,$mysqli,$order_by);
    $res_rows      = $result->num_rows;
    $title         = "Special Feature Listing";

    require('templates/cp_template_header.php');
    require('templates/cp_template_sidebar.php');
    require('templates/cp_template_topnav.php');

?>
<div class="right_col" role="main">
<div class="col-md-12 col-sm-12 ">
                <div class="x_panel">
                  <div class="x_title">
                    <h2>Special Feature Listing: <small>Special Feature</small></h2>
                    <ul class="nav navbar-right panel_toolbox">
                    </ul>
                    <div class="clearfix"></div>
                  </div>
                  <div class="x_content">
                    <div class="row">
                      <div class="col-sm-12">
                        <div class="card-box table-responsive">
                          <p class="text-muted font-13 m-b-30">
                            The following data are showing for Special Feature.
                          </p>

                          <table id="datatable-responsive" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                            <thead>
                              <tr>
                                <th>Special Feature ID</th>
                                <th>Special Feature Name</th>
                                <th>Action</th>
                              </tr>
                            </thead>
                            <tbody>
                            <?php
                                if($res_rows >= 1){
                                    while($row = $result->fetch_assoc()){
                                        $special_feature_id   = (int)$row['id'];
                                        $special_feature_name = htmlspecialchars($row['special_feature_name']);
                                        $edit_url   = $cp_base_url."special_feature_edit.php?id=" . $special_feature_id;
                                        $delete_url = $cp_base_url."special_feature_delete.php?id=" . $special_feature_id;
                                    ?> 
                                        <tr>
                                        <td><?php echo $special_feature_id; ?></td>
                                        <td><?php echo $special_feature_name; ?></td>
                                        <td>
                                        <a href="<?php echo $edit_url; ?>" class="btn btn-info btn-xs"><small><i class="fa fa-pencil"></i>Edit</small></a>
                                        <a href="<?php echo $delete_url; ?>" class="btn btn-danger btn-xs"><small><i class="fa fa-trash-o"></i>Delete</small></a>
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