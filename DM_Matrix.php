<?php 
/** GetSimple CMS Schema Manager 
* Web site: http://www.digimute.com/
* @version  1.0
* @author   mike@digimute.com
*/


# get correct id for plugin
$thisfile = basename(__FILE__, '.php');

# register plugin
register_plugin(
  $thisfile,
  'The Matrix',
  '0.1',
  'Mike Swan',
  'http://digimute.com/',
  'The Matrix',
  'DM_Matrix',
  'matrix_manager'
);
    

define('GSSCHEMAPATH',GSDATAOTHERPATH.'matrix');

// check and make sure the base folders are there. 
if (!is_dir(GSSCHEMAPATH)){
	mkdir(GSSCHEMAPATH);
	debuglog("DM_Matrix: Created Base Folder, ".GSSCHEMAPATH);
} else {
	debuglog("DM_Matrix: Base Folder, ".GSSCHEMAPATH." exists");
}

$defaultDebug = true;
$schemaArray = array();
$item_title='Matrix';

require "DM_Matrix/include/sql4array.php";


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

queue_script('jquery-ui', GSBACK);

register_script('DM_Matrix_timepicker',$SITEURL.'plugins/DM_Matrix/js/timepicker.js', '0.1',FALSE);
queue_script('DM_Matrix_timepicker', GSBACK);

register_style('jquery-ui-css',$SITEURL.'plugins/DM_Matrix/css/redmond/jquery-ui-1.8.16.custom.css','screen',FALSE);
queue_style('jquery-ui-css', GSBACK);

add_action('nav-tab','createNavTab',array('DM_Matrix','DM_Matrix','The Matrix','action=matrix_manager&schema'));
DM_getSchema();



if (isset($_GET['add']) && isset($_POST['post-addtable'])){
	debugLog('adding a new table..'.$_POST['post-addtable'].'.woo hooo');
	$ret=createSchemaTable($_POST['post-addtable'],array());
}

if (isset($_GET['edit']) && isset($_GET['addfield']) && $flag==false){
  	echo "adding Field to ".$_GET['edit']."/".$_POST['post-name']."=".$_POST['post-type'];
	  addSchemaField($_GET['edit'],array($_POST['post-name']=>$_POST['post-type']),true);
	  //DM_saveSchema();
  }

