<?php

class MatrixPlugin {
  /* constants */
  const FILE    = 'matrix';
  const VERSION = '1.03';
  const AUTHOR  = 'Mike Swan';
  const URL     = 'http://digimute.com/';
  
  /* properties */
  
  /* methods */
  
  # info used for registering plugin
  public static function info($info) {
    $plugin = array();
    $plugin['id']          = self::FILE;
    $plugin['name']        = i18n_r(self::FILE.'/PLUGIN_TITLE');
    $plugin['version']     = self::VERSION;
    $plugin['author']      = self::AUTHOR;
    $plugin['url']         = self::URL;
    $plugin['description'] = i18n_r(self::FILE.'/PLUGIN_DESC');
    $plugin['page']        = self::FILE;
    $plugin['sidebar']     = i18n_r(self::FILE.'/PLUGIN_SIDEBAR');
    
    if (isset($plugin[$info])) {
      return $plugin[$info];
    }
    else return null;
  }
  
  # general dependencies
  public static function dependencies($args=array(), $classname=array()) {
    // load global plugins array
    global $plugins;
    
    // set up array of plugins to check by (and add $args to it)
    $check = array();
    $check = is_array($args) ? array_merge($check, $args) : $check;
    $check = array_fill_keys($check, false);
    
    // loop through each plugin and see if our list checks out
    foreach ($plugins as $key => $plugin) {
      if (isset($plugin['file']) && array_key_exists($plugin['file'], $check)) {
        $check[$plugin['file']] = true;
      }
    }
    
    // final return array
    $return = array(
      'health' => (!in_array(false, $check)) ? true : false,
      'plugins' => $check,
    );
    
    if ($return['health'] == true && isset($obj['class']) && isset($obj['params'])) {
      global ${$obj['class']};
      ${$obj['class']} = new $obj['class']($obj['params']);
    }
    
    return $return;
  }
  
  # paths
  public static function paths() {
    $paths = array();
    return $paths;
  }
}

?>