<?php

class TheMatrix {
  # constants
    const FILE    = 'matrix';
    const SINGLE  = 0;
    const MULTI   = 1;
    const COUNT   = 2;
    const VERSION = '1.02';
    const AUTHOR  = 'Mike Swan';
    const URL     = 'http://digimute.com/';
    const WIKI    = 'https://github.com/n00dles/DM_Matrix/wiki/';
  
  # properties
    private $salt;
    private $directories;
    private $fields;
    private $plugin;
    private $debug;
    private $defaultDebug = true;
    private $schema = array();
    private $itemTitle;
    private $editing; 
    private $uri;
    private $formColumns;
    private $sql;
    private $mytable;
    private $tablesCache; // hold cached schema loads
    private $globals;
  
  # initialization
  public function __construct($salt='') {
    // initialize plugin properties
    $this->plugin = array();
    $this->plugin['id']          = self::FILE;
    $this->plugin['name']        = i18n_r(self::FILE.'/PLUGIN_TITLE');
    $this->plugin['version']     = self::VERSION;
    $this->plugin['author']      = self::AUTHOR;
    $this->plugin['url']         = self::URL;
    $this->plugin['wiki']        = self::WIKI;
    $this->plugin['description'] = i18n_r(self::FILE.'/PLUGIN_DESC');
    $this->plugin['page']        = self::FILE;
    $this->plugin['sidebar']     = i18n_r(self::FILE.'/PLUGIN_SIDEBAR');
    
    // if the core dependencies exist, continue
    if ($this->checkDependencies()) {
      $this->salt = $salt;          // used for salting passwords
      $this->debug = true;          // turn debugging on
      $this->defaultDebug = true;
      $this->schema = array();
      $this->itemTitle = 'Matrix';
      $this->editing = false; 
      $this->uri = '';
      $this->formColumns = 0;
      $this->sql = new sql4array();
      $this->myTable = array();
      $this->tablesCache = array();
      $this->directories = $this->getDirs();
      $this->fields = $this->fields();
      $this->globals = $this->globals();
      
      // loads schema
      $this->getSchema();
      
      // initialize (create folders, core schema, etc...)
      $this->initialize();
    }
  }
  
  # check plugin dependencies (really something for later, if TheMatrix expands to require other plugins)
  private function checkDependencies() {
    return true;
  }
  
  # output missing dependencies array
  private function missingDependencies() {
  }
  
  # get defined directories
  private function getDirs() {
    $dirs = array();
    
    // plugin folder
    $dirs['plugin']['core']   = array('dir' => GSPLUGINPATH.self::FILE.'/');
    $dirs['plugin']['php']    = array('dir' => $dirs['plugin']['core']['dir'].'/php/');
    $dirs['plugin']['var']    = array('dir' => $dirs['plugin']['php']['dir'].'/var/');
    $dirs['plugin']['admin']  = array('dir' => $dirs['plugin']['php']['dir'].'/admin/');
    $dirs['plugin']['forms']  = array('dir' => $dirs['plugin']['php']['dir'].'/forms/');
    
    // data folder
    $dirs['data']['core']    = array('dir' => GSDATAOTHERPATH.self::FILE.'/');
    
    return $dirs;
  }
  
  # creates base folders
  private function initialize() {
    // array used to indicate success of processes
    $return = array();
    $dataDir = $this->directories['data']['core']['dir'];
    
    // create main data folder
    if (!is_dir($dataDir)) {
      $return[] = mkdir($this->directories['data']['core']['dir'], 0755);
      $this->debugLog(i18n_r(self::FILE.'/BASEFOLDER_CREATESUCCESS'));
    }
    else {
      $this->debugLog(i18n_r(self::FILE.'/BASEFOLDER_CREATESUCCESS'));
    }
    
    // create core schema file
    if (!file_exists($dataDir.'schema.xml')) {
      $schema = <<<EOF
<?xml version="1.0" encoding="UTF-8"?>
<channel version="1">
  <item>
    <name>_routes</name>
    <id>0</id>
    <maxrecords>0</maxrecords>
    <field type="int" label="ID Field" desc="" default="" cacheindex="1" tableview="" table="" row="id" options="" path="" size="100" visibility="1" class="">id</field>
    <field type="text" desc="" label="" default="" cacheindex="1" tableview="" table="" row="id" options="" path="" size="100" visibility="1" class="">route</field>
    <field type="text" desc="" label="" default="" cacheindex="1" tableview="" table="" row="id" options="" path="" size="100" visibility="1" class="">rewrite</field>
  </item>
</channel>
EOF;
      $return[] = file_put_contents($dataDir.'schema.xml', $schema);
    }
    
    // create _routes table
    if (!$this->tableExists('_routes')) {
      $this->debugLog('Creating table "_routes"');
      
      $return[] = $this->createTable(
        '_routes',
        array(
          array(
            'name'    =>  'route',
            'type'    =>  'text',
          ),
          array(
            'name'    => 'rewrite',
            'type' => 'text',
          ),
        ),
        0, 0);
    }
    if (!in_array(false, $return) || count($return) == 0) {
      return true;
    }
    else return false;
  }
  
  # loads global variables directly from XML file
  private function globals() {
    global $pagesArray;
    
    $xml = XML2Array::createArray(file_get_contents(GSDATAOTHERPATH.'website.xml'));
    
    // turn PRETTYURLS into boolean
      if ($xml['item']['PRETTYURLS']=='') $xml['item']['PRETTYURLS'] = false;
      else $xml['item']['PRETTYURLS'] = true;
    
    // globals array
    $globals = array(
      'sitename'    => $xml['item']['SITENAME']['@cdata'],
      'siteurl'     => $xml['item']['SITEURL']['@cdata'],
      'template'    => $xml['item']['TEMPLATE']['@cdata'],
      'pages'       => $pagesArray,
      'permalink'   => $xml['item']['PERMALINK'],
      'prettyurls'  => $xml['item']['PRETTYURLS'],
    );
    return $globals;
  }
  
  # loads large array for field properties
  private function fields() {
    $fields = array();
    include($this->directories['plugin']['var']['dir'].'/fields.php');
    return $fields;
  }

  # ======= GENERAL FUNCTIONS ======= #

  public function pluginInfo($info) {
    if (isset($this->plugin[$info])) return $this->plugin[$info];
  }
  
  /* TheMatrix::debugLog($log)
   * @param $log: message to be logged
   */
  public function debugLog($log) {
    if ($this->debug) debuglog($log);
  }
  
  /* TheMatrix::getGlobals()
   */
  public function getGlobals() {
    return $this->globals;
  }

  /* TheMatrix::getUsers()
   */
  public function getUsers() {
    $usersArray = glob(GSUSERSPATH.'*.xml');
    $users = array();
    foreach ($usersArray as $user) {
      $array = XML2Array::createArray(file_get_contents($user));
      unset($array['item']['PWD']);
      $users[$array['item']['USR']] = $array['item'];
    }
    return $users;
  }
  
  /* TheMatrix::getComponents()
   */
  public function getComponents() {
    $array = XML2Array::createArray(file_get_contents(GSDATAOTHERPATH.'components.xml'));
    $components = array();
    foreach ($array['channel']['item'] as $component) {
      $components[$component['slug']] = array(
        'title'   => $component['title']['@cdata'],
        'slug'    => $component['slug']['@cdata'],
        'value'   => $component['value'],
      );
    }
    return $components;
  }
  
  /* TheMatrix::getThemes()
   */
  public function getThemes() {
    $dir = glob(GSTHEMESPATH.'*/');
    $themes = array();
    foreach ($dir as $theme) {
      $theme = substr($theme, 0, strlen($theme)-1);
      $themes[] = end(explode('/', $theme));
    }
    return $themes;
  }
  
  public function getSiteName() {
    return $this->globals['sitename'];
  }
  
  public function getSiteURL() {
    return $this->globals['siteurl'];
  }
  
  public function getPrettyURLS() {
    return $this->globals['prettyurls'];
  }
  
  public function getFieldTypes() {
    return $this->fields['type'];
  }

  /* TheMatrix:getOptions($string, $delimiter)
   * @param $string:    string to explode into options array
   * @param $delimiter: delimiter to explode on (defaults to line break)
   */
  public function getOptions($string, $delimiter="\n") {
    if (is_string($string)) {
      $string = explode($delimiter, $string);
      $string = array_map('trim', $string);
      return $string;
    }
    else return false;
  }
  
  public function explodeTrim($delim="\n", $string) {
    if (is_string($string)) {
      $string = explode($delim, $string);
      $string = array_map('trim', $string);
    }
    return $string;
  }
  
  public function implodeTrim($delim="\n", $array) {
    if (is_array($array)) {
      $array = array_map('trim', $array);
      $array = implode("\n", $array);
    }
    return $array;
  }
  
  public function salt($salt) {
    $this->salt = $salt;
    return true;
  }

  public function getHierarcalOptions($string, $delimiter=">") {
    $options = new TheMatrixOptions($string);
    return $options->output($delimiter);
  }

