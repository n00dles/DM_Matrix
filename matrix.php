<?php
/*
 * The Matrix, a plugin for GetSimple CMS 3.1
 * Copyright (C) 2012-2013 Mike Swan mike@digimute.com
 *
 * Contributions have been made by:
 * Shawn A (github.com/tablatronix)
 * Angryboy (lokida.co.uk)
 */

# thisfile
  $thisfile = basename(__FILE__, ".php");
 
# language
  i18n_merge($thisfile) || i18n_merge($thisfile, 'en_US'); 
 
# requires
  require_once(GSPLUGINPATH.$thisfile.'/php/classes/sql4array.php');
  require_once(GSPLUGINPATH.$thisfile.'/php/classes/array2xml.php');
  require_once(GSPLUGINPATH.$thisfile.'/php/classes/xml2array.php');
  require_once(GSPLUGINPATH.$thisfile.'/php/classes/parser.php');
  require_once(GSPLUGINPATH.$thisfile.'/php/classes/options.php');
  require_once(GSPLUGINPATH.$thisfile.'/php/classes/imageresize.php');
  require_once(GSPLUGINPATH.$thisfile.'/php/classes/imageupload.php');
  require_once(GSPLUGINPATH.$thisfile.'/php/classes/matrix.php');
  require_once(GSPLUGINPATH.$thisfile.'/php/classes/search.php');
  require_once(GSPLUGINPATH.$thisfile.'/php/classes/extended.php');
  
# initialize matrix
  $matrix = new TheMatrix;

# register plugin
  register_plugin(
    $matrix->pluginInfo('id'),           // id
    $matrix->pluginInfo('name'),         // name
    $matrix->pluginInfo('version'),      // version
    $matrix->pluginInfo('author'),       // author
    $matrix->pluginInfo('url'),          // url
    $matrix->pluginInfo('description'),  // description
    $matrix->pluginInfo('page'),         // page type - on which admin tab to display
    array($matrix, 'admin')              // administration function
  );

# actions & filters
  # front-end
    queue_script('jquery', GSFRONT);
    add_action('error-404', array($matrix, 'doRoute'), array(0));
  
  # back-end 
    # tab
    add_action('nav-tab', 'createNavTab', array($thisfile, $thisfile, i18n_r($thisfile.'/PLUGIN_TITLE'), 'tables'));
    
    # sidebar
    add_action($thisfile.'-sidebar','createSideMenu', array($thisfile, i18n_r($thisfile.'/TABLES'), 'tables')); 
    if (isset($_GET['table']) && !empty($_GET['table'])) {
      add_action($thisfile.'-sidebar','createSideMenu', array($thisfile, strtoupper($_GET['table']), 'table'));
    }
    add_action($thisfile.'-sidebar','createSideMenu', array($thisfile, i18n_r($thisfile.'/ABOUT'),'about')); 

  # both
    # javascript
    add_action('theme-header', array($matrix, 'themeHeader'), array($thisfile));
    add_action('header',       array($matrix, 'themeHeader'), array($thisfile));
    # session (required for undo queries)
    add_action('index-pretemplate', array($matrix, 'sessionStart'), array($thisfile));
    add_action('admin-pre-header',  array($matrix, 'sessionStart'), array($thisfile));

# functions
  $TheMatrixPlugin = $matrix;
  $TheMatrixPluginExtended = new TheMatrixExtended;

  // logging
  function DMdebuglog($log) {
    global $TheMatrixPlugin;
  }

  // tables
  function tableExists($table) {
    global $TheMatrixPlugin;
    return $TheMatrixPlugin->tableExists($table);
  }
  function createSchemaTable($name, $maxrecords=0, $fields=array()){
    global $TheMatrixPlugin;
    return $TheMatrixPlugin->createTable($name, $fields, $maxrecords);
  }
  function getSchemaTable($name, $query='') {
    global $TheMatrixPlugin;
    return $TheMatrixPlugin->getSchemaTable($name, $query);
  }
  function DM_deleteTable($table) {
    global $TheMatrixPlugin;
    return $TheMatrixPlugin->deleteTable($table);
  }

  // fields
  function addSchemaField($table, $field) {
    global $TheMatrixPlugin;
    return $TheMatrixPlugin->createField($table, $field);
  }
  function deleteSchemaField($table, $field) {
    global $TheMatrixPlugin;
    return $TheMatrixPlugin->deleteField($table, $field);
  }

  // records
  function createRecord($table, $query=array()) {
    global $TheMatrixPlugin;
    return $TheMatrixPlugin->createRecord($table, $query);
  }
  function updateRecord($table, $id, $query, $overwrite=false) {
    global $TheMatrixPlugin;
    return $TheMatrixPlugin->updateRecord($table, $id, $query, $overwrite);
  }
  function DM_deleteRecord($table,$id) {
    global $TheMatrixPlugin;
    return $TheMatrixPlugin->deleteRecord($table, $id);
  }
  function DM_query($query, $type='MULTI', $cache=true) {
    global $TheMatrixPlugin;
    return $TheMatrixPlugin->query($query, $type, $cache);
  }

  // form
  function displayFieldType($table, $field, $value='') {
    global $TheMatrixPlugin;
    return $TheMatrixPlugin->displayField($table, $field, $value);
  }
  function DM_createForm($table) {
    global $TheMatrixPlugin;
    return $TheMatrixPlugin->displayForm($table);
  }
  function DM_editForm($table, $id) {
    global $TheMatrixPlugin;
    return $TheMatrixPlugin->displayForm($table, $id);
  }    
?>