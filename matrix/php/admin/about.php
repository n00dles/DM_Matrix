<!--css-->
  <style>
    #about .edit-nav a:hover { color: <?php global $secondary_1; echo $secondary_1; ?>; }
    #about ul, #about li { margin: 0; padding: 0; list-style-type: none; }
    #about div { margin-top: -20px; }
    #about .clear { height: 15px; }
  </style>

<!--javascript-->
  <script>
    $(document).ready(function() {
      $('#about').easytabs();
    }); // ready
  </script>

<!--tabs-->
  <div id="about">
    <ul class="edit-nav">
      <li><a href="#about-tab"><?php echo i18n_r(self::FILE.'/ABOUT'); ?></a></li>
      <li><a href="#credit-tab"><?php echo i18n_r(self::FILE.'/CREDIT'); ?></a></li>
    </ul>
    <div id="about-tab">
      <h3 class="floated"><?php echo i18n_r(self::FILE.'/ABOUT'); ?></h3>
      <div class="clear"></div>
      
      <p><b>What is The Matrix?</b></p>
      
      <p>The Matrix is a plugin that exists to make plugin development easier. When building GetSimple plugins, the core problem
      for those starting out (besides learning PHP) is building the correct data structures necessary to handle the content that
      the plugin will manipulate - settings, content entries, search indexing etc...</p>
      
      <p>By adopting a pseudo SQL-styled database approach to creating and managing its data structures, The Matrix allows you to
      quickly and easily design a whole range of plugins - from blogging software, to user logins, to feedback forms and even message boards!</p>
      
      <p><b>How do I use The Matrix?</b></p>
      
      <p>From the admin panel under the main Matrix tab, you can create your tables (any number of them necessary for your specific project). By clicking on a table's
      name, you can configure its settings, add/change/remove fields (from which there are a wide variety!), add/edit/delete records and 
      (most importantly) back up your data to zip files that you can FTP download from the server at any time.</p>
      
      <p>To actually output the data onto a page, you can use PHP code (say, in a component, for instance). Instantiate 'TheMatrix' PHP class first, then call a SQL-like query and loop through the results:</p>
      
      <p><pre><code>&lt;?php
  $var = new TheMatrix;
  $results = $var->query('SELECT * FROM tablename ORDER BY fieldname ASC');
  foreach ($results as $result) {
    // formatted output
  }
?&gt;</code></pre></p>
      
      <p>Further documentation and tutorials are available at the <a href="<?php echo $this->pluginInfo('wiki'); ?>">Github Wiki</a>.</p>
      
    </div>
    <div id="credit-tab">
      <h3 class="floated"><?php echo i18n_r(self::FILE.'/CREDIT'); ?></h3>
      <div class="clear"></div>
      
      <table>
        <thead>
          <tr>
            <th colspan="100%" style="text-align: center;">PHP</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th width="100">n00dles001</th>
            <td>Original creator of The Matrix</td>
          </tr>
          <tr>
            <th>shawn_a</th>
            <td>Editor of The Matrix</td>
          </tr>
          <tr>
            <th>Angryboy</th>
            <td>Editor of The Matrix</td>
          </tr>
          <tr>
            <th>Lalit Patel</th>
            <td>Creator of the XML2Array and Array2XML classes</td>
          </tr>
          <tr>
            <th>bluelovers</th>
            <td>Creator of the sql4array class</td>
          </tr>
        </tbody>

        <thead>
          <tr>
            <th colspan="100%" style="text-align: center;">Javascript & jQuery Plugins</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th width="100">Jay Salvat</th>
            <td>Creator of markItUp!</td>
          </tr>
          <tr>
            <th>wesnolte</th>
            <td>Creator of Pajinate</td>
          </tr>
          <tr>
            <th>Ali Farhadi</th>
            <td>Creator of HTML5Sortable</td>
          </tr>
          <tr>
            <th>Sjeiti</th>
            <td>Creator of TinySort</td>
          </tr>
          <tr>
            <th>xoxco</th>
            <td>Creator of TagsInput</td>
          </tr>
          <tr>
            <th>Stefan Gabos</th>
            <td>Creator of ZebraDialog</td>
          </tr>
          <tr>
            <th>Anthony Bush</th>
            <td>Creator of FastLiveFilter</td>
          </tr>
          <tr>
            <th>Steve Schwartz</th>
            <td>Creator of EasyTabs</td>
          </tr>
        </tbody>

        <thead>
          <tr>
            <th colspan="100%" style="text-align: center;">Graphics</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <th width="100">FamFam</th>
            <td>Icons used in plugin and markItUp editor</td>
          </tr>
        </tbody>
      </table>
      
    </div>
  </div>