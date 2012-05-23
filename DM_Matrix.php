<?php

/*
 * TheMatrix, a plugin for GetSimple CMS 3.1
 * 
 * version 0.1
 *  
 * Copyright (c) 2012 Mike Swan mike@digimute.com
 *
 * Contributions have been made by:
 * Shawn A (github.com/tablatronix)
 *
 */


// Turn dubgging on 
$DM_Matrix_debug=true; 

require "DM_Matrix/include/sql4array.php";
require "DM_Matrix/include/DM_matrix_functions.php";

# get correct id for plugin
$thisfile_DM_Matrix = basename(__FILE__, '.php');

# add in this plugin's language file
i18n_merge($thisfile_DM_Matrix) || i18n_merge($thisfile_DM_Matrix, 'en_US');

# register plugin
register_plugin(
  $thisfile_DM_Matrix,
  'The Matrix',
  '0.1',
  'Mike Swan',
  'http://digimute.com/',
  'The Matrix',
  'DM_Matrix',
  'matrix_manager'
);

debugLog(''.$TIMEZONE);   

define('GSSCHEMAPATH',GSDATAOTHERPATH.'matrix');

// check and make sure the base folders are there. 
if (!is_dir(GSSCHEMAPATH)){
	mkdir(GSSCHEMAPATH);
	DMdebuglog(i18n_r($thisfile_DM_Matrix.'/DM_ERROR_CREATEBASEFOLDER'));

} else {
	DMdebuglog(i18n_r($thisfile_DM_Matrix.'/DM_ERROR_CREATEBASEFOLDERFAIL'));
}


$defaultDebug = true;
$schemaArray = array();
$item_title='Matrix';
$editing=false; 
$uri='';
$uriRoutes=array();

$sql = new sql4array();
$mytable=array();

$DM_tables_cache = array(); // hold cached schema loads

// only load all our scripts and style if were on the MAtrix Plugin page
if (isset($_GET['id']) && $_GET['id']=="DM_Matrix"){
	register_script('DM_Matrix',$SITEURL.'plugins/DM_Matrix/js/DM_Matrix.js', '0.1',FALSE);
	queue_script('DM_Matrix', GSBACK);

	
	register_script('codemirror', $SITEURL.'admin/template/js/codemirror/lib/codemirror-compressed.js', '0.2.0', FALSE);
	queue_script('codemirror', GSBACK);
	if (file_exists(GSADMINPATH.'/template/js/codemirror/lib/searchcursor.js')){
		register_script('codemirror-search', $SITEURL.'admin/template/js/codemirror/lib/searchcursor.js', '0.2.0', FALSE);
		register_script('codemirror-search-cursor', $SITEURL.'admin/template/js/codemirror/lib/search.js', '0.2.0', FALSE);
		register_script('codemirror-dialog', $SITEURL.'admin/template/js/codemirror/lib/dialog.js', '0.2.0', FALSE);
		register_script('codemirror-folding', $SITEURL.'admin/template/js/codemirror/lib/foldcode.js', '0.2.0', FALSE);

		queue_script('codemirror-dialog', GSBACK);
		queue_script('codemirror-search', GSBACK);
		queue_script('codemirror-search-cursor', GSBACK);
		queue_script('codemirror-folding', GSBACK);
	} 
	
	register_style('codemirror-css',$SITEURL.'admin/template/js/codemirror/lib/codemirror.css','screen',FALSE);
	register_style('codemirror-theme',$SITEURL.'admin/template/js/codemirror/theme/default.css','screen',FALSE);		
	
	queue_style('codemirror-css', GSBACK);
	queue_style('codemirror-theme', GSBACK);
	
	register_script('DM_tablesorter',$SITEURL.'plugins/DM_Matrix/js/jquery.tablesorter.js', '0.1',FALSE);
	queue_script('DM_tablesorter', GSBACK);
	register_script('DM_tablepager',$SITEURL.'plugins/DM_Matrix/js/jquery.tablesorter.pager.js', '0.1',FALSE);
	queue_script('DM_tablepager', GSBACK);
	register_style('DM_tablesorter',$SITEURL.'plugins/DM_Matrix/css/blue/style.css','screen',FALSE);
	queue_style('DM_tablesorter', GSBACK);
	register_style('DM_tablepager',$SITEURL.'plugins/DM_Matrix/js/jquery.tablesorter.pager.css','screen',FALSE);
	queue_style('DM_tablepager', GSBACK);
	
	register_script('DM_Matrix_timepicker',$SITEURL.'plugins/DM_Matrix/js/timepicker.js', '0.1',FALSE);
	queue_script('DM_Matrix_timepicker', GSBACK);
	
	register_style('jquery-ui-css',$SITEURL.'plugins/DM_Matrix/css/redmond/jquery-ui-1.8.16.custom.css','screen',FALSE);
	queue_style('jquery-ui-css', GSBACK);
	queue_script('jquery-ui', GSBACK);	
	register_style('DM_Matrix_css',$SITEURL.'plugins/DM_Matrix/css/style.css', '0.1',FALSE);
	queue_style('DM_Matrix_css', GSBACK);
	
	register_script('ckeditor', $SITEURL.'admin/template/js/ckeditor/ckeditor.js', '0.2.0', FALSE);
	queue_script('ckeditor', GSBACK);
	
	
}

