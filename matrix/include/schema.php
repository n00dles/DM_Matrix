<?php
  
  if (!empty($_POST['submit'])) {
    unset($_POST['field']);
    unset($_POST['submit']);

    $_POST['fields'] = $_POST['name'];
    
    // reorder the array to fit the createTable format
    foreach ($_POST['name'] as $key=>$field) {
      $_POST['fields'][$key] = array('name'=>$field);
    }
    
    foreach ($_POST as $key => $value) {
      if ($key!='tableName' && $key!='fields' && $key!='maxrecords') {
        foreach ($value as $fieldKey => $fieldValue) {
          $_POST['fields'][$fieldKey][$key] = $fieldValue;
        }
      }
    }

    if (empty($_POST['fields'])) $_POST['fields'] = array();
    
    $create =     $this->createTable($_POST['tableName'], $_POST['fields'], $_POST['maxrecords']);
    if($create)   $this->getAdminError('Table created successfully.', true);  // record successfully editted
    else          $this->getAdminError('Table not created successfully.', false);    // record not successfully editted
  }
  
  // delete table
  if (substr($_GET['tables'], 0, 7)=='delete:') {
    $table = substr($_GET['tables'], 7);
    $delete   = $this->deleteTable($table);
    if($delete) $this->getAdminError('Table <strong>'.$table.'</strong> deleted successfully', true);
    else        $this->getAdminError('Table not deleted', false);
  }
  
  // load 'add field' content into variable
  $content = '';
  ob_start(); // output buffering
  $field = array('name'=>'');
  
  foreach ($this->fieldProperties as $key=>$property) {
    $field[$key] = $property['default'];
  }
  include(MATRIXPATH.'/include/forms/edit_fields.php');
  $content = ob_get_contents(); // loads content from buffer
  ob_end_clean(); // ends output buffering
  
  // return the content
