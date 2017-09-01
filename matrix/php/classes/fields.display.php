<?php
/* class for handling field inputs
 */

use TheMatrix\View; 

class MatrixDisplayField {
  /* constants */
  /* properties */
  private $matrix;
  private $schema;
  private $name;
  private $type;
  private $method;
  private $paths = array();
  private $properties;
  private $value;
  
  /* methods */
  # constructor
  public function __construct($matrix, $schema=array(), $value=null, $paths=array()) {
    // initialize
    $this->matrix = $matrix;
    $this->schema = $schema;
    $this->name   = $schema['name'];
    $this->id     = 'post-'.$this->name;
    $this->type   = $schema['type'];
    if (is_string($value)) {
      $this->value  = (strlen($value) > 0) ? $value : $schema['default'];
    }
    else $this->value = $value;
    $this->paths  = $paths;
    
    // backwards compatibility
    if (!isset($schema['mask'])) $schema['mask'] = null;
    
    // get the correct method name
    $this->method = $schema['type'] . (!empty($schema['mask']) ? '_'.$schema['mask'] : '') ;
    
    // fill in properties
    $this->properties .= 'style="';
    $this->properties .= !empty($schema['width'])? 'width: '.$schema['width'].'; ' : '';
    $this->properties .= !empty($schema['height'])? 'height: '.$schema['height'].'; ' : '';
    $this->properties .= !empty($schema['style'])? $schema['style'] : '';
    $this->properties .= '" ';
    $this->properties .= 'name="post-'.$schema['name'].'" ';
    $this->properties .= 'id="post-'.$schema['name'].'" ';
    $this->properties .= 'placeholder="'.$schema['placeholder'].'" ';
    $this->properties .= $schema['maxlength'] > 0 ? 'maxlength="'.$schema['maxlength'].'" ' : '';
    $this->properties .= strlen($schema['max']) > 0 ? 'max="'.$schema['max'].'" ' : '';
    $this->properties .= strlen($schema['min']) > 0 ? 'min="'.$schema['min'].'" ' : '';
    $this->properties .= strlen($schema['step']) > 0 ? 'step="'.$schema['step'].'" ' : '';
    $this->properties .= !empty($schema['readonly']) ? 'readonly="readonly" ' : '';
    $this->properties .= !empty($schema['required']) ? 'required="required" ' : '';
    $this->properties .= !empty($schema['validation']) ? 'pattern="'.$schema['validation'].'" ' : '';
  }
  
  /* functions needed for parsing */
  # get keys for multi-based fields
  private function get_multi_keys($rows) {
    if (is_numeric($rows)) {
      $keys = array_fill(0, $rows, 'key');
    }
    else {
      $keys = $this->matrix->explodeTrim("\n", $rows);
      $keys = array_fill_keys($keys, 'key');
    }
    return $keys;
  }
  
  /* inputs */
  # input (text)
  private function input()
  {
    $view = new View('fields/input');
    
    echo $view->render(['value' => $this->value, 'properties' => $this->properties]);
  }
  
  # input (textlong)
  private function input_long()
  {
    $view = new View('fields/input_long');
    
    echo $view->render(['value' => $this->value, 'properties' => $this->properties]);
  }
  
  # input (slug)
  private function input_slug()
  {
    $view = new View('fields/input_slug');
    
    echo $view->render([
      'value'      => $this->value,
      'properties' => $this->properties,
      'selector'   => '#' . $this->id
    ]);
  }
  
  # password
  private function input_password() {
    ?><input type="password" class="text" value="" <?php echo $this->properties; ?>/><?php   
  }
  
  # url
  private function input_url() {
    ?><input type="url" class="text" value="<?php echo $this->value; ?>" <?php echo $this->properties; ?>/><?php   
  }
  
  # email
  private function input_email() {
    ?><input type="email" class="text" value="<?php echo $this->value; ?>" <?php echo $this->properties; ?>/><?php   
  }
  
  # int
  private function input_number() {
    ?><input type="number" class="text" value="<?php echo $this->value; ?>" <?php echo $this->properties; ?>/><?php   
  }
  