add_action('nav-tab','createNavTab',array('DM_Matrix','DM_Matrix','The Matrix','action=matrix_manager&schema'));

add_action($thisfile_DM_Matrix.'-sidebar','createSideMenu',array($thisfile_DM_Matrix, "Manage Tables",'schema')); 
if (isset($_GET['edit'])){
	add_action($thisfile_DM_Matrix.'-sidebar','createSideMenu',array($thisfile_DM_Matrix, "Edit Tables",'edit')); 
}
if (isset($_GET['view'])){
  add_action($thisfile_DM_Matrix.'-sidebar','createSideMenu',array($thisfile_DM_Matrix, "Manage Records",'view')); 
}
add_action($thisfile_DM_Matrix.'-sidebar','createSideMenu',array($thisfile_DM_Matrix, "Manage Routes",'routes')); 
# add_action($thisfile_DM_Matrix.'-sidebar','createSideMenu',array($thisfile_DM_Matrix, "Settings",'settings')); 
# add_action($thisfile_DM_Matrix.'-sidebar','createSideMenu',array($thisfile_DM_Matrix, "About",'about')); 

add_action('error-404','doRoute',array());


addRoute('blogger','news');
addRoute('news','news');

function doRoute(){
	global $file,$id,$uriRoutes,$uri;
	$uri = trim(str_replace('index.php', '', $_SERVER['REQUEST_URI']), '/#');
	$parts=explode('/',$uri);
	foreach ($uriRoutes as $route=>$key){
		if ($parts[1]==$route){
			$file=GSDATAPAGESPATH . $key.'.xml';
			$id=$key;
		}
	}
}


DM_getSchema();

if (!tableExists('_routes')){
	DMdebuglog('Creating table _routes ');
	$ret = createSchemaTable('_routes','0',array('route'=>'text','rewrite'=>'text'));
}

if (!tableExists('_settings')){
	DMdebuglog('Creating table _settings ');
	$ret = createSchemaTable('_settings','1',array());
}


if (isset($_GET['add']) && isset($_POST['post-addtable'])){
	DMdebuglog('Trying to add a new table: '.$_POST['post-addtable']);
	$ret=createSchemaTable($_POST['post-addtable'],$_POST['post-maxrecords'],array());
}

if (isset($_GET['add']) && isset($_GET['addrecord'])){
	$table=$_GET['add'];
	addRecordFromForm($table);
	}
	
if (isset($_GET['add']) && isset($_GET['updaterecord'])){
	$table=$_GET['add'];
	updateRecordFromForm($table);
	header('Location: load.php?id=DM_Matrix&action=matrix_manager&view='.$table);
	}
	


