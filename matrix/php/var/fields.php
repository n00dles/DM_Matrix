<?php
  /* this is the main fields array
   * it contains all of the field types, properties, etc...
   * adding to this array automatically adds to the fields (e.g. adding a new field type in this array
     will make it an available option anywhere in The Matrix.
   */
   
  // types
  $fields['type'] = array(
    'input'    => array('masks' => array('text', 'number', 'password', 'range', 'email', 'url', 'long', 'slug', 'color')),
    'date'     => array('masks' => array('time', 'timelocal', 'week', 'month')),
    'multi'    => array('masks' => array('text', 'number', 'color', 'textarea', 'rte', 'code')),
    'dropdown' => array('masks' => array('custom', 'table', 'users', 'themes', 'template')),
    'options'  => array('masks' => array('checkbox', 'radio', 'selectmulti')),
    'textarea' => array('masks' => array('plain', 'tags', 'wysiwyg', 'code', 'bbcode', 'wiki', 'markdown')),
    'upload'   => array('masks' => array('image', 'imageadmin')),
    'picker'   => array('masks' => array('image', 'file')),
  );
  
  // properties
  $fields['properties'] = array(
    'type'            => array(
      'default' => 'input',
      'key'     => 'type',
    ),
    'mask'            => array(
      'default' => '',
      'key'     => 'mask',
    ),
    'desc'            => array(
      'default' => '',
      'key'     => 'desc',
    ),
    'placeholder'            => array(
      'default' => '',
      'key'     => 'placeholder',
    ),
    'label'           => array(
      'default' => '',
      'key'     => 'label',
    ),
    'labels'           => array(
      'default' => '',
      'key'     => 'labels',
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
    'salt'      => array(
      'default' => '',
      'key'     => 'salt',
    ),
    'size'       => array(
      'default' => 100,
      'key'     => 'size',
    ),
    'width'       => array(
      'default' => '',
      'key'     => 'width',
    ),
    'height'       => array(
      'default' => '',
      'key'     => 'height',
    ),
    'style'       => array(
      'default' => '',
      'key'     => 'style',
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
    'min'      => array(
      'default' => '',
      'key'     => 'min',
    ),
    'max'      => array(
      'default' => '',
      'key'     => 'max',
    ),
    'step'      => array(
      'default' => '',
      'key'     => 'step',
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
  
  // properties
  $fields['compatibility'] = array(
    'types' => array(
      'text'              =>  array('input', 'text'),
      'password'          =>  array('input', 'password'),
      'int'               =>  array('input', 'number'),
      'range'             =>  array('input', 'range'),
      'email'             =>  array('input', 'email'),
      'url'               =>  array('input', 'url'),
      'textlong'          =>  array('input', 'long'),
      'textarea'          =>  array('textarea', 'plain'),
      'textmulti'         =>  array('multi', 'text'),
      'intmulti'          =>  array('multi', 'number'),
      'tags'              =>  array('textarea', 'tags'),
      'slug'              =>  array('input', 'slug'),
      'pages'             =>  array('dropdown', 'pages'),
      'users'             =>  array('dropdown', 'users'),
      'template'          =>  array('dropdown', 'template'),
      'themes'            =>  array('dropdown', 'themes'),
      'components'        =>  array('dropdown', 'components'),
      'datetimelocal'     =>  array('date', 'timelocal'),
      'dropdown'          =>  array('dropdown', 'table'),
      'dropdowncustom'    =>  array('dropdown', 'custom'),
      'dropdowncustomkey' =>  array('dropdown', 'custom'),
      'dropdownhierarchy' =>  array('dropdown', 'hierarchy'),
      'checkbox'          =>  array('options', 'checkbox'),
      'radio'             =>  array('options', 'radio'),
      'bbcodeeditor'      =>  array('textarea', 'bbcode'),
      'wikieditor'        =>  array('textarea', 'wiki'),
      'markdowneditor'    =>  array('textarea', 'markdown'),
      'wysiwyg'           =>  array('textarea', 'wysiwyg'),
      'codeeditor'        =>  array('textarea', 'code'),
      'imagepicker'       =>  array('picker', 'image'),
      'imageuploadadmin'  =>  array('upload', 'imageadmin'),
      'filepicker'        =>  array('picker', 'file'),
    ),
  );
?>