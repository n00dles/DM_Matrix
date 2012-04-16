<?php 
/** GetSimple CMS Schema Manager 
* Web site: http://www.digimute.com/
* @version  1.0
* @author   mike@digimute.com
*/

// Turn dubgging on 
$DM_Matrix_debug=true; 


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

require "DM_Matrix/include/sql4array.php";
$sql = new sql4array();
$mytable=array();


register_script('DM_Matrix',$SITEURL.'plugins/DM_Matrix/js/DM_Matrix.js', '0.1',FALSE);
queue_script('DM_Matrix', GSBACK);
register_style('DM_Matrix_css',$SITEURL.'plugins/DM_Matrix/css/style.css', '0.1',FALSE);
queue_style('DM_Matrix_css', GSBACK);


register_script('codemirror', $SITEURL.'admin/template/js/codemirror/lib/codemirror-compressed.js', '0.2.0', FALSE);
register_script('codemirror-search', $SITEURL.'admin/template/js/codemirror/lib/searchcursor.js', '0.2.0', FALSE);
register_script('codemirror-search-cursor', $SITEURL.'admin/template/js/codemirror/lib/search.js', '0.2.0', FALSE);
register_script('codemirror-dialog', $SITEURL.'admin/template/js/codemirror/lib/dialog.js', '0.2.0', FALSE);
register_script('codemirror-folding', $SITEURL.'admin/template/js/codemirror/lib/foldcode.js', '0.2.0', FALSE);

register_style('codemirror-css',$SITEURL.'admin/template/js/codemirror/lib/codemirror.css','screen',FALSE);
register_style('codemirror-theme',$SITEURL.'admin/template/js/codemirror/theme/default.css','screen',FALSE);
register_style('codemirror-dialog',$SITEURL.'admin/template/js/codemirror/lib/dialog.css','screen',FALSE);

queue_script('codemirror', GSBACK);
queue_script('codemirror-search', GSBACK);
queue_script('codemirror-search-cursor', GSBACK);
queue_script('codemirror-dialog', GSBACK);
queue_script('codemirror-folding', GSBACK);


queue_style('codemirror-css', GSBACK);
queue_style('codemirror-theme', GSBACK);
queue_style('codemirror-dialog', GSBACK);

register_script('DM_tablesorter',$SITEURL.'plugins/DM_Matrix/js/jquery.tablesorter.js', '0.1',FALSE);
queue_script('DM_tablesorter', GSBACK);
register_script('DM_tablepager',$SITEURL.'plugins/DM_Matrix/js/jquery.tablesorter.pager.js', '0.1',FALSE);
queue_script('DM_tablepager', GSBACK);
register_style('DM_tablesorter',$SITEURL.'plugins/DM_Matrix/css/blue/style.css','screen',FALSE);
queue_style('DM_tablesorter', GSBACK);

register_script('DM_Matrix_timepicker',$SITEURL.'plugins/DM_Matrix/js/timepicker.js', '0.1',FALSE);
queue_script('DM_Matrix_timepicker', GSBACK);

register_style('jquery-ui-css',$SITEURL.'plugins/DM_Matrix/css/redmond/jquery-ui-1.8.16.custom.css','screen',FALSE);
queue_style('jquery-ui-css', GSBACK);
queue_script('jquery-ui', GSBACK);

add_action('nav-tab','createNavTab',array('DM_Matrix','DM_Matrix','The Matrix','action=matrix_manager&schema'));

add_action($thisfile_DM_Matrix.'-sidebar','createSideMenu',array($thisfile_DM_Matrix, "Manage Tables",'schema')); 
if (isset($_GET['edit'])){
	add_action($thisfile_DM_Matrix.'-sidebar','createSideMenu',array($thisfile_DM_Matrix, "Edit Tables",'edit')); 
}
add_action($thisfile_DM_Matrix.'-sidebar','createSideMenu',array($thisfile_DM_Matrix, "Manage Records",'view')); 
add_action($thisfile_DM_Matrix.'-sidebar','createSideMenu',array($thisfile_DM_Matrix, "Settings",'settings')); 
add_action($thisfile_DM_Matrix.'-sidebar','createSideMenu',array($thisfile_DM_Matrix, "About",'about')); 