  # range
  private function input_range() {
    ?><input type="range" class="text" value="<?php echo $this->value; ?>" <?php echo $this->properties; ?>/><?php   
  }
  
  # color
  private function input_color() {
    ?><input type="text" class="text color" value="<?php echo $this->value; ?>" <?php echo $this->properties; ?>/><?php   
  }
  
  # multi (text)
  private function multi_text() {
    ?>
    <span class="multi_text">
      <?php
        $this->schema['desc'] = $this->matrix->explodeTrim("\n", $this->schema['desc']);
        $options = $this->matrix->explodeTrim("\n", $this->schema['options']);
        $keys    = $this->get_multi_keys($this->schema['rows']);
        $labels  = !empty($this->schema['labels']) ? $this->matrix->explodeTrim("\n", $this->schema['labels']) : array();
        $values = explode_trim("\n", $this->value);
        
        $s = 0;
        foreach ($keys as $i => $val) {
          // value
          $value = 0;
          if (isset($values[$i])) {
            $value = $values[$i];
          }
          elseif (isset($values[$s])) {
            $value = $values[$s];
          }
      ?>
        <?php if (!empty($labels) && isset($labels[$s])) { ?><label><?php echo $labels[$s]; ?> : </label><?php } ?>
        <input type="text" value="<?php echo $value; ?>" style="margin-bottom: 4px;" name="post-<?php echo $this->name; ?>[]" <?php echo $this->properties; ?> class="text textmulti" value="<?php if (isset($options[$i])) echo $options[$i]; ?>" placeholder="<?php if (isset($this->schema['desc'][$i])) echo $this->schema['desc'][$i]; ?>" <?php echo $this->schema['readonly']; ?> <?php echo $this->schema['required']; ?> <?php if (strlen(trim($this->schema['validation']))>0) echo 'pattern="'.$this->schema['validation'].'"'; ?> <?php if (strlen(trim($this->schema['validation']))>0) echo 'pattern="'.$this->schema['validation'].'"'; ?>/>
      <?php
          $s++;
        } ?>
    </span>
    <?php
  }
  
  # multiple colors
  private function multi_color() {
    ?>
    <span class="multi_color">
      <?php
        $this->schema['desc'] = $this->matrix->explodeTrim("\n", $this->schema['desc']);
        $options = $this->matrix->explodeTrim("\n", $this->schema['options']);
        $keys = $this->get_multi_keys($this->schema['rows']);
        $labels = !empty($this->schema['labels']) ? $this->matrix->explodeTrim("\n", $this->schema['labels']) : array();
        $values = explode_trim("\n", $this->value);
        
        $s = 0;
        foreach ($keys as $i => $val) {
          // value
          $value = 0;
          if (isset($values[$i])) {
            $value = $values[$i];
          }
          elseif (isset($values[$s])) {
            $value = $values[$s];
          }
      ?>
        <?php if (!empty($labels) && isset($labels[$s])) { ?><label><?php echo $labels[$s]; ?> : </label><?php } ?>
        <input type="text" class="text color" value="<?php echo $value; ?>" style="margin-bottom: 4px;" name="post-<?php echo $this->name; ?>[]" <?php echo $this->properties; ?> class="text textmulti" value="<?php if (isset($options[$i])) echo $options[$i]; ?>" placeholder="<?php if (isset($this->schema['desc'][$i])) echo $this->schema['desc'][$i]; ?>" <?php echo $this->schema['readonly']; ?> <?php echo $this->schema['required']; ?> <?php if (strlen(trim($this->schema['validation']))>0) echo 'pattern="'.$this->schema['validation'].'"'; ?> <?php if (strlen(trim($this->schema['validation']))>0) echo 'pattern="'.$this->schema['validation'].'"'; ?>/>
      <?php
        $s++;
        } ?>
    </span>
    <?php
  }
  