  /* TheMatrix::renameKey($key, $rename, $array)
   * @param $key:    existing key
   * @param $rename: new name for key
   * @param $array:  array to search
   */
  public function renameKey($key, $rename, $array) {
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
  
  /* TheMatrix::recurseCopy()
   * (lifted from php documentation site)
   */ 
  public function recurseCopy($src,$dst) { 
    $dir = opendir($src); 
    @mkdir($dst); 
    while(false !== ( $file = readdir($dir)) ) { 
      if (( $file != '.' ) && ( $file != '..' )) { 
        if ( is_dir($src . '/' . $file) ) { 
          recurseCopy($src . '/' . $file,$dst . '/' . $file); 
        } 
        else { 
          copy($src . '/' . $file,$dst . '/' . $file); 
        } 
      } 
    } 
    closedir($dir); 
  }

  /* TheMatrix::str2Slug($string)
   * @param $string: string to be slugified
   */
  public function str2Slug($string) {
    if (is_string($string)) {
      return strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string)));
    }
    else return false;
  }

  /* TheMatrix::getExcerpt($string, $length)
   * Help from 'http://www.internoetics.com/2010/01/04/php-function-to-truncate-text-into-a-preview-or-excerpt-with-trailing-dots/'
   * @param $string: string to parse
   * @param $type:   type of excerpt; e.g. characters or paragraph
   * @param $length: length of excerpt
   */
  public function getExcerpt($string, $length=50) {
    if (is_string($string)) { 
      $excerpt = '';
      $string = strip_tags($string);
      if (strlen($string)>$length) {
        $excerpt = substr($string, 0, $length);
        $excerpt = substr($excerpt, 0, strrpos($excerpt,' '));
        $excerpt .= ' ...'; 
      }
      else $excerpt = $string;

      // return
      return $excerpt; 
    }
    else return false;
  } 

  /* TheMatrix::outputFunction($array, $depth, $tab)
   * @param $array: input array
   * @param $depth: depth level of array (multidimensional)
   * @param $tab:   indent type (space or tab; custom defined)
   */
  public function outputFunction($array, $depth=0, $tab="&nbsp;&nbsp;") {
    foreach ($array as $key=>$value) {
      // line break
      for ($i=0; $i<$depth; $i++) echo $tab;
      if (is_numeric($key)) {
        echo $key.' => ';
      }
      else {
        echo '\''.$key.'\' => ';
      }
      if (is_array($value)) {
        echo 'array('."\n";
        $this->outputFunction($value, $depth+1);
                // line break
        for ($i=0; $i<$depth; $i++) echo $tab;
        echo '),'."\n";
      }
      else {
        // value
        if (is_numeric($value)) {
          echo $value.','."\n";
        }
        else {
          echo '\''.$value.'\','."\n";
        }
      }
    }
  }

  # ======= SCHEMA FUNCTIONS ======= #

  /* TheMatrix::createSchemaFolder($name)
   * @param $name: name of the folder to be created (corresponds to the table name)
   */
  public function createSchemaFolder($name) {
    if (!is_dir($this->directories['data']['core']['dir'].$name)) {  
      $ret = mkdir($this->directories['data']['core']['dir'].$name, 0755);
      return $ret;
    }
    else return false;
  }

  /* TheMatrix::getSchemaVersion()
   */
  public function getSchemaVersion() {
    $file = $this->directories['data']['core']['dir'].'schema.xml';
    if (file_exists($file)) {
      $this->debugLog('Schema file loaded...');
      
      // load the xml file and setup the array. 
      $schemaXML = file_get_contents($file);
      $data = simplexml_load_string($schemaXML);
      $att = $data->attributes();
      return $att['version'];
    }
    else return false;
  }
  
  /* TheMatrix::getSchema($tableName, $fields)
   * @param $tableName: table name (e.g. 'foo')
   * @param $fields:    boolean as to whether just the fields array is desired
   */
  public function getSchema($tableName=false, $fields=false) {
    // initialize
    $file = $this->directories['data']['core']['dir'].'schema.xml';
    if (file_exists($file)) {
      $this->debugLog('Schema file loaded...');
      $schemaXML = XML2Array::createArray(file_get_contents($file));
      
      // reconditions the array into a workable one for the rest of the plugin
      $schema = array();
      
      // fixes array structure if there is only one table
      if (!isset($schemaXML['channel']['item'][0])) {
        $schemaXML['channel']['item'] = array($schemaXML['channel']['item']);
      }
      foreach ($schemaXML['channel']['item'] as $table) {
        $name = $table['name'];
        unset($table['name']);
        
        foreach ($table['field'] as $field) {
          $array = array();
          if (isset($field['@value'])) {
            $array = array('name'=>$field['@value']);
            if (is_array($field['@attributes'])) $array = array_merge($array, $field['@attributes']);
            $table['fields'][$field['@value']] = $array;
            
            foreach ($this->fields['properties'] as $key => $properties) {
              if (!isset($table['fields'][$field['@value']][$key])) $table['fields'][$field['@value']][$properties['key']] = $properties['default'];
            }
          }
          elseif(!is_array($field)) {
            $table['fields'][$field] = $field;
          }
        }
        // ensures ID field exists
        if (!isset($table['fields']) || !array_key_exists('id', $table['fields'])) {
          $table['fields']['id'] = array('name'=>'id');
          foreach ($this->fields['properties'] as $key=>$properties) {
            $table['fields']['id'][$properties['key']] = $properties['default'];
          }
        }
        unset($table['field']);
        
        // only add to the schema if the folder exists
        if (file_exists($this->directories['data']['core']['dir'].$name)) $schema[$name] = $table;
      }

      // alters the current schema array
      $this->schema = $schema;
      
      // return conditions (either full schema, table's schema or table field's schema
      if ($tableName && isset($schema[$tableName])) {
        if (!$fields) return $schema[$tableName];
        else          return $schema[$tableName]['fields'];
      }
      else {
        return $schema;
      }
    }
    else return false;
  }
  
  /* TheMatrix::modSchema()
   * @param $table: 
   * @param $array: 
   */
  public function modSchema($table, $array) {
    if ($this->tableExists($table)) {
      $this->schema[$table] = $array;
      $this->saveSchema();
      return true;
    }
    else return false;
  }
  
  /* TheMatrix::saveSchema()
   */
  public function saveSchema() {
    // root node for XML file (channel)
    $schema = array(
      '@attributes' => array(
        'version' => self::VERSION,
      )
    );
  
    $newSchema = array();
    
    foreach ($this->schema as $key=>$table) {
      $table = array_merge(array('name'=>$key), $table);
      $table['field'] = array();
      foreach ($table['fields'] as $field) {
        if (isset($field['name'])) {
          $array = array('@value'=>$field['name']);
          unset($field['name']);
        }
        $array['@attributes'] = $field;
        $table['field'][] = $array;
      }
      unset($table['fields']);
      $newSchema[] = $table;
    }
    
    $schema['item'] = $newSchema;
    $xml = Array2XML::createXML('channel', $schema);
    $xml->save($this->directories['data']['core']['dir'].'schema.xml');
    return $this->getSchema();
  }

  # ======= TABLE FUNCTIONS ======= #

  /* TheMatrix::tableExists($table)
   * @param $table: table name (e.g. 'foo')
   */
  public function tableExists($table) {
    if (array_key_exists($table, $this->schema) && is_writable($this->directories['data']['core']['dir'].$table)) {
      return true;
    }
    else return false;
  }

  /* TheMatrix::getSchemaTable($name, $query)
   * @param $name:  name of the table (e.g. 'foo')
   * @param $query: SQL-like query to perform (eg "SELECT id FROM foo WHERE id = 0")
   */
  public function getSchemaTable($name, $query='') {
    $returnArray = array();
    $table = array();
    if (is_dir($this->directories['data']['core']['dir'].'/'.$name."/")) {
      $path = $this->directories['data']['core']['dir'].'/'.$name."/";
      $dir_handle = @opendir($path) or die('Unable to open '.$path);
      $filenames = array();
      while ($filename = readdir($dir_handle)) {
        $ext = substr($filename, strrpos($filename, '.') + 1);
        $fname=substr($filename,0, strrpos($filename, '.'));
        if ($ext=="xml") {
          $thisfile_DM_Matrix = file_get_contents($path.$filename);
          $data = simplexml_load_string($thisfile_DM_Matrix);
          $id=$data->item;
          $idNum=$id->id;
          foreach ($id->children() as $opt=>$val) {
            $table[(int)$idNum][(string)$opt]=(string)$val;
          }         
        }
      }
      if ($query!='') {
        $returnArray = $table;
        $table = $this->sql->query($query);
      }
    }
    return $table;
  }
  
  /* TheMatrix::createTable($table, $fields, $maxrecords, $id)
   * @param $name:       name of the table (e.g. 'foo')
   * @param $fields:     array of fields to be created
                         (e.g. $fields = array(
                                            array('name'=>'field1', 'type'=>'text'),
                                            array('name'=>'field2', 'type'=>'text'),
                                            array('name'=>'field3', 'type'=>'text'),
                                          ))
   * @param $maxrecords: maximum number of records the table will hold (set to 0 for unlimited)
   * @param $id:         initial id for the next record to be created (default is 0; used for restoring tables)
   */
  public function createTable($name, $fields=array(), $maxrecords=0, $id=0) {
    if (array_key_exists($name, $this->schema)){
      $this->debugLog(i18n_r(self::FILE.'/TABLE_CREATEERROR'));
      return false;
    }
    
    // quick validation
    if ($name!='_routes') $name = $this->str2Slug($name);
    if (!is_numeric($maxrecords) || empty($maxrecords)) $maxrecords = 0;
    if (!is_numeric($id) || empty($id)) $id = 0;
    
    // start building new schema for table
    $this->schema[(string)$name] = array();
    $this->schema[(string)$name]['id']= $id;
    $this->schema[(string)$name]['maxrecords'] = $maxrecords;

    // ensure ID field exists
    if (!array_key_exists('id', $fields)) {
      $idField = array(
        'name'  => 'id',
        'type'  => 'int',
        'label' => 'ID Field',
        'size'  => 1,
      );
      $fields = array_merge(array($idField), $fields);
    }
    
    // formats the array key for the field schema to be created and fills in defaults
    foreach ($fields as $key=>$field) {
      foreach ($this->fields['properties'] as $fieldKey=>$fieldProperty) {
        if (!isset($fields[$key][$fieldKey])) {
          $field[$fieldKey] = $fieldProperty['default'];
        }
      }
      $fields[$field['name']] = $field;
      unset($fields[$key]);
    }
    
    $this->schema[(string)$name]['fields'] = $fields;
    
    // create the folder and save the schema
    $this->createSchemaFolder($name);
    $this->saveSchema();
    $this->debugLog(i18n_r(self::FILE.'/TABLE_CREATESUCCESS'));
    
    // uncomment for debugging
    #var_dump($name);
    #var_dump($fields);
    #var_dump($maxrecords);
    #var_dump($id);
    #var_dump($this->schema);
    #var_dump($this->schema[$name]);
    
    if ($this->tableExists($name) && file_exists($this->directories['data']['core']['dir'].$name.'/')) {
      return true;
    }
    else return false;
  }
  
  /* TheMatrix::copyTable($table, $name)
   * @param $table:  name of the table to be copied (e.g. 'foo')
   * @param $name:   name of copied table (e.g. null results in 'foo_copy' - set to any desired name)
   */
  public function copyTable($table, $name=null) {
    if ($this->tableExists($table)) {
      // ensures name isn't in the schema already
      if (empty($name)) {
        $name = $table.'_copy';
      }
      elseif($this->tableExists($name)) {
        $name = $name.'_copy';
      }
      
      // duplicate schema, save then copy files
      $this->schema[$name] = $this->schema[$table];
      $this->saveSchema();
      $this->recurseCopy(GSDATAOTHERPATH.'matrix/'.$table, GSDATAOTHERPATH.'matrix/'.$name);
        
      // return
      if ($this->tableExists($name)) {
        return true;
      }
      else return false;
    }
    // no table found
    else return false;
  }
  
  /* TheMatrix::emptyTable($table)
   * @param $table: name of table to flush of its records
   */
  public function emptyTable($table) {
    $records = glob($this->directories['data']['core']['dir'].'/'.$table.'/*.xml');
    if ($this->tableExists($table)) {
      foreach ($records as $record) {
        unlink($record);
      }
      $records = glob($this->directories['data']['core']['dir'].'/'.$table.'/*.xml');
      if (empty($records)) return true;
      else return false;
    }
    else return false;
  }
  
  /* TheMatrix::deleteTable($table)
   * @param $table: name of table delete
   */
  public function deleteTable($table) {
    if ($this->tableExists($table)) {
      // empty table, remove table from schema, delete the directory
      $this->emptyTable($table);
      unset($this->schema[$table]);
      rmdir($this->directories['data']['core']['dir'].'/'.$table);
      $this->saveSchema(true);
      
      if (!$this->tableExists($table)) {
        return true;
      }
      else return true;
    }
    else return false;
  }
  
  /* TheMatrix::emptyTable($table, $newName)
   * @param $table:   old name of table
   * @param $newName: new name of table
   */
  public function renameTable($table, $newName) {
    if ($this->tableExists($table) && !$this->tableExists($newName)) {
      // renaming
      $this->schema[$newName] = $this->schema[$table];
      unset($this->schema[$table]);
      rename($this->directories['data']['core']['dir'].'/'.$table.'/', $this->directories['data']['core']['dir'].'/'.$newName.'/');
      $this->saveSchema(true);
      
      if ($this->tableExists($newName)) {
        return true;
      }
      else return false;
    }
    else return false;
  }
  
  /* TheMatrix::getBackups($table, $directory, $file)
   * @param $table:     table name (e.g. 'foo')
   * @param $directory: directory to look in for backups (defaults to data/other/matrix/)
   * @param $file:      file name to search for (searches for 'foo_timestamp')
   */
  public function getBackups($table, $directory=false, $file=false) {
    // fixes root path and filename then prepares the backups array
    if ($directory==false) {
      $reldir = 'data/other/matrix/';
      $directory = $this->directories['data']['core']['dir'];
    }
    else {
      $reldir = $directory.'/';
      $directory = GSROOTPATH.$directory;
    }
    if ($file==false)      $file = $table.'_*';
    $getBackups = glob($directory.'/'.$file.'.zip');
    
    // fills in the backups array and sorts it accordingly (latest first)
    $backups = array();
    foreach ($getBackups as $backup) {
      $tmp = explode('/', $backup);
      $filename = end($tmp);
      $file = $filename;
      $filename = substr($filename, 0, strlen($filename)-4);
      $date = filemtime($backup);
      $backups[$date]['timestamp']  = $date;
      $backups[$date]['date']       = date('r', $date);
      $backups[$date]['link']       = $this->globals['siteurl'].$reldir.$file;
      $backups[$date]['path']       = GSROOTPATH.$reldir.$file;
      $backups[$date]['file']       = $file;
    }
    ksort($backups);
    $backups = array_reverse($backups);
    return $backups;
  }
  
  /* TheMatrix::backupTable($table, $directory, $file)
   * @param $table:     table name (e.g. 'foo')
   * @param $directory: directory to look in for backups (defaults to data/other/matrix/)
   * @param $file:      file name to search for (searches for 'foo_timestamp')
   */
  public function backupTable($table, $directory=false, $file=false) {
    if ($this->tableExists($table)) {
      $zip = new ZipArchive();
      
      // sets file and directory
      if(!$file)        $file = $table.'_'.time();
      if(!$directory)   $directory = 'data/other/matrix/';  

      // ensures directory will exist
      if (!file_exists(GSROOTPATH.$directory)) {
        echo GSROOTPATH.$directory;
        if (!mkdir(GSROOTPATH.$directory, 0755, true)) return false;
      }
      
      // concatenates directory and file
      $directory .= '/'.$file;
      $filename = GSROOTPATH.$directory.'.zip';
      
      if ($zip->open($filename, ZipArchive::CREATE)===TRUE) {
        // creates folder
        $zip->addEmptyDir($table);
        
        // adds files to folder
        foreach(glob(GSROOTPATH.'data/other/matrix/'.$table.'/*') as $xml) {
          $tmp = explode('/', $xml);
          $zip->addFile($xml, $table.'/'.end($tmp));
        }
        
        // adds a schema of the table to the backup file
        $originalSchema = XML2Array::createArray(file_get_contents($this->directories['data']['core']['dir'].'schema.xml'));
        
        $newSchema = array('item'=>array());
        
        foreach ($originalSchema['channel']['item'] as $key=>$properties) {
          if ($properties['name']==$table) $newSchema['item'] = $properties;
        }

        $backupFile = GSROOTPATH.'data/other/matrix/'.$table.'/'.$table.'_schema.xml';
        $array2XML = Array2XML::createXML('channel', $newSchema);
        $array2XML->save($backupFile);
        $tmp = explode('/', $backupFile);
        $zip->addFile($backupFile, end($tmp));
        
        // closes zip file
        $zip->close();
        
        // deletes backup.xml
        if (file_exists($backupFile)) unlink($backupFile);
        return $filename;
      }
      else return false;
    }
    else return false;
  }
  
  /* TheMatrix::restoreTable($table, $directory, $file)
   * @param $table:     table name (e.g. 'foo')
   * @param $directory: directory to look in for backups (defaults to data/other/matrix/)
   * @param $file:      file name to search for (searches for 'foo_timestamp')
   */
  public function restoreTable($table, $directory=false, $file=false) {
    // first empty the table if it's there
    if ($this->tableExists($table)) $this->emptyTable($table);
    
    // find backups and unpack the correct file
    $backups = $this->getBackups($table, $directory, $file);
    if ($backups && isset($backups[0])) {
      $backups = $backups[0];
      
      // checks if the zip file exists
      $zip = new ZipArchive();
      if ($zip->open($backups['path'])===TRUE) {
        // unzips files
        $zip->extractTo($this->directories['data']['core']['dir']);
        
        // ensures table is in the schema
        $schemaFile  = $this->directories['data']['core']['dir'].$table.'_schema.xml';
        $schemaTable = XML2Array::createArray(file_get_contents($schemaFile));
        $schemaFull  = XML2Array::createArray(file_get_contents($this->directories['data']['core']['dir'].'schema.xml'));
        
        // pull the schema from the backup file, add it to the existing full schema and save the xml file
        $schemaTable = $schemaTable['channel']['item'];
        
        // removes table from the schema so that it can be put in freshly
        foreach ($schemaFull['channel']['item'] as $key => $details) {
          if ($details['name']==$table) unset($schemaFull['channel']['item'][$key]);
        }
        
        // adds to the schema, saves file and removes the backup schema xml
        $schemaFull['channel']['item'][] = $schemaTable;
        $xml = Array2XML::createXML('channel', $schemaFull['channel']);
        $xml->save($this->directories['data']['core']['dir'].'schema.xml');
        $zip->close();
        unlink($schemaFile);
        
        // saves the schema array just for good measure
        $this->getSchema();
        $this->saveSchema();
        
        // return
        if ($this->tableExists($table)) {
          return true;
        }
        else return false;
      }
    }
    else return false; 
  }

  # ======= FIELD FUNCTIONS ======= #

  # ======= RECORD/QUERY FUNCTIONS ======= #

  /* TheMatrix::recordExists($table, $id)
   * @param $table: table name (e.g. 'foo')
   * @param $id:    record id
   */
  public function recordExists($table, $id) {
    $file = $this->directories['data']['core']['dir'].'/'.$table.'/'.$id.'.xml';
    if ($this->tableExists($table) && file_exists($file)) {
      $record = XML2Array::createArray(file_get_contents($file));
      if (isset ($record['channel']['item'])) {
        $record = $record['channel']['item'];
        foreach ($record as $field => $value) {
          if (is_array($value)) {
            sort($value);
            $record[$field] = $value[0];
          }
        }
        return $record;
      }
      else return false;
    }
    else return false;
  }

  /* TheMatrix::paginateQuery($query, $key, $max, $range, $url, $delim, $display)
   * @param $query:   array to paginate
   * @param $key:     $_GET key for pagination (e.g. 'page')
   * @param $max:     maximum number of entries per page
   * @param $range:   range of links to show spread about the current page
   * @param $url:     base url links for the paginated navigtation
   * @param $delim:   url structure of paginated links ($1 is the placeholderfor the page number)
   * @param $display: array for labels of of output links (keys are 'first', 'prev', 'next', 'last')
   */
  public function paginateQuery($query, $key='page', $max=5, $range=2, $url='', $delim='&page=$1', $display=array('first'=>'|&lt;&lt;', 'prev'=>'&lt;', 'next'=>'&gt;', 'last'=>'&gt;&gt;|')) {
    // initialisation
    $paginatedQuery = array();
    if(!isset($_GET[$key])) {
      $_GET[$key] = 1; // so if GET isn't set, it still shows the first page's results
    }
      
    // gets total pages and results
    $paginatedQuery['totalnum']     = count($query);
    $paginatedQuery['pages']        = ceil($paginatedQuery['totalnum']/$max);
    $paginatedQuery['results']      = array_slice($query, ($max*($_GET[$key]-1)), $max, true);
    $paginatedQuery['resultsnum']   = count($paginatedQuery['results']);  
      
    // formats paginated links
      // current
      $current = $_GET[$key];
        
      // first
      $first = 1;
        
      // prev
      if ($current==1) $prev = 1; 
      else $prev = $current-1;
        
      // next
      if ($paginatedQuery['totalnum']==1) $next = 1; 
      else $next = $current+1;
        
      // last
      $last = $paginatedQuery['pages'];
        
      // display 
        $paginatedQuery['links'] = ''; // initialisation
          
        // first, prev
        if($current!=$first) $paginatedQuery['links'] = '<a class="first" href="'.$url.str_replace('$1', $first, $delim).'">'.$display['first'].'</a>'."\n".'<a class="prev" href="'.$url.str_replace('$1', $prev, $delim).'">'.$display['prev'].'</a>'."\n";
         
        // numbers
        for ($i = ($current - $range); $i < ($current + $range + 1); $i++) {
          if ($i > 0 && $i <= $paginatedQuery['pages']) {
            // current
            if ($i==$current) {
              $paginatedQuery['links'] .= '<span class="current">'.$i.'</span>'."\n";
            }
            // link
            else {
              $paginatedQuery['links'] .= '<a class="page" href="'.$url.str_replace('$1', $i, $delim).'">'.$i.'</a>'."\n";
            }
          }
        }
          
        // next, last
        if($current!=$last) $paginatedQuery['links'] .= '<a class="next" href="'.$url.str_replace('$1', $next, $delim).'">'.$display['next'].'</a>'."\n".'<a class="last" href="'.$url.str_replace('$1', $last, $delim).'">'.$display['last'].'</a>';
      
    // return array
    return $paginatedQuery;
  }
  
  // paginate i18n search results
  public function paginateI18nResults($table, $searchid, $query, $key='page', $max=5, $range=2, $url='', $delim='&page=$1', $display=array('first'=>'|&lt;&lt;', 'prev'=>'&lt;', 'next'=>'&gt;', 'last'=>'&gt;&gt;|')) {
    if ($this->tableExists($table)) {
      if (isset($query['results'])) $query = $query['results'];
      $results = $this->paginateQuery($query, $key, $max, $range, $url, $delim, $display);
      $newarray = array();
      foreach ($results['results'] as $result) {
        $record = array();
        foreach ($this->schema[$table]['fields'] as $field) {
          $record[$field['name']] = $result->{$field['name']};
        }
        $record['id'] = substr($result->id, strlen($searchid));
        $newarray[] = $record;
      }
      $results['results'] = $newarray;
      return $results;
    }
  }

  # ======= ADMIN FUNCTIONS ======= #
  
  /* TheMatrix::themeHeader($plugin, $prefix)
   * @param $plugin: id of your plugin (e.g. 'matrix')
   * @param $prefix: prefix if you only want to show certain on a particular end (e.g. 'front_' to load all front-end scripts, 'back_' to load all back-end scripts)
   */
  public function themeHeader($plugin, $prefix='') {
    echo "\n".'<!--'.$plugin.' files-->'."\n";
    // css
    echo '  <!--css-->'."\n";
    foreach (glob(GSPLUGINPATH.$plugin.'/css/*.css') as $css) {
      $tmp = explode('/', $css);
      echo '    <link rel="stylesheet" href="'.$this->globals['siteurl'].'plugins/'.$plugin.'/css/'.$prefix.end($tmp).'"/>'."\n";
    }
    echo '  <!--/css-->'."\n";
    
    // js
    echo '  <!--js-->'."\n";
    $javascript = glob(GSPLUGINPATH.$plugin.'/js/*.js');
    sort($javascript);
    $javascript = array_reverse($javascript);
    foreach ($javascript as $js) {
      $tmp = explode('/', $js);
      echo '    <script src="'.$this->globals['siteurl'].'plugins/'.$plugin.'/js/'.$prefix.end($tmp).'"></script>'."\n";
    }
    echo '  <!--/js-->'."\n";
    echo '<!--/'.$plugin.' files-->'."\n";
  }

  /* TheMatrix::addRoute($url, $route)
   * @param $url:   404 url
   * @param $route: redirection url
   */
  public function addRoute($url, $route) {
    $this->createRecord('_routes', array('route'=>$url,'rewrite'=>$route));  
  }
  
  /* ===== TABLE FUNCTIONS ===== */

  
  /* TheMatrix::dloadFile($path)
   * @param $path: path to file for download
   */
  public function dloadFile($path) {
    if (file_exists($path)) {
      header('Content-Description: File Transfer');
      header('Content-Type: application/octet-stream');
      header('Content-Disposition: attachment; filename='.basename($path));
      header('Content-Transfer-Encoding: binary');
      header('Expires: 0');
      header('Cache-Control: must-revalidate');
      header('Pragma: public');
      header('Content-Length: ' . filesize($path));
      ob_clean();
      flush();
      readfile($path);
      exit;
      return true;
    }
    else return false;
  }
  
  /*
   * FIELD FUNCTIONS
   *
   */
  
  // create field
  public function createField($table, $field) {
    if ($this->tableExists($table) && isset($field['name'])) {
      // fill in defaults
      foreach ($this->fields['properties'] as $fieldKey=>$fieldProperty) {
        if (!isset($field[$fieldKey])) {
          $field[$fieldKey] = $fieldProperty['default'];
        }
      }

      // save schema
      $this->schema[$table]['fields'][$field['name']] = $field;
      $this->saveSchema();
      
      // uncomment for debugging
      #$schema = $this->schema;
      #var_dump($table);
      #var_dump($field);
      #var_dump($schema[$table]);
      
      // return status
      if (isset($this->schema[$table]['fields'][$field['name']]) && is_array($this->schema[$table]['fields'][$field['name']])) {
        return true;
      }
      else return false;
    }
    else return false;
  }
  
  // checks that a field exists
  public function fieldExists($table, $field) {
    if ($this->tableExists($table) && isset($this->schema[$table]['fields'][$field])) {
      return true;
    }
    else return false;
  }

  
  // rename field
  public function renameField($table, $field, $name) {
    if ($this->fieldExists($table, $field) && !$this->fieldExists($table, $name)) {
      // first rename the field in the schema and save it
      $this->schema[$table]['fields'] = $this->renameKey($field, $name, $this->schema[$table]['fields']);
      $this->schema[$table]['fields'][$name]['name'] = $name;
      $this->saveSchema();
      
      // rename the fields in the dataset
      $data = glob($this->directories['data']['core']['dir'].$table.'/*.xml');
      foreach ($data as $record) {
        // pull the array, rename the field, save the xml file
        $array = XML2Array::createArray(file_get_contents($record)); 
        
        if (isset($array['channel']['item'][$field])) {
          $array['channel']['item'] = $this->renameKey($field, $name, $array['channel']['item']);
          $xml = Array2XML::createXML('channel', $array['channel']);
          $xml->save($record);
        }
      }
      return true;
    }
    else return false;
  }
  
  // remove field
  public function deleteField($table, $field) {
    if ($this->fieldExists($table, $field)) {
      unset($this->schema[$table]['fields'][$field]);
      $this->saveSchema();
      if (!isset($this->schema[$table]['fields'][$field])) {
        return true;
      }
      else return false;
    }
    else return true;
  }
  
  // reorder fields
  public function reorderFields($table, $fields) {
    if ($this->tableExists($table)) {
      // check that the fields array provided covers all of the existing fields
      $i = 1; // 1 because the id field is part of the total
      $total = count($this->schema[$table]['fields']);
      foreach ($fields as $field) {
        if (array_key_exists($field, $this->schema[$table]['fields'])) $i++;
      }
      if ($i==$total) {
        foreach ($fields as $field) {
          $array = $this->schema[$table]['fields'][$field];
          unset($this->schema[$table]['fields'][$field]);
          $this->schema[$table]['fields'][$field] = $array;
        }
        $this->saveSchema();
        return true;
      }
      else return false;
    }
    else return false;
  }
  
  public function buildFields($table, $array) {
    if ($this->tableExists($table) && !empty($array['name'])) {
      // reorder the array to fit the createTable format
      $array['name'] = array_map('self::str2Slug', $array['name']);
      foreach ($array['name'] as $key => $field) {
        $array['fields'][$key] = array('name'=>$field);
      }
      
      foreach ($array as $key => $value) {
        if ($key!='tableName' && $key!='fields' && $key!='maxrecords' && is_array($value)) {
          foreach ($value as $fieldKey => $fieldValue) {
            $array['fields'][$fieldKey][$key] = $fieldValue;
          }
        }
      }
      
      // create/delete fields
      foreach ($array['fields'] as $key => $field) {
        
        if ($field['oldname']!=$field['name']) {
          $this->renameField($table, $field['oldname'], $field['name']);
        }
        $this->createField($table, $field);
        
      }
      foreach ($this->schema[$table]['fields'] as $field => $details) {
        if ($field!='id' && !in_array($field, $array['name'])) {
          $this->deleteField($table, $field);
        }
      }
      
      // reorder fields
      $this->reorderFields($table, $array['name']);
      
      // final return
      return true;
    }
    else return false; 
  }

  public function displayField($table, $field, $value='', $ckeditor=false) {
    if ($this->fieldExists($table, $field)) {
      $schema = $this->schema[$table]['fields'][$field];
      // quick formatting
      if (empty($value) && strlen($schema['default']) == 0) {
        if ($schema['type'] == 'datetimelocal') $value = time();
        elseif ($schema['type'] == 'int') $value = 0;
        else $value = $schema['default'];
      }
      if ($schema['type']=='password') $value = '';
      
      // maxlength
      if (empty($schema['maxlength'])) {
        $maxlength = '';
      }
      else {
        $maxlength = 'maxlength="'.$schema['maxlength'].'"';
      }
      
      // text
      if ($schema['type']=='text') {
        ?>
        <input type="text" name="post-<?php echo $field; ?>" class="text" value="<?php echo $value; ?>" placeholder="<?php echo $schema['desc']; ?>" <?php echo $maxlength;?> <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>/>
        <?php
      }
      // password
      elseif ($schema['type']=='password') {
        ?>
        <input type="password" name="post-<?php echo $field; ?>" class="text" value="<?php echo $value; ?>" pattern=".{6,}" placeholder="<?php echo $schema['desc']; ?>" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>/>
        <?php
      }
      // int
      elseif ($schema['type']=='int') {
        ?>
        <input type="number" name="post-<?php echo $field; ?>" class="text int" value="<?php echo $value; ?>" placeholder="<?php echo $schema['desc']; ?>" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>/>
        <?php
      }
      // email
      elseif ($schema['type']=='email') {
        ?>
        <input type="email" name="post-<?php echo $field; ?>" class="MatrixEmail text email" value="<?php echo $value; ?>" placeholder="<?php echo $schema['desc']; ?>" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>/>
        <?php
      }
      // url
      elseif ($schema['type']=='url') {
        ?>
        <input type="url" name="post-<?php echo $field; ?>" class="MatrixURL text url" value="<?php echo $value; ?>" placeholder="<?php echo $schema['desc']; ?>" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>/>
        <?php
      }
      // textlong
      elseif ($schema['type']=='textlong') {
        ?>
        <input type="text" name="post-<?php echo $field; ?>" class="text textlong title" value="<?php echo $value; ?>" placeholder="<?php echo $schema['desc']; ?>"  <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>/> 
        <?php
      }
      // textarea
      elseif ($schema['type']=='textarea') {
        ?>
        <textarea name="post-<?php echo $field; ?>" id="post-<?php echo $field; ?>" class="MatrixTextarea textarea text" placeholder="<?php echo $schema['desc']; ?>" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>><?php echo $value; ?></textarea>
        <?php
      }
      // textmulti
      elseif ($schema['type']=='textmulti') {
        ?>
        <span class="textmultiSpan">
          <?php
            $schema['desc'] = explode("\n", $schema['desc']);
            $schema['desc'] = array_map('trim', $schema['desc']);
            $options = explode("\n", $value);
            $options = array_map('trim', $options);
            for ($i=0; $i<$schema['rows']; $i++) {
          ?>
            <input type="text" style="margin-bottom: 4px;" name="post-<?php echo $field; ?>[]" class="text textmulti" value="<?php if (isset($options[$i])) echo $options[$i]; ?>" placeholder="<?php if (isset($schema['desc'][$i])) echo $schema['desc'][$i]; ?>" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>/>
          <?php
            }?>
        </span>
        <?php
      }
      // intmulti
      elseif ($schema['type']=='intmulti') {
        ?>
        <span class="intmultiSpan">
          <?php
            // label
            $schema['other'] = explode("\n", $schema['other']);
            $schema['other'] = array_map('trim', $schema['other']);
            
            // description
            $schema['desc'] = explode("\n", $schema['desc']);
            $schema['desc'] = array_map('trim', $schema['desc']);
            
            // value
            $options = explode("\n", $value);
            $options = array_map('trim', $options);
            for ($i=0; $i<$schema['rows']; $i++) {
          ?>
            <?php if (isset($schema['other'][$i])) echo '<span style="display: block; overflow: hidden;"><label style="display: block; padding: 5px 0 5px 0; float: left; width: 40%;">'.$schema['other'][$i].' : </label>'; ?><input type="number" style="margin-bottom: 4px; <?php if (isset($schema['other'][$i])) echo 'width: 50%; float: right;'; ?>" name="post-<?php echo $field; ?>[]" class="text intmulti" value="<?php if (isset($options[$i])) echo $options[$i]; ?>" placeholder="<?php if (isset($schema['desc'][$i])) echo $schema['desc'][$i]; ?>" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>/><?php if (isset($schema['other'][$i])) echo '</span>'; ?>
          <?php
            }?>
        </span>
        <?php
      }
      // textmulticustom
      elseif ($schema['type']=='textmulticustom') {
        ?>
        <span class="textmultiSpan">
          <a href="#" class="add">+</a><br>
          <?php
            $options = explode("\n", $value);
            $options = array_map('trim', $options);
            foreach ($options as $option) {
          ?>
            <span><input type="text" name="post-<?php echo $field; ?>[]" class="text textmulti" value="<?php echo $option; ?>" placeholder="<?php echo $schema['desc']; ?>" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>/> <a href="#" class="remove">-</a></span>
          <?php
            }
            if (empty($options)) {
          ?>
            <span><input type="text" name="post-<?php echo $field; ?>[]" class="text textmulti" placeholder="<?php echo $schema['desc']; ?>" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>/> <a href="#" class="remove">-</a></span>
          <?php } ?>
        </span>
        <script>
          $(document).ready(function(){
            $('.textmultiSpan a').click(function(){
              $(this).parent().append('<span><input type="text" name="post-<?php echo $field; ?>[]" class="text textmulti" value="<?php echo $option; ?>" placeholder="<?php echo $schema['desc']; ?>" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>/> <a href="#" class="remove">-</a></span>');
              return false;
            }); // click
          }); // ready
        </script>
        <?php
      }
      // tags
      elseif ($schema['type']=='tags') {
        ?>
        <textarea name="post-<?php echo $field; ?>" id="post-<?php echo $field; ?>" class="MatrixTags tags" placeholder="<?php echo $schema['desc']; ?>" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>><?php echo $value; ?></textarea>
        <?php
        $this->initialiseTagsInput($id='.MatrixTags');
      }
      // slug
      elseif ($schema['type']=='slug') {
        ?>
        <input type="text" name="post-<?php echo $field; ?>" class="MatrixSlug text slug" value="<?php echo $value; ?>" placeholder="<?php echo $schema['desc']; ?>" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>/>
        <?php
      }
      // dropdown
      elseif ($schema['type']=='dropdown') { 
        if ($this->fieldExists($schema['row'], $schema['table'])) {
          $query = $this->query('SELECT '.$schema['row'].' FROM '.$schema['table'].' ORDER BY id ASC');
        }
        else $query = array();
        ?>
          <select name="post-<?php echo $field; ?>" class="text dropdown" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>>
            <?php foreach ($query as $record) { ?>
              <option value="<?php echo $record[$schema['row']]; ?>" <?php if ($record[$schema['row']]==$value) echo 'selected="selected"'; ?> ><?php echo $record[$schema['row']]; ?></option>
            <?php } ?>
          </select>
        <?php
      }
      // pages
      elseif ($schema['type']=='pages') {
        // load current existing pages
        getPagesXmlValues();
        global $pagesArray;
        $pages = $pagesArray;
        ?>
          <select name="post-<?php echo $field; ?>" class="text dropdown" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>>
            <?php foreach ($pages as $slug => $properties) { ?>
              <option value="<?php echo $slug; ?>" <?php if ($slug==$value) echo 'selected="selected"'; ?> ><?php echo $properties['title']; ?></option>
            <?php } ?>
          </select>
        <?php
      }
      // users
      elseif ($schema['type']=='users') {
        $users = $this->getUsers();
        ?>
          <select name="post-<?php echo $field; ?>" class="text dropdown" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>>
            <?php foreach ($users as $user => $details) { ?>
              <option value="<?php echo $user; ?>" <?php if ($user==$value || (empty($value) && $user==$_COOKIE['GS_ADMIN_USERNAME'])) echo 'selected="selected"'; ?> ><?php if (empty($details['NAME'])) echo $user; else echo $details['NAME']; ?></option>
            <?php } ?>
          </select>
        <?php
      }
      // components
      elseif ($schema['type']=='components') {
        $components = $this->getComponents();
        ?>
          <select name="post-<?php echo $field; ?>" class="text dropdown" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>>
            <?php foreach ($components as $slug => $component) { ?>
              <option value="<?php echo $slug; ?>" <?php if ($slug==$value) echo 'selected="selected"'; ?> ><?php echo $component['title']; ?></option>
            <?php } ?>
          </select>
        <?php
      }
      // themes
      elseif ($schema['type']=='themes') {
        $themes = $this->getThemes();
        ?>
          <select name="post-<?php echo $field; ?>" class="text dropdown" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>>
            <?php foreach ($themes as $theme) { ?>
              <option value="<?php echo $theme; ?>" <?php if ($theme==$value || (empty($value)) && $theme == $this->globals['template']) echo 'selected="selected"'; ?> ><?php echo $theme; ?></option>
            <?php } ?>
          </select>
        <?php
      }
      // template
      elseif ($schema['type']=='template') {
        // load templates for current theme
        $templates = glob(GSTHEMESPATH.$this->globals['template'].'/*.php');
        
        // unset 'functions.php' and '*.inc.php'
        foreach ($templates as $key => $template) {
          $tmp = explode('/', $template);
          $templates[$key] = $template = end($tmp);
          if (
            strtolower($template) == 'functions.php' ||
            substr($template, -7, 7) == 'inc.php'
          ) {
            unset($templates[$key]);
          }
        }
        sort($templates);
        ?>
          <select name="post-<?php echo $field; ?>" class="text dropdown" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>>
            <?php foreach ($templates as $template) { ?>
              <option value="<?php echo $template; ?>" <?php if ($template == $value) echo 'selected="selected"'; ?> ><?php echo $template; ?></option>
            <?php } ?>
          </select>
        <?php
      }
      // dropdowncustom
      elseif ($schema['type']=='dropdowncustom') {
        $options = explode("\n", $schema['options']);
        $options = array_map('trim', $options);
        ?>
          <select name="post-<?php echo $field; ?>" class="text dropdown" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>>
            <?php foreach ($options as $option) { ?>
              <option value="<?php echo $option; ?>" <?php if ($option==$value) echo 'selected="selected"'; ?> ><?php echo $option; ?></option>
            <?php } ?>
          </select>
        <?php
      }
      // dropdowncustomkey
      elseif ($schema['type']=='dropdowncustomkey') {
        $options = explode("\n", $schema['options']);
        $options = array_map('trim', $options);
        ?>
          <select name="post-<?php echo $field; ?>" class="text dropdown" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>>
            <?php foreach ($options as $key => $option) { ?>
              <option value="<?php echo $key; ?>" <?php if ($key==$value) echo 'selected="selected"'; ?> ><?php echo $option; ?></option>
            <?php } ?>
          </select>
        <?php
      }
      // dropdownhierarchy
      elseif ($schema['type']=='dropdownhierarchy') {
        $options = $this->getHierarcalOptions($schema['options']);
        ?>
          <select name="post-<?php echo $field; ?>" class="text dropdown" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>>
            <?php foreach ($options as $option) { ?>
              <option value="<?php echo $option['value']; ?>" <?php if ($option['value']==$value) echo 'selected="selected"'; ?> ><?php echo $option['option']; ?></option>
            <?php } ?>
          </select>
        <?php
      }
      // checkbox
      elseif ($schema['type']=='checkbox') {
        $selected = $this->getOptions($value);
        $options = $this->getOptions($schema['options']);
      ?>
      <div class="MatrixCheckbox">
      <?php
        foreach ($options as $key => $option) {
          $option = filter_var($option, FILTER_SANITIZE_STRING);
        ?>
          <input type="checkbox" name="post-<?php echo $field; ?>[<?php echo $key; ?>]" <?php if (in_array($option, $selected)) echo 'checked="checked"'; ?> class="input"  <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>/> <span class="option"><?php echo $option; ?></span><br />
        <?php
        }
      ?>
      </div>
      <?php
      }
      // datetimelocal
      elseif (
        $schema['type']=='datetimelocal' ||
        $schema['type']=='datetime' ||
        $schema['type']=='datepicker'
      ) {
        if (!is_numeric($value)) $value = time();
        ?>
        <input type="datetime-local" class="text datetimelocal" name="post-<?php echo $field; ?>" value="<?php echo date('Y-m-d\TH:i', $value); ?>" placeholder="<?php echo $schema['desc']; ?>" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>/>
        <?php
      }
      // imagepicker
      elseif ($schema['type']=='imagepicker') {
        ?>
        <input class="text imagepicker DM_filepicker " type="text" id="post-<?php echo $field; ?>" name="post-<?php echo $field; ?>" style="width:98%;" placeholder="<?php echo $schema['desc']; ?>" value="<?php echo $value; ?>" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>/>
        <span class="edit-nav"><a id="browse-<?php echo $field; ?>" href="javascript:void(0);">Browse</a></span>
        <script type="text/javascript">
          $(function() { 
            $('#browse-<?php echo $field; ?>').click(function(e) {
              window.open('<?php echo $this->globals['siteurl']; ?>admin/filebrowser.php?CKEditorFuncNum=1&func=addImageThumbNail&returnid=post-<?php echo $field; ?>&type=images', 'browser', 'width=800,height=500,left=100,top=100,scrollbars=yes');
            });
          });
        </script>
        <?php
      }
      // imageuploadadmin
      elseif ($schema['type']=='imageuploadadmin') {
        ?>
        <input type="file" class="text imageuploadadmin DM_imageuploadadmin" style="margin: 0 0 10px 0 !important;" name="post-<?php echo $field; ?>" disabled/>
        <select class="text imageuploadadmin DM_imageuploadadmin " name="post-<?php echo $field; ?>">
          <option value="">--no file--</option>
          <option value="upload">--upload--</option>
          <?php
            $images = glob(GSDATAUPLOADPATH.$schema['path'].'*.*');
            $thumbs = glob(GSTHUMBNAILPATH.$schema['path'].'*.*');
            foreach ($images as $image) {
              $tmp = explode('/', $image);
              $file = end($tmp);
          ?>
          <option value="<?php echo $file; ?>" <?php if ($file==$value) echo 'selected="selected"'; ?>><?php echo $file; ?></option>
          <?php } ?>
        </select>
        <script>
          $(document).ready(function(){
            $('input.imageuploadadmin').hide();
            $('select.imageuploadadmin').change(function(){
              if ($(this).val() == 'upload') {
                $(this).prev('input.imageuploadadmin').slideDown().prop('disabled', false);
              }
              else {
                $(this).prev('input.imageuploadadmin').slideUp().prop('disabled', true);
              }
            }); // change
          }); // ready
        </script>
        <?php
      }
      // filepicker
      elseif ($schema['type']=='filepicker') {
        ?>
        <input class="MatrixFilepicker text filepicker" name="post-<?php echo $field; ?>" type="text" id="post-<?php echo $field; ?>" name="post-<?php echo $field; ?>" style="width:98%;" placeholder="<?php echo $schema['desc']; ?>" value="<?php echo $value; ?>" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>/>
        <span class="edit-nav"><a id="browse-<?php echo $field; ?>" href="javascript:void(0);">Browse</a></span>
        <script type="text/javascript">
          $(function() { 
            $('#browse-<?php echo $field; ?>').click(function(e) {
              window.open('<?php echo $this->globals['siteurl']; ?>admin/filebrowser.php?CKEditorFuncNum=1&returnid=post-<?php echo $field; ?>&type=all', 'browser', 'width=800,height=500,left=100,top=100,scrollbars=yes');
            });
          });
        </script>
        <?php
      }
      // bbcodeeditor
      elseif ($schema['type']=='bbcodeeditor') {
        ?>
        <textarea id="post-<?php echo $field; ?>" name="post-<?php echo $field; ?>" class="MatrixBBCode bbcodeeditor" placeholder="<?php echo $schema['desc']; ?>" <?php echo $maxlength;?> <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>><?php echo $value; ?></textarea>
        <script language="javascript">
          $(document).ready(function()  {
            $('.MatrixBBCode').markItUp(GSBBCodeSettings);
          });
        </script>
        <?php
      }
      // wikieditor
      elseif ($schema['type']=='wikieditor') {
        ?>
        <textarea id="post-<?php echo $field; ?>" name="post-<?php echo $field; ?>" class="MatrixWiki wikieditor" placeholder="<?php echo $schema['desc']; ?>" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>><?php echo $value; ?></textarea>
        <script language="javascript">
          $(document).ready(function()  {
            $('.MatrixWiki').markItUp(GSWikiSettings);
          });
        </script>
        <?php
      }
      // markdowneditor
      elseif ($schema['type']=='markdowneditor') {
        ?>
        <textarea id="post-<?php echo $field; ?>" name="post-<?php echo $field; ?>" class="MatrixMarkDown markdown" placeholder="<?php echo $schema['desc']; ?>" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>><?php echo $value; ?></textarea>
        <script language="javascript">
          $(document).ready(function()  {
            $('.MatrixMarkDown').markItUp(GSMarkDownSettings);
          });
        </script>
        <?php
      }
      // wysiwyg
      elseif ($schema['type']=='wysiwyg') {
        ?>
        <textarea id="post-<?php echo $field; ?>" name="post-<?php echo $field; ?>" class="DMckeditor text wysiwyg" style="width:513px; height:200px; border: 1px solid #AAAAAA;" placeholder="<?php echo $schema['desc']; ?>" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>><?php echo $value; ?></textarea>
        <?php
        if ($ckeditor) $this->initialiseCKEditor();
      }
      // codeeditor
      elseif ($schema['type']=='codeeditor') {
        ?>
        <textarea id="post-<?php echo $field; ?>" name="post-<?php echo $field; ?>" class="codeeditor DM_codeeditor text" placeholder="<?php echo $schema['desc']; ?>" <?php echo $schema['readonly']; ?> <?php echo $schema['required']; ?> <?php if (strlen(trim($schema['validation']))>0) echo 'pattern="'.$schema['validation'].'"'; ?>><?php echo $value; ?></textarea>
        <?php
        $this->initialiseCodeMirror();
        $this->instantiateCodeMirror($field);
      }
      else {
        echo 'Unknown';
      }
    }
    else return false;
  }

  public function displayForm($table, $id=null) {
    if ($this->tableExists($table)) {
      $fields = $this->schema[$table]['fields'];
      
      // used for updating a record
      if (isset($id) && is_numeric($id)) {
        $record = $this->recordExists($table, $id);
        if ($record) {
          foreach ($record as $field => $value) {
            $fields[$field]['default'] = $value;
          }
        }
      }
      unset($fields['id']);
      
      // format the array to show correct classes (leftopt, rightsec, etc...)
      $array = array();
      
      foreach ($fields as $field) {
        if ($field['class']=='leftopt') {
          $array['metadata_window']['window']['leftopt'][] = $field;
        }
        elseif ($field['class']=='rightopt') {
          $array['metadata_window']['window']['rightopt'][] = $field;
        }
        elseif ($field['class']=='leftsec') {
          $array['section']['window']['leftsec'][] = $field;
        }
        elseif ($field['class']=='rightsec') {
          $array['section']['window']['rightsec'][] = $field;
        }
        else {
          $array[] = $field;
        }
      }
      
      // invisible fields
      foreach ($array as $key => $value) {

          if ($key == 'metadata_window' && isset($value['window'])) {
          ?>
            <div id="metadata_window">
              <div class="leftopt">
              <?php foreach ($value['window']['leftopt'] as $field) { ?>
                <p>
                  <label><?php if (!empty($field['label'])) { ?> <?php echo $field['label']; ?> : <?php } ?></label>
                  <?php $this->displayField($table, $field['name'], $field['default']); ?>
                </p>
              <?php } ?>
              </div>
              <div class="rightopt">
              <?php foreach ($value['window']['rightopt'] as $field) { ?>
                <p>
                  <label><?php if (!empty($field['label'])) { ?> <?php echo $field['label']; ?> : <?php } ?></label>
                  <?php $this->displayField($table, $field['name'], $field['default']); ?>
                </p>
              <?php } ?>
              </div>
              <div class="clear"></div>
            </div>
          <?php
          }
          elseif ($key == 'section' && isset($value['window'])) {
          ?>
            <div class="leftsec">
            <?php foreach ($value['window']['leftsec'] as $field) { ?>
                <p>
                  <label><?php if (!empty($field['label'])) { ?> <?php echo $field['label']; ?> : <?php } ?></label>
                  <?php $this->displayField($table, $field['name'], $field['default']); ?>
                </p>
            <?php } ?>
            </div>
            <div class="rightsec">
            <?php foreach ($value['window']['rightsec'] as $field) { ?>
              <p>
                <label><?php if (!empty($field['label'])) { ?> <?php echo $field['label']; ?> : <?php } ?></label>
                <?php $this->displayField($table, $field['name'], $field['default']); ?>
              </p>
            <?php } ?>
            </div>
            <div class="clear"></div>
          <?php
          }
          elseif($value['visibility']==1) {
          ?>
          <p>
            <label><?php if (!empty($value['label'])) { ?> <?php echo $value['label']; ?> : <?php } ?></label>
            <?php $this->displayField($table, $value['name'], $value['default']); ?>
          </p>
          <?php
          }
        
      }
      $this->initialiseCKEditor();
      return true;
    }
    else return false;
  }
  
  /* ===== RECORD FUNCTIONS ===== */
  
  /* TheMatrix::getNextRecord($table)
   * @param $table: name of table (e.g. 'foo')
   */
  public function getNextRecord($table) {
    $this->debugLog($table.':returned:'.$this->schema[$table]['id']);
    return $this->schema[$table]['id'];
  }
  
  /* TheMatrix::manipulateData($table, $query)
   * @param $table: name of table (e.g. 'foo')
   * @param $query: array of data to be manipulated prior to record creation/updating
   */
  public function manipulateData($table, $query, $type='create') {
    if ($this->tableExists($table)) {
      // removes 'post-' prefix
      $query = $this->stripPost($query); 
      
      // load fields schema
      $fields = $this->schema[$table]['fields'];
      
      // remove unnecessary fields
      foreach ($query as $field => $value) {
        if (!array_key_exists($field, $fields)) unset($query[$field]);
      }
      
      foreach ($query as $field=>$value) {
        if ($field!='id') {
          if ($fields[$field]['type']=='checkbox') {
            $options = explode("\n", $fields[$field]['options']);
            $end = '';
            if (is_array($value)) {
              foreach ($value as $key => $selected) {
                if (array_key_exists($key, $options)) $end .= $options[$key]."\n";
              }
            }
            $query[$field] = $end;
          }
          // password (sha1 encoding)
          if ($fields[$field]['type']=='password') {
            $removeSalt = trim(str_replace($this->salt, '', $value));
            // only encode it if it hasn't already been sha1ed
            if (!(ctype_xdigit($removeSalt) && strlen($removeSalt) == 40)) {
              $query[$field] = $this->salt.sha1($value);
            }
          }
          // dropdown (for trimming)
          if ($fields[$field]['type']=='dropdowncustom') {
            $query[$field] = trim($value);
          }
          // slug
          if ($fields[$field]['type']=='slug') {
            $query[$field] = $this->str2slug($value);
          }
          // datetimelocal
          if ($fields[$field]['type']=='datetimelocal' && !is_numeric($value)) {
            $query[$field] = strtotime($value);
          }
          // imageuploadadmin
          if ($fields[$field]['type']=='imageuploadadmin') {
            // get settings
            $op = explode("\n", $fields[$field]['options']);
            $op = array_map('trim', $op);
            
            // defaults
            if (!isset($op[0])) $op[0] = 5*10*10*10*1024; // default max size set to 5mb
            if (!isset($op[1])) $op[1] = 900; // max width
            if (!isset($op[2])) $op[2] = 900; // max height
            if (!isset($op[3])) $op[3] = 100; // thumb width
            if (!isset($op[4])) $op[4] = 100; // thumb height
            if (!isset($op[5])) $op[5] = 'auto'; // thumb resize method
            
            if ($op[1]==0 && $op[2]==0) {
              $resize = false;
            }
            else {
              $resize = array('width' => $op[1], 'height' => $op[2]);
            }
            if (!empty($_FILES)) {
              $upload = new SimpleImageUpload('post-'.$field);
              $success = $upload->upload($maxSize=(int)$op[0], $path=GSDATAUPLOADPATH.$fields[$field]['path'], $thumb=GSTHUMBNAILPATH.$fields[$field]['path'], $filename=false, $enableThumbnail=array('maxwidth'=>$op[3], 'maxheight'=>$op[4], 'method'=>$op[5]), $resize);
              if ($success['status']==true) {
                $query[$field] = $success['file'];
              }
              else {
                $query[$field] = '';
              }
            }
          }
          // textmulti
          if (($fields[$field]['type']=='textmulti' || $fields[$field]['type']=='intmulti') && is_array($value)) {
            $value = array_map('trim', $value);
            $query[$field] = implode("\n", ($value));
          }
          // corrects cdata tags
          if (isset($this->fieldTypes[$fields[$field]['type']]['cdata'])) {
            $query[$field] = array('@cdata'=>$query[$field]);
          }
        }
      }
      
      // fill in unspecified fields
      foreach ($fields as $field=>$properties) {
        if (!isset($query[$field])) {
          // sets default
          if (isset($properties['default'])) {
            $defaultCheck = explode(':', $properties['default']);
            if ($properties['default']=='time') {
              $query[$field] = time();
            }
            else $query[$field] = $properties['default'];
          }
          else $query[$field] = '';
        }
      }

      // ensures id is first
      if (isset($query['id'])) {
        $id = $query['id'];
        unset($query['id']);
        $query = array_merge(array('id'=>$id), $query);
      }
      
      // final return
      return $query;
    }
    else return false;
  }
  
  /* TheMatrix::validateData($table, $query)
   * @param $table: name of table (e.g. 'foo')
   * @param $query: array of data to be validated
   */
  public function validateData($table, $query) {
    if ($this->tableExists($table)) {
      $fields = $this->schema[$table]['fields'];
      foreach ($fields as $field => $properties) {
        if (isset($this->fieldTypes[$properties['type']]['validate'])) {
          
          $validation = $this->fieldTypes[$properties['type']]['validate'];
          echo $validation;
          $query[$field] = filter_var($query[$field], $validation);
        }
      }
    }
    else return false;
  }
  
  /* TheMatrix::stripPost($query)
   * @param $query: array of data to have the 'post-' key prefix stripped
   */
  public function stripPost($query) {
    if (is_array($query)) {
      // gets rid of the 'submit' button
      if (isset($query['post-submitform'])) unset($query['post-submitform']);
      foreach ($query as $field=>$value) {
        // fixes query
        if (substr($field, 0, 5)=='post-') {
          $query[substr($field, 5)] = $value;
          unset($query[$field]);
        }
      }
      return $query;
    }
    else return false;
  }
   
  /* TheMatrix::createRecord($table, $query)
   * @param $table: table name (e.g. 'foo')
   * @param $query: array of data to be inserted (array keys correspond to field names)
   */
  public function createRecord($table, $query) {
    if ($this->tableExists($table)) {
      $maxRecords = (int)$this->schema[$table]['maxrecords'];
      $totalRecords = count(glob($this->directories['data']['core']['dir'].'/'.$table.'/*.xml'));
      
      // ensures max record count is not exceeded
      if ($maxRecords==0 || $totalRecords<$maxRecords) {
        // ensures id is first in the query and strips 'post-' prefix from the query
        $id = $this->getNextRecord($table);
        $query = array_merge(array('id'=>$id), $query); 
        
        // prepare xml file and debugging log
        $this->debugLog('record:'.$id);
        $file = $this->directories['data']['core']['dir'].$table.'/'.$id.'.xml';
        
        // format the query appropriately and create the xml file
        $query = $this->manipulateData($table, $query);
        #var_dump($this->validateData($table, $query));
        $query = array('item' => $query);
        $xml = Array2XML::createXML('channel', $query);
        
        // increase the id count and save the schema
        $xml->save($file);
        $this->debugLog('file:'.$file);
        $this->schema[$table]['id']= $id+1;
        $this->saveSchema();
        return true;
      }
      else return false;
    }
    else {
      $this->debugLog('Table does not exist: '.$table);
      return false;
    }
  }
  
  /* TheMatrix::updateRecord($table, $id, $query, $overwrite)
   * @param $table:     table name (e.g. 'foo')
   * @param $id:        record id
   * @param $query:     array of data to be inserted (array keys correspond to field names)
   * @param $overwrite: overwrite mode - any missing keys from $query are overwritten if set to true
   */
  public function updateRecord($table, $id, $query, $overwrite=false) {
    $file = $this->directories['data']['core']['dir'].'/'.$table.'/'.$id.'.xml';
    if ($this->tableExists($table) && file_exists($file)) {
      $this->debugLog('updating record:'.$table.'/'.$id);
      
      // pull the original data and manipulate the query data
      $oldXML = file_get_contents($file);
      $array = XML2Array::createArray($oldXML); 
      $query['id'] = $id;
      $old = $array['channel']['item'];
      
      // to overwrite or not to overwrite
      if ($overwrite==false) {
        $newarray = $this->recordExists($table, $id);
        $newarray = array_merge($newarray, $query);
        $newarray = $this->manipulateData($table, $newarray);
        $array['channel']['item'] = $newarray;
      }
      else {
        $query = $this->manipulateData($table, $query);
        $array['channel']['item'] = $query;
      }
      
      // save to xml and stick the xml data into the session
      $xml = Array2XML::createXML('channel', $array['channel']);
      $xml->save($file);
      $_SESSION['matrix'][$table]['records'][$id] = $oldXML;
      
      // return
      return array(
        'status' => true,
        'old'   => $old,
        'new' => $array['channel']['item'],
      );
    }
    else return array(
        'status' => false,
        'old'   => $query,
        'new' => false,
      );
  }
  
  /* TheMatrix::undoRecord($table, $id)
   * @param $table: table name (e.g. 'foo')
   * @param $id:    record id
   */
  public function undoRecord($table, $id) {
    if (isset($_SESSION['matrix'][$table]['records'][$id])) {
      $file = $this->directories['data']['core']['dir'].'/'.$table.'/'.$id.'.xml';
      file_put_contents($file, $_SESSION['matrix'][$table]['records'][$id]);
      if (file_exists($file)) {
        unset($_SESSION['matrix'][$table]['records'][$id]);
        return true;
      }
      else return false;
    }
    else return false;
  }
  
  /* TheMatrix::deleteRecord($table, $id)
   * @param $table: table name (e.g. 'foo')
   * @param $id:    record id
   */
  public function deleteRecord($table, $id) {
    $file = $this->directories['data']['core']['dir'].'/'.$table.'/'.$id.'.xml';
    if (file_exists($file)) {
      $_SESSION['matrix'][$table]['records'][$id] = file_get_contents($file);
      unlink($file);
      return true;
    }
    else return false;
  }
   
  /* TheMatrix::getNumRecords($table)
   * @param $table:     table name (e.g. 'foo')
   */
  public function getNumRecords($table) {
    if ($this->tableExists($table)) {
      return (count(glob($this->directories['data']['core']['dir'].$table.'/*.xml')));
    }
    else return false;
  }
  
  /* TheMatrix::query($query, $type, $cache)
   * @param $query: SQL-like query (e.g. 'SELECT * FROM foo ORDER BY bar ASC')
   * @param $type:  'SINGLE' (one record), 'MULTI' (all records), 'COUNT' (number of records), or a number (number of records to show).
   * @param $cache: true to cache the result, false to not
   */
  public function query($query, $type='MULTI', $cache=true, $idKey=false) {
    $type = strtoupper($type);
    $this->sql->createFromGlobals(false);
    $tables = $this->sql->get_tablenames($query);
    $this->debugLog('Query:'.$query);
    foreach ($tables as $table) {
      if(!isset($this->tablesCache[$table]) or $cache==false) $this->tablesCache[$table] = $this->getSchemaTable($table);
      
      // fixes sorting order for 'int' fields by adding leading zeros
      $fieldSchema = $this->getSchema($table, true);
      foreach ($this->tablesCache[$table] as $key=>$record) {
        foreach ($record as $field=>$value) {
          // fix missing fields (fill with defaults)
          foreach ($fieldSchema as $fieldname) {
            if (!isset($record[$fieldname['name']])) $this->tablesCache[$table][$key][$fieldname['name']] = $fieldname['default'];
          }
          // fix field padding for int
          if (isset($fieldSchema[$field]['type']) && $fieldSchema[$field]['type']=='int') {
            $this->tablesCache[$table][$key][$field] = str_pad($value, 8, 0, STR_PAD_LEFT);
          }
        }
      }
      $this->sql->asset($table, $this->tablesCache[$table]);
    }
    $results = $this->sql->query($query);
    
    // removes leading zeroes on formatted array
    $newresults = array();
    foreach ($results as $key => $record) {
      foreach ($record as $field=>$value) {
        if (is_numeric($value) || is_numeric(ltrim($value, '0'))) {
          if (ltrim($value, '0') == '') $results[$key][$field] = 0;
          else $results[$key][$field] = (int)ltrim($value, '0');
        }
      }
      
      // fix $record variable
      $record = $results[$key];
      
      // change key
      if ($idKey && isset($record[$idKey])) {
        $newresults[$record[$idKey]] = $record;
      }
    }
    
    // fix results array with newly ordered results
    if ($idKey && !empty($newresults)) $results = $newresults;
    
    // fix cache
    if ($cache == false) unset($this->tablesCache[$table]);
    
    // single
    if ($type=='SINGLE' || $type=='DM_SINGLE') {
      if (count($results)){
        return $results[0];
      }
      else {
        return $results;
      }
    }
    // count
    elseif ($type=='COUNT' || $type=='DM_COUNT') return count($results);
    // set number of results
    elseif (is_numeric($type)) {
      return array_slice($results, 0, $type);
    }
    // multi
    else {
      return $results;
    }
  }
  
  // format tags for i18n
  public function formatTags($tags=array()) {
    if (is_array($tags)) {
      foreach ($tags as $key => $tag) {
        $tag = strtolower($tag);
        $tags[$key] = preg_replace('/[\W]+/', '_', $tag);
      }
      return $tags;
    }
    else return false;
  }
  
  // formats query output according to field type
  public function formatQuery($query, $table, $tags=array('url'=>'', 'separator'=>', ')) {
    foreach ($query as $key=>$record) {
      // get fields
      $fields = $this->getSchema($table, true);
      
      // format record according to field
      foreach ($fields as $field=>$properties) {
        $parser = new TheMatrixParser;
        if (isset($record[$properties['name']])) {
          // text
          // textlong
          // email
          if ($properties['type'] == 'email') {
            $query[$key][$properties['name']] = '<a href="mailto:'.$query[$key][$properties['name']].'" class="email">'.$query[$key][$properties['name']].'</a>';
          }
          // tags
          if ($properties['type']=='tags') {
            $query[$key][$properties['name']] = $parser->getTags($query[$key][$properties['name']], $tags['separator'], $tags['url']);
          }
          // datetimelocal
          if ($properties['type']=='datetimelocal' && is_numeric($query[$key][$properties['name']])) $query[$key][$properties['name']] = date('r', $query[$key][$properties['name']]);
          // codeeditor
          // bbcodeeditor
          if ($properties['type']=='bbcodeeditor') {
            $query[$key][$properties['name']] = $parser->bbcode($query[$key][$properties['name']]);
          }
          // wiki
          if ($properties['type']=='wikieditor') {
            $query[$key][$properties['name']] = $parser->wiki($query[$key][$properties['name']]);
          }
          // markdown
          if ($properties['type']=='markdowneditor') {
            $query[$key][$properties['name']] = $parser->markdown($query[$key][$properties['name']]);
          }
          // wysiwyg
          // checkbox
          // dropdown
          // file
          // image
        }
        else $query[$key][$properties['name']] = ''; // stops unidentified index errors from coming up
      }
    }
    return $query;
  }
  
   
  /* TheMatrix::initialiseCKEditor()
   */
  public function initialiseCKEditor() {
    echo '<script src="'.$this->globals['siteurl'].'admin/template/js/ckeditor/ckeditor.js?v=0.2.0"></script>';
    $dateformat=i18n('DATE_FORMAT',false);
    $dateformat = str_replace('Y', 'yy', $dateformat);
    $dateformat = str_replace('j', 'd', $dateformat);
  
  if (defined('GSEDITORHEIGHT')) { $EDHEIGHT = GSEDITORHEIGHT .'px'; } else {  $EDHEIGHT = '500px'; }
    if (defined('GSEDITORLANG')) { $EDLANG = GSEDITORLANG; } else {  $EDLANG = i18n_r('CKEDITOR_LANG'); }
    if (defined('GSEDITORTOOL')) { $EDTOOL = GSEDITORTOOL; } else {  $EDTOOL = 'basic'; }
    if (defined('GSEDITOROPTIONS') && trim(GSEDITOROPTIONS)!="") { $EDOPTIONS = ", ".GSEDITOROPTIONS; } else {  $EDOPTIONS = ''; }
      
    if ($EDTOOL == 'advanced') {
      $toolbar = "
          ['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Table', 'TextColor', 'BGColor', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source'],
       '/',
       ['Styles','Format','Font','FontSize']
     ";
      } elseif ($EDTOOL == 'basic') {
      $toolbar = "['Bold', 'Italic', 'Underline', 'NumberedList', 'BulletedList', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 'Link', 'Unlink', 'Image', 'RemoveFormat', 'Source']";
    } else {
      $toolbar = GSEDITORTOOL;
    }
    ?>
    
      <script type="text/javascript">
      
      CKEDITOR.replaceAll(function(textarea,config){
        
        // converts all textareas with class of 'DMckeditor' to ckeditor instances.
        if (textarea.className.search("DMckeditor")) return false; //for only assign a class
        jQuery.extend(config,
        {
          forcePasteAsPlainText : true,
          language : '<?php echo $EDLANG; ?>',
          defaultLanguage : 'en',
          <?php if (file_exists(GSTHEMESPATH .$this->globals['template']."/editor.css")) { 
            $fullpath = suggest_site_path();
          ?>
          contentsCss : '<?php echo $fullpath; ?>theme/<?php echo $this->globals['template']; ?>/editor.css',
          <?php } ?>
          entities : false,
          uiColor : '#FFFFFF',
          height : '<?php echo $EDHEIGHT; ?>',
          baseHref : '<?php echo $this->globals['siteurl']; ?>',
          toolbar : 
          [
          <?php echo $toolbar; ?>
          ]
          <?php echo $EDOPTIONS; ?>,
          tabSpaces : 10,
          filebrowserBrowseUrl : 'filebrowser.php?type=all',
          filebrowserImageBrowseUrl : 'filebrowser.php?type=images',
          filebrowserWindowWidth : '730',
          filebrowserWindowHeight : '500',
          skin : 'getsimple'
        });        
      });
      
      $('.datepicker').each(function(){
          $(this).datepicker({ dateFormat: '<?php echo $dateformat; ?>' });
      });
      
      $('.datetimepicker').each(function(){
        $(this).datetimepicker({ 
          dateFormat: '<?php echo $dateformat; ?>',
          timeFormat: 'hh:mm'
        })
      })
      </script>
  <?php  
      return true;
  }
  
  // just fixing the method name
  public function initializeCKEditor() {
    return initialiseCKEditor();
  }
    
  // initialises codemirror (gets CSS and JS)
  public function initialiseCodeMirror() {
    $codemirrorCSS = file_get_contents(GSADMINPATH.'template/js/codemirror/lib/codemirror.css');
    $codemirrorTheme = file_get_contents(GSADMINPATH.'template/js/codemirror/theme/default.css');
    echo '<style>'.$codemirrorCSS."\n\n".$codemirrorTheme.'</style>';
    echo '<script src="'.$this->globals['siteurl'].'admin/template/js/codemirror/lib/codemirror-compressed.js?v=0.2.0"></script>';
    return true;
  }
  public function initializeCodeMirror() {
    return initialiseCodeMirror();
  }
    
  // instantiates codemirror onto a textarea
  public function instantiateCodeMirror($name) {
  ?>
    <script type="text/javascript">
    jQuery(document).ready(function() { 
        var foldFunc = CodeMirror.newFoldFunction(CodeMirror.braceRangeFinder);
        function keyEvent(cm, e) {
        if (e.keyCode == 81 && e.ctrlKey) {
          if (e.type == "keydown") {
          e.stop();
          setTimeout(function() {foldFunc(cm, cm.getCursor().line);}, 50);
          }
          return true;
        }
        }
        function toggleFullscreenEditing()
        {
          var editorDiv = $('.CodeMirror-scroll');
          if (!editorDiv.hasClass('fullscreen')) {
            toggleFullscreenEditing.beforeFullscreen = { height: editorDiv.height(), width: editorDiv.width() }
            editorDiv.addClass('fullscreen');
            editorDiv.height('100%');
            editorDiv.width('100%');
            editor.refresh();
          }
          else {
            editorDiv.removeClass('fullscreen');
            editorDiv.height(toggleFullscreenEditing.beforeFullscreen.height);
            editorDiv.width(toggleFullscreenEditing.beforeFullscreen.width);
            editor.refresh();
          }
        }
        var editor = CodeMirror.fromTextArea(document.getElementById("post-<?php echo $name; ?>"), {
        lineNumbers: true,
        matchBrackets: true,
        indentUnit: 4,
        indentWithTabs: true,
        enterMode: "keep",
        tabMode: "shift",
        theme:'default',
        mode: "text/html",
        onGutterClick: foldFunc,
        extraKeys: {"Ctrl-Q": function(cm){foldFunc(cm, cm.getCursor().line);},
              "F11": toggleFullscreenEditing, "Esc": toggleFullscreenEditing},
        onCursorActivity: function() {
          editor.setLineClass(hlLine, null);
          hlLine = editor.setLineClass(editor.getCursor().line, "activeline");
        }
        });
        var hlLine = editor.setLineClass(0, "activeline");
        
      })
         
    </script>
  <?php
  }
    
  public function initialiseTagsInput($id='.DM_tags') { ?>
    <script type="text/javascript">
      $(document).ready(function() {
        $("<?php echo $id?>").tagsInput();
      });
    </script>
  <?php
  }
  
  public function initializeTagsInput($id='.DM_tags') {
    initialiseTagsInput($id);
  }

  /* TheMatrix::doRoute($key)
   * @param $key: key in the uri to look for (e.g. for uri 'blog/slug', 0 refers to 'blog'
   */
  public function doRoute($key=0) {
    global $file, $id, $uri;
    $uriRoutes= $this->query("SELECT * FROM _routes");
    $uri = trim(str_replace('index.php', '', $_SERVER['REQUEST_URI']), '/#');
    #echo strstr($uri, '?id=');
    #echo $uri;
    $parts = explode('/',$uri);
    foreach ($uriRoutes as $routes) {
      if ($parts[$key]==$routes['route']) {
        $file=GSDATAPAGESPATH . str_replace('.php','.xml',$routes['rewrite']);
        $id = pathinfo($routes['rewrite'],PATHINFO_FILENAME);
      }
    }
  }
   
  public function getAdminHeader($header, $nav=array()) {
    if (!empty($nav)) {
      // header
      echo '<h3 class="floated">'.$header.'</h3>';
      
      // navigation
      $nav = array_reverse($nav); // ensures array is in the right order
      $navigation = '<div class="edit-nav">';
      foreach ($nav as $label=>$properties) {
        $navigation .= '<a href="'.$this->globals['siteurl'].'admin/load.php?id='.$properties['link'].'" class="'.$properties['key'];
        if (isset($_GET[$properties['key']]) || $_GET['id']==$properties['key']) $navigation .= ' current '; // applies 'current' class to active page;
        $navigation .= '">'.$label.'</a>';
      }
      echo $navigation.'<div class="clear"></div>'."\n".'</div>';
    }
    // only header
    else echo '<h3>'.$header.'</h3>';
  }
  
  // admin success/error message(s) (taken from the GetSimple wiki)
  public function getAdminError($msg, $isSuccess=true, $canUndo=false, $url=false) {
    if (isset($msg)) {
      if ($canUndo) $msg .= ' <a href="'.$url.'">' . i18n_r('UNDO') . '</a>' 
  ?>
  <script type="text/javascript">
    $(function() {
      $('div.bodycontent').before('<div class="<?php echo $isSuccess ? 'updated' : 'error'; ?>" style="display:block;">'+
              <?php echo json_encode($msg); ?>+'</div>');
      $(".updated, .error").fadeOut(500).fadeIn(500);
    });
  </script>
  <?php 
    } 
  }

  // admin panel
  public function admin() {
    $url = 'load.php?id='.self::FILE;
    $dir = $this->directories['plugin']['admin']['dir'];
    end($_GET);
    if (isset($_GET['tables'])) {
      include($dir.'/tables.php');
    }
    elseif (isset($_GET['table']) && $this->tableExists($_GET['table'])) {
      if (key($_GET)=='view')     include($dir.'table.php');
      if (key($_GET)=='fields')   include($dir.'fields.php');
      if (key($_GET)=='form')     include($dir.'form.php');
      if (key($_GET)=='backup')   include($dir.'backup.php');
      if (key($_GET)=='auto')     include($dir.'auto.php');
      if (key($_GET)=='add')      include($dir.'add.php');
      if (key($_GET)=='edit')     include($dir.'edit.php');
    } 
    elseif (isset($_GET['add'])) {
      include($dir.'add.php');
    }
    elseif (isset($_GET['edit'])) {
      include($dir.'edit.php');
    } 
    elseif (isset($_GET['about'])) {
      include($dir.'about.php');
    }
    else {
      include($dir.'/tables.php');
    }
  }

  // start the session
  public function sessionStart() {
    if(session_id() == '') session_start();
  }
  
  // refresh search index
  public function refreshIndex() {
    if (function_exists('delete_i18n_search_index')) {
      delete_i18n_search_index();
      return true;
    }
    else return false;
  }
}
 
?>