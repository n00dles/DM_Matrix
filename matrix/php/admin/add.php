<!--header-->
  <h3 class="floated"><?php echo $_GET['table']; ?>: <?php echo i18n_r(self::FILE.'/ADD'); ?></h3>
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
  <form method="post" enctype="multipart/form-data" action="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&view">
    <?php $this->displayForm($_GET['table']); ?>
    <input type="submit" class="submit" name="post-submitform"/>
  </form>