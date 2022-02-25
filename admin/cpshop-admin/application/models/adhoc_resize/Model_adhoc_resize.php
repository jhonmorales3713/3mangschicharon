<?php 
if (function_exists("set_time_limit") == TRUE AND @ini_get("safe_mode") == 0) //to ignore maximum time limit
{
    @set_time_limit(0);
}

ini_set( 'memory_limit', '2048M' );
ini_set('upload_max_filesize', '2048M');  
ini_set('post_max_size', '2048M');  
ini_set('max_input_time', 3600);  
ini_set('max_execution_time', 3600);

class Model_adhoc_resize extends CI_Model {

// $this->Model_adhoc_resize->resize_images($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'.$f_id.'/').$file_name, $shopcode);

public function get_all_products_and_repopulate(){
    $existing_dir_with_images = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/products/')."*";

    $existing_images = glob($existing_dir_with_images);
    $existing_image_succeed_arr = [];
    foreach ($existing_images as $existing_image) {
        if (!in_array($existing_image, $existing_image_succeed_arr)) {
            // if not already succeeded.
            
            $existing_image_filename =  pathinfo($existing_image, PATHINFO_FILENAME);
            $existing_image_filename_with_extension =  pathinfo($existing_image, PATHINFO_BASENAME);

            $shopcode = $this->get_shop_using_existing_image($existing_image_filename);
            $f_id = $existing_image_filename;
            $file_name = $existing_image_filename_with_extension;

            //create folder per product id
            if ($shopcode != "") {
                if (!is_dir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'))) {
                    mkdir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'), 0777, TRUE);
                }
                if (!is_dir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-40/'))) {
                    mkdir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-40/'), 0777, TRUE);
                }
                if (!is_dir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-50/'))) {
                    mkdir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-50/'), 0777, TRUE);
                }
                if (!is_dir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-250/'))) {
                    mkdir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-250/'), 0777, TRUE);
                }
                if (!is_dir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-520/'))) {
                    mkdir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-520/'), 0777, TRUE);
                }

                if (!is_dir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'.$f_id.'/'))) {
                    mkdir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'.$f_id.'/'), 0777, TRUE);
                }
                if (!is_dir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-40/'.$f_id.'/'))) {
                    mkdir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-40/'.$f_id.'/'), 0777, TRUE);
                }
                if (!is_dir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-50/'.$f_id.'/'))) {
                    mkdir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-50/'.$f_id.'/'), 0777, TRUE);
                }
                if (!is_dir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-250/'.$f_id.'/'))) {
                    mkdir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-250/'.$f_id.'/'), 0777, TRUE);
                }
                if (!is_dir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-520/'.$f_id.'/'))) {
                    mkdir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products-520/'.$f_id.'/'), 0777, TRUE);
                }//end
                // print_r("<pre>");
                // print_r($existing_image);
                

                $newfile_dest = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'.$f_id.'/0-').$file_name;

                if (copy($existing_image, $newfile_dest)) {
                    $this->resize_images($newfile_dest, $shopcode); 
                    $existing_image_succeed_arr[] = $existing_image;
                    echo "success to copy $existing_image\n";
                    echo "<br>";

                }else{
                    echo "failed to copy $existing_image\n";
                    echo "<br>";
                }
            }
        }else{
            echo "already been copied $existing_image\n";
            echo "<br>";
        }
        // print_r("<pre>");
        // print_r($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$shopcode.'/products/'.$existing_image_filename.'/0-').$existing_image_filename_with_extension);
    }

    // print_r("<pre>");
    // print_r($file_name_as_id);
}

public function get_shop_using_existing_image($existing_image_filename){
    $sql = "SELECT shopcode FROM sys_shops WHERE id = (SELECT sys_shop FROM sys_products WHERE id = ?) LIMIT 1";
    $data = array($existing_image_filename);
    $query = $this->db->query($sql, $data);
    if ($query->num_rows() > 0) {
        $shopcode = $query->row()->shopcode;
    }else{
        $shopcode = "";
    }

    return $shopcode;
}

public function resize_images($file_name, $shopcode = ""){
    if ($shopcode == "banners0110") {
        $sizes_array = ['1500'];
        foreach( $sizes_array as $sizes ){
           $this->do_resize($file_name, $sizes);
        }
    }else if($shopcode == "shops0110"){
        $sizes_array = ['60'];
        foreach( $sizes_array as $sizes ){
           $this->do_resize($file_name, $sizes);
        }
    }else if($shopcode == "shops-banner0110"){
        $sizes_array = ['1400'];
        foreach( $sizes_array as $sizes ){
           $this->do_resize($file_name, $sizes);
        }
    }else{
        $sizes_array = ['40', '50', '250', '520'];
        foreach( $sizes_array as $sizes ){
           $this->do_resize($file_name, $sizes, $shopcode);
        }
    }
}


public function do_resize($imgPath, $sizes, $shopcode = "") {
    if ($sizes == '40') {
        $foldername = 'products-40';
        $newImgPath = str_replace("products", $foldername, $imgPath);
    }else if ($sizes == '50') {
        $foldername = 'products-50';
        $newImgPath = str_replace("products", $foldername, $imgPath);
    }else if ($sizes == '250') {
        $foldername = 'products-250';
        $newImgPath = str_replace("products", $foldername, $imgPath);
    }else if ($sizes == '520') {
        $foldername = 'products-520';
        $newImgPath = str_replace("products", $foldername, $imgPath);
    }else if ($sizes == '1500') {
        $foldername = 'all_banner';
        $newImgPath = str_replace("ad-banner", $foldername, $imgPath); 
    }else if ($sizes == '60') {
        $foldername = 'shops-60';
        $newImgPath = str_replace("shops", $foldername, $imgPath);
    }else if ($sizes == '1400'){
        $sizes = '1500';
        $foldername = 'shops-banner1500';
        $newImgPath = str_replace("shops-banner", $foldername, $imgPath); 
    }else{
        $foldername = 'no folder';
        die();
    }

    if ($sizes == '1500' || $sizes == '60') {
        if(!is_dir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops-60/'))) {
            mkdir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops-60/'), 0777, TRUE);
        }
        if(!is_dir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops-banner1500/'))) {
            mkdir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops-banner1500/'), 0777, TRUE);
        }
    }else{
        if(!is_dir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops-60/'))) {
            mkdir($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops-60/'), 0777, TRUE);
        }
    }

    if(mime_content_type($imgPath) == 'image/png'){
        $image = imagecreatefrompng($imgPath);
    }else if(mime_content_type($imgPath) == 'image/jpeg'){
        $image = imagecreatefromjpeg($imgPath);
    }
    
    $exif = @exif_read_data($imgPath);

    if (!empty($exif['Orientation'])) {
        switch ($exif['Orientation']) {
            case 3:
                $image = imagerotate($image, 180, 0);
                break;

            case 6:
                $image = imagerotate($image, -90, 0);
                break;

            case 8:
                $image = imagerotate($image, 90, 0);
                break;
        }
    }
        
    $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
    imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
    imagealphablending($bg, TRUE);
    imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
    imagedestroy($image); 
    if ($sizes == '1500') {
        $quality = 99; // 0 = worst / smaller file, 100 = better / bigger file 
    }else{
        $quality = 50; // 0 = worst / smaller file, 100 = better / bigger file 
    }
    $newImgPath = pathinfo($newImgPath, PATHINFO_DIRNAME).'/'.pathinfo($newImgPath, PATHINFO_FILENAME).".jpg";
    imagejpeg($bg, $newImgPath, $quality);
    imagedestroy($bg);

    $config = array();
    $config['image_library'] = 'gd2';
    $config['source_image'] = $newImgPath;
    $config['new_image'] = $newImgPath;
    $config['create_thumb'] = FALSE;
    $config['maintain_ratio'] = TRUE;
    $config['overwrite'] = TRUE;
    $config['width'] = $sizes;
    $this->image_lib->initialize($config);

    if ( ! $this->image_lib->resize()){
        echo $this->image_lib->display_errors();
    }else {
        // $image = imagecreatefromjpeg($newImgPath);
        // $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
        // imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
        // imagealphablending($bg, TRUE);
        // imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
        // imagedestroy($image);
        // if ($sizes == '1500') {
        //     $quality = 99; // 0 = worst / smaller file, 100 = better / bigger file 
        // }else{
        //     $quality = 80; // 0 = worst / smaller file, 100 = better / bigger file 
        // }
        // $newImgPaths = pathinfo($newImgPath, PATHINFO_DIRNAME).'/webp/'.pathinfo($newImgPath, PATHINFO_FILENAME).".webp";
        // imagewebp($bg, $newImgPaths, $quality);
        // imagedestroy($bg);
    }
}













}