<?php
  if ($_SERVER['REQUEST_METHOD']=='POST') {
    #echo '<pre><code>'; var_dump($_POST); echo '</code></pre>';
    $update    = $this->buildFields($_GET['table'], $_POST);
    if ($update) $this->getAdminError('Fields updated successfully', true);
    else         $this->getAdminError('Fields not updated successfully', false);
  } 
  $fields = $this->schema[$_GET['table']]['fields'];
  unset($fields['id']); 
?>

<!--header-->
  <h3 class="floated"><?php echo $_GET['table']; ?></h3>
  <div class="edit-nav">
    <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&auto"><?php echo i18n_r(self::FILE.'/AUTO'); ?></a>
    <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&backup"><?php echo i18n_r(self::FILE.'/BACKUP'); ?></a>
    <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&form"><?php echo i18n_r(self::FILE.'/FORM'); ?></a>
    <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&fields" class="current"><?php echo i18n_r(self::FILE.'/FIELDS'); ?></a>
    <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&view"><?php echo i18n_r(self::FILE.'/VIEW'); ?></a>
    <div class="clear"></div>
  </div>

<!--css-->
  <style>
    .advanced, #menu-items, .masks select:not(.input) { display: none; }
    #menu-items {
      height: auto !important;
      margin: 0 0 5px 0 !important;
      padding: 10px !important;
    }
    label h3 { display: inline; font-size: 15px; }
  </style>
  
<!--javascript-->
  <script>
    $('.sortable').sortable();
    $(document).ready(function() {
      // sortable
      $('.sortable').sortable();
      
    <?php
      // load 'add field' content into variable
      $content = '';
      ob_start(); // output buffering
      $field = array('name'=>'');
      
      foreach ($this->fields['properties'] as $key=>$property) {
        $field[$key] = $property['default'];
      }
      include($this->directories['plugin']['forms']['dir'].'/edit_fields.php');
      $content = ob_get_contents(); // loads content from buffer
      ob_end_clean(); // ends output buffering
      
      // return the content
    ?>
    
      $('.addField').click(function() {
        $('#editFields .fieldList').append(
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
      
      if (typeof(mask) === 'undefined') {
        var _class = type;
      }
      else {
        var _class = type + '_' + mask;
      }
      
      selector.closest('#metadata_window').find('#menu-items > div:not(.' + _class + ')').stop().hide();
      var s = selector.closest('#metadata_window').find('.' + _class);
      s.slideDown('fast');
      
      //$('.plugin_sb').text(_class);
      
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
      }); // each
      
      $('.name').keyup(function() {
        var Text = $(this).val();
        Text =  Text.toLowerCase().replace(/ /g,'-').replace(/[^\w-]+/g,'').replace(/(-)+/g, '-');
        $(this).val(Text);	
      }); // keyup
    }); // ready
    
    $('body').on('change', '.type', function() {
      fixType($(this));
    }); // on
    
    $('body').on('change', '.mask', function() {
      fixMask($(this));
    }); // on
    
    $('body').on('keyup', '.name', function() {
      var Text = $(this).val();
      Text =  Text.toLowerCase().replace(/ /g,'-').replace(/[^\w-]+/g,'').replace(/(-)+/g, '-');
      $(this).val(Text);	
    }); // on
    
  </script>
  
<!--fields-->
  <form method="post">
    <table id="editFields">
      <thead>
        <tr>
          <th colspan="100%"><a class="addField" href="">+</a></th>
        </tr>
      </thead>
      <tbody class="fieldList sortable">
        <?php foreach ($fields as $fieldname => $field) { ?>
        <tr>
          <td>
            <?php include($this->directories['plugin']['forms']['dir'].'/edit_fields.php'); ?>
          </td>
        </tr>
        <?php } ?>
      </tbody>
      <thead>
        <tr>
          <th colspan="100%"><a class="addField" href="">+</a></th>
        </tr>
      </thead>
  </table>
  <input type="submit" name="submit" class="submit" value="Edit fields">
  </form>
