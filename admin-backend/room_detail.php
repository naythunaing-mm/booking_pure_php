<?php
  session_start();
    require('../require/common.php');
    require('../require/connect.php');
    require('../require/setting.php');
    require('../require/check_authentication.php');
    require('../require/include_function.php');
    
    $error       = false;
    $process_err = false; 
    $err_msg     = "";

    $table            = "room";
    $amenities        = [];
    $special_feature  = [];
    
    $sf_table         = "special_feature";
    $sf_select_column = ["id","special_feature_name"];
    $sf_order_by      = ["id" => "DESC"];
    $sf_result        = selectQuery($sf_select_column,$sf_table,$mysqli,$sf_order_by);
    $sf_res_rows      = $sf_result->num_rows;

    $bed_table         = "bed";
    $bed_select_column = ["id","bed_name"];
    $bed_order_by      = ["id"  => "DESC"];
    $bed_result        = selectQuery($bed_select_column,$bed_table,$mysqli,$bed_order_by);
    $bed_res_rows      = $bed_result->num_rows;

    $view_table         = "view";
    $view_select_column = ["id","view_name"];
    $view_order_by      = ["id"  => "DESC"];
    $view_result        = selectQuery($view_select_column,$view_table,$mysqli,$view_order_by);
    $view_res_rows      = $view_result->num_rows;
    
    $amenity_table = "amenity";
    $select_column = ["id","amenity_name"];
    $order_by      = ["id"   => "DESC"];
    $result        = selectQuery($select_column,$amenity_table,$mysqli,$order_by);
    $res_rows      = $result->num_rows;

    $id = (int)($_GET['id']);
    $table = "room";
    $select_column = [
                        "id","room_name","occupancy","bed_id","room_size",
                        "view_id","room_description","room_detail","room_price_per_day",
                        "extra_bed_price_per_day","room_thumbnail"
    ];
    $room_res = selectQueryById($select_column,$table,$id,$mysqli);
    $room_res_row = $room_res->num_rows;
    if($room_res_row <= 0) {
    $error = true;
    $err_msg = "This id does not exist in database.";
    } else {
    $row                  = $room_res->fetch_assoc();
    $room_name            = htmlspecialchars($row['room_name']);
    $room_occupancy       = htmlspecialchars($row['occupancy']);
    $room_bed             = (int)($row['bed_id']);
    $room_size            = (int)($row['room_size']);
    $room_view            = (int)($row['view_id']);
    $room_price           = htmlspecialchars($row['room_price_per_day']);
    $room_extra_bed_price = htmlspecialchars($row['extra_bed_price_per_day']);
    $thumbnail            = htmlspecialchars($row['room_thumbnail']);
    $thumbnail_path       = $base_url . "assets/upload-img/" . $id . "/thumb/" . $thumbnail;
    $room_description     = htmlspecialchars($row['room_description']);
    $room_detail          = htmlspecialchars($row['room_detail']);

    // start room special 
    $room_feature         = "room_special_feature";
    $room_feature_column  = ["special_feature_id"];
    $room_feature_order_by = ["id" => "DESC"];
    $room_feature_where   = ["room_id" => $id];
    $room_feature_res     = selectQuery($room_feature_column,$room_feature,$mysqli,$room_feature_order_by,$room_feature_where);
    $room_feature_res_rows = $room_feature_res->num_rows;
    while($room_feature_row = $room_feature_res->fetch_assoc()){
        array_push($special_feature,$room_feature_row['special_feature_id']);
    }
    // end room special feature 

    // start room amenity 
    $room_amenity = "room_amenity";
    $room_amenity_column = ["amenity_id"];
    $order_by = ["id" => "DESC"];
    $room_amenity_where = ["room_id" => $id];
    $room_amenity_res = selectQuery($room_amenity_column,$room_amenity,$mysqli,$order_by,$room_amenity_where);
    while($room_amenity_row = $room_amenity_res->fetch_assoc()){
        array_push($amenities,$room_amenity_row['amenity_id']);
    }
    }

    $title = "Room Detail";

    require('templates/cp_template_header.php');
    require('templates/cp_template_sidebar.php');
    require('templates/cp_template_topnav.php');
