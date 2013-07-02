<?php
/**
 * TheMatrixOptions: A class for parsing options lists.
 */

class TheMatrixOptions {
  private $options;

  public function __construct($options) {
    $this->options = explode("\n", $options);
  }


  public function output($delimiter='>') {
    $array = array();
    $level = array();
    foreach ($this->options as $option) {
      $option = trim($option);
      $lv = preg_match('/'.$delimiter.'* /', $option, $matches);
      if (isset($matches[0])) {
        $lv = strlen($matches[0]);
      }
      else $lv = 1;
      if ($lv==1) {
        $level[$lv] = $option;
      }
      else {
        $level[$lv] = substr($option, $lv, strlen($option)-$lv);
        
        $option = '&lfloor; '.$level[$lv];
        for ($i=1; $i<$lv; $i++) {
          $option = '&nbsp;&nbsp;&nbsp;'.$option;
        }
      }
      $levelpoint = array_slice($level, 0, $lv);
      
      
      if ($lv>1) {
        
      }
      
      $value = implode('/', $levelpoint);
      $array[] = array(
        'value'  => $value,
        'option' => $option,
      );
    }
    return $array;
  }
}
