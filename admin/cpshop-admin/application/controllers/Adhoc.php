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

class Adhoc extends CI_Controller {

public function __construct() {
    parent::__construct();

    $this->load->model('adhoc_resize/Model_adhoc_resize');
}

public function run_adhoc(){
    $this->Model_adhoc_resize->get_all_products_and_repopulate();
}

public function resizeImages($pass, $sizes = "", $foldername = "") {
    if ($pass == "josh0110") {

        $dir = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$foldername.'/')."*";
        $dir_check = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$foldername.'-'.$sizes.'/')."*";

        $images = glob( $dir );
        $images_check = glob($dir_check);
        $images_new = [];
        foreach ($images as $img) {
            $images_new[] =  str_replace($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$foldername.'/'), '', $img);
        }

        $images_check_new = [];
        foreach ($images_check as $imgchk) {
            $images_check_new[] =  $image_without_extension = pathinfo($imgchk, PATHINFO_FILENAME);
        }

        $new_images = [];
        foreach ($images_new as $image) {
            $image_without_extension = pathinfo($image, PATHINFO_FILENAME);
            if (!in_array($image_without_extension, $images_check_new)) {
                // $image_no_extension = pathinfo($image, PATHINFO_FILENAME);
                $new_images[] = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/'.$foldername.'/').$image;
            }
        }

        foreach( $new_images as $new_image ){
            $this->resizeImage($new_image, $sizes);
        }
        
    }else if ($pass == "j_create_folder") {
        $path = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/products/');
        $newImgPath0 = str_replace("products", 'products', $path);
        $newImgPath1 = str_replace("products", 'products-40', $path);
        $newImgPath2 = str_replace("products", 'products-50', $path);
        $newImgPath3 = str_replace("products", 'products-250', $path);
        $newImgPath4 = str_replace("products", 'products-520', $path);

        $newImgPath5 = str_replace("products", "products-40/webp", $path);
        $newImgPath6 = str_replace("products", "products-50/webp", $path);
        $newImgPath7 = str_replace("products", "products-250/webp", $path);
        $newImgPath8 = str_replace("products", "products-520/webp", $path);

        $newfolder_path0 = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/ad-banner/');
        $newfolder_path = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/all_banner/');
        $newfolder_path_webp = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/all_banner/webp/');

        $newfolder_path_webp2 = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/webp/');

        $pathshops = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/shops/');
        $newpathshops0 = $pathshops;
        $newpathshops = str_replace("shops", 'shops-60', $pathshops);
        $newpathshopswebp = str_replace("shops", 'shops-60/webp', $pathshops);

        if (!mkdir($newImgPath0, 0777, true)) {
            print_r('Failed to create folder... products');
        }else{
            print_r('Success to create folder... products');
        }

        if (!mkdir($newImgPath1, 0777, true)) {
            print_r('Failed to create folder... products-40');
        }else{
            print_r('Success to create folder... products-40');
        }

        if (!mkdir($newImgPath2, 0777, true)) {
            print_r('Failed to create folder... products-50');
        }else{
            print_r('Success to create folder... products-50');
        }
        
        if (!mkdir($newImgPath3, 0777, true)) {
            print_r('Failed to create folder... products-250');
        }else{
            print_r('Success to create folder... products-250');
        }

        if (!mkdir($newImgPath4, 0777, true)) {
            print_r('Failed to create folder... products-520');
        }else{
            print_r('Success to create folder... products-520');
        }

        if (!mkdir($newImgPath5, 0777, true)) {
            print_r('Failed to create folder... products-40/webp');
        }else{
            print_r('Success to create folder... products-40/webp');
        }

        if (!mkdir($newImgPath6, 0777, true)) {
            print_r('Failed to create folder... products-50/webp');
        }else{
            print_r('Success to create folder... products-50/webp');
        }

        if (!mkdir($newImgPath7, 0777, true)) {
            print_r('Failed to create folder... products-250/webp');
        }else{
            print_r('Success to create folder... products-250/webp');
        }

        if (!mkdir($newImgPath8, 0777, true)) {
            print_r('Failed to create folder... products-520/webp');
        }else{
            print_r('Success to create folder... products-520/webp');
        }

        if (!mkdir($newfolder_path0, 0777, true)) {
            print_r('Failed to create folder...  ad-banner');
        }else{
            print_r('Success to create folder... ad-banner');
        }

        if (!mkdir($newfolder_path, 0777, true)) {
            print_r('Failed to create folder...  all_banner');
        }else{
            print_r('Success to create folder... all_banner');
        }

        if (!mkdir($newfolder_path_webp, 0777, true)) {
            print_r('Failed to create folder...  all_banner/webp');
        }else{
            print_r('Success to create folder... all_banner/webp');
        }
       
        if (!mkdir($newfolder_path_webp2, 0777, true)) {
            print_r('Failed to create folder...  img/webp');
        }else{
            print_r('Success to create folder... img/webp');
        }

        if (!mkdir($newpathshops, 0777, true)) {
            print_r('Failed to create folder...  img/shops-60');
        }else{
            print_r('Success to create folder... img/shops-60');
        }

        if (!mkdir($newpathshops0, 0777, true)) {
            print_r('Failed to create folder...  img/shops');
        }else{
            print_r('Success to create folder... img/shops');
        }

        if (!mkdir($newpathshopswebp, 0777, true)) {
            print_r('Failed to create folder...  img/shops-60/webp');
        }else{  
            print_r('Success to create folder... img/shops-60/webp');
        }

    }else if($pass == 'banners0110'){
        $dir = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/ad-banner/')."*";
        $dir_check = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/all_banner/')."*";

        $images = glob( $dir );
        $images_check = glob($dir_check);
        $images_new = [];
        foreach ($images as $img) {
            $images_new[] =  str_replace($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/ad-banner/'), '', $img);
        }

        $images_check_new = [];
        foreach ($images_check as $imgchk) {
            // $images_check_new[] =  str_replace($_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/products-'.$sizes.'/'), '', $imgchk);
            $images_check_new[] =  $image_without_extension = pathinfo($imgchk, PATHINFO_FILENAME);
        }

        $new_images = [];
        foreach ($images_new as $image) {
            $image_without_extension = pathinfo($image, PATHINFO_FILENAME);
            if (!in_array($image_without_extension, $images_check_new)) {
                // $image_no_extension = pathinfo($image, PATHINFO_FILENAME);
                $new_images[] = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/ad-banner/').$image;
            }
        }

        foreach( $new_images as $new_image ){
            $this->resizeImage($new_image, $sizes);
        }
    }else {
        echo "invalid";
    }
    
}

