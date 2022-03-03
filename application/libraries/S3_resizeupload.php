<?php

/**
 * Amazon S3 Upload PHP class
 *
 * @version 0.1
 */

class S3_resizeupload {
	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->library('s3_upload');
		
    }
    
    function uploadResize_S3($fileTempName, $s3_directory, $activityContent, $type, $getOrigimageDim, $height = 0){
        $origWidth  = $getOrigimageDim[0];
        $origHeight = $getOrigimageDim[1];

        if($type == '40'){
            $s3_directory    = str_replace("products", 'products-40', $s3_directory);
            $activityContent = str_replace("products", 'products-40', $activityContent);
            $new_width       = 40;
            $new_height      = $this->getresizedHeight(40, $origWidth, $origHeight);
            $upload          = 1;
        }
        else if($type == '50'){
            $s3_directory    = str_replace("products", 'products-50', $s3_directory);
            $activityContent = str_replace("products", 'products-50', $activityContent);
            $new_width       = 50;
            $new_height      = $this->getresizedHeight(50, $origWidth, $origHeight);
            $upload          = 1;
        }
        else if($type == '250'){
            $s3_directory    = str_replace("products", 'products-250', $s3_directory);
            $activityContent = str_replace("products", 'products-250', $activityContent);
            $new_width       = 250;
            $new_height      = $this->getresizedHeight(250, $origWidth, $origHeight);
            $upload          = 1;
        }
        else if($type == '520'){
            $s3_directory    = str_replace("products", 'products-520', $s3_directory);
            $activityContent = str_replace("products", 'products-520', $activityContent);
            $new_width       = 520;
            $new_height      = $this->getresizedHeight(520, $origWidth, $origHeight);
            $upload          = 1;
        }
        else if($type == 'shops-60'){
            $s3_directory    = str_replace("shops", 'shops-60', $s3_directory);
            $activityContent = str_replace("shops", 'shops-60', $activityContent);
            $new_width       = 60;
            $new_height      = $this->getresizedHeight(60, $origWidth, $origHeight);
            $upload          = 1;
        }
        else if($type == 'shops-banner1500'){
            $s3_directory    = str_replace("shops-banner", 'shops-banner1500', $s3_directory);
            $activityContent = str_replace("shops-banner", 'shops-banner1500', $activityContent);
            $new_width       = 1500;
            $new_height      = $this->getresizedHeight(1500, $origWidth, $origHeight);
            $upload          = 1;
        }
        else if($type == 'all_banner'){
            $s3_directory    = str_replace("ad-banner", 'all_banner', $s3_directory);
            $activityContent = str_replace("ad-banner", 'all_banner', $activityContent);
            $new_width       = 1500;
            $new_height      = $this->getresizedHeight(1500, $origWidth, $origHeight);
            $upload          = 1;
        }
        else{
            $upload = 0;
        }

        // For Promo Pop up
        if ($height != 0) {
            $new_width = 380;
            $new_height = $height;
            $upload          = 1;
        }
        
        if($upload == 1){
            $image_p = imagecreatetruecolor($new_width, $new_height);
            $image = imagecreatefromstring(file_get_contents($fileTempName));
            imagealphablending($image_p, false);
            imagesavealpha($image_p, true);
            imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, imagesx($image), imagesy($image));

            $newFielName = tempnam(null,null);
            imagepng($image_p, $newFielName, 9);
            if($this->CI->s3_upload->uploadS3ImagesDiff($newFielName, $activityContent)){
                return 1;
            }else{
                return 0;
            }
        }
        else{
            return false;
        }
        

    }

    function getresizedHeight($type, $origWidth, $origHeight){
        $type       = floatval($type);
        $origWidth  = floatval($origWidth);
        $origHeight = floatval($origHeight);
        $max_width  = $type;
        $new_width  = $origWidth; 
        $new_height = $origHeight;
        $ratio      = $max_width / $origWidth;
        $new_width  = $max_width;
        $new_height = $origHeight * $ratio;

        return $new_height;
    }

	
}