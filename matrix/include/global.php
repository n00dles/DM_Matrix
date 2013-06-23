<?php
  /* Global scope versions of public TheMatrix methods for backwards compatibility with prior versions
   *
   */

  $TheMatrixPlugin = new TheMatrix;
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
