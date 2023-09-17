<?php
    function insertQuery($insert_data,$table,$mysqli){
        $column_name    = implode(',', array_keys($insert_data));
        $column_value   = implode(',', $insert_data);
        $sql            = "INSERT INTO " . $table . " (" . $column_name . ") VALUES (" . $column_value . ")";
        $result         = $mysqli->query($sql);
        return  $result;
    }
    function updateQuery($update_data,$table,$id,$mysqli){
        $sql            = "";
        $sql            = "UPDATE " . $table . " SET ";
        $count          = 0;
        foreach($update_data as $key => $value){
            $count++;
            if($count == 1){
                $sql  .= $key . "='" . $value . "'";
            }else{
                $sql   .= "," . $key . "='" . $value . "'"; 
            }
        }
        $sql            .= " WHERE id = '" . $id . "'";
        $result         = $mysqli->query($sql);
        return $result;
    }
    function checkUniqueValue($check_column,$table,$mysqli){
        $sql            = "";
        $sql            .= "SELECT count(id) AS total FROM ";
        $sql            .= $table;
        $sql            .= " WHERE ";
        $count          = 0;
        foreach($check_column AS $key => $value){
            $count ++;
            if($count == 1){
                $sql .= $key . " = " . "'" . $value . "'";
            }else{
                $sql .= " AND " . $key . " = " . "'" . $value . "'";
            }
        }
        $sql     .= " AND deleted_at IS NULL";
        $result = $mysqli->query($sql);
        while($row  = $result->fetch_assoc()){
            $total  = $row['total'];
        }
        return $total;
        
    }

    function checkUniqueValueUpdate($check_column,$table,$id,$mysqli){
        $sql            = "";
        $sql            .= "SELECT count(id) AS total FROM ";
        $sql            .= $table;
        $sql            .= " WHERE ";
        $count          = 0;
        foreach($check_column AS $key => $value){
            $count ++;
            if($count == 1){
                $sql .= $key . " = " . "'" . $value . "'";
            }else{
                $sql .= " AND " . $key . " = " . "'" . $value . "'";
            }
        }
        $sql     .= " AND id != '" . $id . "' ";
        $sql     .= " AND deleted_at IS NULL";
        $result = $mysqli->query($sql);
        while($row  = $result->fetch_assoc()){
            $total  = $row['total'];
        }
            return $total;
    }

    function selectQuery($select_data,$table,$mysqli,$order=null,$where=null){
        $column_value       = implode(',',$select_data);
        $sql                = "";
        $sql                .= "SELECT " . $column_value . " FROM " . $table . " WHERE deleted_at IS NULL " ;
        if($where != null) {
            foreach($where AS $key => $value){
                $sql .= " AND " . $key . " = " . "'" . $value . "'";
            }
        }
        if($order !== null){
            $sql      .= " ORDER BY ";
            $count     = 0;
            foreach($order as $key => $value){
                $count++;
                if($count == 1){
                    $sql  .= $key . " " . $value;
                }else{
                    $sql   .= "," . $key . " " . $value;
                }
               
            } 
        }
        $result = $mysqli->query($sql);
        return $result;
    }
    function selectQueryById($select_data,$table,$id,$mysqli){
        $column_value       = implode(',', $select_data);
        $sql                = "";
        $sql                .= "SELECT " . $column_value . " FROM " . $table . " WHERE id ='" .$id . "' AND deleted_at IS NULL";
        $result             = $mysqli->query($sql);
        return $result;
    }
    function checkImageExtexsion($image_name,$tmp_name) {
        $return           = [];
        $allow_extension = ["jpg", "png", "jpeg","gif"];
        $explode         = explode(".", $image_name);
        $extension       = end($explode);
        if(in_array($extension,$allow_extension)){
            if(getimagesize($tmp_name)){
                $return['error']     = false;
                $return['extension'] = $extension;
                return $return;
            } else {
                $return['error'] = true;
                return $return;
            }
        } else {
            $return['error'] = true;
            return $return;
        }
    }
    function cropAndResizeImage($sourcePath, $destinationPath, $height, $width){
        $imageInfo    = getimagesize($sourcePath);
        $sourceWith   = $imageInfo[0];
        $sourceHeight = $imageInfo['1'];
        $sourceType   = $imageInfo['mime'];

        switch($sourceType) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;
            
            case 'image/png':
                $sourceImage = imagecreatefrompng($sourcePath);
                break;

            case 'image/gif':
                $sourceImage = imagecreatefromgif($sourcePath);
                break;

            case 'image/jpg':
                $sourceImage = imagecreatefromjpeg($sourcePath);
                break;

            default:
                return false;
        }
        $sourceAspectRatio = $sourceWith / $sourceHeight;
        $targetAspectRatio = $width / $height;

        if($sourceAspectRatio > $targetAspectRatio) {
            $cropWidth = $sourceHeight * $targetAspectRatio;
            $cropHeight = $sourceHeight;
        } else {
            $cropWidth = $sourceWith;
            $cropHeight = $sourceWith / $targetAspectRatio;
        }
        $cropX = ($sourceWith - $cropWidth) / 2;
        $cropY = ($sourceHeight - $cropHeight) / 2;

        $croppedImage = imagecrop($sourceImage, [
            'x'      => $cropX,
            'y'      => $cropY,
            'width'  => $cropWidth,
            'height' => $cropHeight
        ]);

        $resizedImage = imagecreatetruecolor($width,$height);
        imagecopyresampled($resizedImage, $croppedImage, 0, 0, 0, 0, $width, $height, $cropWidth, $cropHeight);

        switch ($sourceType) {
            case 'image/jpeg':
                imagejpeg($resizedImage, $destinationPath, 90);
                break;
            
            case 'image/jpg':
                imagejpeg($resizedImage, $destinationPath, 90);
                break;

            case 'image/png':
                imagepng($resizedImage, $destinationPath);
                break;
            case 'image/gif':
                imagegif($resizedImage, $destinationPath);
                break;
        }
        imagedestroy($sourceImage);
        imagedestroy($croppedImage);
        imagedestroy($resizedImage);
    
        return true;
    }
    
    function addWatermarkToImage($uploadedImage, $watermarkImage, $outputPath)
    {
        $imageInfo    = getimagesize($uploadedImage);
        $sourceType   = $imageInfo['mime'];

        switch($sourceType) {
            case 'image/jpeg':
                $sourceImage = imagecreatefromjpeg($uploadedImage);
                break;
            
            case 'image/png':
                $sourceImage = imagecreatefrompng($uploadedImage);
                break;

            case 'image/gif':
                $sourceImage = imagecreatefromgif($uploadedImage);
                break;

            case 'image/jpg':
                $sourceImage = imagecreatefromjpeg($uploadedImage);
                break;

            default:
                return false;
        }
        // Load the uploaded image and the watermark image
        $sourceImage = imagecreatefromstring(file_get_contents($uploadedImage));
        $watermark = imagecreatefromjpeg($watermarkImage);
    
        // Get the dimensions of the images
        $sourceWidth     = imagesx($sourceImage);
        $sourceHeight    = imagesy($sourceImage);
        $watermarkWidth  = imagesx($watermark);
        $watermarkHeight = imagesy($watermark);
    
        // Calculate the position to place the watermark (bottom right corner with 10 pixels padding)
        $padding = 10;
        $watermarkX = $sourceWidth - $watermarkWidth - $padding;
        $watermarkY = $sourceHeight - $watermarkHeight - $padding;
    
        // Merge the images (overlay the watermark on the uploaded image)
        imagecopy($sourceImage, $watermark, $watermarkX, $watermarkY, 0, 0, $watermarkWidth, $watermarkHeight);
    
        // Save the final image to the specified output path
        imagepng($sourceImage, $outputPath);
    
        // Clean up memory
        imagedestroy($sourceImage);
        imagedestroy($watermark);
    }
    function getuploadImage($thumbnail,$id){
        $image_path = "assets/upload-img/" . $id . "/thumb/". $thumbnail;
        return $image_path;
    }
    function convertYmdFormat($date){
        $input_date = DateTime::createFromFormat("m/d/y", $date);
        if($input_date){
            return $input_date->format("Y-m-d");
        }
        return null;
    }

?>