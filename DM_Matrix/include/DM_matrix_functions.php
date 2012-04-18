<?php

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


