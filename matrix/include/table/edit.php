<?php $this->getAdminHeader('Edit Record', $nav); ?>

<form method="post" enctype="multipart/form-data">
  <?php 
    // POST query
    if ($_SERVER['REQUEST_METHOD']=='POST') {
      $update    = $this->updateRecord($_GET['table'], $_GET['edit'], $_POST);
      if ($update) $this->getAdminError('Record updated successfully', true);
      else         $this->getAdminError('Record not updated successfully', false);
    }
    
    // form
    $this->displayForm($_GET['table'], $_GET['edit']);
  ?>
  <input type="submit" class="submit" name="post-submitform"/>
</form>

<?php
  
?>