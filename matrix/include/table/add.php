<?php $this->getAdminHeader(i18n_r('matrix/DM_ADD_RECORD_BUTTON'), $nav); ?>

<form method="post" enctype="multipart/form-data" action="load.php?id=<?php echo MATRIX; ?>&table=<?php echo $_GET['table']; ?>&view">
  <?php $this->displayForm($_GET['table']); ?>
  <input type="submit" class="submit" name="post-submitform"/>
</form>