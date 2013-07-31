<!--header-->
  <h3 class="floated"><?php echo $_GET['table']; ?>: <?php echo $_GET['edit']; ?></h3>
  <div class="edit-nav">
    <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&view"><?php echo i18n_r(self::FILE.'/BACK'); ?></a>
    <a href="#" id="metadata_toggle" accesskey="n"><?php echo i18n_r(self::FILE.'/OPTIONS'); ?></a>
    <div class="clear"></div>
  </div>
  
<!--javascript-->
  <script>
    $(document).ready(function() {
      $('#metadata_window').hide();
    }); // ready
  </script>

<!--form-->
  <form method="post" enctype="multipart/form-data">
  <?php 
    // POST query
    if ($_SERVER['REQUEST_METHOD']=='POST') {
      $update    = $this->updateRecord($_GET['table'], $_GET['edit'], $_POST);
      if ($update) $this->getAdminError(i18n_r(self::FILE.'/UPDATE_SUCCESS'), true);
      else         $this->getAdminError(i18n_r(self::FILE.'/UPDATE_ERROR'), false);
    }
    
    // form
    $this->displayForm($_GET['table'], $_GET['edit']);
  ?>
  <input type="submit" class="submit" name="post-submitform"/>
</form>