if (isset($_GET['edit']) && isset($_GET['addfield'])){
	if (isset($_POST['post-cacheindex'])){
		$cacheindex=1;
	} else {
		$cacheindex=0;
	}
	if (isset($_POST['post-tableview'])){
		$tableview=1;
	} else {
		$tableview=0;
	}
	
	$field=array(
		'name'=>$_POST['post-name'],
		'type'=>$_POST['post-type'],
		'label'=>$_POST['post-label'],
		'description'=>$_POST['post-desc'],
		'cacheindex'=>$cacheindex,
		'tableview'=>$tableview
	);
	if ($_POST['post-type']=='dropdown'){
		$field['table']=$_POST['post-table'];
		$field['row']=$_POST['post-row'];
	}
	addSchemaField($_GET['edit'],$field,true);
	  //DM_saveSchema();
}

function addRecordFromForm($tbl){
		debugLog("adding form");
		global $fieldtypes,$schemaArray;
		$tempArray=array();	
		foreach ($schemaArray[$tbl]['fields'] as $field=>$type)
		{
			if (isset($_POST["post-".$field]))
			{
				$data=DM_manipulate($_POST["post-".$field], $type); 
				$tempArray[(string)$field]=$data;
			}
		}

		createRecord($tbl, $tempArray);		
}

function updateRecordFromForm($tbl){
		debugLog("updating record from form...");
		global $fieldtypes,$schemaArray;
		$tempArray=array();	
		foreach ($schemaArray[$tbl]['fields'] as $field=>$type)
		{
			if (isset($_POST["post-".$field]))
			{
				$data=DM_manipulate($_POST["post-".$field], $type); 
				$tempArray[(string)$field]=$data;
			}
		}

		updateRecord($tbl,$_POST['post-id'], $tempArray);		
}


function DM_manipulate($field, $type){
	switch ($type){
		case "datetimepicker":
			return (int)strtotime($field);
			break;	
		case "datepicker":
			return (int)strtotime($field);
			break;	
		case "texteditor":
			return safe_slash_html($field);
			break;
		case "textarea":
			return safe_slash_html($field);
			break;
		case "codeeditor":
			return safe_slash_html($field);
			break;
				
		default: 
			return $field;
	}
		
}

