<?php
  /*
   *
   */
  
class TheMatrixExtended extends TheMatrix {
  public function __construct() {
    $matrix = new TheMatrix;
    $this->globals = $matrix->getGlobals();
  }

  public function editFileForm($path, $id, $denyDirect=true) {
    if (file_exists(GSROOTPATH.$path)) {
      $fileContents = file_get_contents(GSROOTPATH.$path);
      if ($denyDirect) $fileContents = str_replace('<?php if(!defined("GSROOTPATH")) header("Location: '.$this->globals['siteurl'].'404/"); ?>', '', $fileContents); // ensures file cannot be accessed directly
      $this->initialiseCodeMirror();
      $this->instantiateCodeMirror($id);
      echo '<textarea class="codeeditor DM_codeeditor" name="'.$id.'" id="post-'.$id.'">'.$fileContents.'</textarea>';
      return true;
    }
    else return false;
  }

  // saving a file
  public function saveFile($data, $path, $denyDirect=true) {
    if (!is_array($data) && file_exists(GSROOTPATH.$path)) {
      $file = fopen(GSROOTPATH.$path, "w");
      if ($denyDirect) $data = '<?php if(!defined("GSROOTPATH")) header("Location: '.$this->globals['siteurl'].'404/"); ?>'.$data;
      fwrite ($file, $data);
      fclose($file);
      return true;
    }
    else return false;
  }

  // editing .htaccess files
  public function editHtaccess($plugin, $rules) {
    $htaccess = file_get_contents(GSROOTPATH.'.htaccess');
      
      
      
    preg_match("#\nRewriteBase (/+)(.*)#", $htaccess, $rewriteBase);
      
    #$rewriteBase = $matches[0];
      
    #$rules = '# just some rules here';
      
    $block = array();
    $block['header'] = '# == HTACCESS RULES FOR '.$plugin.' == #';
    $block['footer'] = '# == END OF HTACCESS RULES FOR '.$plugin.' == #';
    $block['full'] = $block['header']."\n\n".$rules."\n\n".$block['footer'];
      
    if (preg_match("%".$block['header']."(.*?)".$block['footer']."%s", $htaccess, $matches)) {
    #if (preg_match("%".$block['header']."(.*?)".$block['header']."%", $htaccess, $matches)) {
      
      $htaccess = str_replace($matches[0], $block['full'], $htaccess);
      
      echo 'match';
        
    }
    else {
        
      $htaccess = preg_replace("#\nRewriteBase (/+)(.*)#", $rewriteBase[0]."\n\n".$block['full']."\n", $htaccess);
      
      echo 'no match';
    }
      
    #$htaccess = preg_replace($matches[0], $)
      
    var_dump($htaccess);
    var_dump($block);
    #var_dump($matches);
    
    // put the content back
    file_put_contents(GSROOTPATH.'.htaccess', $htaccess);
  }
}

?>