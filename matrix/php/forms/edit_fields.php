<label><?php echo $field['name']; ?><span class="right"><a href="" class="openAdvanced cancel">more</a> <a href="" class="removeField cancel">x</a></span></label>
<div class="clear"></div>

<div id="metadata_window">
  <div class="basic">
    <div class="leftopt">
      <p>
        <input class="hidden" name="oldname[]" value="<?php echo $field['name']; ?>">
        <input class="text" name="name[]" placeholder="<?php echo i18n_r(self::FILE.'/NAME'); ?>" value="<?php echo $field['name']; ?>">
      </p>
    </div>
    <div class="rightopt">
      <p>
        <select class="text type" name="type[]">
        <?php foreach ($this->fields['type'] as $type => $properties) { ?>
           <option value="<?php echo $type; ?>" <?php if ($field['type'] == $type) echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/'.strtoupper($type).'_FIELD'); ?></option>
        <?php } ?>
        </select>
      </p>
    </div>
    <div class="clear"></div>
  </div>
  <div class="advanced">
    <div class="leftopt">
      <p><input class="text" name="label[]" placeholder="<?php echo i18n_r(self::FILE.'/LABEL'); ?>" value="<?php echo $field['label']; ?>"/></p>
      <p>
        <textarea class="text" name="desc[]" placeholder="<?php echo i18n_r(self::FILE.'/DESCRIPTION'); ?>"><?php echo $field['desc']; ?></textarea>
      </p>
      <p>
        <textarea class="text" name="default[]" placeholder="<?php echo i18n_r(self::FILE.'/DEFAULT'); ?>"><?php echo $field['default']; ?></textarea>
      </p>
      <p><textarea class="text" name="other[]" placeholder="<?php echo i18n_r(self::FILE.'/OTHER'); ?>"/><?php echo $field['other']; ?></textarea></p>
      <p><input class="text" name="maxlength[]" placeholder="<?php echo i18n_r(self::FILE.'/MAX_LENGTH'); ?>" value="<?php echo $field['maxlength']; ?>"/></p>
    </div>
    <div class="rightopt">
      <div class="showOptions dropdown">
      <p>
        <select class="text tableName" name="table[]">
          <?php foreach ($this->schemaArray as $table => $properties) { ?>
          <option value="<?php echo $table; ?>" <?php if (isset($field['table']) && $field['table'] == $table) echo 'selected="selected"'; ?>><?php echo $table; ?></option>
          <?php } ?>
        </select>
      </p>
      <p><input class="text tableRow" name="row[]" value="<?php echo $field['row']; ?>" placeholder="<?php echo i18n_r(self::FILE.'/COLUMN'); ?>"/></p>
      </div>
      
      <div class="showOptions dropdowncustom dropdowncustomkey checkbox dropdownhierarchy imageuploadadmin">
      <p>
        <textarea class="text" name="options[]"><?php echo $field['options']; ?></textarea>
      </p>
      </div>
      
      <div class="showOptions imageuploadadmin">
        <p><input class="text uploadpath" name="path[]" placeholder="<?php if (isset($_GET['table'])) echo $_GET['table']; ?>/" value="<?php echo $field['path']; ?>"/></p>
      </div>
      
      <div class="showOptions textmulti intmulti">
        <p><input class="text rows" name="rows[]" placeholder="Number of fields to show" value="<?php echo $field['rows']; ?>"/></p>
      </div>
      
      <p><input class="text" name="size[]" placeholder="Field size" value="<?php echo $field['size']; ?>"/></p>
      <p>
        <select class="text" name="cacheindex[]">
          <option value="1" <?php if ($field['cacheindex'] == 1) echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/CACHE');?></option>
          <option value="0" <?php if ($field['cacheindex'] == 0) echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/CACHE_NO');?></option>
        </select>
      </p>
      <p>
        <select class="text" name="visibility[]">
          <option value="1" <?php if ($field['visibility'] == 1) echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/VISIBLE_FORMYES');?></option>
          <option value="0" <?php if ($field['visibility'] == 0) echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/VISIBLE_FORMNO');?></option>
        </select>
      </p>
      <p>
        <select class="text" name="tableview[]">
          <option value="1" <?php if ($field['tableview'] == 1) echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/VISIBLE_TABLEYES');?></option>
          <option value="0" <?php if ($field['tableview'] == 0) echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/VISIBLE_TABLENO');?></option>
        </select>
      </p>
      <p>
        <select class="text" name="class[]">
          <option value="" <?php if ($field['class'] == '') echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/NORMAL');?></option>
          <option value="leftopt" <?php if ($field['class'] == 'leftopt') echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/METADATA_LEFT');?></option>
          <option value="rightopt" <?php if ($field['class'] == 'rightopt') echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/METADATA_RIGHT');?></option>
          <option value="leftsec" <?php if ($field['class'] == 'leftsec') echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/LEFT');?></option>
          <option value="rightsec" <?php if ($field['class'] == 'rightsec') echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/RIGHT');?></option>
        </select>
      </p>
      <p>
        <select class="text" name="readonly[]">
          <option value="readonly" <?php if ($field['readonly'] == 'readonly') echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/READONLY'); ?></option>
          <option value="" <?php if ($field['readonly'] == '') echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/WRITEABLE'); ?></option>
        </select>
      </p>
      <p>
        <select class="text" name="required[]">
          <option value="required" <?php if ($field['required'] == 'required') echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/REQUIRED'); ?></option>
          <option value="" <?php if ($field['required'] == '') echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/REQUIRED_NO'); ?></option>
        </select>
      </p>
      <p>
        <input type="text" class="text" name="validation[]" value="<?php echo $field['validation']; ?>" placeholder="<?php echo i18n_r(self::FILE.'/VALIDATION'); ?>"/>
      </p>
      <p>
        <select class="text" name="index[]">
          <option value="1" <?php if ($field['index'] == 1) echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/INDEX');?></option>
          <option value="0" <?php if ($field['index'] == 0) echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/INDEX_NO');?></option>
        </select>
      </p>
    </div>
    <div class="clear"></div>
  </div>
</div>