//Admin Content
function matrix_manager() {
global $item_title,$thisfile_DM_Matrix, $fieldtypes,$schemaArray, $sql, $mytable;





//Main Navigation For Admin Panel
?>

	<div style="margin:0 -15px -15px -10px;padding:0px;">
	<h3 ><?php echo i18n_r($thisfile_DM_Matrix.'/DM_PLUGINTITLE') ?></h3>  
</div>

</div>
<div class="main" style="margin-top:-10px;">

<?php

//Alert Admin If Items Manager Settings XML File Is Directory Does Not Exist
if (isset($_GET['schema'])) {
?>
		
		<h2><?php echo i18n_r($thisfile_DM_Matrix.'/DM_SHOWTABLE') ?></h2>
		<table id="editpages" class="tablesorter">
		<thead>
			<tr>
				<th class='sort'><?php echo i18n_r($thisfile_DM_Matrix.'/DM_TABLENAME') ?></th>
				<th class='sort' ><?php echo i18n_r($thisfile_DM_Matrix.'/DM_NUMRECORDS') ?></th>
				<th class='sort'><?php echo i18n_r($thisfile_DM_Matrix.'/DM_NUMFIELDS') ?></th>
				<th style="width:75px;"><?php echo i18n_r($thisfile_DM_Matrix.'/DM_OPTIONS') ?></th>
			</tr>
		</thead>
		<tbody>
		<?php 
	$tables=0;    
		foreach($schemaArray as $schema=>$key){
			$fieldcnt = isset($key['fields']) ? count($key['fields']) : '0';
			if (substr($schema,0,1)!="_"){
				if ($fieldcnt > 1){
					echo "<tr><td><a href='load.php?id=DM_Matrix&action=matrix_manager&view=".$schema."' >".$schema."</a></td>";
				} else {
					echo "<tr><td>".$schema."</td>";	
				}
				echo "<td>".($key['id'])." / ".$key['maxrecords']."</td>";
				echo "<td>".$fieldcnt."</td>";
				echo "<td>";
				echo "<a href='load.php?id=DM_Matrix&action=matrix_manager&edit=".$schema."'>";
				echo "<img src='../plugins/DM_Matrix/images/edit.png' title='".i18n_r($thisfile_DM_Matrix.'/DM_EDITTABLE')."' /></a>";
				if ($fieldcnt > 1){
					echo " <a href='load.php?id=DM_Matrix&action=matrix_manager&add=".$schema."'>";
					echo "<img src='../plugins/DM_Matrix/images/add.png' title='".i18n_r($thisfile_DM_Matrix.'/DM_ADDRECORD')."' /></a>";
				}
		  // todo: add drop table functionality
					// echo " <a href='load.php?id=DM_Matrix&action=matrix_manager&drop=".$schema."'>";
					// echo "<img src='../plugins/DM_Matrix/images/delete.png' title='Drop Table $schema' /></a>";        
				echo "</td></tr>";
		$tables++;
			}
		}
	if ($tables==0){
	  echo '<tr><td colspan="4">No Tables defined</td></tr>';	
	}		
		?>
		</tbody>
		</table>
		<div id="pager" class="pager">
		<form>
			<img src="../plugins/DM_Matrix/images/first.png" class="first"/>
			<img src="../plugins/DM_Matrix/images/prev.png" class="prev"/>
			<input type="text" class="pagedisplay"/>
			<img src="../plugins/DM_Matrix/images/next.png" class="next"/>
			<img src="../plugins/DM_Matrix/images/last.png" class="last"/>
			<select class="pagesize">
				<option selected="selected"  value="10">10</option>
				<option value="20">20</option>
				<option value="30">30</option>
				<option  value="40">40</option>
			</select>
		</form>
		</div>
		<form method="post" action="load.php?id=DM_Matrix&schema&action=matrix_manager&add">
		<ul class="fields">
		
		<li class="ui-widget" id="wrap_Inputfield_name">
			<label class="ui-widget-header fieldstateToggle" for="Inputfield_name"><?php echo i18n_r($thisfile_DM_Matrix.'/DM_ADDTABLE') ?></label>
			<div class="ui-widget-content">
				<p class="description"><?php echo i18n_r($thisfile_DM_Matrix.'/DM_ADDTABLE_DESC') ?></p>
				<input type="text" class="required" id="post-addtable" name="post-addtable" />	
				<br/><br/>
				<p class="description">Max Number of records, leave blank for unlimited</p>
				<input type="text" id="post-maxrecords" name="post-maxrecords" />	
				<br/><br/>
				<button id="Inputfield_submit" class="mtrx_but_add form_submit" name="addtable" id="addtable" value="Submit" type="button">Add Table</button>
			</div>
		</li>
		</ul>
		</form>
	
<?php
	
	} elseif (isset($_GET['add']))	{
		$schemaname=$_GET['add'];
		if (isset($_GET['field'])){
			$record=$_GET['field'];
			echo "<h2>Editing '".$schemaname."' record : ".$record."</h2>";
			echo '<form method="post" action="load.php?id=DM_Matrix&action=matrix_manager&add='.$schemaname.'&updaterecord">';
			DM_editForm($schemaname,$record);
			echo '</form>';
		} else {
			echo "<h2>Add new '".$schemaname."' record</h2>";
	  echo "<a href='load.php?id=DM_Matrix&action=matrix_manager&view=$schemaname'>View all records for $schemaname</a>";
			echo '<form method="post" action="load.php?id=DM_Matrix&action=matrix_manager&add='.$schemaname.'&addrecord">';
			DM_createForm($schemaname);
			echo '</form>';
		}
	}
	elseif (isset($_GET['edit']))
	{

			
		$schemaname=$_GET['edit'];
		echo "<h2>Edit Schema: ".$schemaname."</h2>";
		?>
		<table id="edittable" class="tablesorter">
		<thead><tr><th>Name</th><th >Type</th><th style="width:75px;">Options</th></tr>
		</thead>
		<tbody>
		<?php 
		if( isset($schemaArray[$schemaname]['fields'])){
			foreach($schemaArray[$schemaname]['fields'] as $schema=>$key){
				echo "<tr><td>".$schema."</td><td>".$key."</td>";
				if ($schema!="id"){
					echo "<td><a href='load.php?id=DM_Matrix&action=matrix_manager&edit=".$schemaname."&field=".$schema."'><img src='../plugins/DM_Matrix/images/edit.png' title='Edit Field' /></a></td>";
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
			echo '<h3>Editing Field : '.$_GET['field'].'</h3>'; 
			$editing=true;
			
		} else {
			echo '<h3>Add New Field</h3>';
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
				<label class="ui-widget-header fieldstateToggle" for="Inputfield_name">Name</label>
				<div class="ui-widget-content">
					<p class="description">Any combination of ASCII letters [a-z], numbers [0-9], or underscores (no dashes or spaces).</p>
					<input type="text" id="post-name" name="post-name" class="required" size="25" <?php echo " value='".$formName."'"; ?> >
				</div>
			</li>
			<li class="ui-widget" id="wrap_Inputfield_name">
				<label class="ui-widget-header fieldstateToggle" for="Inputfield_name">Type</label>
				<div class="ui-widget-content">
					<p class="description">After selecting your field type, you may be presented with additional configuration options specific to the field type you selected.</p>
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
							<p class="description">Please Select a Table</p>
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
							<p class="description">Please select a row from the table</p>
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
			<label class="ui-widget-header fieldstateToggle" for="Inputfield_name">Add a label</label>
			<div class="ui-widget-content">
				<p class="description">Add a label for this Field.</p>
				<input type="text" <?php echo " value='".$formLabel."'"; ?> id="post-label" name="post-label" class="required" size="115">
				<br/>		
			</div>
			</li>
			<li class="ui-widget" id="wrap_Inputfield_name">
			<label class="ui-widget-header fieldstateToggle" for="Inputfield_name">Add a Description</label>
			<div class="ui-widget-content">
				<p class="description">Additional information describing this field and/or instructions on how to enter the content.</p>
				<input type="text" <?php echo " value='".$formDesc."'"; ?> id="post-desc" name="post-desc" class="required" size="115">
				<br/>		
			</div>
			</li>
			<li class="ui-widget" id="wrap_Inputfield_name">
			<label class="ui-widget-header fieldstateToggle" for="Inputfield_name">Additional Options</label>
			<div class="ui-widget-content">
				<p class="description">Additional options for this Field</p>
				<input class="hidden" type="checkbox" id="post-cacheindex" name="post-cacheindex" <?php if ($formCacheIndex=='1') echo " checked "; ?> >
				<!--&nbsp;Allow this field to be indexed<br/> -->
				
				<input type="checkbox" id="post-tableview" name="post-tableview" <?php if ($formTableView=='1') echo " checked "; ?>>
				&nbsp;Show in Table View
				
				<br/>		
			</div>
			</li>
			<li class="ui-widget" id="wrap_Inputfield_submit">
				<label class="ui-widget-header fieldStateToggle" for="field_submit">Save this Field</label>
				<div class="ui-widget-content">
					<button id="field_submit" class="mtrx_but_add form_submit" name="submit" value="Save Field" type="submit">Save Field</button>
				</div>
			</li>
		</form>
		</ul>
		<!-- hidden elements for additional options on fields -->
		<div id='field-dropdown' class='hidden'>
			<br/>
			<p class="description">Please Select a Table</p>
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
			<p class="description">Please select a rown from the table</p>
			<select id="post-row" name="post-row" >
				<option></option>
			</select>
		</div>
	<?php
	} 
elseif (isset($_GET['add']))
	{
		//
	} 
elseif (isset($_GET['view']))
	{
		$table=$_GET['view'];
		$fields=array();
		$tableheader='';
		$count=0;
		if(isset($schemaArray[$table]) && isset($schemaArray[$table]['fields'])){
		  foreach($schemaArray[$table]['fields'] as $schema=>$key){
			if ($schemaArray[$table]['tableview'][$schema]==1){
			  $fields[$count]['name']=$schema;
			  $fields[$count]['type']=$key;
			  
			  $tableheader.="<th class='sort'>".$schema."</th>";
			}
			$count++;
		  }
		}
		if ($table=='_routes'){
			echo "<h2>Manage Routes</h2>";
		} else {
			echo "<h2>Manage Records: ".$table."</h2>";
		}
?>
		<table id="editpages" class="tablesorter">
		<thead><tr><?php echo $tableheader; ?><th>Opts</th></tr></thead>
		<tbody>
		<?php 
		getPagesXmlValues();
		$mytable=getSchemaTable($table);
		$record_cnt = 0;
		if(isset($mytable)){
		  foreach($mytable as $key=>$value){
			#$fields = isset($mytable[$key]['fields']) ? $mytable[$key]['fields'] : array();
			#$id = 0;
			echo "<tr>";
			foreach ($fields as $field){
			  if ($field['name']=='id') $id=$mytable[$key][$field['name']];
			  if ($field['type']=='datepicker'){
				$data= isset($mytable[$key][$field['name']]) ? date(i18n('DATE_FORMAT',false),$mytable[$key][$field['name']]) : '<b>NULL</b>';
			  } elseif ($field['type']=='datetimepicker') {
				$data= isset($mytable[$key][$field['name']]) ? date(i18n('DATE_FORMAT',false).' H:i',$mytable[$key][$field['name']]) : '<b>NULL</b>';
			  } else {
				$data= isset($mytable[$key][$field['name']]) ? $mytable[$key][$field['name']] : '<b>NULL</b>';
			  }
			  echo "<td>".$data."</td>"; 
			}
			echo "<td><a href='load.php?id=DM_Matrix&action=matrix_manager&add=".$table."&field=".$id."'><img src='../plugins/DM_Matrix/images/edit.png' title='Edit Record ".$id."' /></a>";
			//todo delete functionality
			// echo " <a href='load.php?id=DM_Matrix&action=matrix_manager&delete=".$table."&field=".$id."'><img src='../plugins/DM_Matrix/images/delete.png' title='Delete Record ".$id."!' /></a>";
			echo "</td></tr>";
			$record_cnt++;
		  }
		} else {
			
		}  
		if($record_cnt==0){
		  echo '<tr><td colspan="'.($count+1).'">Table has no records</td></tr>';	 
		}
		?>
		
		</tbody>
		</table>
	<?php 
	echo "<a class='mtrx_but_add' id='matrix_recordadd' href='load.php?id=DM_Matrix&action=matrix_manager&add=".$table."'>Add Record</a>";
	?>     
		<div id="pager" class="pager">
		<form>
			<img src="../plugins/DM_Matrix/images/first.png" class="first"/>
			<img src="../plugins/DM_Matrix/images/prev.png" class="prev"/>
			<input type="text" class="pagedisplay"/>
			<img src="../plugins/DM_Matrix/images/next.png" class="next"/>
			<img src="../plugins/DM_Matrix/images/last.png" class="last"/>
			<select class="pagesize">
				<option selected="selected"  value="10">10</option>
				<option value="20">20</option>
				<option value="30">30</option>
				<option  value="40">40</option>
			</select>
		</form>
		</div>
<?
	} 		
}



//echo "<pre>";
//print_r($schemaArray);
//echo "</pre>";
