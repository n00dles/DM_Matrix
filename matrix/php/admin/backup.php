<?php
  // create a backup
  if ($_GET['backup']=='create') {
    $backupTable   = $this->backupTable($_GET['table'], $directory=false, $file=false);
    if($backupTable) $this->getAdminError(i18n_r(self::FILE.'/BACKUP_SUCCESS'), true);
    else             $this->getAdminError(i18n_r(self::FILE.'/BACKUP_ERROR'), false);
  }
  
  // download backup
  if (substr($_GET['backup'], 0, 6)=='dload:') {
    $this->dloadFile($this->directories['data']['core']['dir'].substr($_GET['backup'], 6));
  }
  
  // delete backup
  if (substr($_GET['backup'], 0, 7)=='delete:') {
    $path = $this->directories['data']['core']['dir'].substr($_GET['backup'], 7);
    // deletes
    unlink($path);
    if (!file_exists($path)) $this->getAdminError(i18n_r(self::FILE.'/DELETE_SUCCESS'), true);
    else                     $this->getAdminError(i18n_r(self::FILE.'/DELETE_ERROR'), false);
  }
  
  // restore table
  if (substr($_GET['backup'], 0, 8)=='restore:') {
    $file = str_replace(array('restore:', '.zip'), '', $_GET['backup']);
    $restoreTable   = $this->restoreTable($_GET['table'], $directory=false, $file);
    if($restoreTable) $this->getAdminError(i18n_r(self::FILE.'/UNDO_SUCCESS'), true);
    else              $this->getAdminError(i18n_r(self::FILE.'/UNDO_ERROR'), false);
  }
  
  // build backups array
  $backups = $this->getBackups($_GET['table']);
?>
<!--header-->
  <h3 class="floated"><?php echo $_GET['table']; ?></h3>
  <div class="edit-nav">
    <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&auto"><?php echo i18n_r(self::FILE.'/AUTO'); ?></a>
    <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&backup" class="current"><?php echo i18n_r(self::FILE.'/BACKUP'); ?></a>
    <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&form"><?php echo i18n_r(self::FILE.'/FORM'); ?></a>
    <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&fields"><?php echo i18n_r(self::FILE.'/FIELDS'); ?></a>
    <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&view"><?php echo i18n_r(self::FILE.'/VIEW'); ?></a>
    <div class="clear"></div>
  </div>
  
<!--javascript--> 
  <script>
    $(document).ready(function() {
      // pajinate the query
      $('.pajinate').pajinate({
        'nav_label_first' : '|&lt;&lt;', 
        'nav_label_prev'  : '&lt;', 
        'nav_label_next'  : '&gt;', 
        'nav_label_last'  : '&gt;&gt;|', 
      });
      $('.pajinate .page_navigation a').addClass('cancel');
      
      // filter
      $('#search_input').fastLiveFilter('.content');
      
      // sortable
      $('.sortable').sortable();
      
      // show a dialog box when clicking on a link
      $('.deleteBackup').bind('click', function(e) {
        var date = $(this).data('date');
        var file = $(this).data('file');
        e.preventDefault();
        $.Zebra_Dialog(<?php echo json_encode(i18n_r(self::FILE.'/ARE_YOU_SURE')); ?>, {
          'type':     'question',
          'title':    <?php echo json_encode(i18n_r(self::FILE.'/DELETE').' : '.$_GET['table'].' @ '); ?> + date,
          'buttons':  [
            {caption: <?php echo json_encode(i18n_r(self::FILE.'/NO')); ?>, },
            {caption: <?php echo json_encode(i18n_r(self::FILE.'/YES')); ?>, callback: function() { window.location = "<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&backup=delete:" + file }},
          ]
        });
      });
      // show a dialog box when clicking on a link
      $('.restoreBackup').bind('click', function(e) {
        var date = $(this).data('date');
        var file = $(this).data('file');
        e.preventDefault();
        $.Zebra_Dialog(<?php echo json_encode(i18n_r(self::FILE.'/ARE_YOU_SURE')); ?>, {
            'type':     'question',
            'title':    <?php echo json_encode(i18n_r(self::FILE.'/RESTORE').' : '.$_GET['table'].' @ '); ?> + date,
            'buttons':  [
              {caption: <?php echo json_encode(i18n_r(self::FILE.'/NO')); ?>, },
              {caption: <?php echo json_encode(i18n_r(self::FILE.'/YES')); ?>, callback: function() { window.location = "<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&backup=restore:" + file }},
            ]
        });
      });
    });
  </script>

<!--table--> 
  <table class="pajinate edittable highlight" width="100%">
    <thead>
      <tr><th colspan="100%"><div class="page_navigation"></div></th></tr>
    </thead>
    <thead>
      <th><?php echo i18n_r(self::FILE.'/BACKUP'); ?></th>
      <th width="1"><?php echo i18n_r(self::FILE.'/OPTIONS'); ?></th>
    </thead>
    <tbody class="content">
      <?php foreach ($backups as $backup=>$details) { ?>
        <tr>
          <td>
            <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&backup=dload:<?php echo $details['file']?>">
              <?php echo $details['date']; ?>
            </a>
          </td>
          <td class="">
            <a title="Restore" href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&backup=restore:<?php echo $details['file']?>" class="cancel restoreBackup" data-date="<?php echo $details['date']; ?>" data-file="<?php echo $details['file']; ?>">#</a>
            <a title="Delete" href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&backup=delete:<?php echo $details['file']?>" class="cancel deleteBackup" data-date="<?php echo $details['date']; ?>" data-file="<?php echo $details['file']; ?>">&times;</a>
          </td>
        </tr>
      <?php }
        if (empty($backups)) { ?>
        <tr>
          <td colspan="100%"><?php echo i18n_r(self::FILE.'/BACKUPS_NONE');?></td>
        </tr>
      <?php }?>
    </tbody>
    <thead>
      <tr>
        <th colspan="100%" style="overflow: hidden;">
          <div style="float: left;">
            <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&backup=create" class="cancel">+ <?php echo i18n_r(self::FILE.'/BACKUP');?></a>
          </div>
          <div class="page_navigation" style=" float: right;"></div>
        </th>
      </tr>
    </thead>
  </table>