  # multi (numeric)
  private function multi_number() {
    ?>
    <span class="multi_number">
      <?php
        $this->schema['desc'] = $this->matrix->explodeTrim("\n", $this->schema['desc']);
        $options = $this->matrix->explodeTrim("\n", $this->schema['options']);
        $keys = $this->get_multi_keys($this->schema['rows']);
        $labels = !empty($this->schema['labels']) ? $this->matrix->explodeTrim("\n", $this->schema['labels']) : array();
        $values = explode_trim("\n", $this->value);
        
        $s = 0;
        foreach ($keys as $i => $val) {
          // value
          $value = 0;
          if (isset($values[$i])) {
            $value = $values[$i];
          }
          elseif (isset($values[$s])) {
            $value = $values[$s];
          }
      ?>
        <?php if (!empty($labels) && isset($labels[$s])) { ?><label><?php echo $labels[$s]; ?> : </label><?php } ?>
        <input type="number" value="<?php echo $value; ?>" style="margin-bottom: 4px;" name="post-<?php echo $this->name; ?>[]" <?php echo $this->properties; ?> class="text textmulti" value="<?php if (isset($options[$i])) echo $options[$i]; ?>" placeholder="<?php if (isset($this->schema['desc'][$i])) echo $this->schema['desc'][$i]; ?>" <?php echo $this->schema['readonly']; ?> <?php echo $this->schema['required']; ?> <?php if (strlen(trim($this->schema['validation']))>0) echo 'pattern="'.$this->schema['validation'].'"'; ?> <?php if (strlen(trim($this->schema['validation']))>0) echo 'pattern="'.$this->schema['validation'].'"'; ?>/>
      <?php
          $s++;
        } ?>
    </span>
    <?php
  }
  
  # multi (textarea)
  private function multi_textarea() {
    ?>
    <span class="multi_textarea">
      <?php
        $this->schema['desc'] = $this->matrix->explodeTrim("\n", $this->schema['desc']);
        $options = $this->matrix->explodeTrim("\n", $this->schema['options']);
        $keys    = $this->get_multi_keys($this->schema['rows']);
        $labels  = !empty($this->schema['labels']) ? $this->matrix->explodeTrim("\n", $this->schema['labels']) : array();
        $values = explode_trim("\n", $this->value);
        
        $s = 0;
        foreach ($keys as $i => $val) {
          // value
          $value = 0;
          if (isset($values[$i])) {
            $value = $values[$i];
          }
          elseif (isset($values[$s])) {
            $value = $values[$s];
          }
      ?>
        <?php if (!empty($labels) && isset($labels[$s])) { ?><label><?php echo $labels[$s]; ?> : </label><?php } ?>
        <textarea class="text" name="post-<?php echo $this->name; ?>[]" <?php echo $this->properties; ?>><?php echo $value; ?></textarea>
      <?php
          $s++;
        } ?>
    </span>
    <?php
  }
  
  # multi (rte)
  private function multi_rte() {
    ?>
    <script>
      $(document).ready(function() {
        $('.<?php echo $this->id; ?>').jqte();
      }); // ready
    </script>
    <span class="multi_rte">
      <?php
        $this->schema['desc'] = $this->matrix->explodeTrim("\n", $this->schema['desc']);
        $options = $this->matrix->explodeTrim("\n", $this->schema['options']);
        $keys    = $this->get_multi_keys($this->schema['rows']);
        $labels  = !empty($this->schema['labels']) ? $this->matrix->explodeTrim("\n", $this->schema['labels']) : array();
        $values = explode_trim("\n", $this->value);
        
        $s = 0;
        foreach ($keys as $i => $val) {
          // value
          $value = 0;
          if (isset($values[$i])) {
            $value = $values[$i];
          }
          elseif (isset($values[$s])) {
            $value = $values[$s];
          }
      ?>
        <?php if (!empty($labels) && isset($labels[$s])) { ?><label><?php echo $labels[$s]; ?> : </label><?php } ?>
        <textarea class="text <?php echo $this->id; ?>" name="<?php echo $this->id; ?>[]" <?php echo $this->properties; ?>><?php echo $value; ?></textarea>
      <?php
          $s++;
        } ?>
    </span>
    <?php
  }
  
