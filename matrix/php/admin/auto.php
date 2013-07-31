<?php
  $fields = $this->schema[$_GET['table']]['fields'];
  unset($fields['id']);
  foreach ($fields as $key => $field) {
    unset($field['oldname']);
    $fields[] = $field;
    unset($fields[$key]);
  }
  
?>

<!--header-->
  <h3 class="floated"><?php echo $_GET['table']; ?></h3>
  <div class="edit-nav">
    <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&auto" class="current"><?php echo i18n_r(self::FILE.'/AUTO'); ?></a>
    <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&backup"><?php echo i18n_r(self::FILE.'/BACKUP'); ?></a>
    <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&form"><?php echo i18n_r(self::FILE.'/FORM'); ?></a>
    <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&fields"><?php echo i18n_r(self::FILE.'/FIELDS'); ?></a>
    <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&view"><?php echo i18n_r(self::FILE.'/VIEW'); ?></a>
    <div class="clear"></div>
  </div>

<!--functions/methods-->
  <table>
    <thead>
      <tr>
        <th><?php echo i18n_r(self::FILE.'/INITIALIZE'); ?></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          <pre><code>
  $var = new TheMatrix;
  </code></pre>
        </td>
      </tr>
    </tbody>
    <thead>
      <tr>
        <th><?php echo i18n_r(self::FILE.'/TABLES'); ?></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          <pre><code>
  <b>// FIELDS ARRAY</b>
  $fields = array(
  <?php $this->outputFunction($fields, $depth=1); ?>
  );

  <b>// CREATE</b>
  $var->createTable('<?php echo $_GET['table']; ?>', $fields, $maxrecords=<?php echo $this->schema[$_GET['table']]['maxrecords']; ?>);

  <b>// COPY</b>
  $var->copyTable('<?php echo $_GET['table']; ?>');

  <b>// EMPTY</b>
  $var->emptyTable('<?php echo $_GET['table']; ?>');
   
  <b>// DELETE</b>
  $var->deleteTable('<?php echo $_GET['table']; ?>');
   
  <b>// BACKUPS</b>
  $var->backupTable('<?php echo $_GET['table']; ?>'); <b>// to data/other/matrix/ folder</b>
  $var->backupTable('<?php echo $_GET['table']; ?>', 'data/<?php echo $_GET['table']; ?>'); <b>// to desired folder, e.g. data/<?php echo $_GET['table']; ?></b>
  </code></pre>
        </td>
      </tr>
    </tbody>
    <thead>
      <tr>
        <th><?php echo i18n_r(self::FILE.'/FORM'); ?></th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>
          <pre><code>
  <b>// 'create record' form</b>
  &lt;?php
    if ($_SERVER['REQUEST_METHOD']=='POST') {
      $var->createRecord('<?php echo $_GET['table']; ?>', $_POST);
    }
  ?&gt;
  &lt;form method="post"&gt;
    &lt;?php $var->displayForm('<?php echo $_GET['table']; ?>'); ?&gt;
  &lt;/form&gt;


  <b>// 'edit record' form</b>
  &lt;form method="post"&gt;
    &lt;?php
      $id = 0; <b>// $id should be the desired record number from the table</b>

      if ($_SERVER['REQUEST_METHOD']=='POST') {
        $var->updateRecord('<?php echo $_GET['table']; ?>', $id, $_POST);
      }

      $var->displayForm('<?php echo $_GET['table']; ?>', $id);
    ?&gt;
  &lt;/form&gt;
  </code></pre>
        </td>
      </tr>
    </tbody>
  </table>