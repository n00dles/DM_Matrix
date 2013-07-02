<?php
/*
 * TheMatrix, a plugin for GetSimple CMS 3.1
 * 
 * version 0.1
 *  
 * Copyright (c) 2012 Mike Swan mike@digimute.com
 *
 * Contributions have been made by:
 * Shawn A (github.com/tablatronix)
 * Angryboy (lokida.co.uk)
 */

# global constants
  define('MATRIX',          'matrix');
  define('MATRIXPATH',       GSPLUGINPATH.MATRIX.'/');
  define('MATRIXDATAPATH',   GSDATAOTHERPATH.MATRIX.'/');
  
# requires
  require_once(MATRIXPATH.'include/sql4array.class.php');
  require_once(MATRIXPATH.'include/array2xml.class.php');
  require_once(MATRIXPATH.'include/xml2array.class.php');
  require_once(MATRIXPATH.'include/parser.class.php');
  require_once(MATRIXPATH.'include/options.class.php');
  require_once(MATRIXPATH.'include/imageresize.class.php');
  require_once(MATRIXPATH.'include/imageupload.class.php');
  require_once(MATRIXPATH.'include/matrix.class.php');
  require_once(MATRIXPATH.'include/search.class.php');
  require_once(MATRIXPATH.'include/extended.class.php');
  require_once(MATRIXPATH.'include/global.php');
  
# global variables
  $thisfile = basename(__FILE__, ".php");
  $matrix = new TheMatrix;

# register plugin
  register_plugin(
    $thisfile,
    'The Matrix',
    '1.0',
    'Mike Swan',
    'http://digimute.com/',
    'The Matrix',
     MATRIX,
     array($matrix, 'adminBody')
  ); 

# actions & filters
  # front-end
    queue_script('jquery', GSFRONT);
    add_action('error-404', array($matrix, 'doRoute'), array(0));
  
  # back-end
    # lang
    i18n_merge(MATRIX) || i18n_merge(MATRIX, 'en_US');
    
    # tab
    add_action('nav-tab', 'createNavTab', array(MATRIX, MATRIX, 'The Matrix', 'tables'));
    
    # sidebar
    add_action(MATRIX.'-sidebar','createSideMenu', array(MATRIX, "View All Tables", 'tables')); 
    if (isset($_GET['table']) && !empty($_GET['table'])) {
      add_action(MATRIX.'-sidebar','createSideMenu', array(MATRIX, "Edit Table", 'table'));

    }
    add_action(MATRIX.'-sidebar','createSideMenu', array(MATRIX, "About",'about&credit')); 

  # both
    # javascript
    add_action('theme-header', array($matrix, 'themeHeader'), array(MATRIX));
    add_action('header',       array($matrix, 'themeHeader'), array(MATRIX));
    # session (required for undo queries)
    add_action('index-pretemplate', array($matrix, 'sessionStart'), array(MATRIX));
    add_action('admin-pre-header',  array($matrix, 'sessionStart'), array(MATRIX));
?>