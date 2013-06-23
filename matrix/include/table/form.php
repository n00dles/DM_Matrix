<?php
  $this->getAdminHeader('View Form', $nav);
  $allFields = $this->getSchema($_GET['table'], true);
  
  #$outputArray = array();
  #foreach ($allFields as $field=>$properties) {
    #foreach ($properties as $property) $outputArray[$field][$property] = $properties[$property];
    #$outputArray[$field]['default']     = $properties['default'];
    #$outputArray[$field]['class']  = $properties['class'];
  #}
  
  
?>

<p>This is a preview of how your form will look from the admin panel if you use the following function:</p>

<form>
  <?php $this->displayForm($_GET['table']); ?>
</form>