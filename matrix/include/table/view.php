<?php
  // POST query
  if (!empty($_POST['post-submitform'])) {
    $create = $this->createRecord($_GET['table'], $_POST);
    if($create) $this->getAdminError('Record added successfully', true);
    else        $this->getAdminError('Record not added successfully', false);
  }
  
  // header
  $this->getAdminHeader($_GET['table'], $nav);
  
  // copy table
  if ($_GET['view']=='copy') {
    $copy   = $this->copyTable($_GET['table'], $name=null);
    if($copy) $this->getAdminError('Table copied successfully', true);
    else      $this->getAdminError('Table not copied successfully', false);
  }
  
  // empty table
  if ($_GET['view']=='empty') {
    $empty   = $this->emptyTable($_GET['table']);
    if($empty) $this->getAdminError('Table emptied successfully', true);
    else       $this->getAdminError('Table not emptied successfully', false);
    
    $this->saveSchema(true);
    $this->getSchema(true);
  }
  
  // delete record
  if (substr($_GET['view'], 0, 7)=='delete:') {
    $record = substr($_GET['view'], 7);
    // deletes
    $delete = $this->deleteRecord($_GET['table'], $record);
    if ($delete) $this->getAdminError('Record deleted', true, true, 'load.php?id='.MATRIX.'&table='.$_GET['table'].'&view=undo:'.$record);
    else         $this->getAdminError('Record not deleted', false);
  }
  
  // undo update/delete record
  if (substr($_GET['view'], 0, 5)=='undo:') {
    $record = substr($_GET['view'], 5);
    $undo = $this->undoRecord($_GET['table'], $record);
    if ($undo) $this->getAdminError('Changes reverted', true);
    else       $this->getAdminError('Changes not reverted', false);
  }
  
  // gets the table contents
  $fields   = $this->getSchema($_GET['table'], true);
  $records  = $this->query('SELECT * FROM '.$_GET['table'].' ORDER BY id ASC');
  $records  = $this->formatQuery($records, $_GET['table']);
?>

<script>
  $(document).ready(function() {
  
    // pajinate the query
    $('.pajinate').pajinate();
    $('.pajinate .page_navigation a').addClass('cancel');
    
    // filter
    $('#search_input').fastLiveFilter('.content');
    
    // sortable
    $('.sortable').sortable();
    
    // dialogs
      // show a dialog box when clicking on a link
      $('.deleteTable').bind('click', function(e) {
        var recordID = $(this).data('id');
          e.preventDefault();
          $.Zebra_Dialog('Are you sure that you wish to delete this table?', {
              'type':     'question',
              'title':    'Deleting table',
              'buttons':  [
                    {caption: 'No', },
                    {caption: 'Yes', callback: function() { window.location = 'load.php?id=<?php echo MATRIX; ?>&tables=delete:<?php echo $_GET['table']; ?>' }},
                ]
          });
      });
      $('.record .delete').bind('click', function(e) {
        var recordID = $(this).data('id');
          e.preventDefault();
          $.Zebra_Dialog('Are you sure that you wish to delete record ' + recordID + '?', {
              'type':     'question',
              'title':    'Deleting record ' + recordID,
              'buttons':  [
                    {caption: 'No', },
                    {caption: 'Yes', callback: function() { window.location = 'load.php?id=<?php echo MATRIX; ?>&table=<?php echo $_GET['table']; ?>&view=delete:' + recordID }},
                ]
          });
      });

  });
</script>


<table class="pajinate edittable highlight" width="100%">
  <thead>
    <tr>
      <th colspan="100%" style="overflow: hidden;">
        <div class="page_navigation" style=" float: left;"></div>
        <div style="float: right;">
          <a href="load.php?id=<?php echo MATRIX; ?>&table=<?php echo $_GET['table']; ?>&view=copy" class="cancel copyTable">Copy</a>
          <a href="load.php?id=<?php echo MATRIX; ?>&table=<?php echo $_GET['table']; ?>&view=empty" class="cancel emptyTable">Empty</a>
          <a href="load.php?id=<?php echo MATRIX; ?>&table=<?php echo $_GET['table']; ?>&view=delete" class="cancel deleteTable">Delete</a>
        </div>
      </th>
    </tr>
  </thead>
  <thead>
    <?php foreach ($fields as $field=>$properties) {
            // fields only shown if they have their visibility enabled
            if ($properties['tableview']==1) { ?>
    <th width="<?php echo $properties['size']; ?>"><?php echo $properties['name']; ?></th>
    <?php   }
          }
    ?>
    <th style="width: 1px;">Options</th>
  </thead>
  <tbody class="content">
    <?php foreach ($records as $record) { ?>
    <tr class="record">
    <?php   foreach ($fields as $field=>$properties) {
            // fields only shown if they have their visibility enabled
            if ($properties['tableview']==1) { ?>
      <td style="word-wrap: break-word;"><?php echo $record[$field]; ?></td>
    <?php   }
          }
    ?>
      <td style="text-align: right;">
        <a href="load.php?id=<?php echo MATRIX; ?>&table=<?php echo $_GET['table']; ?>&edit=<?php echo $record['id']; ?>" class="edit cancel" data-id="<?php echo $record['id']; ?>">#</a>
        <a href="" class="delete cancel" data-id="<?php echo $record['id']; ?>">x</a>
      </td>
    </tr>
    <?php }
          if (empty($records)) { ?>
      <tr>
        <td colspan="100%">No records</td>
      </tr>
    <?php } ?>
  </tbody>
  <thead>
    <tr>
      <th colspan="100%" style="overflow: hidden;">
        <div style="float: left;">
          <?php
            $maxRecords = (int)$this->schemaArray[$_GET['table']]['maxrecords'];
          if ($maxRecords==0 || ($maxRecords!=0 && (count($records)<$maxRecords))) {?>
          <a href="load.php?id=<?php echo MATRIX; ?>&table=<?php echo $_GET['table']; ?>&add" class="cancel"><?php echo i18n_r('matrix/DM_ADD_RECORD_BUTTON'); ?></a>
          <?php }
                else { ?>
          Table full      
          <?php } ?>
        </div>
        <div class="page_navigation" style=" float: right;"></div>
      </th>
    </tr>
  </thead>
</table>