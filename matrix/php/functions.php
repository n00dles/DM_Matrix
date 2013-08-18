<?php

function array_key_rename($key, $rename, $array) {
  if (is_array($array) && array_key_exists($key, $array) && !array_key_exists($rename, $array)) {
    // find position of old key
    $i = array_search($key, array_keys($array));
    $new = $array[$key];
    unset($array[$key]);
    $newArray = array_slice($array, 0, $i);
    $newArray = array_merge($newArray, array($rename => $new));
    $newArray = array_merge($newArray, array_slice($array, $i, count($array)));
    $array = $newArray;
  }
  return $array;
}
  
function recurse_copy($src,$dst) { 
  $dir = opendir($src); 
  @mkdir($dst); 
  while(false !== ( $file = readdir($dir)) ) { 
    if (( $file != '.' ) && ( $file != '..' )) { 
      if ( is_dir($src . '/' . $file) ) { 
        recurse_copy($src . '/' . $file,$dst . '/' . $file); 
      } 
      else { 
        copy($src . '/' . $file,$dst . '/' . $file); 
      } 
    } 
  } 
  closedir($dir); 
}

function strtoslug($string) {
  if (is_string($string)) {
    return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
  }
  else return false;
}

function explode_trim($delim="\n", $string) {
  if (is_string($string)) {
    $string = explode($delim, $string);
    $string = array_map('trim', $string);
  }
  return $string;
}

function implode_trim($delim="\n", $array) {
  if (is_array($array)) {
    $array = array_map('trim', $array);
    $array = implode($delim, $array);
  }
  return $array;
}