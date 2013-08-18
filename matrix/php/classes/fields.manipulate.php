<?php
/* class for manipulating fields */

class MatrixManipulateField {
  /* constants */
  /* properties */
  private $matrix;
  private $schema;
  private $value;
  private $name;
  private $id, $type;
  
  /* methods */
  # constructor
  public function __construct($matrix, $schema=array(), $value=null, $paths=array()) {
    // initialize
    $this->matrix = $matrix;
    $this->schema = $schema;
    $this->name   = $schema['name'];
    $this->id     = 'post-'.$this->name;
    $this->type   = $schema['type'];
    if (is_string($value)) {
      $this->value  = (strlen($value) > 0) ? $value : $schema['default'];
    }
    else $this->value = $value;
    
    $this->paths  = $paths;
    $this->value  = $value;
    
    // get the correct method name
    $this->method = $schema['type'] . ($schema['mask'] ? '_'.$schema['mask'] : '') ;
  }
  
  /* inputs */
  # input (text)
  public function input() {
    return array('@cdata' => (string)trim($this->value));
  }
  public function input_number() {
    return $this->value;
  }
  public function input_password() {
    $removeSalt = trim(str_replace($this->schema['salt'], '', $this->value));
    // only encode it if it hasn't already been sha1ed
    if (!(ctype_xdigit($removeSalt) && strlen($removeSalt) == 40)) {
      return $this->schema['salt'] . sha1($this->value);
    }
    else return $this->value;
  }
  public function input_slug() {
    return $this->matrix->str2slug($this->value);
  }
  public function input_color() {
    return $this->value;
  }
  
  /* date */
  public function date() {
    $date = $this->value;
    // format to UNIX
    if (!is_numeric($date)) {
      $date = strtotime($date);
    }
    return date('r', $date);
  }

  /* textarea */
  # tags
  public function textarea_tags() {
    $tags = explode_trim(',', $this->value);
    if (is_array($tags)) {
      foreach ($tags as $key => $tag) {
        $tag = strtolower($tag);
        $tags[$key] = preg_replace('/[\W]+/', '_', $tag);
      }
    }
    $tags = implode_trim(',', $tags);
    return array('@cdata' => $tags);
  }
  
  /* options */
  # default
  public function options() {
    $return = array();
    
    if (is_array($this->value)) {
      foreach ($this->value as $key => $val) {
        $return[] = array('@cdata' => $val);
      }
    }
    
    return array('value' => $return);
  }
  # radio
  public function options_radio() {
    return array('@cdata' => (string)trim($this->value));
  }
  
  /* multiple fields */
  # multi
  public function multi() {
    $return = array();
    $keys = $this->matrix->explodeTrim("\n", $this->schema['rows']);
    if (!is_array($this->value)) {
      $this->value = array($this->value);
    }

    foreach ($this->value as $key => $val) {
      if (isset($keys[$key])) {
        $key = $keys[$key];
        $return[$key] = array('@cdata' => $val);
      }
      else {
        $return[] = array('@cdata' => $val);
      }
    }
    
    return array('value' => $return);
  }
  # multi color
  public function multi_color() {
    return $this->multi();
  }
  # multi number
  public function multi_number() {
    $return = array();
    $keys = $this->matrix->explodeTrim("\n", $this->schema['rows']);
    if (!is_array($this->value)) {
      $this->value = array($this->value);
    }

    foreach ($this->value as $key => $val) {
      if (isset($keys[$key])) {
        $key = $keys[$key];
        $return[$key] = $val;
      }
      else {
        $return[] = $val;
      }
    }
    
    return array('value' => $return);
  }
  
  /* uploads */
  private function upload_imageadmin() {
    // get settings
    $op = explode("\n", $this->schema['options']);
    $op = array_map('trim', $op);
    
    
    // defaults
    if (empty($op[0])) $op[0] = 5*10*10*10*1024; // default max size set to 5mb
    if (empty($op[1])) $op[1] = 900; // max width
    if (empty($op[2])) $op[2] = 900; // max height
    if (empty($op[3])) $op[3] = 100; // thumb width
    if (empty($op[4])) $op[4] = 100; // thumb height
    if (empty($op[5])) $op[5] = 'auto'; // thumb resize method
    
    if ($op[1]==0 && $op[2]==0) {
      $resize = false;
    }
    else {
      $resize = array('width' => $op[1], 'height' => $op[2]);
    }
    
    if (!empty($_FILES)) {
      $upload = new SimpleImageUpload('post-'.$this->schema['name']);
      $success = $upload->upload($maxSize=(int)$op[0], $path=GSDATAUPLOADPATH.$this->schema['path'], $thumb=GSTHUMBNAILPATH.$this->schema['path'], $filename=false, $enableThumbnail=array('maxwidth'=>$op[3], 'maxheight'=>$op[4], 'method'=>$op[5]), $resize);
      if ($success['status']==true) {
        return $success['file'];
      }
      else {
        return '';
      }
    }
    else return $this->value;
  }
  
  # manipulation
  public function manipulate($params=array()) {
    if (method_exists(get_class($this), $this->method)) {
      $method = $this->method;
    }
    elseif (method_exists(get_class($this), $this->type)) {
      $method = $this->type;
    }
    else $method = 'input';
    return call_user_func_array(array($this, $method), $params);
  }
}

?>