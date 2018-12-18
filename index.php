<?php
define( 'DS', DIRECTORY_SEPARATOR );
define( 'ImageDir', __DIR__ . DS ."images" . DS );
define( 'ThumbDir', __DIR__ . DS ."thumb" . DS);

$root = isset($_REQUEST['dir']) ? urldecode($_REQUEST['dir']) : ImageDir;
thumb_creator(ImageDir, 200, 500, ThumbDir);

//if Thumb Dir not set save thumb in Image Dir
$image = 'D:\xampp\htdocs\thumb_generator\images\1_ (1).jpg';
thumb_creator($image, 100, 50);
thumb_creator($image, 200, 100);
thumb_creator($image, 100, 200);
thumb_creator($image, 100, 350);
thumb_creator($image, 350, 100);

function thumb_creator($source, $thm_width, $thm_height,$thumb_dir=''){
   $images = [];
   if( !is_dir($source) && isset(pathinfo($source)['extension'])){
      $images[] = $source;
   }
   else{
      foreach( new DirectoryIterator($source) as $node ){
         if($node->isFile()) $images[] = $source.$node->getFilename();
      }
   }
   foreach ($images as $img) {
      $image = imagecreatefromstring(file_get_contents($img));
      $img_width = imagesx($image);
      $img_height = imagesy($image);

      $finfo = pathinfo($img);
      //if thumb dir not set save thumb in source dir
      if (empty($thumb_dir)) $thumb_dir = $finfo['dirname'] . DS;

      $thumb_name = $thumb_dir . $finfo['filename'] . "_{$thm_width}x{$thm_height}." . $finfo['extension'];

      $img_ratio = $img_width / $img_height;
      $thm_ratio = $thm_width / $thm_height;

      if ( $img_ratio >= $thm_ratio ){
         // If image is wider than thumbnail (in aspect ratio sense)
         $new_height = $thm_height;
         $new_width = $img_width / ($img_height / $thm_height);
      }else{
         // If the thumbnail is wider than the image
         $new_width = $thm_width;
         $new_height = $img_height / ($img_width / $thm_width);
      }

      $thumb = imagecreatetruecolor( $thm_width, $thm_height );
      imagecopyresampled($thumb, $image,
                      0 - ($new_width - $thm_width) / 2, // Center the image horizontally
                      0 - ($new_height - $thm_height) / 2, // Center the image vertically
                      0, 0,
                      $new_width, $new_height,
                      $img_width, $img_height);
      imagejpeg($thumb, $thumb_name, 80); 
   }
}
