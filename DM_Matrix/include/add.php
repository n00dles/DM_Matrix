<?php 
$schemaname=$_GET['add'];
		if (isset($_GET['field'])){
			$record=$_GET['field'];
			echo "<h2>".i18n_r($thisfile_DM_Matrix.'/DM_EDITING')." '".$schemaname."' ".i18n_r($thisfile_DM_Matrix.'/DM_RECORD')." : ".$record."</h2>";
			echo '<form method="post" action="load.php?id=DM_Matrix&action=matrix_manager&add='.$schemaname.'&updaterecord">';
			DM_editForm($schemaname,$record);
			echo '</form>';
		} else {
			echo "<h2>".i18n_r($thisfile_DM_Matrix.'/DM_ADD_NEW')." '".$schemaname."' ".i18n_r($thisfile_DM_Matrix.'/DM_RECORD')."</h2>";
			echo "<a href='load.php?id=DM_Matrix&action=matrix_manager&view=$schemaname'>View all records for $schemaname</a>";
			echo '<form method="post" action="load.php?id=DM_Matrix&action=matrix_manager&add='.$schemaname.'&addrecord">';
			DM_createForm($schemaname);
			echo '</form>';
		}