DM_getSchema();

if (isset($_GET['add']) && isset($_POST['post-addtable'])){
	DMdebuglog('Trying to add a new table: '.$_POST['post-addtable']);
	$ret=createSchemaTable($_POST['post-addtable'],$_POST['post-maxrecords'],array());
}

if (isset($_GET['add']) && isset($_GET['addrecord'])){
	$table=$_GET['add'];
	addRecordFromForm($table);
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
	
	addSchemaField($_GET['edit'],$field,true);
	  //DM_saveSchema();
}

function addRecordFromForm($tbl){
		debugLog("addign form");
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

function DM_manipulate($field, $type){
	switch ($type){
		case "datetimepicker":
			return (int)strtotime($field);
			break;	
		case "datepicker":
			return (int)strtotime($field);
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
				<th><?php echo i18n_r($thisfile_DM_Matrix.'/DM_TABLENAME') ?></th>
				<th ><?php echo i18n_r($thisfile_DM_Matrix.'/DM_NUMRECORDS') ?></th>
				<th><?php echo i18n_r($thisfile_DM_Matrix.'/DM_NUMFIELDS') ?></th>
				<th style="width:75px;"><?php echo i18n_r($thisfile_DM_Matrix.'/DM_OPTIONS') ?></th>
			</tr>
		</thead>
		<tbody>
		<?php 
		foreach($schemaArray as $schema=>$key){
			echo "<tr><td><a href='load.php?id=DM_Matrix&action=matrix_manager&view=".$schema."' >".$schema."</a></td>";
			echo "<td>".($key['id'])." / ".$key['maxrecords']."</td>";
			echo "<td>".count($key['fields'])."</td>";
			echo "<td>";
			echo "<a href='load.php?id=DM_Matrix&action=matrix_manager&edit=".$schema."'>";
			echo "<img src='../plugins/DM_Matrix/images/edit.png' title='".i18n_r($thisfile_DM_Matrix.'/DM_EDITTABLE')."' /></a>";
			if (count($key['fields'])>1){
				echo "<a href='load.php?id=DM_Matrix&action=matrix_manager&add=".$schema."'>";
				echo "<img src='../plugins/DM_Matrix/images/add.png' title='".i18n_r($thisfile_DM_Matrix.'/DM_ADDRECORD')."' /></a>";
			}
			echo "</td></tr>";
		}
		
		?>
		</tbody>
		</table>
		<form method="post" action="load.php?id=DM_Matrix&schema&action=matrix_manager&add">
		<ul class="fields">
		
		<li class="InputfieldName Inputfield_name ui-widget" id="wrap_Inputfield_name">
			<label class="ui-widget-header fieldstateToggle" for="Inputfield_name"><?php echo i18n_r($thisfile_DM_Matrix.'/DM_ADDTABLE') ?></label>
			<div class="ui-widget-content">
				<p class="description"><?php echo i18n_r($thisfile_DM_Matrix.'/DM_ADDTABLE_DESC') ?></p>
				<input type="text" class="required" id="post-addtable" name="post-addtable" />	
				<br/><br/>
				<p class="description">Max Number of records, leave blank for unlimited</p>
				<input type="text" id="post-maxrecords" name="post-maxrecords" />	
				<br/><br/>
				<button id="Inputfield_submit" class="ui-button ui-widget  ui-state-default form_submit" name="addtable" id="addtable" value="Submit" type="button"><span class="ui-button-text">Submit</span></button>
			</div>
		</li>
		</ul>
		</form>
	
<?php
	
	} elseif (isset($_GET['add']))	{
		$schemaname=$_GET['add'];
		echo "<h2>Add new ".$schemaname." record</h2>";
		echo '<form method="post" action="load.php?id=DM_Matrix&action=matrix_manager&add='.$schemaname.'&addrecord">';
		DM_createForm($schemaname);
		echo '</form>';
	}
	elseif (isset($_GET['edit']))
	{

			
		$schemaname=$_GET['edit'];
		echo "<h2>Edit Schema: ".$schemaname."</h2>";
		?>
		<table id="editpages" class="tablesorter">
		<thead><tr><th>Name</th><th >Type</th><th style="width:75px;">Options</th></tr>
		</thead>
		<tbody>
		<?php 
		foreach($schemaArray[$schemaname]['fields'] as $schema=>$key){
			echo "<tr><td>".$schema."</td><td>".$key."</td>";
			if ($schema!="id"){
				echo "<td><a href='load.php?id=DM_Matrix&action=matrix_manager&edit=".$schemaname."&field=".$schema."'><img src='../plugins/DM_Matrix/images/edit.png' title='Edit Field' /></a></td>";
			} else {
				echo "<td></td>";
			}
			echo "</tr>";
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
		}
		?>
		<ul class="fields">
			<li class="InputfieldName Inputfield_name ui-widget" id="wrap_Inputfield_name">
				<label class="ui-widget-header fieldstateToggle" for="Inputfield_name">Name</label>
				<div class="ui-widget-content">
					<p class="description">Any combination of ASCII letters [a-z], numbers [0-9], or underscores (no dashes or spaces).</p>
					<input type="text" id="post-name" name="post-name" class="required" size="25" <?php echo " value='".$formName."'"; ?> >
				</div>
			</li>
			<li class="InputfieldName Inputfield_name ui-widget" id="wrap_Inputfield_name">
				<label class="ui-widget-header fieldstateToggle" for="Inputfield_name">Type</label>
				<div class="ui-widget-content">
					<p class="description">After selecting your field type, you may be presented with additional configuration options specific to the field type you selected.</p>
					<select id="post-type" name="post-type" class="required">
						<option value=""></option>
						
						<?php 
						$types=array('int','text','textlong','checkbox','pages','dropdown','templates','datepicker','datetimepicker','image','textarea','codeeditor','texteditor'); 
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
					<div id="fieldoptions"></div>	
				</div>
			</li>
			<li class="InputfieldName Inputfield_name ui-widget" id="wrap_Inputfield_name">
			<label class="ui-widget-header fieldstateToggle" for="Inputfield_name">Add a label</label>
			<div class="ui-widget-content">
				<p class="description">Add a label for this Field.</p>
				<input type="text" <?php echo " value='".$formLabel."'"; ?> id="post-label" name="post-label" class="required" size="115">
				<br/>		
			</div>
			</li>
			<li class="InputfieldName Inputfield_name ui-widget" id="wrap_Inputfield_name">
			<label class="ui-widget-header fieldstateToggle" for="Inputfield_name">Add a Description</label>
			<div class="ui-widget-content">
				<p class="description">Additional information describing this field and/or instructions on how to enter the content.</p>
				<input type="text" <?php echo " value='".$formDesc."'"; ?> id="post-desc" name="post-desc" class="required" size="115">
				<br/>		
			</div>
			</li>
			<li class="InputfieldName Inputfield_name ui-widget" id="wrap_Inputfield_name">
			<label class="ui-widget-header fieldstateToggle" for="Inputfield_name">Additional Options</label>
			<div class="ui-widget-content">
				<p class="description">Additional options for this Field</p>
				<input type="checkbox" id="post-cacheindex" name="post-cacheindex" <?php if ($formCacheIndex=='1') echo " checked "; ?> >&nbsp;Allow this field to be indexed<br/> 
				<input type="checkbox" id="post-tableview" name="post-tableview" <?php if ($formTableView=='1') echo " checked "; ?>>&nbsp;Show in Table View
				
				<br/>		
			</div>
			</li>
			<li class="InputfieldSubmit field_submit ui-widget" id="wrap_Inputfield_submit">
				<label class="ui-widget-header fieldStateToggle" for="field_submit">Save this Field</label>
				<div class="ui-widget-content">
					<button id="field_submit" class="ui-button ui-widget ui-corner-all ui-state-default form_submit" name="submit" value="Save Field" type="submit"><span class="ui-button-text">Save Field</span></button>
				</div>
			</li>
		</form>
		</ul>
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
		foreach($schemaArray[$table]['fields'] as $schema=>$key){
			if ($schemaArray[$table]['tableview'][$schema]==1){
				$fields[$count]['name']=$schema;
				$fields[$count]['type']=$key;
				
				$tableheader.="<th>".$schema."</th>";
			}
			$count++;
		}
?>
		<table id="editpages" class="tablesorter">
		<thead><tr><?php echo $tableheader; ?></tr></thead>
		<tbody>
		<?php 
		getPagesXmlValues();
		$mytable=getSchemaTable($table);
		foreach($mytable as $key=>$value){
			echo "<tr>";
			foreach ($fields as $field){
				if ($field['type']=='datepicker'){
					$data=date('d-m-Y',$mytable[$key][$field['name']]);
				} elseif ($field['type']=='datetimepicker') {
					$data=date('d-m-Y i:M',$mytable[$key][$field['name']]);
				} else {
					$data=$mytable[$key][$field['name']];
				}
				echo "<td>".$data."</td>"; 
			}
			echo "</tr>";
		}
		
		?>
		
		</tbody>
		</table>
<?
	} 		
}

/** Load The main schema XML file and fill the array $schema
  */
function DM_getSchema($flag=false){
  global $schemaArray;	
  
  $file=GSSCHEMAPATH."/schema.xml";
  if (file_exists($file)){
  DMdebuglog('Schema file loaded...');
  // load the xml file and setup the array. 
	$thisfile_DM_Matrix = file_get_contents($file);
		$data = simplexml_load_string($thisfile_DM_Matrix);
		$components = @$data->item;
		if (count($components) != 0) {
			foreach ($components as $component) {
				$att = $component->attributes();
				$key=$component->name;
				//$schemaArray[(string)$key] =$key;
				$schemaArray[(string)$key]=array();				
				$schemaArray[(string)$key]['id']=(int)$component->id;
				$schemaArray[(string)$key]['maxrecords']=(int)$component->maxrecords;
				$fields=$component->field;	
				foreach ($fields as $field) {
					$att = $field->attributes();
					$type =(string)$att['type'];
					$desc=(string)$att['description'];
					$label=(string)$att['label'];
					$cacheindex=(string)$att['cacheindex'];
					$tableview=(string)$att['tableview'];
					
					$schemaArray[(string)$key]['fields'][(string)$field]=(string)$type;
					$schemaArray[(string)$key]['desc'][(string)$field]=(string)$desc;
					$schemaArray[(string)$key]['label'][(string)$field]=(string)$label;
					$schemaArray[(string)$key]['cacheindex'][(string)$field]=(string)$cacheindex;
					$schemaArray[(string)$key]['tableview'][(string)$field]=(string)$tableview;
					
					if ((string)$type=="dropdown"){
						$schemaArray[(string)$key]['table'][(string)$field]=(string)$att['table'];;
						$schemaArray[(string)$key]['row'][(string)$field]=(string)$att['row'];;
					}
					if ((string)$type=="checkbox"){
						$schemaArray[(string)$key]['label'][(string)$field]=(string)$att['label'];;
					}
					
					}
				
  			}
		}
	}
}

function DM_saveSchema(){
	global $schemaArray;	
  	$file=GSSCHEMAPATH."/schema.xml";
	$xml = @new SimpleXMLExtended('<channel></channel>');
	foreach ($schemaArray as $table=>$key){
		$pages = $xml->addChild('item');
		$pages->addChild('name',$table);
		$pages->addChild('id',$key['id']);
		$pages->addChild('maxrecords',$key['maxrecords']);
		foreach($key['fields'] as $field=>$type){
			//$options=$schemaArray[$table]['options'];

			$field=$pages->addChild('field',$field);
			$field->addAttribute('type',$type);
			$field->addAttribute('tableview',@$schemaArray[$table]['tableview'][(string)$field]);
			$field->addAttribute('cacheindex',@$schemaArray[$table]['cacheindex'][(string)$field]);
			$field->addAttribute('description',@$schemaArray[$table]['desc'][(string)$field]);
			$field->addAttribute('label',@$schemaArray[$table]['label'][(string)$field]);
		}
		
	}
	$xml->asXML($file);
	DM_getSchema(true);
	return true;
}

/**
 * Create a Schema folder
 * 
 * Creates a fodler for each of the Tables in the Schema. 
 *
 * @param string $name , Name of the table to create
 * @return boolean , whether table was created or not. 
 */
function createSchemaFolder($name){
	$ret = mkdir (GSSCHEMAPATH.'/'.$name);
	return $ret;
}

function createRecord($name,$data=array()){
	global $schemaArray;
	$id=getNextRecord($name);
	DMdebuglog('record:'.$id);
	$file=GSSCHEMAPATH.'/'.$name."/".$id.".xml";
	$xml = @new SimpleXMLExtended('<channel></channel>');
	$pages = $xml->addChild('item');
	$pages->addChild('id',$id);
	foreach ($data as $field=>$txt){
		$pages->addChild($field,$txt);	
	}
	$xml->asXML($file);
	DMdebuglog('file:'.$file);
	$schemaArray[$name]['id']=$id+1;
	$ret=DM_saveSchema();
	
}

/**
 * Get the next record ID
 *
 * returns the next record ID in the sequence
 *
 * @param string $name , Name of the table to create
 * @return string , Record ID 
 */
function getNextRecord($name){
	global $schemaArray;
	DMdebuglog($name.":returned:".$schemaArray[$name]['id']);
	return $schemaArray[$name]['id'];
}


/**
 * Create a new Table
 *
 * Creates a new table in the Schema, by creating a folder for the files and adding data to the schema
 *
 * @param string $name , Name of the table to create
 * @param array $fields , array of fields and types to create, default is to create an id (int) fields
 * @return boolean , whether table was created or not. 
 */
function createSchemaTable($name, $maxrecords=0, $fields=array()){
	global $schemaArray, $thisfile_DM_Matrix;
	if (array_key_exists($name , $schemaArray)){
		DMdebuglog(i18n_r($thisfile_DM_Matrix.'/DM_ERROR_CREATETABLEFAIL'));
		return false;
	}
	$schemaArray[(string)$name] =array();
	$schemaArray[(string)$name]['id']=0;
	$schemaArray[(string)$name]['maxrecords']=$maxrecords;
	if (!in_array('id', $schemaArray)){
		$schemaArray[(string)$name]['fields']['id']='int';
	}
	foreach ($fields as $field=>$value) {
		$schemaArray[(string)$name]['fields'][(string)$field]=(string)$value;
	}
	createSchemaFolder($name);		
	$ret=DM_saveSchema();
	DMdebuglog(i18n_r($thisfile_DM_Matrix.'/DM_ERROR_CREATETABLESUCCESS'));
	return true;
}

/**
 * Drop a Schema table
 *
 * Delete a Schema Table from the system 
 * 
 * Todo: Need to check the folder is empty before delting. 
 *
 * @param string $name , Name of the table to create
 */
function dropSchemaTable($name){
	global $schemaArray;
	unset($schemaArray[(string)$name]);
	$ret=DM_saveSchema();	
}

/**
 * Add a field to a table
 *
 * Creates a new Field in the table. 
 *
 * @param string $name , Name of the table 
 * @param array $fields , array of fields and types to create
 * @param boolean, whether to save the Schema after adding the field, default to true
 * @return boolean , whether field was created or not. 
 */
function addSchemaField($name,$fields=array(),$save=true){
	global $schemaArray;
	$schemaArray[(string)$name]['fields'][(string)$fields['name']]=(string)$fields['type'];	
	$schemaArray[(string)$name]['label'][(string)$fields['name']]=(string)$fields['label'];
	$schemaArray[(string)$name]['desc'][(string)$fields['name']]=(string)$fields['description'];
	$schemaArray[(string)$name]['cacheindex'][(string)$fields['name']]=(string)$fields['cacheindex'];
	$schemaArray[(string)$name]['tableview'][(string)$fields['name']]=(string)$fields['tableview'];
	if ($save==true) {
		$ret=DM_saveSchema();
		$ret=true;
	} else {
		$ret=true;
	}

	return $ret;
}

/**
 * Delete a field from a table
 *
 * Delete a Field(s) from a table.  
 *
 * @param string $name , Name of the table 
 * @param array $fields , array of fields to delete from the table
 * @return boolean , whether table was created or not. 
 */
function deleteSchemaField($name,$fields=array(),$save=true){
	global $schemaArray;
	foreach ($fields as $field) {
		unset($schemaArray[(string)$name]['fields'][(string)$field]);
	}
	if ($save==true) {
		$ret=DM_saveSchema();
	} else {
		$ret=true;
	}
	return $ret;
}


function getSchemaTable($name,$query=''){
	global $returnArray;
	$table=array();
	$path = GSSCHEMAPATH.'/'.$name."/";
	  $dir_handle = @opendir($path) or die("Unable to open $path");
	  $filenames = array();
	  while ($filename = readdir($dir_handle)) {
	    $ext = substr($filename, strrpos($filename, '.') + 1);
		$fname=substr($filename,0, strrpos($filename, '.'));
	    if ($ext=="xml"){
			$thisfile_DM_Matrix = file_get_contents($path.$filename);
	        $data = simplexml_load_string($thisfile_DM_Matrix);
	        //$count++;   
	        $id=$data->item;
			
			foreach ($id->children() as $opt=>$val) {
	            //$pagesArray[(string)$key][(string)$opt]=(string)$val;
				$table[$fname][(string)$opt]=(string)$val;
	        }		
	    }
	  }
	if ($query!=''){
		$returnArray=$table;
		$sql=new sql4array();
		$table = $sql->query($query);
	}
	return $table;
}


function DM_createForm($name){
	global $schemaArray;	
	echo '<ul class="fields">';
	foreach ($schemaArray[$name]['fields'] as $field=>$value) {

	if ($field!="id"){
	?>
	
		<li class="InputfieldName Inputfield_name ui-widget" id="wrap_Inputfield_name">
			<label class="ui-widget-header fieldstateToggle" for="Inputfield_name"><?php echo $field; ?></label>
			<div class="ui-widget-content">
				<p class="description"><?php echo $schemaArray[$name]['desc'][$field]; ?></p>
				<?php displayFieldType($field, $value,$name); ?>
			</div>
		</li>
	
	<?php
	} else {
	?>
	<li class="InputfieldHidden Inputfield_id ui-widget" id="wrap_Inputfield_id">
		<label class="ui-widget-header fieldstateToggle" for="Inputfield_id">id</label>
		<div class="ui-widget-content">
			<input id="Inputfield_id" name="id" value="0" type="hidden">
		</div>
	</li>

		
	<?php	
		}
	}
?>

	<li class="fieldsubmit Inputfield_submit_save_field ui-widget" id="wrap_Inputfield_submit">
		<label class="ui-widget-header fieldstateToggle" for="Inputfield_submit">Save Record</label>
		<div class="ui-widget-content">
			<button id="Inputfield_submit" class="ui-button ui-widget ui-state-default ui-corner-all" name="submit_save_field" value="Submit" type="submit"><span class="ui-button-text">Save This Record</span></button>
		</div>
	</li>

</ul>
<?php	
}


function displayFieldType($name, $type, $schema){
	global $schemaArray;
	global $pagesArray;
	global $TEMPLATE;
	global $SITEURL;
	// flags for javascript code. 
	$codeedit=false;
	$datepick=false;
	$datetimepick=false;
	$textedit=false;
	$value='';
	
	// get caching info in case we need it. 
	getPagesXmlValues();
	
	// Get the filed type
	switch ($type){
		// normal text field
		case "text":
			echo '<p><input id="post-'.$name.'" class="required" name="post-'.$name.'" type="text" size="50" maxlength="128"></p>';
			break; 
		// long text field, full width		
		case "textlong":
			echo '<p><input id="post-'.$name.'" class="required" name="post-'.$name.'" type="text" size="115" maxlength="128"></p>';
			break;
		// Checkbox
		case "checkbox":
			$label=$schemaArray[$schema]['label'][$name];
			echo '<p><input id="post-'.$name.'" class="required" name="post-'.$name.'" type="checkbox" > '.$label.'</p>';
			break;
		// Dropdown box of existing pages on the site. Values are skug/url 
		case "pages":
			echo '<p><select id="post-'.$name.'" name="post-'.$name.'">';
			foreach ($pagesArray as $page){
				echo '<option value="'.$page['url'].'">'.$page['title'].'</option>';
			}
			echo '</select></p>';
			break;
		// a dropdown of current templates
		case "templates":
			$theme_templates='';
			$themes_path = GSTHEMESPATH . $TEMPLATE;
			$themes_handle = opendir($themes_path) or die("Unable to open ". GSTHEMESPATH);		
			while ($file = readdir($themes_handle))	{		
				if( isFile($file, $themes_path, 'php') ) {		
					if ($file != 'functions.php' && !strpos(strtolower($file), '.inc.php')) {		
			      $templates[] = $file;		
			    }		
				}		
			}			
			sort($templates);	
			foreach ($templates as $file){
				$theme_templates .= '<option value="'.$file.'" >'.$file.'</option>';
			}
			echo '<p><select  id="post-'.$name.'" name="post-'.$name.'">';
			echo $theme_templates;
			echo '</select></p>';
			break;
		// Datepicker. Use settings page to set the front end date format, saved as Unix timestamp
		case "datepicker";
			echo '<p><input id="post-'.$name.'" class="datepicker required" name="post-'.$name.'" type="text" size="50" maxlength="128"></p>';
			$datetimepick=true;
			break;
		// DateTimepicker. Use settings page to set the front end date format, saved as Unix timestamp
		case "datetimepicker";
			echo '<p><input id="post-'.$name.'" class="datetimepicker required" name="post-'.$name.'" type="text" size="50" maxlength="128"></p>';	
			$datepick=true;
			break;
		// Dropdown from another Table/column 
		case "dropdown":
			$table=$schemaArray[$schema]['table'][$name];
			$column=$schemaArray[$schema]['row'][$name];
			$maintable=getSchemaTable('gallery');
			echo '<p><select  id="post-'.$name.'" name="post-'.$name.'">';
			foreach ($maintable as $row){
				echo '<option value="'.$row[$column].'">'.$row[$column].'</option>';
			}
			echo '</select></p>';
			break;
		case 'image':
        	echo '<p><input class="text imagepicker" type="text" id="post-'.$name.'" name="post-'.$name.'" value="" />';
        	echo ' <span class="edit-nav"><a id="browse-'.$name.'" href="#">Browse</a></span>';
			echo '<div id="image-'.$name.'"></div>';
       		echo '</p>'; 
        
		?>
		<script type="text/javascript">
		  $(function() { 
		    $('#browse-<?php echo $name; ?>').click(function(e) {
		      window.open('<?php echo $SITEURL; ?>admin/filebrowser.php?CKEditorFuncNum=1&func=test&returnid=post-<?php echo $name; ?>&type=images', 'browser', 'width=800,height=500,left=100,top=100,scrollbars=yes');
		    });
		  });
		</script>
		<?php
		break;
		// Textarea converted to a code editor.
		case "codeeditor":
       		echo '<p><textarea class="codeeditor" id="post-'.$name.'" name="post-'.$name.'" style="width:513px;height:200px;border: 1px solid #AAAAAA;">'.$value.'</textarea></p>';
			$codeedit=true;
			break;
		// texteditor converted to CKEditor
		case "texteditor":
       		echo '<p><textarea class="codeeditor" id="post-'.$name.'" name="post-'.$name.'" style="width:513px;height:200px;border: 1px solid #AAAAAA;">'.$value.'</textarea></p>';
			$textedit=true;
			break;
		// Textarea Plain
		case "textarea":
       		echo '<p><textarea class="codeeditor" id="post-'.$name.'" name="post-'.$name.'" style="width:513px;height:200px;border: 1px solid #AAAAAA;">'.$value.'</textarea></p>';
			break;
		default:
			echo "Unknown"; 
	}

	if ($codeedit){
		?>
			<script type="text/javascript">
			jQuery(document).ready(function() { 
				  var foldFunc = CodeMirror.newFoldFunction(CodeMirror.braceRangeFinder);
				  function keyEvent(cm, e) {
				    if (e.keyCode == 81 && e.ctrlKey) {
				      if (e.type == "keydown") {
				        e.stop();
				        setTimeout(function() {foldFunc(cm, cm.getCursor().line);}, 50);
				      }
				      return true;
				    }
				  }
				  function toggleFullscreenEditing()
				    {
				        var editorDiv = $('.CodeMirror-scroll');
				        if (!editorDiv.hasClass('fullscreen')) {
				            toggleFullscreenEditing.beforeFullscreen = { height: editorDiv.height(), width: editorDiv.width() }
				            editorDiv.addClass('fullscreen');
				            editorDiv.height('100%');
				            editorDiv.width('100%');
				            editor.refresh();
				        }
				        else {
				            editorDiv.removeClass('fullscreen');
				            editorDiv.height(toggleFullscreenEditing.beforeFullscreen.height);
				            editorDiv.width(toggleFullscreenEditing.beforeFullscreen.width);
				            editor.refresh();
				        }
				    }
			      var editor = CodeMirror.fromTextArea(document.getElementById("post-<?php echo $name; ?>"), {
			        lineNumbers: true,
			        matchBrackets: true,
			        indentUnit: 4,
			        indentWithTabs: true,
			        enterMode: "keep",
			        tabMode: "shift",
			        theme:'default',
			        mode: "text/html",
			    	onGutterClick: foldFunc,
			    	extraKeys: {"Ctrl-Q": function(cm){foldFunc(cm, cm.getCursor().line);},
			    				"F11": toggleFullscreenEditing, "Esc": toggleFullscreenEditing},
			        onCursorActivity: function() {
					   	editor.setLineClass(hlLine, null);
					   	hlLine = editor.setLineClass(editor.getCursor().line, "activeline");
					}
			      	});
			     var hlLine = editor.setLineClass(0, "activeline");
			    
			    })
			     
			</script>
			<?php
	}
}


function DMdebuglog($log){
	global $DM_Matrix_debug;
	if ($DM_Matrix_debug){
		debuglog($log);
	}
}


//echo "<pre>";
//print_r($schemaArray);
//echo "</pre>";

