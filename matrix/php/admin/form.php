<!--header-->
  <h3 class="floated"><?php echo $_GET['table']; ?></h3>
  <div class="edit-nav">
    <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&auto"><?php echo i18n_r(self::FILE.'/AUTO'); ?></a>
    <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&backup"><?php echo i18n_r(self::FILE.'/BACKUP'); ?></a>
    <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&form" class="current"><?php echo i18n_r(self::FILE.'/FORM'); ?></a>
    <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&fields"><?php echo i18n_r(self::FILE.'/FIELDS'); ?></a>
    <a href="<?php echo $url; ?>&table=<?php echo $_GET['table']; ?>&view"><?php echo i18n_r(self::FILE.'/VIEW'); ?></a>
    <div class="clear"></div>
  </div>

<!--form-->  
  <p><?php echo i18n_r(self::FILE.'/PREVIEW_FORM'); ?></p>
  <p><pre><code>&lt;?php $this->displayForm($table); ?&gt;</code></pre></p>
  <form><?php $this->displayForm($_GET['table']); ?></form>