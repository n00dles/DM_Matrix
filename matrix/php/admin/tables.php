<?php
  
  if (!empty($_POST['submit'])) {
    unset($_POST['field']);
    unset($_POST['submit']);
    
    if (isset($_POST['name'])) {
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
    }
    
    $create =     $this->createTable($_POST['tableName'], $_POST['fields'], $_POST['maxrecords']);
    if($create)   $this->getAdminError(i18n_r(self::FILE.'/TABLE_CREATESUCCESS'), true);  // record successfully editted
    else          $this->getAdminError(i18n_r(self::FILE.'/TABLE_CREATEERROR'), false);    // record not successfully editted
  }
  
  // delete table
  if (isset($_GET['tables']) && substr($_GET['tables'], 0, 7)=='delete:') {
    $table = substr($_GET['tables'], 7);
    $delete   = $this->deleteTable($table);
    if($delete) $this->getAdminError(str_replace('%s', '<strong>'.$table.'</strong>', i18n_r(self::FILE.'/TABLE_DELETESUCCESS')), true);
    else        $this->getAdminError(i18n_r(self::FILE.'/DELETE_ERROR'), false);
  }
  
  // load 'add field' content into variable
  $content = '';
  ob_start(); // output buffering
  $field = array('name'=>'');
  
  foreach ($this->fields['properties'] as $key => $property) {
    $field[$key] = $property['default'];
  }
  include($this->directories['plugin']['forms']['dir'].'/edit_fields.php');
  $content = ob_get_contents(); // loads content from buffer
  ob_end_clean(); // ends output buffering
  
  // return the content
