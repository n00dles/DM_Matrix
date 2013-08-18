<?php
  // add record
  if (!empty($_POST['post-submitform'])) {
    $create = $this->createRecord($_GET['table'], $_POST);
    if($create) $this->getAdminError(i18n_r(self::FILE.'/RECORD_ADDSUCCESS'), true);
    else        $this->getAdminError(i18n_r(self::FILE.'/RECORD_ADDERROR'), false);
  }
  
  // copy table
  if ($_GET['view']=='copy') {
    $copy   = $this->copyTable($_GET['table'], $name=null);
    if($copy) $this->getAdminError(i18n_r(self::FILE.'/TABLE_COPYSUCCESS'), true);
    else      $this->getAdminError(i18n_r(self::FILE.'/TABLE_COPYERROR'), false);
  }
  
  // empty table
  if ($_GET['view']=='empty') {
    $empty   = $this->emptyTable($_GET['table']);
    if($empty) $this->getAdminError(i18n_r(self::FILE.'/TABLE_EMPTYSUCCESS'), true);
    else       $this->getAdminError(i18n_r(self::FILE.'/TABLE_EMPTYERROR'), false);
    
    $this->saveSchema(true);
    $this->getSchema(true);
  }
  
  // delete record
  if (substr($_GET['view'], 0, 7)=='delete:') {
    $record = substr($_GET['view'], 7);
    // deletes
    $delete = $this->deleteRecord($_GET['table'], $record);
    if ($delete) $this->getAdminError(i18n_r(self::FILE.'/RECORD_DELETESUCCESS'), true, true, $url.'&table='.$_GET['table'].'&view=undo:'.$record);
    else         $this->getAdminError(i18n_r(self::FILE.'/RECORD_DELETEERROR'), false);
  }
  
  // undo update/delete record
  if (substr($_GET['view'], 0, 5)=='undo:') {
    $record = substr($_GET['view'], 5);
    $undo = $this->undoRecord($_GET['table'], $record);
    if ($undo) $this->getAdminError(i18n_r(self::FILE.'/UNDO_SUCCESS'), true);
    else       $this->getAdminError(i18n_r(self::FILE.'/UNDO_ERROR'), false);
  }
  
  // gets the table contents
  $fields   = $this->getSchema($_GET['table'], true);
  $records  = $this->query('SELECT * FROM '.$_GET['table'].' ORDER BY id ASC');
  $records  = $this->formatQuery($records, $_GET['table']);
?>

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
    
    // dialogs
      // show a dialog box when clicking on a link
      $('.deleteTable').bind('click', function(e) {
        var recordID = $(this).data('id');
          e.preventDefault();
          $.Zebra_Dialog(<?php echo json_encode(i18n_r(self::FILE.'/ARE_YOU_SURE')); ?>, {
              'type':     'question',
              'title':    <?php echo json_encode(i18n_r(self::FILE.'/DELETE').' : '.$_GET['table']); ?>,
              'buttons':  [
                    {caption: <?php echo json_encode(i18n_r(self::FILE.'/NO')); ?>, },
                    {caption: <?php echo json_encode(i18n_r(self::FILE.'/YES')); ?>, callback: function() { window.location = '<?php echo $url; ?>&tables=delete:<?php echo $_GET['table']; ?>' }},
                ]
          });
      });
      $('.record .delete').bind('click', function(e) {
        var recordID = $(this).data('id');
          e.preventDefault();
          $.Zebra_Dialog(<?php echo json_encode(i18n_r(self::FILE.'/ARE_YOU_SURE')); ?>, {
              'type':     'question',
              'title':    <?php echo json_encode(i18n_r(self::FILE.'/DELETE').' : #'); ?> + recordID,
              'buttons':  [
                    {caption: <?php echo json_encode(i18n_r(self::FILE.'/NO')); ?>, },
                    {caption: <?php echo json_encode(i18n_r(self::FILE.'/YES')); ?>, callback: function() { window.location = '<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&view=delete:' + recordID }},
                ]
          });
      });

  });
</script>


<h3 class="floated"><?php echo $_GET['table']; ?></h3>
<div class="edit-nav">
  <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&auto"><?php echo i18n_r(self::FILE.'/AUTO'); ?></a>
  <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&backup"><?php echo i18n_r(self::FILE.'/BACKUP'); ?></a>
  <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&form"><?php echo i18n_r(self::FILE.'/FORM'); ?></a>
  <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&fields"><?php echo i18n_r(self::FILE.'/FIELDS'); ?></a>
  <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&view" class="current"><?php echo i18n_r(self::FILE.'/VIEW'); ?></a>
  <div class="clear"></div>
</div>

<table class="pajinate edittable highlight" width="100%">
  <thead>
    <tr>
      <th colspan="100%" style="overflow: hidden;">
        <div class="page_navigation" style=" float: left;"></div>
        <div style="float: right;">
          <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&view=copy" class="cancel copyTable"><?php echo i18n_r(self::FILE.'/COPY'); ?></a>
          <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&view=empty" class="cancel emptyTable"><?php echo i18n_r(self::FILE.'/EMPTY'); ?></a>
          <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&view=delete" class="cancel deleteTable"><?php echo i18n_r(self::FILE.'/DELETE'); ?></a>
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
    <th style="width: 1px;"><?php echo i18n_r(self::FILE.'/OPTIONS'); ?></th>
  </thead>
  <tbody class="content">
    <?php foreach ($records as $record) { ?>
    <tr class="record">
    <?php   foreach ($fields as $field=>$properties) {
            // fields only shown if they have their visibility enabled
            if ($properties['tableview'] == 1) { ?>
      <td style="word-wrap: break-word;">
        <?php
          // display regular field
          if (!is_array($record[$field])) echo $record[$field];
          // display array field
          else {
            foreach ($record[$field] as $f => $v) { ?>
          <div>
            <label><?php echo $f; ?></label>
            <span><?php echo $v; ?></span>
          </div>
          <?php
            }
          }
        ?>
      </td>
    <?php   }
          }
    ?>
      <td style="text-align: right;">
        <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&edit=<?php echo $record['id']; ?>" class="edit cancel" data-id="<?php echo $record['id']; ?>">#</a>
        <a href="" class="delete cancel" data-id="<?php echo $record['id']; ?>">x</a>
      </td>
    </tr>
    <?php }
          if (empty($records)) { ?>
      <tr>
        <td colspan="100%"><?php echo i18n_r(self::FILE.'/RECORDS_NONE'); ?></td>
      </tr>
    <?php } ?>
  </tbody>
  <thead>
    <tr>
      <th colspan="100%" style="overflow: hidden;">
        <div style="float: left;">
          <?php
            $maxRecords = (int)$this->schema[$_GET['table']]['maxrecords'];
          if ($maxRecords==0 || ($maxRecords != 0 && (count($records) < $maxRecords))) {?>
          <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&add" class="cancel">+ <?php echo i18n_r(self::FILE.'/RECORD'); ?></a>
          <?php }
                else { ?>
          <?php echo i18n_r(self::FILE.'/TABLE_FULL'); ?>     
          <?php } ?>
        </div>
        <div class="page_navigation" style=" float: right;"></div>
      </th>
    </tr>
  </thead>
</table>