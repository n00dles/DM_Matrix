<?php
			
		$schemaname=$_GET['edit'];
		echo "<h2>".i18n_r($thisfile_DM_Matrix.'/DM_EDIT_TABLE')."".$schemaname."</h2>";
		?>
		<table id="edittable" class="tablesorter">
		<thead><tr>
			<th><?php echo i18n_r($thisfile_DM_Matrix.'/DM_NAME'); ?></th>
			<th><?php echo i18n_r($thisfile_DM_Matrix.'/DM_TYPE'); ?></th>
			<th style="width:75px;"><?php echo i18n_r($thisfile_DM_Matrix.'/DM_OPTIONS'); ?></th></tr>
		</thead>
		<tbody>
		<?php 
		if( isset($schemaArray[$schemaname]['fields'])){
			foreach($schemaArray[$schemaname]['fields'] as $schema=>$key){
				echo "<tr><td>".$schema."</td><td>".$key."</td>";
				if ($schema!="id"){
					echo "<td><a href='load.php?id=DM_Matrix&action=matrix_manager&edit=".$schemaname."&field=".$schema."'><img src='../plugins/DM_Matrix/images/edit.png' title='".i18n_r($thisfile_DM_Matrix.'/DM_EDIT_FIELD')."' /></a></td>";
				} else {
					echo "<td></td>";
				}
				echo "</tr>";
			}
		}	
		?>
		
		</tbody>
		</table>
		<form method="post" action="load.php?id=DM_Matrix&action=matrix_manager&edit=<?php echo $schemaname; ?>&addfield">
		<?php if (isset($_GET['field'])){
			$formName = $_GET['field'];
			$formType = $schemaArray[$_GET['edit']]['fields'][$_GET['field']];
			$formDesc= $schemaArray[$_GET['edit']]['desc'][$_GET['field']];
			$formLabel = $schemaArray[$_GET['edit']]['label'][$_GET['field']];
			$formHeading = $schemaArray[$_GET['edit']]['desc'][$_GET['field']];
			$formCacheIndex = $schemaArray[$_GET['edit']]['cacheindex'][$_GET['field']];
			$formTableView = $schemaArray[$_GET['edit']]['tableview'][$_GET['field']];
			if ($formType=='dropdown'){
				$formTable = $schemaArray[$_GET['edit']]['table'][$_GET['field']];
				$formTableRow = $schemaArray[$_GET['edit']]['row'][$_GET['field']];
			}
			$editing=true;
			echo '<h3>'.i18n_r($thisfile_DM_Matrix.'/DM_EDITING_FIELD').' : '.$_GET['field'].'</h3>'; 
			$editing=true;
			
		} else {
			echo '<h3>'.i18n_r($thisfile_DM_Matrix.'/DM_ADD_NEW_FIELD').'</h3>';
			$formName = "";
			$formType = "";
			$formDesc= "";
			$formLabel = "";
			$formHeading = "";
			$formCacheIndex = "";
			$formTableView = "";
			$formTable = "";
			$formTableRow = "";
		}
		?>
		<ul class="fields">
			<li class="ui-widget" id="wrap_Inputfield_name">
				<label class="ui-widget-header fieldstateToggle" for="Inputfield_name"><?php echo i18n_r($thisfile_DM_Matrix.'/DM_NAME'); ?></label>
				<div class="ui-widget-content">
					<p class="description"><?php echo i18n_r($thisfile_DM_Matrix.'/DM_NAME_DESC'); ?></p>
					<input type="text" id="post-name" name="post-name" class="required" size="25" <?php echo " value='".$formName."'"; ?> >
				</div>
			</li>
			<li class="ui-widget" id="wrap_Inputfield_name">
				<label class="ui-widget-header fieldstateToggle" for="Inputfield_name"><?php echo i18n_r($thisfile_DM_Matrix.'/DM_TYPE'); ?></label>
				<div class="ui-widget-content">
					<p class="description"><?php echo i18n_r($thisfile_DM_Matrix.'/DM_TYPE_DESC'); ?></p>
					<select id="post-type" name="post-type" class="required">
						<option value=""></option>
						
						<?php 
						$types=array('int','slug','text','textlong','checkbox','pages','dropdown','templates','datepicker','datetimepicker','image','filepicker','textarea','codeeditor','wysiwyg'); 
						foreach ($types as $type){
							if ($formType==$type){
								$sel=" selected ";
							} else {
								$sel="";
							}
							echo "<option value='".$type."' ".$sel.">".$type."</option>"; 
						}
						?>	
					</select>
					<div id="fieldoptions">
						<?php 
						if ($formType=='dropdown'){
						?>
						<div id='field-dropdown' >
							<br/>
							<p class="description"><?php echo i18n_r($thisfile_DM_Matrix.'/DM_SELECT_TABLE'); ?></p>
							<select id="post-table" name="post-table" >
								<option value=""></option>
								<?php 
									foreach($schemaArray as $schema=>$key){
										echo '<option value="'.$schema.'" data-fields="';
											foreach ($schemaArray[$schema]['fields'] as $field=>$key){
												echo $field.',';
											}
										
										echo '"';
										if ($schema==$formTable) echo " selected ";
										echo ' ">'.$schema.'</option>';	
									}
								
								?>
							</select>
							<p class="description"><?php echo i18n_r($thisfile_DM_Matrix.'/DM_SELECT_ROW'); ?></p>
							<select id="post-row" name="post-row" >
								<option></option>
								<?php 
									foreach ($schemaArray[$formTable]['fields'] as $field=>$key){
										echo '<option ';
										if ($field==$formTableRow) echo " selected ";
										echo '>'.$field.'</option>';
									}
								?>
							</select>
						</div>
						<?php	
						}
						?>
					</div>	
				</div>
			</li>
			<li class="ui-widget" id="wrap_Inputfield_name">
			<label class="ui-widget-header fieldstateToggle" for="Inputfield_name"><?php echo i18n_r($thisfile_DM_Matrix.'/DM_ADD_LABEL'); ?></label>
			<div class="ui-widget-content">
				<p class="description"><?php echo i18n_r($thisfile_DM_Matrix.'/DM_ADD_LABEL_DESC'); ?></p>
				<input type="text" <?php echo " value='".$formLabel."'"; ?> id="post-label" name="post-label" class="required" size="115">
				<br/>		
			</div>
			</li>
			<li class="ui-widget" id="wrap_Inputfield_name">
			<label class="ui-widget-header fieldstateToggle" for="Inputfield_name"><?php echo i18n_r($thisfile_DM_Matrix.'/DM_ADD_DESC'); ?></label>
			<div class="ui-widget-content">
				<p class="description"><?php echo i18n_r($thisfile_DM_Matrix.'/DM_ADD_DESC_DESC'); ?></p>
				<input type="text" <?php echo " value='".$formDesc."'"; ?> id="post-desc" name="post-desc" class="required" size="115">
				<br/>		
			</div>
			</li>
			<li class="ui-widget" id="wrap_Inputfield_name">
			<label class="ui-widget-header fieldstateToggle" for="Inputfield_name"><?php echo i18n_r($thisfile_DM_Matrix.'/DM_ADDITIONAL_OPTIONS'); ?></label>
			<div class="ui-widget-content">
				<p class="description"><?php echo i18n_r($thisfile_DM_Matrix.'/DM_ADDITIONAL_OPTIONS_DESC'); ?></p>
				<input class="hidden" type="checkbox" id="post-cacheindex" name="post-cacheindex" <?php if ($formCacheIndex=='1') echo " checked "; ?> >
				<!--&nbsp;Allow this field to be indexed<br/> -->
				
				<input type="checkbox" id="post-tableview" name="post-tableview" <?php if ($formTableView=='1') echo " checked "; ?>>
				&nbsp;<?php echo i18n_r($thisfile_DM_Matrix.'/DM_TABLE_VIEW'); ?>
				
				<br/>		
			</div>
			</li>
			<li class="ui-widget" id="wrap_Inputfield_submit">
				<label class="ui-widget-header fieldStateToggle" for="field_submit"><?php echo i18n_r($thisfile_DM_Matrix.'/DM_SAVE_FIELD'); ?></label>
				<div class="ui-widget-content">
					<button id="field_submit" class="mtrx_but_add form_submit" name="submit" value="Save Field" type="submit"><?php echo i18n_r($thisfile_DM_Matrix.'/DM_SAVE_FIELD_BUTTON'); ?></button>
				</div>
			</li>
		</form>
		</ul>
		<!-- hidden elements for additional options on fields -->
		<div id='field-dropdown' class='hidden'>
			<br/>
			<p class="description"><?php echo i18n_r($thisfile_DM_Matrix.'/DM_SELECT_TABLE'); ?></p>
			<select id="post-table" name="post-table" >
				<option value=""></option>
				<?php 
					foreach($schemaArray as $schema=>$key){
						echo '<option value="'.$schema.'" data-fields="';
							foreach ($schemaArray[$schema]['fields'] as $field=>$key){
								echo $field.',';
							}
						echo '" ">'.$schema.'</option>';	
					}
				
				?>
			</select>
			<p class="description"><?php echo i18n_r($thisfile_DM_Matrix.'/DM_SELECT_ROW'); ?></p>
			<select id="post-row" name="post-row" >
				<option></option>
			</select>
		</div>