  # multi (code)
  private function multi_code() {
    $this->schema['desc'] = $this->matrix->explodeTrim("\n", $this->schema['desc']);
    $options = $this->matrix->explodeTrim("\n", $this->schema['options']);
    $keys    = $this->get_multi_keys($this->schema['rows']);
    $labels  = !empty($this->schema['labels']) ? $this->matrix->explodeTrim("\n", $this->schema['labels']) : array();
    $values = explode_trim("\n", $this->value);
    ?>
    <script>
      <?php for ($s = 0; $s < count($keys); $s++ ) { ?>
      editAreaLoader.init({
        id: '<?php echo $this->id; ?>_<?php echo $s; ?>',
        start_highlight: true,
        allow_resize: "both",
        allow_toggle: true,
        word_wrap: true,
        language: "en",
        syntax: "php"	
      });
      <?php } ?>
    </script>
    <span class="multi_code">
      <?php
        $s = 0;
        foreach ($keys as $i => $val) {
          // value
          $value = 0;
          if (isset($values[$i])) {
            $value = $values[$i];
          }
          elseif (isset($values[$s])) {
            $value = $values[$s];
          }
      ?>
        <?php if (!empty($labels) && isset($labels[$s])) { ?><label><?php echo $labels[$s]; ?> : </label><?php } ?>
        <textarea class="text" id="<?php echo $this->id.'_'.$s; ?>" name="<?php echo $this->id; ?>[]" <?php echo $this->properties; ?>><?php echo $value; ?></textarea>
      <?php
          $s++;
        } ?>
    </span>
    <?php
  }
  
  # date
  private function date() {
    ?><input type="date" class="text" value="<?php echo $this->value; ?>" <?php echo $this->properties; ?>/><?php   
  }
  
  # time
  private function date_time() {
    if (empty($this->value)) $this->value = time();
    $timestamp = (is_numeric($this->value)) ? $this->value : strtotime($this->value);
    $value = date('H:i:s', $timestamp);
    ?><input type="time" class="text" value="<?php echo $value; ?>" <?php echo $this->properties; ?>/><?php   
  }
  
  # datetimelocal
  private function date_timelocal() {
    if (empty($this->value)) $this->value = time();
    $timestamp = (is_numeric($this->value)) ? $this->value : strtotime($this->value);
    $value = date('Y-m-d\TH:i', $timestamp);
    ?><input type="datetime-local" class="text" value="<?php echo $value; ?>" <?php echo $this->properties; ?>/><?php   
  }
  
  # week
  private function date_week() {
    if (empty($this->value)) $this->value = time();
    $timestamp = (is_numeric($this->value)) ? $this->value : strtotime($this->value);
    $value = date('Y-\WW', $timestamp);
    ?><input type="week" class="text" value="<?php echo $value; ?>" <?php echo $this->properties; ?>/><?php   
  }

  # month
  private function date_month() {
    if (empty($this->value)) $this->value = time();
    $timestamp = (is_numeric($this->value)) ? $this->value : strtotime($this->value);
    $value = date('Y-m-d\TH:i', $timestamp);
    $value = date('Y-m', $timestamp);
    ?><input type="month" class="text" value="<?php echo $value; ?>" <?php echo $this->properties; ?>/><?php   
  }
  
  /* textareas */
  # textarea
  private function textarea() {
    ?><textarea class="text" <?php echo $this->properties; ?>><?php echo $this->value; ?></textarea><?php
  }
  
  # tags
  private function textarea_tags() {
    ?>
    <script type="text/javascript">
      $(document).ready(function() {
        $("#<?php echo $this->id; ?>").tagsInput();
      });
    </script>
    <textarea <?php echo $this->properties; ?>><?php echo $this->value; ?></textarea>
    <?php
  }
  
  # bbcode
  private function textarea_bbcode() {
    ?>
    <script language="javascript">
      $(document).ready(function()  {
        $("#<?php echo $this->id; ?>").markItUp(GSBBCodeSettings);
      });
    </script>
    <textarea <?php echo $this->properties; ?>><?php echo $this->value; ?></textarea>
    <?php
  }
  
