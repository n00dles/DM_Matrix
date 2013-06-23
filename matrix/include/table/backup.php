<?php
  $this->getAdminHeader('Backup '.$_GET['table'], $nav);
  
  // create a backup
  if ($_GET['backup']=='create') {
    $backupTable   = $this->backupTable($_GET['table'], $directory=false, $file=false);
    if($backupTable) $this->getAdminError('Backup successfull', true);
    else             $this->getAdminError('Backup not successfully', false);
  }
  
  // download backup
  if (substr($_GET['backup'], 0, 6)=='dload:') {
    $this->dloadFile(MATRIXDATAPATH.substr($_GET['backup'], 6));
  }
  
  // delete backup
  if (substr($_GET['backup'], 0, 7)=='delete:') {
    $path = MATRIXDATAPATH.substr($_GET['backup'], 7);
    // deletes
    unlink($path);
    if (!file_exists($path)) $this->getAdminError('Backup deleted', true);
    else                     $this->getAdminError('Backup not deleted', false);
  }
  
  // restore table
  if (substr($_GET['backup'], 0, 8)=='restore:') {
    $file = str_replace(array('restore:', '.zip'), '', $_GET['backup']);
    $restoreTable   = $this->restoreTable($_GET['table'], $directory=false, $file);
    if($restoreTable) $this->getAdminError('Table restoration successfull', true);
    else              $this->getAdminError('Table restoration unsuccessfull', false);
  }
  
  // build backups array
  $backups = $this->getBackups($_GET['table']);
  
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
      $('.deleteBackup').bind('click', function(e) {
        var date = $(this).data('date');
        var file = $(this).data('file');
          e.preventDefault();
          $.Zebra_Dialog('Are you sure that you wish to delete the backup of <?php echo json_encode($_GET['table']); ?> made @ ' + date + '?', {
              'type':     'question',
              'title':    'Deleting backup ' + <?php echo json_encode($_GET['table']); ?>,
              'buttons':  [
                    {caption: 'No', },
                    {caption: 'Yes', callback: function() { window.location = "load.php?id=<?php echo MATRIX; ?>&table=<?php echo $_GET['table']; ?>&backup=delete:" + file }},
                ]
          });
      });
      // show a dialog box when clicking on a link
      $('.restoreBackup').bind('click', function(e) {
        var date = $(this).data('date');
        var file = $(this).data('file');
          e.preventDefault();
          $.Zebra_Dialog('Are you sure that you wish to restore the backup of <?php echo json_encode($_GET['table']); ?> made @ ' + date + '? This will bring the state of the table to that of the backup (deleting any new entries).', {
              'type':     'question',
              'title':    'Restoring backup ' + <?php echo json_encode($_GET['table']); ?>,
              'buttons':  [
                    {caption: 'No', },
                    {caption: 'Yes', callback: function() { window.location = "load.php?id=<?php echo MATRIX; ?>&table=<?php echo $_GET['table']; ?>&backup=restore:" + file }},
                ]
          });
      });

  });
</script>

<table class="pajinate edittable highlight" width="100%">
  <thead>
    <tr><th colspan="100%"><div class="page_navigation"></div></th></tr>
  </thead>
  <thead>
    <th>Backup</th>
    <th width="1">Options</th>
  </thead>
  <tbody class="content">
    <?php foreach ($backups as $backup=>$details) { ?>
      <tr>
        <td>
          <a href="load.php?id=<?php echo MATRIX; ?>&table=<?php echo $_GET['table']; ?>&backup=dload:<?php echo $details['file']?>">
            <?php echo $details['date']; ?>
          </a>
        </td>
        <td class="">
          <a title="Restore" href="load.php?id=<?php echo MATRIX; ?>&table=<?php echo $_GET['table']; ?>&backup=restore:<?php echo $details['file']?>" class="cancel restoreBackup" data-date="<?php echo $details['date']; ?>" data-file="<?php echo $details['file']; ?>">#</a>
          <a title="Delete" href="load.php?id=<?php echo MATRIX; ?>&table=<?php echo $_GET['table']; ?>&backup=delete:<?php echo $details['file']?>" class="cancel deleteBackup" data-date="<?php echo $details['date']; ?>" data-file="<?php echo $details['file']; ?>">&times;</a>
        </td>
      </tr>
    <?php }
      if (empty($backups)) { ?>
      <tr>
        <td colspan="100%">No backups</td>
      </tr>
    <?php }?>
  </tbody>
  <thead>
    <tr>
      <th colspan="100%" style="overflow: hidden;">
        <div style="float: left;">
          <a href="load.php?id=<?php echo MATRIX; ?>&table=<?php echo $_GET['table']; ?>&backup=create" class="cancel">Create Backup</a>
        </div>
        <div class="page_navigation" style=" float: right;"></div>
      </th>
    </tr>
  </thead>
</table>