public function resizeImage($imgPath, $sizes) {
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
    }else{
        $foldername = 'no folder';
        die();
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
    $quality = 50; // 0 = worst / smaller file, 100 = better / bigger file 
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
        // $quality = 80; // 0 = worst / smaller file, 100 = better / bigger file 
        // $newImgPaths = pathinfo($newImgPath, PATHINFO_DIRNAME).'/webp/'.pathinfo($newImgPath, PATHINFO_FILENAME).".webp";
        // imagewebp($bg, $newImgPaths, $quality);
        // imagedestroy($bg);

        echo "<pre>";
        echo $config['source_image'];
    }
}

public function unlink_images($pass, $sizes = "", $directorypathwithfilename = ""){
    if ($pass == "josh0110"){
        $dir_size = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/products-'.$sizes.'/')."*.jpg";
        array_map('unlink', glob($dir_size));   
        $dir_size2 = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/products-'.$sizes.'/')."*.png";
        array_map('unlink', glob($dir_size2));   
    
        $dir_size3 = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/products-'.$sizes.'/').'webp/'."*";
        array_map('unlink', glob($dir_size3));  

        echo "deleted all the files"; 
    }else if ($pass == "specific"){
        $dpfilename = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/').str_replace("-", "/", $directorypathwithfilename);

        if (unlink($dpfilename)) {
            print_r("success remove: ".$dpfilename);
        }else{
            print_r("undefined filepath");
        }
    }
}

public function others_img($pass, $filename, $action){
    if ($pass == "josh0110") {
        if ($action == "write") {
            $filepath = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/').$filename;

            //webp
            if(mime_content_type($filepath) == 'image/png'){
                $image = imagecreatefrompng($filepath);
            }else if(mime_content_type($filepath) == 'image/jpeg'){
                $image = imagecreatefromjpeg($filepath);
            }

            $w = imagesx($image);
            $h = imagesy($image);

            // $bg = imagecreatetruecolor(imagesx($image), imagesy($image));
            // imagefill($bg, 0, 0, imagecolorallocate($bg, 255, 255, 255));
            // imagealphablending($bg, FALSE);
            // imageSaveAlpha($bg, true);
            // $trans = imagecolorallocatealpha($bg, 0, 0, 0, 127);
            // imagefilledrectangle($bg, 0, 0, $w - 1, $h - 1, $trans);
            // imagecopy($bg, $image, 0, 0, 0, 0, imagesx($image), imagesy($image));
            // imagedestroy($image);
            // $quality = 99; // 0 = worst / smaller file, 100 = better / bigger file 
            // $newImgPaths = pathinfo($filepath, PATHINFO_DIRNAME).'/webp/'.pathinfo($filepath, PATHINFO_FILENAME).".webp";
            // imagewebp($bg, $newImgPaths, $quality);
            // imagedestroy($bg);

            print_r("success write: ".$filepath);
        }else if ($action == "remove"){
            $filepath = $_SERVER['DOCUMENT_ROOT'].get_root_dir('assets/img/webp/').$filename;

            if (unlink($filepath)) {
                print_r("success remove: ".$filepath);
            }else{
                print_r("undefined filepath");
            }
        }
    }else{
        print_r("wrong password");
    }
}



}