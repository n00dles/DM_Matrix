<?php
  // header
  $this->getAdminHeader('Usage', $nav);
?>

<table>
  <tr>
    <th>Editing files</th>
  </tr>
  <tr>
    <td>
      <p>The example below lets you show and edit a file with CodeMirror (this example is actually the schema for your current installation of The Matrix).<p>
      <pre><code>&lt;?php
  $ext = new TheMatrixExtended;
  $ext->editFileForm('data/other/matrix/schema.xml', 'example', $denyDirect=false);
  if ($_SERVER['REQUEST_METHOD']=='POST') {
    $ext->saveFile($_POST['example'], 'data/other/matrix/schema.xml', $denyDirect=false);
  }
?&gt;</code></pre>
    </td>
  </tr>
</table>



<p>
  <?php
    $ext = new TheMatrixExtended;
    $ext->editFileForm('data/other/matrix/schema.xml', 'example', $denyDirect=false);
  ?>
</p>