  # wiki
  private function textarea_wiki() {
    ?>
    <script language="javascript">
      $(document).ready(function()  {
        $("#<?php echo $this->id; ?>").markItUp(GSWikiSettings);
      });
    </script>
    <textarea <?php echo $this->properties; ?>><?php echo $this->value; ?></textarea>
    <?php
  }
  
  # markdown
  private function textarea_markdown() {
    ?>
    <script language="javascript">
      $(document).ready(function()  {
        $("#<?php echo $this->id; ?>").markItUp(GSMarkDownSettings);
      });
    </script>
    <textarea <?php echo $this->properties; ?>><?php echo $this->value; ?></textarea>
    <?php
  }
  
  # wysiwyg
  private function textarea_wysiwyg() {
    ?>
    <style>
      div.<?php echo $this->id; ?> .jqte_editor {
        min-height: <?php echo $this->schema['height'] ?>;
      }
    </style>
    <script type="text/javascript">
      $(document).ready(function() {
        $('textarea.<?php echo $this->id; ?>').jqte({
          placeholder: <?php echo json_encode($this->schema['placeholder']); ?>
        });
      }); // ready
    </script>
    <div class="<?php echo $this->id; ?>">
      <textarea class="<?php echo $this->id; ?>" <?php echo $this->properties; ?>><?php echo $this->value; ?></textarea>
    </div>
    <?php
  }
  
  # code editor
  private function textarea_code() {
    // set up parameters
    $params = array();
    $params['value'] = $this->value;
    $params['properties'] = $this->properties;
    $params['id'] = $this->id;
    
    // output editor
    $this->matrix->getEditor($params);
  }
  
  # dropdown
  private function dropdown() {
    $options = $this->matrix->getOptions($this->schema['options']);
    ?>
      <select class="text" <?php echo $this->properties; ?>>
        <?php foreach ($options as $key => $option) { ?>
          <option value="<?php echo $key; ?>" <?php if ($key == $this->value) echo 'selected="selected"'; ?> ><?php echo $option; ?></option>
        <?php } ?>
      </select>
    <?php
  }
  
  # dropdown for tables
  private function dropdown_table() {
    if ($this->matrix->fieldExists($this->schema['table'], $this->schema['row'])) {
      $fields = 'id' . (($this->schema['row'] != 'id') ? ', '.$this->schema['row'] : '');
      $query = $this->matrix->query('SELECT '.$fields.' FROM '.$this->schema['table'].' ORDER BY id ASC');
    }
    else $query = array();
    ?>
    <select class="text" <?php echo $this->properties; ?>>
      <?php foreach ($query as $record) { ?>
        <option value="<?php echo $record['id']; ?>" <?php if ($record['id'] == $this->value) echo 'selected="selected"'; ?> ><?php echo $record[$this->schema['row']]; ?></option>
      <?php } ?>
    </select>
    <?php
  }
  
  # dropdown with hierarchy
  private function dropdown_hierarchy() {
    $options = $this->matrix->getHierarcalOptions($this->schema['options']);
    ?>
      <select class="text" <?php echo $this->properties; ?>>
        <?php foreach ($options as $option) { ?>
          <option value="<?php echo $option['value']; ?>" <?php if ($option['value'] == $this->value) echo 'selected="selected"'; ?> ><?php echo $option['option']; ?></option>
        <?php } ?>
      </select>
    <?php
  }
  
  # pages
  private function dropdown_pages() {
    getPagesXmlValues();
    global $pagesArray;
    $pages = $pagesArray;
    ?>
    <select class="text" <?php echo $this->properties; ?>>
      <?php foreach ($pages as $slug => $properties) { ?>
        <option value="<?php echo $slug; ?>" <?php if ($slug == $this->value) echo 'selected="selected"'; ?> ><?php echo $properties['title']; ?></option>
      <?php } ?>
    </select>
    <?php
  }
  