//Admin Content
function matrix_manager() {
global $item_title, $fieldtypes,$schemaArray;

//Main Navigation For Admin Panel
?>
<div style="margin:0 -15px -15px -10px;padding:0px;">
	<h3 class="floated"><?php echo $item_title; ?> Manager</h3>  
	<div class="edit-nav clearfix" style="">
		<a href="load.php?id=DM_Matrix&action=matrix_manager&settings" <?php if (isset($_GET['settings'])) { echo 'class="current"'; } ?>>Settings</a>
		<a href="load.php?id=DM_Matrix&action=matrix_manager&fields" <?php if (isset($_GET['fields'])) { echo 'class="current"'; } ?>>Manage Records</a>
		<a href="load.php?id=DM_Matrix&action=matrix_manager&schema" <?php if (isset($_GET['schema'])) { echo 'class="current"'; } ?>>Manage Tables</a>
	</div> 
</div>
</div>
<div class="main" style="margin-top:-10px;">

<?php

//Alert Admin If Items Manager Settings XML File Is Directory Does Not Exist
if (isset($_GET['schema'])) {
?>
		
		<h2>Show Tables</h2>
		<table id="editpages" class="edittable highlight paginate">
		<tbody><tr><th>Table Name</th><th ># records</th><th># Fields</th><th style="width:75px;">Options</th></tr>
		<?php 
		foreach($schemaArray as $schema=>$key){
			echo "<tr><td>".$schema."</td><td>".($key['id'])."</td><td>".count($key['fields'])."</td><td><a href='load.php?id=DM_Matrix&action=matrix_manager&edit=".$schema."'><img src='../plugins/DM_Matrix/images/edit.png' title='Edit Schema' /></a><a href='load.php?id=DM_Matrix&action=matrix_manager&add=".$schema."'><img src='../plugins/DM_Matrix/images/add.png' title='Add Record' /></a></td></tr>";
		}
		
		?>
		</tbody>
		</table>
		<form method="post" action="load.php?id=DM_Matrix&schema&action=matrix_manager&add">
		<ul class="fields">
		
		<li class="InputfieldName Inputfield_name ui-widget" id="wrap_Inputfield_name">
			<label class="ui-widget-header fieldstateToggle" for="Inputfield_name">Add a new Table</label>
			<div class="ui-widget-content">
				<p class="description">Enter a name for your new Table</p>
				<input type="text" id="post-addtable" name="post-addtable" />		
				<button id="Inputfield_submit" class="ui-button ui-widget  ui-state-default" name="addtable" id="addtable" value="Submit" type="submit"><span class="ui-button-text">Submit</span></button>
			</div>
		</li>
		</ul>
		</form>
		</div>
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
		<table id="editpages" class="edittable highlight paginate">
		<tbody><tr><th>Name</th><th >Type</th><th style="width:75px;">Options</th></tr>
		<?php 
		foreach($schemaArray[$schemaname]['fields'] as $schema=>$key){
			echo "<tr><td>".$schema."</td><td>".$key."</td><td><a href='load.php?id=DM_Matrix&action=matrix_manager&edit=".$schema."'><img src='../plugins/DM_Matrix/images/edit.png' title='Edit Records' /></a></td></tr>";
		}
		
		?>
		
		</tbody>
		</table>
		
		<form method="post" action="load.php?id=DM_Matrix&action=matrix_manager&edit=<?php echo $schemaname; ?>&addfield">
		<h3>Add New Field</h3>
		<ul class="fields">
			<li class="InputfieldName Inputfield_name ui-widget" id="wrap_Inputfield_name">
				<label class="ui-widget-header fieldstateToggle" for="Inputfield_name">Name</label>
				<div class="ui-widget-content">
					<p class="description">Any combination of ASCII letters [a-z], numbers [0-9], or underscores (no dashes or spaces).</p>
					<input type="text" value="" id="post-name" name="post-name" class="required" size="25">
				</div>
			</li>
			<li class="InputfieldName Inputfield_name ui-widget" id="wrap_Inputfield_name">
				<label class="ui-widget-header fieldstateToggle" for="Inputfield_name">Type</label>
				<div class="ui-widget-content">
					<p class="description">After selecting your field type, you may be presented with additional configuration options specific to the field type you selected.</p>
					<select id="post-type" name="post-type">
						<option value="int">int</option>		
						<option value="text">text</option>	
						<option value="textlong">textlong</option>
						<option value="checkbox">checkbox</option>
						<option value="pages">pages</option>
						<option value="templates">templates</option>
						<option value="datepicker">datepicker</option>
						<option value="datetimepicker">datetimepicker</option>
						<option value="image">Image Picker</option>								
						<option value="textarea">textarea</option>	
						<option value="codeeditor">codeeditor</option>	
						<option value="texteditor">texteditor</option>		
					</select>
					<div id="fieldoptions"></div>	
				</div>
			</li>
			<li class="InputfieldName Inputfield_name ui-widget" id="wrap_Inputfield_name">
			<label class="ui-widget-header fieldstateToggle" for="Inputfield_name">Add a new Field</label>
			<div class="ui-widget-content">
				<p class="description">Additional information describing this field and/or instructions on how to enter the content.</p>
				<input type="text" value="" id="post-desc" name="post-desc" class="required" size="25">
				<br/>		
			</div>
		</li>
		<li class="InputfieldSubmit field_submit ui-widget" id="wrap_Inputfield_submit">
			<label class="ui-widget-header fieldStateToggle" for="field_submit">submit</label>
			<div class="ui-widget-content">
				<button id="field_submit" class="ui-button ui-widget ui-corner-all ui-state-default" name="submit" value="Save Template" type="submit"><span class="ui-button-text">Save Template</span></button>
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
}

/** Load The main schema XML file and fill the array $schema
  */
function DM_getSchema($flag=false){
  global $schemaArray;	
  
  $file=GSSCHEMAPATH."/schema.xml";
  debugLog($file);
  if (file_exists($file)){
  debugLog('file loaded...');
  // load the xml file and setup the array. 
	$thisfile = file_get_contents($file);
		$data = simplexml_load_string($thisfile);
		$components = @$data->item;
		if (count($components) != 0) {
			foreach ($components as $component) {
				$att = $component->attributes();
				$key=$component->name;
				//$schemaArray[(string)$key] =$key;
				$schemaArray[(string)$key]=array();				
				$schemaArray[(string)$key]['id']=(int)$component->id;
				
				$fields=$component->field;	
				foreach ($fields as $field) {
					$att = $field->attributes();
					$type =(string)$att['type'];
					$desc=(string)$att['desc'];
					$schemaArray[(string)$key]['fields'][(string)$field]=(string)$type;
					$schemaArray[(string)$key]['desc'][(string)$field]=(string)$desc;
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
		foreach($key['fields'] as $field=>$type){
			$pages->addChild('field',$field)->addAttribute('type',$type);
		}
	}
	$xml->asXML($file);
	DM_getSchema(true);
	return true;
}

function createSchemaFolder($name){
	$ret = mkdir (GSSCHEMAPATH.'/'.$name);
	return $ret;
}

function createRecord($name,$data=array()){
	global $schemaArray;
	$id=getNextRecord($name);
	debugLog('record:'.$id);
	$file=GSSCHEMAPATH.'/'.$name."/".$id.".xml";
	$xml = @new SimpleXMLExtended('<channel></channel>');
	$pages = $xml->addChild('item');
	$pages->addChild('id',$id);
	foreach ($data as $field=>$txt){
		$pages->addChild($field,$txt);	
	}
	$xml->asXML($file);
	debugLog('file:'.$file);
	$schemaArray[$name]['id']=$id+1;
	$ret=DM_saveSchema();
	
}

function getNextRecord($name){
	global $schemaArray;
	debugLog($name.":returned:".$schemaArray[$name]['id']);
	return $schemaArray[$name]['id'];
}

function createSchemaTable($name, $fields=array()){
	global $schemaArray;
	if (array_key_exists($name , $schemaArray)){
		return false;
	}
	$schemaArray[(string)$name] =array();
	$schemaArray[(string)$name]['id']=0;
	if (!in_array('id', $schemaArray)){
		$schemaArray[(string)$name]['fields']['id']='int';
	}
	foreach ($fields as $field=>$value) {
		$schemaArray[(string)$name]['fields'][(string)$field]=(string)$value;
	}	
	createSchemaFolder($name);		
	$ret=DM_saveSchema();
	return true;
}

function dropSchemaTable($name){
	global $schemaArray;
	unset($schemaArray[(string)$name]);
	$ret=DM_saveSchema();
	
}

function addSchemaField($name,$fields=array(),$save=true){
	global $schemaArray;
	foreach ($fields as $field=>$value) {
		$schemaArray[(string)$name]['fields'][(string)$field]=(string)$value;
	}			
	if ($save==true) {
		$ret=DM_saveSchema();
	} else {
		$ret=true;
	}
}

function deleteSchemaField($name,$fields=array()){
	global $schemaArray;
	foreach ($fields as $field) {
		unset($schemaArray[(string)$name]['fields'][(string)$field]);
	}
}


function getSchemaTable($name){
	$table=array();
	$path = GSSCHEMAPATH.'/'.$name."/";
	  $dir_handle = @opendir($path) or die("Unable to open $path");
	  $filenames = array();
	  while ($filename = readdir($dir_handle)) {
	    $ext = substr($filename, strrpos($filename, '.') + 1);
		$fname=substr($filename,0, strrpos($filename, '.'));
	    if ($ext=="xml"){
		$thisfile = file_get_contents($path.$filename);
        $data = simplexml_load_string($thisfile);
        //$count++;   
        $id=$data->item;
		
		foreach ($id->children() as $opt=>$val) {
            //$pagesArray[(string)$key][(string)$opt]=(string)$val;
			$table[$fname][(string)$opt]=(string)$val;
        }
		
    	//$table[$fname]['id']=$fname;
		//$table[$fname]['name']=(string)$id;
		
	    }
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
		<label class="ui-widget-header fieldstateToggle" for="Inputfield_submit">submit_save_field</label>
		<div class="ui-widget-content">
			<button id="Inputfield_submit" class="ui-button ui-widget ui-state-default ui-corner-all" name="submit_save_field" value="Submit" type="submit"><span class="ui-button-text">Submit</span></button>
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
	$textedit=false;
	$value='';
	
	// get caching info in case we need it. 
	getPagesXmlValues();
	
	
	//echo "<pre>";
	//print_r($pagesArray);
	//echo "</pre>";
	
	
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
			$datepick=true;
			break;
		// DateTimepicker. Use settings page to set the front end date format, saved as Unix timestamp
		case "datetimepicker";
			echo '<p><input id="post-'.$name.'" class="datepicker required" name="post-'.$name.'" type="text" size="50" maxlength="128"></p>';	
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
        	echo '<p><input class="text" type="text" id="post-'.$name.'" name="post-'.$name.'" value="" />';
        	echo ' <span class="edit-nav"><a id="browse-'.$name.'" href="#">Browse</a></span>';
       		echo '</p>'; 
        
		?>
		<script type="text/javascript">
		  $(function() { 
		    $('#browse-<?php echo $name; ?>').click(function(e) {
		      window.open('<?php echo $SITEURL; ?>admin/filebrowser.php?CKEditorFuncNum=1&returnid=post-<?php echo $name; ?>&type=images', 'browser', 'width=800,height=500,left=100,top=100,scrollbars=yes');
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

	if ($datepick){
		?>
			<script type="text/javascript">
			jQuery(document).ready(function() { 
				$('#post-<?php echo $name; ?>').datetimepicker({ dateFormat: 'dd-mm-yy' });	
			})
			</script>
			<?php
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

//echo "<pre>";
//print_r($schemaArray);
//echo "</pre>";