?>
<div class="right_col" role="main">
    <div class="">
        <div class="page-title">
            <div class="title_left">
                <h3>Hotel Room Detail</h3>
            </div>
        </div>
        <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="x_panel">
                        <div class="x_content">
                            <form action="<?php echo $cp_base_url; ?>room_edit.php" method="POST" id="form-create" enctype="multipart/form-data"  >
                                <span class="section">Room Detail</span>
                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align">Image<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <div id="preview-wrapper">
                                            <div class="" id="preview-img" >
                                                <img disabled src="<?php echo $thumbnail_path; ?>" alt="" id="upload-img">
                                            </div>
                                         </div> 
                                    </div>
                                </div>

                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align" for="name">Name<span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input disabled class="form-control room_name" name="room_name" placeholder="ex. Depluex" type="text" id="name" value="<?php echo $room_name; ?>" />
                                    </div>
                                </div>
                                
                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align " for="occupancy">Occupancy <span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input disabled class="form-control room_occupancy" type="number"  name="room_occupancy" id="occupancy" placeholder="ex. 2 or 3" value="<?php echo $room_occupancy; ?>">
                                    </div>
                                </div>

                                <div class=" field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align " for="bed">Bed <span calss="required">*</span> </label>
                                    <div class="col-md-6 col-sm-6">
                                        <select class="select2_group form-control room_bed" id="bed" name="room_bed">
                                        <option value=""> Choose Bed Type </option>
                                                <?php 
                                                    if($bed_res_rows >= 1){
                                                        while($bed_row = $bed_result->fetch_assoc()){
                                                            $bed_id    = (int)($bed_row['id']);
                                                            $bed_name  = htmlspecialchars($bed_row['bed_name']);
                                                ?>
                                                <option disabled value="<?php echo $bed_id; ?>" <?php if($bed_id == $room_bed) {echo "selected";} ?> ><?php echo $bed_name;?></option>
                                                <?php
                                                        }
                                                    }
                                                 ?>
                                        </select>
                                    </div>
								</div>

                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align" for="room_size">Room Size <span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input disabled class="form-control room_size" type="number" name="room_size" id="room_size" data-validate-minmax="10,100" required='required'  placeholder="ex. 10'" value="<?php echo $room_size; ?>">
                                    </div>
                                </div>

                                <div class=" field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align " for="view">View <span calss="required">*</span> </label>
                                    <div class="col-md-6 col-sm-6">
                                        <select class="select2_group form-control room_view"  name="room_view">
                                                <option value=""> Choose View </option>
                                                <?php 
                                                    if($view_res_rows >= 1){
                                                        while($view_row = $view_result->fetch_assoc()){
                                                            $view_id   = (int)($view_row['id']);
                                                            $view_name = htmlspecialchars($view_row['view_name']);
                                                ?>
                                                <option disabled value="<?php echo $view_id; ?>" <?php if($view_id == $room_view){echo "selected";} ?> ><?php echo $view_name;?></option>
                                                <?php
                                                        }
                                                    }
                                                 ?>
                                        </select>
                                    </div>
								</div>


                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align" for="price_per_day">Price Per Day <small>($)</small> <span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input disabled class="form-control room_price" type="number"  name="room_price" data-validate-minmax="10,100" required='required' id="price_per_day" placeholder="ex. 30$" value="<?php echo $room_price; ?>">
                                    </div>
                                </div>

                                <div class="field item form-group">
                                    <label class="col-form-label col-md-3 col-sm-3  label-align" for="extra_price">Extra Bed Price Per Day <small>($)</small> <span class="required">*</span></label>
                                    <div class="col-md-6 col-sm-6">
                                        <input disabled class="form-control room_extra" type="number"  name="room_extra_bed_price" data-validate-minmax="10,100" required='required' id="extra_price" placeholder="ex. 1.8$"value="<?php echo $room_extra_bed_price; ?>" >
                                    </div>
                                </div>

                                <div class="form-group row">
											<label class="col-md-3 col-sm-3  label-align">Choose Special Feature <span class="required">*</span></label>
                                            <div class="col-md-6 col-sm-6 " >
                                            <div class="row">
                                            <?php if($sf_res_rows >= 1) {
                                                while($row = $sf_result->fetch_assoc()){
                                                    $special_feature_id   = (int)($row['id']);
                                                    $special_feature_name = htmlspecialchars($row['special_feature_name']);
                                                 ?> 
                                                    <div class="col-md-6">
												    <div class="checkbox">
													<label>
														<input disabled type="checkbox" class="room_feature"  name="special_feature[]" value="<?php echo $special_feature_id;?>" <?php if(in_array($special_feature_id,$special_feature)){echo "checked";} ?>><?php echo $special_feature_name;?>
													</label> 
												    </div>
											        </div>
                                               <?php }
                                            }
                                             ?>
                                            </div>
									</div>
                                </div>

                                <div class="form-group row">
											<label class="col-md-3 col-sm-3  label-align">Choose Amenities <span class="required">*</span></label>
                                            <div class="col-md-6 col-sm-6 ">
                                            <div class="row">
                                            <?php if($res_rows >= 1) {
                                                while($row = $result->fetch_assoc()){
                                                    $amenity_id   = (int)($row['id']);
                                                    $amenity_name = htmlspecialchars($row['amenity_name']);
                                                 ?> 
                                                    <div class="col-md-6">
												    <div class="checkbox">
													<label>
														<input disabled type="checkbox" class="room_amenity" value="<?php echo $amenity_id;?>" name="amenity[]" <?php if(in_array($amenity_id,$amenities)){echo "checked";}?>><?php echo $amenity_name;?>
													</label> 
												    </div>
											        </div>
                                               <?php }
                                            }
                                             ?>
                                            </div>
									</div>
                                </div>
                                <div class="form-group row">
                                    <label class="control-label col-md-3 col-sm-3 label-align" for="room_detail">Room Details<span class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 ">
                                        <textarea class="form-control room_detail" rows="3" name="room_detail" id="room_detail" placeholder="ex. Room Details" disabled><?php echo $room_detail; ?></textarea>
                                    </div>
                                    <label class="col-form-label col-md-3 col-sm-3  detail-label-error hide" id="detail-name-error"><span class="detail-error-msg" style="color:red;"></span></label>
                                </div>

                                <div class="form-group row">
                                    <label class="control-label col-md-3 col-sm-3 label-align" for="description">Room Description<span class="required">*</span>
                                    </label>
                                    <div class="col-md-6 col-sm-6 ">
                                        <textarea class="form-control room_des" rows="3" name="room_description" id="description" placeholder="ex. Room Description" disabled><?php echo $room_description; ?></textarea>
                                    </div>
                                </div>

                                <div class="">
                                    <div class="form-group">
                                        <div class="col-md-6 offset-md-3">
                                            <input type="hidden" name="id" value="<?php echo $id; ?>">
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

</script>
         <!-- pnotify -->
     <script src="<?php echo $base_url; ?>assets/backend/css/pnotify/pnotify.js"></script>
    <script src="<?php echo $base_url; ?>assets/backend/css/pnotify/pnotify.buttons.js"></script>
    <script src="<?php echo $base_url; ?>assets/backend/css/pnotify/pnotify.nonblock.js"></script>
    <script src="<?php echo $base_url; ?>assets/backend/js/pages/upload_img.js?v=20230802"></script>
</html>