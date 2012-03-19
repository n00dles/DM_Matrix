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
  'Schema Manager',
  '1.0',
  'Mike Swan',
  'http://digimute.com/',
  'Schema Manager',
  'DM_schema',
  'schema_manager'
);
    

define('GSSCHEMAPATH',GSDATAOTHERPATH.'schema');
 
$defaultDebug = true;
$schemaArray = array();
$item_title='Schema';

require "DM_schema/include/sql4array.php";


register_script('DM_schema',$SITEURL.'plugins/DM_schema/js/DM_schema.js', '0.1',FALSE);
queue_script('DM_schema', GSBACK);

add_action('file-uploaded','file_upload', array());
add_action('nav-tab','createNavTab',array('DM_schema','DM_schema','Schema','action=schema_manager&schema'));
DM_getSchema();


$sql = new sql4array();

$sql->createFromGlobals();

//getPagesXmlValues();

//print_r($schemaArray);

//$r = array();

//	$r = $sql->query("SELECT id FROM schemaArray as arr WHERE id > 14 order by id DESC");

//print_r($r);


//createSchemaTable('test',array('test'=>'int','blogger'=>'text'));

//createRecord('gallery', array('name'=>'This is a test 1','title'=>'title1','image'=>'image 1'));
//createRecord('gallery', array('name'=>'This is a test 2','title'=>'title2','image'=>'image 2'));
//createRecord('gallery', array('name'=>'This is a test 3','title'=>'title3','image'=>'image 3'));
//createRecord('gallery', array('name'=>'This is a test 4','title'=>'title4','image'=>'image 4'));


//deleteSchemaField('blog',array('title','author','date'));
//dropSchemaTable('blog');

if (isset($_GET['edit']) && isset($_GET['addfield']) && $flag==false){
  	echo "adding Field to ".$_GET['edit']."/".$_POST['post-name']."=".$_POST['post-type'];
	  addSchemaField($_GET['edit'],array($_POST['post-name']=>$_POST['post-type']),true);
	  //DM_saveSchema();
  }

//Admin Content
function schema_manager() {
global $item_title, $fieldtypes,$schemaArray;

//Main Navigation For Admin Panel
?>
<div style="width:100%;margin:0 -15px -15px -10px;padding:0px;">
	<h3 class="floated"><?php echo $item_title; ?> Manager</h3>  
	<div class="edit-nav clearfix" style="">
		<a href="load.php?id=DM_schema&action=schema_manager&settings" <?php if (isset($_GET['settings'])) { echo 'class="current"'; } ?>>Settings</a>
		<a href="load.php?id=DM_schema&action=schema_manager&fields" <?php if (isset($_GET['fields'])) { echo 'class="current"'; } ?>>Manage Fields</a>
		<a href="load.php?id=DM_schema&action=schema_manager&schema" <?php if (isset($_GET['schema'])) { echo 'class="current"'; } ?>>Show Schemas</a>
	</div> 
</div>
</div>
<div class="main" style="margin-top:-10px;">

<?php

//Alert Admin If Items Manager Settings XML File Is Directory Does Not Exist
if (isset($_GET['schema'])) {
?>
		<h2>Show Schemas</h2>
		<table id="editpages" class="edittable highlight paginate">
		<tbody><tr><th>Schema Name</th><th >records</th><th>Fields</th><th style="width:75px;">Options</th></tr>
		<?php 
		foreach($schemaArray as $schema=>$key){
			echo "<tr><td>".$schema."</td><td>".($key['id']-1)."</td><td>".count($key['fields'])."</td><td><a href='load.php?id=DM_schema&action=schema_manager&edit=".$schema."'><img src='../plugins/DM_schema/images/edit.gif' title='Edit Records' /></a></td></tr>";
		}
		
		?>
		<tr id="DM_addnew_row" style="display: block;height:20px;"><td><input id="post-name" type="text" /></td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
		<tr><td>&nbsp</td><td>&nbsp</td><td>&nbsp</td><td><a href="#" id="dm_addnew"><img src="../plugins/DM_schema/images/box.gif" title="Create Schema" /></a></td></tr>
		</tbody>
		</table>
<?php
	
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
			echo "<tr><td>".$schema."</td><td>".$key."</td><td><a href='load.php?id=DM_schema&action=schema_manager&edit=".$schema."'><img src='../plugins/DM_schema/images/edit.gif' title='Edit Records' /></a></td></tr>";
		}
		
		?>
		<td>
			<form method="post" action="load.php?id=DM_schema&action=schema_manager&edit=<?php echo $schemaname; ?>&addfield">
				<input type="text" value="" id="post-name" name="post-name" class="required"></td>
		<td>
			<select id="post-type" name="post-type">
				<option value="int">int</option>		
				<option value="int">text</option>		
				<option value="int">textarea</option>		
				
			</select>	
		</td>
		<td>
			<input type="submit" value="Add">
		</td>
		</form>
		</tbody>
		</table>
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
					$schemaArray[(string)$key]['fields'][(string)$field]=(string)$type;
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
        $count++;   
        $id=$data->item;
		
		foreach ($id->children() as $opt=>$val) {
            $pagesArray[(string)$key][(string)$opt]=(string)$val;
			$table[$fname][(string)$opt]=(string)$val;
        }
		
    	//$table[$fname]['id']=$fname;
		//$table[$fname]['name']=(string)$id;
		
	    }
	  }
	
	return $table;
}

print_r($schemaArray['gallery']);
$table=array();
$table=getSchemaTable('gallery');
echo "<pre>";
$r = array();

$r = $sql->query("SELECT id,image,name,title FROM table ORDER by title LIKE '%4%'");

print_r($r);
echo "</pre>";