  # users
  private function dropdown_users() {
    $users = $this->matrix->getUsers();
    ?>
    <select class="text" <?php echo $this->properties; ?>>
      <?php foreach ($users as $user => $details) { ?>
        <option value="<?php echo $user; ?>" <?php if ($user == $this->value || (empty($this->value) && $user == $_COOKIE['GS_ADMIN_USERNAME'])) echo 'selected="selected"'; ?> ><?php if (empty($details['NAME'])) echo $user; else echo $details['NAME']; ?></option>
      <?php } ?>
    </select>
    <?php
  }
  
  # components
  private function dropdown_components() {
    $components = $this->matrix->getComponents();
    ?>
    <select class="text" <?php echo $this->properties; ?>>
      <?php foreach ($components as $slug => $component) { ?>
        <option value="<?php echo $slug; ?>" <?php if ($slug == $this->value) echo 'selected="selected"'; ?> ><?php echo $component['title']; ?></option>
      <?php } ?>
    </select>
    <?php
  }
  
  # template
  private function dropdown_template() {
    // load templates for current theme
    $templates = glob(GSTHEMESPATH.$this->paths['template'].'/*.php');
    
    // unset 'functions.php' and '*.inc.php'
    foreach ($templates as $key => $template) {
      $tmp = explode('/', $template);
      $templates[$key] = $template = end($tmp);
      if (
        strtolower($template) == 'functions.php' ||
        substr($template, -7, 7) == 'inc.php'
      ) {
        unset($templates[$key]);
      }
    }
    sort($templates);
    ?>
    <select class="text" <?php echo $this->properties; ?>>
      <?php foreach ($templates as $template) { ?>
        <option value="<?php echo $template; ?>" <?php if ($template == $this->value) echo 'selected="selected"'; ?> ><?php echo $template; ?></option>
      <?php } ?>
    </select>
    <?php
  }
  
  # themes
  private function dropdown_themes() {
    $themes = $this->matrix->getThemes();
    ?>
    <select class="text" <?php echo $this->properties; ?>>
      <?php foreach ($themes as $theme) { ?>
        <option value="<?php echo $theme; ?>" <?php if ($theme == $this->value || (empty($this->value)) && $theme == $this->paths['template']) echo 'selected="selected"'; ?> ><?php echo $theme; ?></option>
      <?php } ?>
    </select>
    <?php
  }
  
  # picker
  private function picker() {
  }
  # upload
  private function upload() {
  }
  
  # radio
  private function radio() {
    $selected = $this->matrix->getOptions($this->value);
    $options = $this->matrix->getOptions($this->schema['options']);
    ?>
    <span class="radio">
    <?php foreach ($options as $key => $option) { ?>
      <input type="radio" <?php echo $this->properties; ?> name="<?php echo $this->id; ?>[<?php echo $key; ?>]" <?php if (in_array($option, $selected)) echo 'checked="checked"'; ?> class="input"/> <span class="option"><?php echo $option; ?></span><br />
    <?php } ?>
    </div>
    <?php
  }

  # checkbox
  private function options_checkbox() {
    // force value to be an array
    if (!is_array($this->value)) $this->value = array($this->value);
    
    // load options and values
    $selected = array_map('trim', $this->value);
    $options = $this->matrix->getOptions($this->schema['options']);
    ?>
    <span class="checkbox">
    <?php foreach ($options as $key => $option) { ?>
      <input type="checkbox" class="input" name="<?php echo $this->id; ?>[]" value="<?php echo $key; ?>" <?php echo $this->properties; ?> <?php if (in_array($key, $selected)) echo 'checked="checked"'; ?>/> <span class="option"><?php echo $option; ?></span><br />
    <?php } ?>
    </span>
    <?php
  }
  
  # radio
  private function options_radio() {
    $selected = $this->matrix->getOptions($this->value);
    $options = $this->matrix->getOptions($this->schema['options']);
    ?>
    <span class="radio">
    <?php foreach ($options as $key => $option) { ?>
      <input type="radio" class="input" <?php echo $this->properties; ?> value="<?php echo $key; ?>" <?php if ($key == $this->value) echo 'checked="checked"'; ?>/> <span class="option"><?php echo $option; ?></span><br />
    <?php } ?>
    </span>
    <?php
  }
  
