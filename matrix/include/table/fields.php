<?php
  $this->getAdminHeader(i18n_r('matrix/DM_EDITING_FIELD'), $nav);

  if ($_SERVER['REQUEST_METHOD']=='POST') {
    $update    = $this->buildFields($_GET['table'], $_POST);
    if ($update) $this->getAdminError('Fields updated successfully', true);
    else         $this->getAdminError('Fields not updated successfully', false);
  } 
    
  $fields = $this->schemaArray[$_GET['table']]['fields'];
  unset($fields['id']); 
?>
  
  <style>
    .advanced, .dropdown, .dropdowncustom, .imageupload { display: none; }
  </style>
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
      
      foreach ($this->fieldProperties as $key=>$property) {
        $field[$key] = $property['default'];
      }
      include(MATRIXPATH.'/include/forms/edit_fields.php');
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
  
  <form method="post">
    <table id="editFields">
      <thead>
        <tr>
          <th colspan="100%"><a class="addField" href="">+</a></th>
        </tr>
      </thead>
      <tbody class="fieldList sortable">
        <?php foreach ($fields as $fieldname=>$field) { ?>
        <tr>
          <td>
            <?php include(MATRIXPATH.'/include/forms/edit_fields.php'); ?>
          </td>
        </tr>
        <?php } ?>
      </tbody>
  </table>
  <input type="submit" name="submit" class="submit" value="Edit fields">
  </form>