?>
  <style>
    #createTable { display: none; }
    .page_navigation a:link, 
    .page_navigation a:visited {	 
      font-weight: 100;
      color: #D94136 !important;
      text-decoration: underline;
      padding: 1px 3px;
      background: none !important;
      line-height: 16px;
      -webkit-transition: all .05s ease-in-out;
      -moz-transition: all .05s ease-in-out;
      -o-transition: all .05s ease-in-out;
      transition: all .05s ease-in-out;
    }

    .page_navigation a:hover {
      font-weight: 100;
      background: #D94136 !important;
      color: #fff !important;
      text-decoration: none !important;
      padding: 1px 3px;
      line-height: 16px;
    }

    .page_navigation a em {
      font-style: normal;
    }
    
    .page_navigation a {
      border-radius:3px;
    }
  </style>
  <script>
  
    $(document).ready(function() {
      // pajination settings
      var pajinateSettings = {
        'items_per_page'  : 10,
        'nav_label_first' : '|&lt;&lt;', 
        'nav_label_prev'  : '&lt;', 
        'nav_label_next'  : '&gt;', 
        'nav_label_last'  : '&gt;&gt;|', 
      };
      
      // pajination
      $('.schema').pajinate(pajinateSettings);
      
      // filter
      $('#search_input').fastLiveFilter('.content');
      
      // change max number of records
      $('.maxTables').change(function(){
        pajinateSettings['items_per_page'] = $(this).val();
        $('.schema').pajinate(pajinateSettings);
      }); // change
      
      // table sorting
      $('.schema .sortColumn').toggle(
        function() {
          $('.schema').pajinate({'items_per_page': 9999});
          $('.schema tbody tr').tsort({attr:'data-' + $(this).data('sort'), order:'asc'});
          $('.schema').pajinate(pajinateSettings);
        },
        function () {
          $('.schema').pajinate({'items_per_page': 9999});
          $('.schema tbody tr').tsort({attr:'data-' + $(this).data('sort'), order:'desc'});
          $('.schema').pajinate(pajinateSettings);
        }
      ); // toggle
    
      $('#createTable').hide();
      $('.createTable').click(function() {
        $('#createTable').stop().slideToggle();
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
      
      $(document).on('click', '.removeField', function(e){
        $(this).closest('tr').remove();
        return false;
      });
      
      function openAdvanced() {
        $('#createTable .openAdvanced').hide();
        return false;
      }
      openAdvanced();
      
      $('#createTable').on( 'openAdvanced', '.addField');
      
      // show a dialog box when clicking on a link
      $('.deleteTable').bind('click', function(e) {
        var table = $(this).closest('td').data('name');
          e.preventDefault();
          $.Zebra_Dialog(<?php echo json_encode(i18n_r(self::FILE.'/ARE_YOU_SURE')); ?>, {
              'type':     'question',
              'title':    <?php echo json_encode(i18n_r(self::FILE.'/DELETE').' : '); ?> + table,
              'buttons':  [
                    {caption: <?php echo json_encode(i18n_r(self::FILE.'/NO')); ?>, },
                    {caption: <?php echo json_encode(i18n_r(self::FILE.'/YES')); ?>, callback: function() { window.location = '<?php echo $url; ?>&tables=delete:' + table }},
                ]
          });
      });

      
    }); // ready
    
    
    $(document).on('click', '.openAdvanced', function(e){
      $(this).closest('td').find('.advanced').slideToggle();
      return false;
    }); // on


    // fix type
    function fixType(selector) {
      var type = selector.val();
      selector.closest('#metadata_window').find('.masks .mask').attr('name', '').hide();
      var s = selector.closest('#metadata_window').find('.mask.' + selector.val());
      var mask = s.val();
      
      s.attr('name', 'mask[]').stop().slideDown('fast');
      if (s.length == 0) {
        selector.closest('#metadata_window').find('.masks .blank').attr('name', 'mask[]');
        selector.closest('#metadata_window').find('.masks p').hide();
      }
      else {
        selector.closest('#metadata_window').find('.masks p').show();
      }
      fixMask(s);
    }


    // fix mask
    function fixMask(selector) {
      var type = selector.closest('#metadata_window').find('.type').val();
      var mask = selector.val();

      selector.closest('#metadata_window').find('#menu-items > div:not(.' + type + '_' + mask + ')').stop().hide();
      var s = selector.closest('#metadata_window').find('.' + type + '_' + mask);
      s.slideDown('fast');
      
      if (s.length == 0) {
        selector.closest('#metadata_window').find('#menu-items').hide();
      }
      else {
        selector.closest('#metadata_window').find('#menu-items').show();
      }
    }



    $(document).ready(function() {
      $('.type').each(function() {
        fixType($(this));
      });
    }); // ready

    $('body').on('change', '.type', function() {
      fixType($(this));
    }); // on

    $('body').on('change', '.mask', function() {
      fixMask($(this));
    }); // on
  </script>

<!--header-->
  <h3 class="floated"><?php echo i18n_r(self::FILE.'/TABLES'); ?></h3>
  <div class="edit-nav">
    <a href="#" class="createTable"><?php echo i18n_r(self::FILE.'/ADD'); ?></a>
    <div class="clear"></div>
  </div>

<!--'create table' form-->
  <form method="post" id="createTable" action="<?php echo $url;?>&tables">
    <p id="table">
      <label style="display: none;"><?php echo i18n_r(self::FILE.'/TABLE'); ?>: </label>
      <input name="tableName" class="text title" placeholder="<?php echo i18n_r(self::FILE.'/TABLE'); ?>" required pattern="[a-z0-9-]+"/>
      <input name="fields[]" type="hidden"/>
    </p>
    <p>
    <p id="fields">
      <label><?php echo i18n_r(self::FILE.'/FIELDS'); ?>: <a href="" class="addField cancel">+</a></label>
      <table class="fieldList">
        <tbody class="sortable">
        </tbody>
      </table>
    </p>
    <p id="maxrecords" class="">
      <label><?php echo i18n_r(self::FILE.'/MAX_RECORDS') ?>: </label>
      <input name="maxrecords" class="text" placeholder="<?php echo i18n_r(self::FILE.'/MAX_RECORDS'); ?>"/>
    </p>
    <input class="submit" type="submit" name="submit" value="<?php echo i18n_r(self::FILE.'/ADD'); ?>"/>
  </form>
  <br />
    
<!--tables/schema-->
  <table id="editpages" class="schema pajinate edittable highlight">
    <thead>
      <tr>
        <th class="sortColumn" data-sort="tablename">
          <form><input class=" autowidth clearfix" style="display: inline; width: 125px;" type="text" id="search_input" placeholder="<?php echo i18n_r(self::FILE.'/FILTER'); ?>"/></form>
        </th>
        <th class="sortColumn" data-sort="records">
          # <?php echo i18n_r(self::FILE.'/RECORDS'); ?> / <?php echo i18n_r(self::FILE.'/MAXIMUM'); ?>
        </th>
        <th class="sortColumn" data-sort="fieldcount">
          # <?php echo i18n_r(self::FILE.'/FIELDS'); ?>
        </th>
        <th style="width:75px;">
          <?php echo i18n_r(self::FILE.'/OPTIONS'); ?>
        </th>
      </tr>
    </thead>
    <tbody class="content">
      <?php 
        $tables = 0;
        foreach ($this->schema as $schema=>$key) {
        $fieldcnt    = isset($key['fields']) ? count($key['fields']) : '0';
        $numRecords  = $this->getNumRecords($schema);
        $maxRecords  = $key['maxrecords'] == 0 ? '&infin;' : $key['maxrecords'];
        $schemaName  = $schema;
      ?>
        <tr data-tablename="<?php echo $schema; ?>" data-records="<?php echo $numRecords; ?>" data-fieldcount="<?php echo $fieldcnt; ?>">
          <td class="tableName">
            <a href="<?php echo $url; ?>&table=<?php echo $schema; ?>&view" ><?php echo $schema; ?></a>
          </td>
          <td class="numRecords">
            <?php echo $numRecords.' / '.$maxRecords; ?>
          </td>
          <td class="fieldCount">
            <?php echo $fieldcnt; ?>
          </td>
          <td class="options" style="text-align: right;" data-name="<?php echo $schema ?>">
            <a title="<?php echo i18n_r(self::FILE.'/COPY'); ?>" href="<?php echo $url; ?>&table=<?php echo $schema; ?>&view=copy" class="cancel copyTable">+</a>
            <a title="<?php echo i18n_r(self::FILE.'/EMPTY'); ?>" href="<?php echo $url; ?>&table=<?php echo $schema; ?>&view=empty" class="cancel emptyTable">-</a>
            <?php if ($schema != '_routes') { ?>
              <a title="<?php echo i18n_r(self::FILE.'/DELETE'); ?>" href="<?php echo $url; ?>&tables=delete:<?php echo $schema; ?>" class="cancel deleteTable">&times;</a>
            <?php } ?>
            <a title="<?php echo i18n_r(self::FILE.'/BACKUP'); ?>" href="<?php echo $url; ?>&table=<?php echo $schema; ?>&backup=create" class="cancel backupTable">&uarr;</a>
            <a title="+<?php echo i18n_r(self::FILE.'/RECORD'); ?>" href="<?php echo $url; ?>&table=<?php echo $schema; ?>&add" class="cancel addRecord">#</a>
         </td>
        </tr>
        
    <?php
      $tables++;
      }
      if ($tables==0) { ?>
      <tr>
        <td colspan="100%"><?php echo i18n_r(self::FILE.'/TABLES_NONE') ?></td>
      </tr>	
    <?php
      } ?>
    </tbody>
    <thead>
      <tr>
        <th colspan="100%" style="overflow: hidden;">
          <div class="page_navigation" style="overflow: hidden; float: left;"></div>
          <select class="maxTables" style="float: right;">
            <option value="1">--</option>
            <option value="5">5</option>
            <option value="10">10</option>
            <option value="15">15</option>
            <option value="20">20</option>
          </select>
        </th>
      </tr>
    </thead>
  </table>