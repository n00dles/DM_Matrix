<label><h3><?php echo $field['name']; ?></h3><span class="right"><a href="" class="openAdvanced cancel">more</a> <a href="" class="removeField cancel">x</a></span></label>
<div class="clear"></div>

<div id="metadata_window">
  <div class="basic">
    <div class="leftopt">
      <p>
        <label><?php echo i18n_r(self::FILE.'/NAME');?> : </label>
        <input class="hidden" name="oldname[]" value="<?php echo $field['name']; ?>">
        <input class="text name" name="name[]" placeholder="<?php echo i18n_r(self::FILE.'/NAME'); ?>" value="<?php echo $field['name']; ?>" required>
      </p>
    </div>
    <div class="rightopt">
      <p>
        <label><?php echo i18n_r(self::FILE.'/TYPE');?> : </label>
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
      <p>
        <label><?php echo i18n_r(self::FILE.'/LABEL');?> : </label>
        <input class="text" name="label[]" placeholder="<?php echo i18n_r(self::FILE.'/LABEL'); ?>" value="<?php echo $field['label']; ?>"/>
        </p>
      <p>
        <label><?php echo i18n_r(self::FILE.'/DESCRIPTION');?> : </label>
        <textarea class="text" name="desc[]" placeholder="<?php echo i18n_r(self::FILE.'/DESCRIPTION'); ?>"><?php echo $field['desc']; ?></textarea>
      </p>
      <p>
        <label><?php echo i18n_r(self::FILE.'/PLACEHOLDER');?> : </label>
        <textarea class="text" name="placeholder[]" placeholder="<?php echo i18n_r(self::FILE.'/PLACEHOLDER'); ?>"><?php echo $field['placeholder']; ?></textarea>
      </p>
      <p>
        <label><?php echo i18n_r(self::FILE.'/DEFAULT');?> : </label>
        <textarea class="text" name="default[]" placeholder="<?php echo i18n_r(self::FILE.'/DEFAULT'); ?>"><?php echo $field['default']; ?></textarea>
      </p>
      <p>
        <label><?php echo i18n_r(self::FILE.'/OTHER');?> : </label>
        <textarea class="text" name="other[]" placeholder="<?php echo i18n_r(self::FILE.'/OTHER'); ?>"/><?php echo $field['other']; ?></textarea>
      </p>
      <p>
        <label><?php echo i18n_r(self::FILE.'/MAX_LENGTH');?> : </label>
        <input class="text" name="maxlength[]" placeholder="<?php echo i18n_r(self::FILE.'/MAX_LENGTH'); ?>" value="<?php echo $field['maxlength']; ?>"/>
      </p>
      <p>
        <label><?php echo i18n_r(self::FILE.'/WIDTH');?> : </label>
        <input class="text" name="width[]" placeholder="<?php echo i18n_r(self::FILE.'/WIDTH'); ?>" value="<?php echo $field['width']; ?>"/>
      </p>
      <p>
        <label><?php echo i18n_r(self::FILE.'/HEIGHT');?> : </label>
        <input class="text" name="height[]" placeholder="<?php echo i18n_r(self::FILE.'/HEIGHT'); ?>" value="<?php echo $field['height']; ?>"/>
      </p>
      <p>
        <label><?php echo i18n_r(self::FILE.'/STYLE');?> : </label>
        <input class="text" name="style[]" placeholder="<?php echo i18n_r(self::FILE.'/STYLE'); ?>" value="<?php echo $field['style']; ?>"/>
      </p>
    </div>
    <div class="rightopt">
      <div class="masks">
        <p class="inline clearfix">
        <label><?php echo i18n_r(self::FILE.'/MASK');?> : </label>
        <?php foreach ($this->fields['type'] as $key => $f) { ?>
          <?php if (isset($f['masks'])) { ?>
            <select class="text mask <?php echo $key; ?> autowidth" name="<?php echo ($key == 'input') ? 'mask[]' : ''; ?>">
            <?php foreach ($f['masks'] as $mask) { ?>
              <option value="<?php echo $mask; ?>" <?php if (isset($field['mask']) && $field['mask'] == $mask) echo 'selected="selected"'; ?>><?php echo $mask; ?></option>
            <?php } ?>
            </select>
          <?php } ?>
        <?php } ?>
        <select style="display:none;" class="blank"><option></option></select>
        </p>
      </div>
      <div id="menu-items">
      
        <div class="dropdown_table">
        <p>
          <span><label><?php echo i18n_r(self::FILE.'/TABLE');?> : </label></span>
          <select class="text tableName" name="table[]">
            <?php foreach ($this->schema as $table => $properties) { ?>
            <option value="<?php echo $table; ?>" <?php if (isset($field['table']) && $field['table'] == $table) echo 'selected="selected"'; ?>><?php echo $table; ?></option>
            <?php } ?>
          </select>
        </p>
        <p>
          <span><label><?php echo i18n_r(self::FILE.'/FIELD');?> : </label></span>
          <input class="text tableRow" name="row[]" value="<?php echo $field['row']; ?>" placeholder="<?php echo i18n_r(self::FILE.'/COLUMN'); ?>"/>
        </p>
        </div>
        <div class="options_checkbox options_radio options_selectmulti dropdown_custom dropdown_key upload_imageadmin">
          <p>
            <span><label><?php echo i18n_r(self::FILE.'/OPTIONS');?> : </label></span>
            <textarea class="text" name="options[]"><?php echo $field['options']; ?></textarea>
          </p>
        </div>
        
        <div class="input_password">
          <span><label><?php echo i18n_r(self::FILE.'/SALT');?> : </label></span>
          <p><input class="text salt" name="salt[]" value="<?php echo $field['salt']; ?>"/></p>
        </div>
        
        <div class="upload_imageadmin">
          <span><label><?php echo i18n_r(self::FILE.'/UPLOAD_PATH');?> : </label></span>
          <p><input class="text uploadpath" name="path[]" placeholder="<?php if (isset($_GET['table'])) echo $_GET['table']; ?>/" value="<?php echo $field['path']; ?>"/></p>
        </div>
        
        <div class="multi_text multi_number multi_color multi_textarea">
          <p>
            <span><label><?php echo i18n_r(self::FILE.'/LABELS');?> : </label></span>
            <textarea class="text labels" name="labels[]"><?php echo $field['labels']; ?></textarea>
          </p>
          <p>
            <span><label><?php echo i18n_r(self::FILE.'/ROWS');?> : </label></span>
            <textarea class="text rows" name="rows[]" placeholder="<?php echo i18n_r(self::FILE.'/ROWS');?>"><?php echo $field['rows']; ?></textarea>
          </p>
        </div>
        
        <div class="input_number input_range">
          <p>
            <span><label><?php echo i18n_r(self::FILE.'/MIN');?> : </label></span>
            <input class="text rows" name="min[]" placeholder="<?php echo i18n_r(self::FILE.'/MIN');?>" value="<?php echo $field['min']; ?>"/>
          </p>
          <p>
            <span><label><?php echo i18n_r(self::FILE.'/MAX');?> : </label></span>
            <input class="text rows" name="max[]" placeholder="<?php echo i18n_r(self::FILE.'/MAX');?>" value="<?php echo $field['max']; ?>"/>
          </p>
          <p>
            <span><label><?php echo i18n_r(self::FILE.'/STEP');?> : </label></span>
            <input class="text rows" name="step[]" placeholder="<?php echo i18n_r(self::FILE.'/STEP');?>" value="<?php echo $field['step']; ?>"/>
          </p>
        </div>
        
      </div>
      
      <p>
        <label><?php echo i18n_r(self::FILE.'/FIELD_SIZE');?> : </label>
        <input class="text" name="size[]" placeholder="<?php echo i18n_r(self::FILE.'/FIELD_SIZE');?>" value="<?php echo $field['size']; ?>"/>
      </p>
      
      <p>
        <label><?php echo i18n_r(self::FILE.'/VALIDATION');?> : </label>
        <input type="text" class="text" name="validation[]" value="<?php echo $field['validation']; ?>" placeholder="<?php echo i18n_r(self::FILE.'/VALIDATION'); ?>"/>
      </p>
      
      <p class="inline clearfix">
        <label><?php echo i18n_r(self::FILE.'/CACHE');?> : </label>
        <select class="text autowidth" name="cacheindex[]">
          <option value="1" <?php if ($field['cacheindex'] == 1) echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/YES');?></option>
          <option value="0" <?php if ($field['cacheindex'] == 0) echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/NO');?></option>
        </select>
      </p>
      <p class="inline clearfix">
        <label><?php echo i18n_r(self::FILE.'/VISIBLE_FORM');?> : </label>
        <select class="text autowidth" name="visibility[]">
          <option value="1" <?php if ($field['visibility'] == 1) echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/YES');?></option>
          <option value="0" <?php if ($field['visibility'] == 0) echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/NO');?></option>
        </select>
      </p>
      <p class="inline clearfix">
        <label><?php echo i18n_r(self::FILE.'/VISIBLE_TABLE');?> : </label>
        <select class="text autowidth" name="tableview[]">
          <option value="1" <?php if ($field['tableview'] == 1) echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/YES');?></option>
          <option value="0" <?php if ($field['tableview'] == 0) echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/NO');?></option>
        </select>
      </p>
      <p class="inline clearfix">
        <label><?php echo i18n_r(self::FILE.'/CLASS');?> : </label>
        <select class="text autowidth" name="class[]">
          <option value="" <?php if ($field['class'] == '') echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/NORMAL');?></option>
          <option value="leftopt" <?php if ($field['class'] == 'leftopt') echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/METADATA_LEFT');?></option>
          <option value="rightopt" <?php if ($field['class'] == 'rightopt') echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/METADATA_RIGHT');?></option>
          <option value="leftsec" <?php if ($field['class'] == 'leftsec') echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/LEFT');?></option>
          <option value="rightsec" <?php if ($field['class'] == 'rightsec') echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/RIGHT');?></option>
        </select>
      </p>
      <p class="inline clearfix">
        <label><?php echo i18n_r(self::FILE.'/WRITEABLE');?> : </label>
        <select class="text autowidth" name="readonly[]">
          <option value="" <?php if ($field['readonly'] == '') echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/YES'); ?></option>
          <option value="readonly" <?php if ($field['readonly'] == 'readonly') echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/NO'); ?></option>
        </select>
      </p>
      <p class="inline clearfix">
        <label><?php echo i18n_r(self::FILE.'/REQUIRED');?> : </label>
        <select class="text autowidth" name="required[]">
          <option value="required" <?php if ($field['required'] == 'required') echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/YES'); ?></option>
          <option value="" <?php if ($field['required'] == '') echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/NO'); ?></option>
        </select>
      </p>
      <p class="inline clearfix">
        <label><?php echo i18n_r(self::FILE.'/INDEX');?> : </label>
        <select class="text autowidth" name="index[]">
          <option value="1" <?php if ($field['index'] == 1) echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/YES');?></option>
          <option value="0" <?php if ($field['index'] == 0) echo 'selected="selected"'; ?>><?php echo i18n_r(self::FILE.'/NO');?></option>
        </select>
      </p>
    </div>
    <div class="clear"></div>
  </div>
</div>