<?php
  /* this is the main fields array
   * it contains all of the field types, properties, etc...
   * adding to this array automatically adds to the fields (e.g. adding a new field type in this array
     will make it an available option anywhere in The Matrix.
   */
   
  // types
  $fields['type'] = array( 
    'text'              =>  array('cdata' => true),
    'password'          =>  array(),
    'int'               =>  array('validate' => FILTER_SANITIZE_NUMBER_INT),
    'email'             =>  array('validate' => FILTER_VALIDATE_EMAIL),
    'url'               =>  array('validate' => FILTER_VALIDATE_URL),
    'textlong'          =>  array(),
    'textarea'          =>  array('cdata' => true),
    'textmulti'         =>  array('cdata' => true),
    'intmulti'          =>  array(),
    'tags'              =>  array(),
    'slug'              =>  array(),
    'pages'             =>  array(),
    'users'             =>  array(),
    'template'          =>  array(),
    'themes'            =>  array(),
    'components'        =>  array(),
    'datetimelocal'     =>  array('manipulate' => 'strtotime'),
    'dropdown'          =>  array(),
    'dropdowncustom'    =>  array(),
    'dropdowncustomkey' =>  array(),
    'dropdownhierarchy' =>  array(),
    'checkbox'          =>  array('manipulate'=>'1'),
    'bbcodeeditor'      =>  array('cdata' => true),
    'wikieditor'        =>  array('cdata' => true),
    'markdowneditor'    =>  array('cdata' => true),
    'wysiwyg'           =>  array('cdata' => true),
    'codeeditor'        =>  array('cdata' => true),
    'imagepicker'       =>  array(),
    'imageuploadadmin'  =>  array(),
    'filepicker'        =>  array(),
  );
  
  // properties
  $fields['properties'] = array(
    'type'            => array(
      'default' => 'text',
      'key'     => 'type',
    ),
    'desc'            => array(
      'default' => '',
      'key'     => 'desc',
    ),
    'label'           => array(
      'default' => '',
      'key'     => 'label',
    ),
    'default'         => array(
      'default' => '',
      'key'     => 'default',
    ),
    'cacheindex'      => array(
      'default' => 1,
      'key'     => 'cacheindex',
    ),
    'tableview'       => array(
      'default' => 1,
      'key'     => 'tableview',
    ),
    'table'           => array(
      'default' => '',
      'key'     => 'table',
    ),
    'row'             => array(
      'default' => 'id',
      'key'     => 'row',
    ),
    'options' => array(
      'default' =>  '',
      'key'     =>  'options',
    ),
    'path'      => array(
      'default' => '',
      'key'     => 'path',
    ),
    'size'       => array(
      'default' => 100,
      'key'     => 'size',
    ),
    'visibility' => array(
      'default' => 1,
      'key'     => 'visibility',
    ),
    'class'      => array(
      'default' => '',
      'key'     => 'class',
    ),
    'required'      => array(
      'default' => '',
      'key'     => 'required',
    ),
    'maxlength'      => array(
      'default' => '',
      'key'     => 'maxlength',
    ),
    'validation'      => array(
      'default' => '',
      'key'     => 'validation',
    ),
    'readonly'      => array(
      'default' => '',
      'key'     => 'readonly',
    ),
    'rows'      => array(
      'default' => 1,
      'key'     => 'rows',
    ),
    'index'      => array(
      'default' => 0,
      'key'     => 'index',
    ),
    'other'      => array(
      'default' => '',
      'key'     => 'other',
    ),
  );
?>