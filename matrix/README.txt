  HOW TO USE THE MATRIX FUNCTIONS

  First declare your variable and instantiate The Matrix.
  
  e.g. $plugin = new TheMatrix;
  
  From here, you can call upon all of the public methods (functions) within TheMatrix class.

  /* 
   * ------------------ TABLE MANIPULATION ------------------ 
   */
 
  // CREATE A TABLE
  TheMatrix::createTable($table, $fields, $maxrecords, $id)
  
  This function allows you to build the main structure that will hold your data: the table. The table is made up of several important components:
  - The 'name' (identifies the table in the overall schema by The Matrix and is called upon for queries (so keep the name simple and avoid special characters)
  - The 'maxrecords':  maximum number of records that the table can hold before stopping new record creation (set to 0 to make it unlimited)
  - The 'id': the unique identifier given to each newly created record - this starts off on 0 but increments with each new record. This setting is here primarily
              for restoring backed up tables to their original state, in turn fixing the id counter to what it was before.
  - The fields:  the various data types that your records will be able to take (e.g. a title may be 'text', a date may be 'datetimelocal', an integer may be 'int', etc...)
      This parameter must be an array. Said array will be of the following structure in order to successfully build all of your fields in the desired order:
      
      $fields = array(
        // each field requires an array of the following structure)
        array(
          'name'       => 'Your_field_name',         // name of your field (MANDATORY - avoid spaces or special characters)
          'type'       => 'yourfieldtype',           // type of field (MANDATORY - choose from the available options (visible in the dropdown))
          'desc'       => 'Your field description',  // description for field -- becomes placeholder for field content (OPTIONAL)
          'label'      => 'Your field label',        // label that appears in forms (OPTIONAL)
          'default'    => 'Default content',         // default content that will fill the field when a record is created (OPTIONAL)
          'cacheindex' => '1',                       // dictates whether the field's value will be cached when performing the query (OPTIONAL. Takes value 0 for no and 1 for yes) 
          'table'      => '_routes',                 // for type 'dropdown' -> the table that the dropdown will pull record values from (MANDATORY iff dropdown is the field type)
          'row'        => 'id',                      // for type 'dropdown' -> the table FIELD that the dropdown will pull record values from (MANDATORY iff dropdown is the field type)
          'options'    => '',                        // for type 'dropdowncustom' -> the values that can be selected from the dropdown menu (separate each option with a line break, e.g. "\n"). (MANDATORY iff 'dropdowncustom' is the field type)
          'path'       => '',                        // for type 'imageupload' -> relative path from root of GS installation to where images will be uploaded (MANDATORY iff 'imageupload' is the field type)
          'size'       => '50'                       // size of the field in The Matrix table view (OPTIONAL)
          'visibility' => '1'                        // visibility of the field in The Matrix table view (1 = visible, 0 = hidden; OPTIONAL)
          'class'      => ''                         // class given to field when a form is built using the buildForm() method (primarily for generating admin panel forms; OPTIONAL)
        ),
        // more fields....
      );
      
    So you can technically create a quick set of basic text fields with the following array:
    
      $fields = array(
        array('name'=>'field1', 'type'=>'text'),
        array('name'=>'field2', 'type'=>'text'),
        array('name'=>'field3', 'type'=>'text'),
      );
      
    Upon table creation, the method returns a boolean true/false dependent on the success of the operation. You can then use this to display success/failure messages.
    
    Overall, use of the method should be as follows:
    
    $fields = array(
      // your fields
    );

    $success = $plugin->createTable(
      'tableName',  // table name
      $fields,      // your fields
      0,            // max records
      0             // initial record ID (by default it is 0)
    );
      
    if ($success) {
      // success message
    }
    else {
      // failure message
    }
    
  // RENAME A TABLE
  TheMatrix::renameTable($table, $name)
  
  Allows you to rename a table. Provide the original name and then the new name. Returns true/false at the end. Will return false if either the table doesn't exist
  or the name you are giving is already taken.
  
    EXAMPLE
  
    $plugin->renameTable('tableName', 'newTableName');
  
  // COPY TABLE
  TheMatrix::copyTable($table, $newTable)
  
  Provides a duplicate of the initial table, suffixed with '_copy' (if $newTable is left blank). You can give it any name that you wish.
  
    EXAMPLE
    $plugin->copyTable('foo');          // creates 'foo_copy'
    $plugin->copyTable('foo', 'bar');   // creates 'bar'
    
  // BACKUP TABLE
  TheMatrix::backupTable($table, $directory, $file)
  
  Backs up the schema of the selected table and dumps its records and schema into a zip file. $directory sets where the zip file will end up (default is 'false', which puts
  it in the 'data/other/matrix' directory) and $file dictates the file name (default is 'false', which sets it to 'tablename_unixtimestamp').
  The schema in the zip is given the name 'tablename_schema.xml'.
  
    EXAMPLE
    $plugin->backupTable('foo');                               // @ Tue, 18 Jun 2013 13:44:13 GMT, 'foo_1371563053.zip' would be created and sent to 'data/other/matrix'
    $plugin->backupTable('foo', 'data/foo/backups');           // @ Tue, 18 Jun 2013 13:44:13 GMT, 'foo_1371563053.zip' would be created and sent to 'data/foo/backups'
    $plugin->backupTable('foo', 'data/foo/backups', 'foo1');   // 'foo1.zip' would be created and sent to 'data/foo/backups'
    
  An essential function for ensuring that your plugin's data is kept accessible should things go south.
  
  // GET TABLE BACKUPS
  TheMatrix::getBackups($table, $directory, $file)
  
  Returns an array of the available backups from the directory specified (default is 'data/other/matrix') and with the file name provided (a '*' can be used as a wildcard
  for searching for file names; the default is just 'tablename_*').
  
    EXAMPLE
    $plugin->getBackups('foo');                           // returns all backups in 'data/other/matrix' for foo with the prefix 'foo_'
    $plugin->getBackups('foo', 'data/foo/backups');       // returns all backups in 'data/foo/backups' with the prefix 'foo_'
    $plugin->getBackups('foo', 'data/foo/backups', '*');  // returns all backups in 'data/foo/backups' (regardless of prefix)
    
  The array returned is sorted by the modification date of the backup and contains the timestamp of the backup's creation, the name of the file and the direct url link
  to the backup file.
  
    EXAMPLE
    array (size=3)
      0 => 
        array (size=3)
          'date' => string 'Tue, 18 Jun 2013 14:23:09 +0000' (length=31)
          'link' => string 'http://yourdomain.com/data/foo/backups/foo_1371565389.zip' (length=64)
          'file' => string 'foo2_1371565389.zip' (length=19)
      1 => 
        array (size=3)
          'date' => string 'Tue, 18 Jun 2013 14:21:59 +0000' (length=31)
          'link' => string 'http://yourdomain.com/data/foo/backups/foo_1371565319.zip' (length=64)
          'file' => string 'foo2_1371565319.zip' (length=19)
      2 => 
        array (size=3)
          'date' => string 'Tue, 18 Jun 2013 14:16:48 +0000' (length=31)
          'link' => string 'http://yourdomain.com/data/foo/backups/foo_1371565008.zip' (length=64)
          'file' => string 'foo2_1371565008.zip' (length=19)
  
  // RESTORE TABLE
  TheMatrix::restoreTable($table, $path)
  
  If a backup file exists, then the contents of the zip will be used to restore the table as it exists in the zip file. If there are any records currently in the table,
  the table will be emptied first. This will also roll back the 'id' value of the table to what the zip file holds. It is indeed a full restoration of the table to how
  it was exactly at the point that the backup was created.
  
  Takes the same parameters as getBackups (as the function is called in order to get the correct backup).
  
    EXAMPLE
    $plugin->getBackups('foo');                           // restores from latest backup in 'data/other/matrix' for foo with the prefix 'foo_'
    $plugin->getBackups('foo', 'data/foo/backups');       // restores from latest backup in 'data/foo/backups' with the prefix 'foo_'
    $plugin->getBackups('foo', 'data/foo/backups', '*');  // restores from latest backup in 'data/foo/backups' (regardless of prefix)
    
  This function/method works even if the table is not present in the current schema.
    
  // EMPTY TABLE
  TheMatrix::emptyTable($table)
  
  Lets you empty the table of its current data without also getting rid of the table. Mainly used if you want to start afresh. Note: this does not reset the 'id' counter
  back to 0, in case you need to restore the table back to its original state and end up overwriting new data.
  
    EXAMPLE
    
    $plugin->emptyTable('foo'); // 'foo' will have no more records.
    
  // DELETE TABLE
  TheMatrix::deleteTable($table)
  
  Empties the table then deletes its contents. Before doing so though, a backup of the current schema is automatically created, so if you accidentally invoke this
  function, there will always be a backup ready for you to roll back to.
  
    EXAMPLE
    
    $plugin->deleteTable('foo');
    
  /* 
   * ------------------ FIELD MANIPULATION ------------------ 
   */
 
  // CREATE A FIELD
  TheMatrix::createField($table, $field)
  
  createField lets you add a field to an existing table. The structure of the $field array is identical with that in the createTable method:
  
  $field = 
    array(
      'name'       => 'Your_field_name',         // name of your field (MANDATORY - avoid spaces or special characters)
      'type'       => 'yourfieldtype',           // type of field (MANDATORY - choose from the available options (visible in the dropdown))
      'desc'       => 'Your field description',  // description for field -- becomes placeholder for field content (OPTIONAL)
      'label'      => 'Your field label',        // label that appears in forms (OPTIONAL)
      'default'    => 'Default content',         // default content that will fill the field when a record is created (OPTIONAL)
      'cacheindex' => '1',                       // dictates whether the field's value will be cached when performing the query (OPTIONAL. Takes value 0 for no and 1 for yes) 
      'table'      => '_routes',                 // for type 'dropdown' -> the table that the dropdown will pull record values from (MANDATORY iff dropdown is the field type)
      'row'        => 'id',                      // for type 'dropdown' -> the table FIELD that the dropdown will pull record values from (MANDATORY iff dropdown is the field type)
      'options'    => '',                        // for type 'dropdowncustom' -> the values that can be selected from the dropdown menu (separate each option with a line break, e.g. "\n"). (MANDATORY iff 'dropdowncustom' is the field type)
      'path'       => '',                        // for type 'imageupload' -> relative path from root of GS installation to where images will be uploaded (MANDATORY iff 'imageupload' is the field type)
      'size'       => '50'                       // size of the field in The Matrix table view (OPTIONAL)
      'visibility' => '1'                        // visibility of the field in The Matrix table view (1 = visible, 0 = hidden; OPTIONAL)
      'class'      => ''                         // class given to field when a form is built using the buildForm() method (primarily for generating admin panel forms; OPTIONAL)
    );
    
  So to use...
  
  $plugin->createField('tableName', $field);
  
  This will append the current fieldset with your new field. To create a number of fields, you can build an array as you would for creating a
  table, then loop through each field:
  
  $fields =
    array(
      array('name'=>'field1', 'type'=>'text'),
      array('name'=>'field2', 'type'=>'text'),
      array('name'=>'field3', 'type'=>'text'),
    );
    
  foreach ($fields as $field) {
    $plugin->createTable('tableName', $field);
  }
  
  A true/false boolean is also returned at the end dependent on the success of the field creation.
  
  // RENAME A FIELD
  TheMatrix::renameField($table, $field, $name)
  
  Renames a field and fixes the field name in all of its corresponding records.
  
    EXAMPLE
    $plugin->renameField('foo', 'field1', 'newfield1');   // all records with the field 'field1' will have the field name switched to 'newfield1'
                                                          // and the schema will be saved
  
  // REMOVE A FIELD
  TheMatrix::deleteField($table, $field)
  
  Removes a field from the schema. NOTE: This does NOT remove the field from the existing records, but it will prevent newly created records from
  having said field.
  
    EXAMPLE
    $plugin->deleteField('foo', 'field1');
    
  // REORDER FIELDS
  TheMatrix::reorderFields($table, $fields)
  
  Reorders existing fields in the order you provide. NOTE: All existing fields (EXCEPT the id) must be included in the array, otherwise the the
  fields will not be reordered.
  
    EXAMPLE
    Table 'foo' has the fields 'field1', 'field2' and 'field3' (in that order).
    $plugin->reorderFields('foo', array('field3', 'field1', 'field2')); // now it is ordered field3, field1, field2
  
  /* 
   * ------------------ RECORD MANIPULATION ------------------ 
   */
   
  // --> FIND RECORDS
  TheMatrix::query($query, $type)
 
  In the spirit of the database structure that The Matrix utilises, SQL-like commands can be used in order to pull a query from a given table. $query
  refers to the query itself that you are performing and $type dictates whether you pull all of the results available ('MULTI'), a single record 
  ('SINGLE'), just the number of records ('NUM') or a fixed number of records (specify the number).
  
    EXAMPLE
    $plugin->query("SELECT * FROM foo WHERE field1 = 'stuff'");           // pulls results from foo with a field1 value of 'stuff'
    $plugin->query("SELECT * FROM foo WHERE field1 = 'stuff'", 'SINGLE'); // pulls first result from foo with a field1 value of 'stuff'
    $plugin->query("SELECT * FROM foo WHERE field1 = 'stuff'", 'NUM');    // pulls number of results from foo with a field1 value of 'stuff'
 
  // --> CREATE A RECORD
  TheMatrix::createRecord($table, $query)
  
  To actually start inputting data into your table, you'll need this method. By specifying the table and providing an appropriate array, you can then
  insert the desired data.
  
    EXAMPLE
    Assuming that we have a table whose fields are 'field1', 'field2' and 'field3'
  
    $query =
      array(
        'field1' => 'value of field 1',
        'field2' => 'value of field 2',
        'field3' => 'value of field 3',
      );
      
    $plugin->createRecord('foo', $query);
  
    This will take the 'id' value of the table and make it the identifier for this new record. It then creates the record, increments the id value by
    1 and saves the schema.
    The keys of the query must correspond to their appropriate fields.
  
  A true/false boolean is also returned at the end dependent on the success of the record creation. Records will not be created if the maximum record
  number is already met.

  // --> UPDATE A RECORD
  TheMatrix::updateRecord($table, $query, $id, $overwrite)
  
  To modify an existing record, call this method. Provide a query with the field values that you'd wish to change and it will be updated. The id of 
  the record in the table must be specified. The overwrite feature is there in the event that you wish to overwrite the rest of the data in the
  record. By default it is set to false (so it will only affect the fields specified in the query).
  
    EXAMPLE
    $query =
      array(
        'field1' => 'NEW value of field 1',
        'field2' => 'NEW value of field 2',
      );
      
    $plugin->updateRecord('foo', $query, 0);    // only the fields 'field1' and 'field2' will be affected on record 0 in table 'foo'
  
  Once the function is called, the $_SESSION variable gets the record stored, so the changes to the record can be undone later on (provided that no 
  further changes have been made, which would simply overwrite the content in the $_SESSION variable.
  Upon success/failure, an array is returned showing the success 'status' (true/false), the 'old' record data and the 'new' record data (false if 
  the update fails).
  
  // --> UNDO UPDATING A RECORD
  TheMatrix::undoRecord($table, $id)
  
  If a record has been modified, the session contains the backup of the previous version of said record in case you need to revert to it. Simply 
  call upon the method above and the data will be reverted.
  
    EXAMPLE
    $plugin->undoRecord('foo', 0); // record 0 goes back to its previous state before the latest update
    
  This subsequently clears the record from the session data (if the undo action is successfull). Said method works on records that have been deleted
  but still exist in the session data (i.e. recently deleted records by the current user).
  
  // --> DELETE A RECORD
  TheMatrix::deleteRecord($table, $id)
  
  Get rid of a record from the selected table (can be undone with undoRecord()).
  
    EXAMPLE
    $plugin->deleteRecord('foo', 0); // deletes record 0 in 'foo'
  
  /* 
   * ------------------ DISPLAYING FORMS ------------------ 
   */
   
  // --> DISPLAY A FIELD
  TheMatrix::displayField($table, $field, $value)
  
  This method is used later for building whole forms for inputting data, but its use will still be explained here. To display an individual field
  that corresponds to its field type (and is editable), call the method.
  
    EXAMPLE 1 (displays field1 from foo with no value in the field)
      <form method="post" method="post" enctype="multipart/form-data">
        <?php $plugin->displayField('foo', 'field1'); ?>
      </form>
      
    EXAMPLE 2 (displays field1 from foo with specified value in the field)
      <form method="post" method="post" enctype="multipart/form-data">
        <?php $plugin->displayField('foo', 'field1', 'specified value here'); ?>
      </form>
      
  Field settings are used to configure the field, i.e. if you have a description set, that will act as the field placeholder; if you have a default
  value set, said value will populate the field if it is empty.
   
  // --> CREATE RECORD FORM
  TheMatrix::createRecordForm($table, $fields)
  
  Form for allowing a user (front or back-end; all depends on where it is displayed) to enter information into the database. Specify the table and the
  fields that you wish to be available (by default all are).
  
    EXAMPLE 1 (shows all fields)
      <form method="post" method="post" enctype="multipart/form-data">
        <?php $plugin->createRecordForm('foo'); ?>
      </form>
      
    EXAMPLE 2 (shows only field1 and field2)
      <form method="post" method="post" enctype="multipart/form-data">
        <?php $plugin->createRecordForm('foo', array('field1', 'field2')); ?>
      </form>
      
  The field property 'class' decides how the fields will look in the admin panel.
  
  // --> UPDATE RECORD FORM
  TheMatrix::updateRecordForm($table, $fields, $id);
  
  
  
  
  
  // --> DISPLAY FORM
  TheMatrix:displayForm($table, $id);
  
  This method acts as a 2-in-1 deal. If the id isn't specified (ie. NULL), the form simply shows default values (i.e. for creating a new record). If 
  the id is specified and said record exists, it populates the form with that record's value. Essentially, you use this form for giving users (front 
  or back-end) and interface with which they can input data into The Matrix. Note: this method does not provide <form> tags or a submit button and
  is not functional unless you code said functionality (an example is shown below).
  
    EXAMPLE 1 (create record form)
      <form method="post" action="register/status/">
        <?php $plugin->displayForm('foo'); ?>
        <input type="submit" class="submit">
      </form>
      
      On the 'register/status/' page have the following code:
      
      <?php
        if ($_SERVER['REQUEST_METHOD']=='POST') {
          $status = $plugin->createRecord('foo', $_POST);
          if ($status) {
            // echo 'Successful registration';
          }
          else {
            // echo 'Registration unsuccessful';
          }
        }  
      ?>
      
    EXAMPLE 2 (update record form)
      <?php
        // action to update the record
        if ($_SERVER['REQUEST_METHOD']=='POST') {
          $status = $plugin->updateRecord('foo', $_POST);
          if ($status) {
            // echo 'Successful edit';
          }
          else {
            // echo 'Edit unsuccessful';
          }
        }  
      ?>
      <form method="post">
        <?php $plugin->displayForm('foo', 0); ?>
        <input type="submit" class="submit">
      </form>
  
    This form can be used safely on the front or back-end of your GetSimple site (data is auto-filtered and saved according to their field type).
  
  
  


getSchema
saveSchema
createRoute
