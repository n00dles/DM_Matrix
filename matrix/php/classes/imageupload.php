<?php
/*
  # Simple Image Upload Class
  # Written by W3Schools @ "http://www.w3schools.com/php/php_file_upload.asp"
  # Adapted by Lawrence Okoth-Odida @ "http://lokida.co.uk"
 */
  
class SimpleImageUpload {
  # properties
  private $extensions;
  private $prefix;
  
  # methods
  
  /* METHOD __construct()
   * @param string $prefix:     name of the image upload field in the form (e.g. 'file')
   * @param array  $extensions: array of extra allowed extensions (keep lowercase).
   */
  
  public function __construct($prefix='file', $extensions=array()) {
    $this->extensions = array('gif', 'jpeg', 'jpg', 'png');
    
    // adds extra extensions provided in initialization
    $this->extensions = array_merge($this->extensions, $extensions);
    
    $this->prefix = $prefix;
  }
  
  /* METHOD upload()
   * @param int    $maxSize:         maximum file upload size in kb.
   * @param string $uploadPath:      path to the uploads directory (relative).
   * @param array  $enableThumbnail: specify the maximum width, height and resize methods ('auto' (decides which is best based), 
   *                                 portrait (resizes according to height), landscape (resizes according to width), crop (crops image to the width/height).
   */
  
  public function upload($maxSize=20000, $uploadPath='upload/', $thumbPath=false, $filename=false, $enableThumbnail=array('maxwidth'=>100, 'maxheight'=>100, 'method'=>'auto'), $resize=false) {
    $tmp = explode('.', $_FILES[$this->prefix]['name']);
    $extension = strtolower(end($tmp));
    $file = array();
    if ((($_FILES[$this->prefix]['type'] == 'image/gif')
    || ($_FILES[$this->prefix]['type'] == 'image/jpeg')
    || ($_FILES[$this->prefix]['type'] == 'image/jpg')
    || ($_FILES[$this->prefix]['type'] == 'image/pjpeg')
    || ($_FILES[$this->prefix]['type'] == 'image/x-png')
    || ($_FILES[$this->prefix]['type'] == 'image/png'))
    && ($_FILES[$this->prefix]['size'] < $maxSize)
    && in_array($extension, $this->extensions)) {
      if ($_FILES[$this->prefix]['error'] > 0) {
        $file['status'] = false;
        $file['error']  = $_FILES[$this->prefix]['error'];
      }
      else {
        $file['status'] = true;
        $file['upload'] = $_FILES[$this->prefix]['name'];
        $file['type']   = $_FILES[$this->prefix]['type'];
        $file['size']   = $_FILES[$this->prefix]['size'] / 1024;
        $file['temp']   = $_FILES[$this->prefix]['tmp_name'];
        
        if (!$filename) {
          $tmp = explode('.', $_FILES[$this->prefix]['name']);
          $filename = reset($tmp);
        }

        // move the file to the desired directory
        if (!file_exists($uploadPath)) mkdir($uploadPath);
        move_uploaded_file($_FILES[$this->prefix]['tmp_name'], $uploadPath.$filename.'.'.$extension);
        
        // creates a thumbnail
        if ($enableThumbnail) {
          $path = $uploadPath.$filename.'.'.$extension;
          if ($thumbPath) {
            if (!file_exists($thumbPath)) mkdir($thumbPath);
            $thumbPath = $thumbPath.$filename.'.'.$extension;
          }
          else {
            $thumbPath = $uploadPath.$filename.'_thumb.'.$extension;
          }
          $resizeObj = new resize($uploadPath.$filename.'.'.$extension);
          $resizeObj->resizeImage($enableThumbnail['maxwidth'], $enableThumbnail['maxheight'], $enableThumbnail['method']);
          $resizeObj->saveImage($thumbPath, 70);
          $file['thumb'] = $thumbPath;
        }
        $file['stored'] = $uploadPath.$filename.'.'.$extension;
        $file['file'] = $filename.'.'.$extension;
        
        // resize
        $dimensions = getimagesize($uploadPath.$filename.'.'.$extension);
        if ($resize && ($dimensions[0]>$resize['width'] || $dimensions[1]>$resize['width'])) {
          $path = $uploadPath.$filename.'.'.$extension;
          $resizeObj = new resize($path);
          $resizeObj->resizeImage($resize['width'], $resize['height'], 'auto');
          $resizeObj->saveImage($path, 70);
        }
      }
    }
    else {
      $file['status'] = false;
      $file['error']  = 'invalid file';
    }
    return $file;
  }
}

?>