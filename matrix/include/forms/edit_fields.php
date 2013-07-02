<label><?php echo $field['name']; ?><span class="right"><a href="" class="openAdvanced cancel">more</a> <a href="" class="removeField cancel">x</a></span></label>
<div class="clear"></div>

<div id="metadata_window">
  <div class="basic">
    <div class="leftopt">
      <p>
        <input class="hidden" name="oldname[]" value="<?php echo $field['name']; ?>">
        <input class="text" name="name[]" placeholder="<?php echo i18n_r('matrix/DM_NAME'); ?>" value="<?php echo $field['name']; ?>">
      </p>
    </div>
    <div class="rightopt">
      <p>
        <select class="text type" name="type[]">
        <?php foreach ($this->fieldTypes as $type=>$properties) { ?>
           <option value="<?php echo $type; ?>" <?php if ($field['type']==$type) echo 'selected="selected"'; ?>><?php echo $type; ?></option>
        <?php } ?>
        </select>
      </p>
    </div>
    <div class="clear"></div>
  </div>
  <div class="advanced">
    <div class="leftopt">
      <p><input class="text" name="label[]" placeholder="Label" value="<?php echo $field['label']; ?>"/></p>
      <p>
        <textarea class="text" name="desc[]" placeholder="<?php echo i18n_r('PLUGIN_DESC'); ?>"><?php echo $field['desc']; ?></textarea>
      </p>
      <p>
        <textarea class="text" name="default[]" placeholder="Default"><?php echo $field['default']; ?></textarea>
      </p>
      <p><textarea class="text" name="other[]" placeholder="Other settings (custom)"/><?php echo $field['other']; ?></textarea></p>
      <p><input class="text" name="maxlength[]" placeholder="Max length" value="<?php echo $field['maxlength']; ?>"/></p>
    </div>
    <div class="rightopt">
      <div class="showOptions dropdown">
      <p>
        <select class="text tableName" name="table[]">
          <?php foreach ($this->schemaArray as $table=>$properties) { ?>
          <option value="<?php echo $table; ?>" <?php if (isset($field['table']) && $field['table']==$table) echo 'selected="selected"'; ?>><?php echo $table; ?></option>
          <?php } ?>
        </select>
      </p>
      <p><input class="text tableRow" name="row[]" value="<?php echo $field['row']; ?>" placeholder="Column name"/></p>
      </div>
      
      <div class="showOptions dropdowncustom checkbox dropdownhierarchy imageuploadadmin">
      <p>
        <textarea class="text" name="options[]"><?php echo $field['options']; ?></textarea>
      </p>
      </div>
      
      <div class="showOptions imageuploadadmin">
        <p><input class="text uploadpath" name="path[]" placeholder="<?php echo $_GET['table']; ?>/" value="<?php echo $field['path']; ?>"/></p>
      </div>
      
      <div class="showOptions textmulti intmulti">
        <p><input class="text rows" name="rows[]" placeholder="Number of fields to show" value="<?php echo $field['rows']; ?>"/></p>
      </div>
      
      <p><input class="text" name="size[]" placeholder="Field size" value="<?php echo $field['size']; ?>"/></p>
      <p>
        <select class="text" name="cacheindex[]">
          <option value="1" <?php if ($field['cacheindex']==1) echo 'selected="selected"'; ?>>Cache</option>
          <option value="0" <?php if ($field['cacheindex']==0) echo 'selected="selected"'; ?>>No Cache</option>
        </select>
      </p>
      <p>
        <select class="text" name="visibility[]">
          <option value="1" <?php if ($field['visibility']==1) echo 'selected="selected"'; ?>>Visible in form</option>
          <option value="0" <?php if ($field['visibility']==0) echo 'selected="selected"'; ?>>Invisible in form</option>
        </select>
      </p>
      <p>
        <select class="text" name="tableview[]">
          <option value="1" <?php if ($field['tableview']==1) echo 'selected="selected"'; ?>>Visible in table</option>
          <option value="0" <?php if ($field['tableview']==0) echo 'selected="selected"'; ?>>Invisible in table</option>
        </select>
      </p>
      <p>
        <select class="text" name="class[]">
          <option value="" <?php if ($field['class']=='') echo 'selected="selected"'; ?>>Normal</option>
          <option value="leftopt" <?php if ($field['class']=='leftopt') echo 'selected="selected"'; ?>>Metadata Window (Left)</option>
          <option value="rightopt" <?php if ($field['class']=='rightopt') echo 'selected="selected"'; ?>>Metadata Window (Right)</option>
          <option value="leftsec" <?php if ($field['class']=='leftsec') echo 'selected="selected"'; ?>>Left</option>
          <option value="rightsec" <?php if ($field['class']=='rightsec') echo 'selected="selected"'; ?>>Right</option>
        </select>
      </p>
      <p>
        <select class="text" name="readonly[]">
          <option value="readonly" <?php if ($field['readonly']=='readonly') echo 'selected="selected"'; ?>>Read Only</option>
          <option value="" <?php if ($field['readonly']=='') echo 'selected="selected"'; ?>>Writetable</option>
        </select>
      </p>
      <p>
        <select class="text" name="required[]">
          <option value="required" <?php if ($field['required']=='required') echo 'selected="selected"'; ?>>Required</option>
          <option value="" <?php if ($field['required']=='') echo 'selected="selected"'; ?>>Not Required</option>
        </select>
      </p>
      <p>
        <input type="text" class="text" name="validation[]" value="<?php echo $field['validation']; ?>" placeholder="<?php echo i18n_r(MATRIX.'/DM_VALIDATION'); ?>"/>
      </p>
    </div>
    <div class="clear"></div>
  </div>
</div>