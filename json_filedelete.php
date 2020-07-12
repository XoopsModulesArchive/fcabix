<?php
error_reporting(0);
// ################# Loading for using xoopsDB ##############
$xoopsOption['nocommon']=1;
include '../../mainfile.php';
include_once XOOPS_ROOT_PATH.'/class/logger.php';
$xoopsLogger =& XoopsLogger::instance();
$xoopsLogger->startTime();
include_once XOOPS_ROOT_PATH.'/class/database/databasefactory.php';
define('XOOPS_DB_PROXY', 1);
$xoopsDB =& XoopsDatabaseFactory::getDatabaseConnection();
// ##########################################################
require './include/functions.php';
$prefix = XOOPS_DB_PREFIX;
function runSQL($rsql) {
	$hostname = XOOPS_DB_HOST;
	$username = XOOPS_DB_USER;
	$password = XOOPS_DB_PASS;
	$dbname   = XOOPS_DB_NAME;
	$connect = mysql_connect($hostname,$username,$password) or die ("Error: could not connect to database");
	$db = mysql_select_db($dbname);
	$result = mysql_query($rsql) or die ($dbname); 
	mysql_close($connect);
	return $result;
}
$folder_id = intval($_POST['folder_id']);
$items = rtrim($_POST['items'],",");
if ($folder_id && $items){
	$file_ids = explode(",", $items);
	$current_dir = get_current_dir_path($folder_id);
	foreach ($file_ids as $file_id) {
		$file_to_erase = $current_dir . get_file_name($file_id, 2);
		unlink($file_to_erase);
	}
	$sql = "DELETE FROM `".$prefix."_fcabix_files` WHERE `files_id` IN ($items)";
	$result = runSQL($sql);
	$total = mysql_affected_rows();
}else{
	$sql = 'NULL';
	$total = count(explode(",",$items));
}
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");
$json = "";
$json .= "{\n";
$json .= "query: '".$sql."',\n";
$json .= "total: $total,\n";
$json .= "}\n";
echo $json;
?>