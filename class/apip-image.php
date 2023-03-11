<?php

/*
 * 作用: 处理图片
 * 来源: 网络
 * URL:
*/
class Apip_SimpleImage {

   var $image;
   var $image_type;

   function load($filename) {
      if(strtolower(substr($filename, 0, 4))=='http'){
         //url  
         $cxContext = stream_context_create();
         $proxy = new WP_HTTP_Proxy();
         if ($proxy->is_enabled()) {
            $proxy_str = $proxy->host().":".$proxy->port();
            $stream_default_opts = array(
               'http'=>array(
                 'proxy'=>$proxy_str,
                 'request_fulluri' => true,
               ),
               'ssl' => array(
                  'verify_peer' => false,
                  'verify_peer_name' => false,
                  'allow_self_signed' => true
               ),
             );
             $cxContext = stream_context_create($stream_default_opts);
         }
         file_put_contents("./temp", file_get_contents($filename,false, $cxContext));
         $filename = "./temp";
      }
      $image_info = getimagesize($filename);
      
      $this->image_type = $image_info[2];
      if( $this->image_type == IMAGETYPE_JPEG ) {

         $this->image = imagecreatefromjpeg($filename);
      } elseif( $this->image_type == IMAGETYPE_GIF ) {

         $this->image = imagecreatefromgif($filename);
      } elseif( $this->image_type == IMAGETYPE_PNG ) {

         $this->image = imagecreatefrompng($filename);
      } elseif( $this->image_type == IMAGETYPE_WEBP ) {
         
         $this->image = imagecreatefromwebp($filename);
      }
      if ($filename==="./temp") {
         unlink("./temp");
      }
   }
   function save($filename, $image_type=IMAGETYPE_JPEG, $compression=75, $permissions=null) {

      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image,$filename,$compression);
      } elseif( $image_type == IMAGETYPE_GIF ) {

         imagegif($this->image,$filename);
      } elseif( $image_type == IMAGETYPE_PNG ) {

         imagepng($this->image,$filename);
      }
      if( $permissions != null) {

         chmod($filename,$permissions);
      }
   }
   function output($image_type=IMAGETYPE_JPEG) {

      if( $image_type == IMAGETYPE_JPEG ) {
         imagejpeg($this->image);
      } elseif( $image_type == IMAGETYPE_GIF ) {

         imagegif($this->image);
      } elseif( $image_type == IMAGETYPE_PNG ) {

         imagepng($this->image);
      }
   }
   function getWidth() {

      return imagesx($this->image);
   }
   function getHeight() {

      return imagesy($this->image);
   }
   function resizeToHeight($height) {

      $ratio = $height / $this->getHeight();
      $width = $this->getWidth() * $ratio;
      $this->resize($width,$height);
   }

   function resizeToWidth($width) {
      $ratio = $width / $this->getWidth();
      $height = $this->getheight() * $ratio;
      $this->resize($width,$height);
   }

   function scale($scale) {
      $width = $this->getWidth() * $scale/100;
      $height = $this->getheight() * $scale/100;
      $this->resize($width,$height);
   }

   function resize($width,$height) {
      $new_image = imagecreatetruecolor($width, $height);
      imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
      $this->image = $new_image;
   }

   function rotate($degrees) {
      $this->image = @imagerotate($this->image, $degrees, 0);
   }
}
