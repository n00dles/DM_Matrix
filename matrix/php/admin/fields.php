<?php
  if ($_SERVER['REQUEST_METHOD']=='POST') {
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
    .advanced, .dropdown, .dropdowncustom, .imageupload { display: none; }
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
  </table>
  <input type="submit" name="submit" class="submit" value="Edit fields">
  </form>