  # multiple select
  private function options_selectmulti() {
    // force value to be an array
    if (!is_array($this->value)) $this->value = array($this->value);
    
    // load options and values
    $selected = array_map('trim', $this->value);
    $options = $this->matrix->getOptions($this->schema['options']);
    ?>
    <select class="text" name="<?php echo $this->id; ?>[]" <?php echo $this->properties; ?> multiple>
      <?php foreach ($options as $key => $option) { ?>
        <option value="<?php echo $key; ?>"<?php if ($key == $this->value) echo 'selected="selected"'; ?> <?php if (in_array($key, $selected)) echo 'selected="selected"'; ?>><?php echo $option; ?></option>
      <?php } ?>
    </select>
    <?php
  }
  
  # upload image (for admins)
  private function upload_imageadmin() {
    ?>
    <input type="file" class="text imageuploadadmin DM_imageuploadadmin" style="margin: 0 0 10px 0 !important;" name="post-<?php echo $this->name; ?>" disabled/>
    <select class="text imageuploadadmin DM_imageuploadadmin " <?php echo $this->properties; ?>>
      <option value="">--no file--</option>
      <option value="upload">--upload--</option>
      <?php
        $images = glob(GSDATAUPLOADPATH.$this->schema['path'].'*.*');
        $thumbs = glob(GSTHUMBNAILPATH.$this->schema['path'].'*.*');
        foreach ($images as $image) {
          $tmp = explode('/', $image);
          $file = end($tmp);
      ?>
      <option value="<?php echo $file; ?>" <?php if ($file == $this->value) echo 'selected="selected"'; ?>><?php echo $file; ?></option>
      <?php } ?>
    </select>
    <script>
      $(document).ready(function(){
        $('input.imageuploadadmin').hide();
        $('select.imageuploadadmin').change(function(){
          if ($(this).val() == 'upload') {
            $(this).prev('input.imageuploadadmin').slideDown().prop('disabled', false);
          }
          else {
            $(this).prev('input.imageuploadadmin').slideUp().prop('disabled', true);
          }
        }); // change
      }); // ready
    </script>
    <?php
  }
  
  # image picker
  private function picker_image() {
    ?>
    <input class="text imagepicker" type="text" <?php echo $this->properties; ?>/>
    <span class="edit-nav"><a id="browse-<?php echo $this->name; ?>" href="javascript:void(0);">Browse</a></span>
    <script type="text/javascript">
      $(function() { 
        $('#browse-<?php echo $this->name; ?>').click(function(e) {
          window.open('<?php echo $this->matrix->getSiteURL().'admin/filebrowser.php?CKEditorFuncNum=1&func=addImageThumbNail&returnid=post-'.$this->name.'type=images'; ?>', 'browser', 'width=800,height=500,left=100,top=100,scrollbars=yes');
        });
      });
    </script>
    <?php
  }
  
  # file picker
  private function picker_file() {
    ?>
    <input class="text filepicker" type="text" <?php echo $this->properties; ?>/>
    <span class="edit-nav"><a id="browse-<?php echo $this->name; ?>" href="javascript:void(0);">Browse</a></span>
    <script type="text/javascript">
      $(function() { 
        $('#browse-<?php echo $this->name; ?>').click(function(e) {
          window.open('<?php echo $this->matrix->getSiteURL().'admin/filebrowser.php?CKEditorFuncNum=1&returnid=post-'.$this->name.'type=all'; ?>', 'browser', 'width=800,height=500,left=100,top=100,scrollbars=yes');
        });
      });
    </script>
    <?php
  }
  
  # display
  public function display($params=array()) {
    // description
    if (!empty($this->schema['desc'])) {
      ?><span class="description"><?php echo $this->schema['desc']; ?></span><?php
    }
    // field
    if (method_exists(get_class($this), $this->method)) {
      $method = $this->method;
    }
    elseif (method_exists(get_class($this), $this->type)) {
      $method = $this->type;
    }
    else $method = 'input';
    return call_user_func_array(array($this, $method), $params);
  }
}

?>