?>
    <style>
      #createTable .advanced, .dropdown, .dropdowncustom, .imageupload { display: none; }
    </style>
    <script>
    
      $(document).ready(function() {
      
        // pajinate the query
        $('.pajinate').pajinate();
        $('.pajinate .page_navigation a').addClass('cancel');
        
        // filter
        $('#search_input').fastLiveFilter('.content');
        
        // sortable
        $('.sortable').sortable();
        
        // tsort the list
        var aAsc = [];
        //$('.sortable').tsort();
        $('.schema tbody tr').tsort();
        $('.schema thead th').toggle(
          function() {
            $('.schema tbody tr').tsort({data:$(this).data('sort')}, {order:'asc'});
          },
          function () {
            $('.schema tbody tr').tsort({data:$(this).data('sort')}, {order:'desc'});
          }
        );
      
        $('#createTable').hide();
        $('.createTable').click(function() {
          $('#createTable').stop().slideToggle();
          //$('html, body').animate({
          //    scrollTop: ($('#createTable').offset().top)
          //},500);
        
          return false;
        });
        $('#createTable .openAdvanced').hide();
        
        $('.addField').click(function() {
          $('#createTable .fieldList').append(
            '<tr draggable="true"><td>' + <?php echo json_encode($content); ?> + '</td></tr>'
          );
          $('.sortable').sortable('destroy');
          $('.sortable').sortable();
          return false;
        });
        
        function openAdvanced() {
          $('#createTable .openAdvanced').hide();
          return false;
        }
        openAdvanced();
        
        $('#createTable').on( 'openAdvanced', '.addField');
        
        // dialogs
        // show a dialog box when clicking on a link
        $('.deleteTable').bind('click', function(e) {
          var table = $(this).closest('td').data('name');
            e.preventDefault();
            $.Zebra_Dialog('Are you sure that you wish to delete this table? This action can be reveresed if you have a recent backup - make sure that one exists!', {
                'type':     'question',
                'title':    'Deleting ' + table,
                'buttons':  [
                      {caption: 'No', },
                      {caption: 'Yes', callback: function() { window.location = 'load.php?id=<?php echo MATRIX; ?>&tables=delete:' + table }},
                  ]
            });
        });

        
      }); // ready
      
      $('.advanced').hide();
      
      $(document).on('ready', '.advanced', function(e){
        $(this).hide();
      });
      
      
      $(document).on('click', '.openAdvanced', function(e){
        $(this).closest('td').find('.advanced').slideToggle();
        return false;
      });
      
      $(document).on('click', '.removeField', function(e){
        $(this).closest('tr').remove();
        return false;
      });
      
      
      // extra options dependent on field type
      $(document).ready(function() {
        $('.type').each(function() {
          $this = $(this);
          $this.closest('#metadata_window').find('.showOptions').hide();
          $this.closest('#metadata_window').find('div.' + $this.val()).stop().show();
        });
      }); // ready
      
      $('body').on('change', '.type', function() {
        $this = $(this);
        $this.closest('#metadata_window').find('.showOptions').not('.' + $this.val()).stop().slideUp('fast');
        $this.closest('#metadata_window').find('div.' + $this.val()).slideDown('fast');
      });
      
      
      

    </script>

		<?php
      $this->getAdminHeader(i18n_r("matrix/DM_MENU_TABLE"), array(i18n_r('matrix/DM_ADD_TABLE_BUTTON')=>array('key'=>'createTable', 'link'=>'load.php?id='.MATRIX.'&tables=create')));
    ?>

    <form method="post" id="createTable" action="load.php?id=<?php echo MATRIX;?>&tables">
      <p id="table">
        <label style="display: none;"><?php echo i18n_r("matrix/DM_TABLENAME"); ?>: </label>
        <input name="tableName" class="text title" placeholder="<?php echo i18n_r("matrix/DM_TABLENAME"); ?>" required pattern="\w*"/>
        <input name="fields[]" type="hidden"/>
      </p>
      <p>
      <p id="fields">
        <label><?php echo i18n_r("matrix/DM_FIELDS"); ?>: <a href="" class="addField cancel">+</a></label>
        <table class="fieldList">
          <tbody class="sortable">
          </tbody>
        </table>
      </p>
      <p id="maxrecords" class="">
        <label>Max records: </label>
        <input name="maxrecords" class="text" placeholder="<?php echo i18n_r("matrix/DM_MAXRECORDS"); ?>"/>
      </p>
      <input class="submit" type="submit" name="submit" value="<?php echo i18n_r('matrix/DM_ADD_TABLE_BUTTON'); ?>"/>
    </form>
    <br />
    
		<table class="schema" id="editpages" class="pajinate edittable highlight">
      <thead>
        <tr>
          <th class="sort" data-sort="tablename">
            <form style="float: left;"><input class=" autowidth clearfix" style="display: inline; width: 125px;" type="text" id="search_input" placeholder=""/></form>
            <div class="page_navigation" style="float: right;"></div>
          </th>
          <th class="sort" data-sort="records"><?php echo i18n_r(MATRIX.'/DM_NUMRECORDS') ?></th>
          <th class="sort" data-sort="fieldcount"><?php echo i18n_r(MATRIX.'/DM_NUMFIELDS') ?></th>
          <th style="width:75px;"><?php echo i18n_r(MATRIX.'/DM_OPTIONS') ?></th>
        </tr>
      </thead>
      <tbody class="content">
        <?php 
          $tables = 0;
          foreach ($this->schemaArray as $schema=>$key) {
          $fieldcnt    = isset($key['fields']) ? count($key['fields']) : '0';
          $numRecords  = $this->getNumRecords($schema);
          $maxRecords  = $key['maxrecords'];
          $schemaName  = $schema;
        ?>
          <tr data-name="<?php echo $schema; ?>" data-records="<?php echo $numRecords; ?>" data-fieldcount="<?php echo $fieldcnt; ?>">
            <td class="tableName">
              <a href="load.php?id=<?php echo MATRIX; ?>&table=<?php echo $schema; ?>&view" ><?php echo $schema; ?></a>
            </td>
            <td class="numRecords">
              <?php echo $numRecords.' / '.$maxRecords; ?>
            </td>
            <td class="fieldCount">
              <?php echo $fieldcnt; ?>
            </td>
            <td class="options" style="text-align: right;" data-name="<?php echo $schema ?>">
              <a title="Copy Table" href="load.php?id=<?php echo MATRIX; ?>&table=<?php echo $schema; ?>&view=copy" class="cancel copyTable">+</a>
              <a title="Empty Table" href="load.php?id=<?php echo MATRIX; ?>&table=<?php echo $schema; ?>&view=empty" class="cancel emptyTable">-</a>
              <?php if ($schema!='_routes') { ?>
                <a title="Delete Table" href="load.php?id=<?php echo MATRIX; ?>&tables=delete:<?php echo $schema; ?>" class="cancel deleteTable">&times;</a>
              <?php } ?>
              <a title="Backup Table" href="load.php?id=<?php echo MATRIX; ?>&table=<?php echo $schema; ?>&backup=create" class="cancel backupTable">&uarr;</a>
              <a title="Add Record" href="load.php?id=<?php echo MATRIX; ?>&table=<?php echo $schema; ?>&add" class="cancel addRecord">#</a>
           </td>
          </tr>
          
      <?php
        $tables++;
        }
        if ($tables==0) { ?>
        <tr>
          <td colspan="100%"><?php echo i18n_r(MATRIX.'/DM_NOTABLES') ?></td>
        </tr>	
      <?php
        } ?>
      </tbody>
		</table>