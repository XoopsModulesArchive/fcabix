<?php
error_reporting(0);
// ################# Loading for using xoopsDB ##############
include 'header.php';
$xoopsOption['nocommon']=1;
// ##########################################################
include_once './class/mbfunction.class.php';	// Include MB function
include_once "./class/gtickets.php";

$mb = new mb_func();

function countRec($fname,$tname,$where) {
	global $xoopsDB;
	$sql = "SELECT count($fname) FROM $tname $where";
	$result = $xoopsDB->query($sql);
	while ($row = $xoopsDB->fetchRow($result)) {
		return $row[0];
	}
}
$page = isset($_POST['page']) ? intval($_POST['page']) : 0;
$rp = isset($_POST['rp']) ? intval($_POST['rp']) : 10 ;
$sortname = isset($_POST['sortname']) ? htmlspecialchars ( $_POST['sortname'], ENT_QUOTES ) : 'file_name';
$sortorder = isset($_POST['sortorder']) ? htmlspecialchars ( $_POST['sortorder'], ENT_QUOTES ) : 'ASC';
$current_dir = !isset($_POST['currentDir']) ? 1 : intval($_POST['currentDir']);
$qtype = isset($_POST['qtype']) ? htmlspecialchars ( $_POST['qtype'], ENT_QUOTES ) : '';
$query = isset($_POST['query']) ? htmlspecialchars ( $_POST['query'], ENT_QUOTES ) : '';

if (!$sortname) $sortname = 'files_name';
if (!$sortorder) $sortorder = 'desc';
if ($query){
//	$where = "WHERE `".$qtype."` LIKE '%".$query."%' ";
	$where = "WHERE `".$qtype."`=".$query;
} else {
	$where ="";
}
/*
if($_POST['letter_pressed']!=''){
	$where = "WHERE `".$qtype."` LIKE '".$_POST['letter_pressed']."%' ";	
}
if($_POST['letter_pressed']=='#'){
	$where = "WHERE `".$qtype."` REGEXP '[[:digit:]]' ";
}
*/
$sort = "ORDER BY $sortname $sortorder";

if (!$page) $page = 1;
if (!$rp) $rp = 10;

$start = (($page-1) * $rp);

$limit = "LIMIT $start, $rp";

$total = countRec('files_id', $xoopsDB->prefix("fcabix_files"), $where);

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" );
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" );
header("Cache-Control: no-cache, must-revalidate" );
header("Pragma: no-cache" );
header("Content-type: text/x-json");
$json = "";
$json .= "{\n";
$json .= "page: $page,\n";
$json .= "total: $total,\n";
$json .= "rows: [\n";

// Insert folders data.

//$sql = "SELECT * FROM ".$xoopsDB->prefix("fcabix_folders")." WHERE folders_parent_id = ".$query." ORDER BY folders_name";
//$result = $xoopsDB->query($sql);
//
//$json_d = '';
//$json_d_count = 0;
//while ($row = $xoopsDB->fetchArray($result)) {
//    if (have_access_right_to_folder($row['folders_id']) && (is_not_hidden($row['folders_id']) || $is_mod_admin)) {
//        $json_d .= "{";
//        $json_d .= "id: '-d-".$row['folders_id']."',"; // ex) <tr id="row-d-13">...</tr>
//        $json_d .= "cell: [ ";
//        /* id              */ $json_d .= "'".$row['folders_id']."', ";
//        /* name            */ $json_d .= "'".addslashes( $row['folders_name'] )."', ";
//        /* created_at      */ $json_d .= "'".addslashes( date('Y/m/d H:i', $row['folders_createddate']) )."', ";
//        /* created_by      */ $json_d .= "'".XoopsUser::getUnameFromId($row['folders_owner'])."', ";
//        /* updated_at      */ $json_d .= "'".addslashes( date('Y/m/d H:i', $row['folders_modificationdate']) )."', ";
//        /* updated_by      */ $json_d .= "'".XoopsUser::getUnameFromId($row['folders_usermod'])."', ";
//        /* type            */ $json_d .= "'dir', ";
//        /* hidden          */ $json_d .= (is_not_hidden($row['folders_id']) ? 0 : 1).", ";
//        /* erasable        */ $json_d .= (have_erase_right_to_folder($row['folders_id']) ? 1 : 0).", ";
//        /* modifiable      */ $json_d .= (have_mod_right_to_folder($row['folders_id']) ? 1 : 0).", ";
//		/* folder_writable */ $json_d .= (have_create_right_for_folder($row['folders_id']) ? 1 : 0).", ";
//		/* file_writable   */ $json_d .= (have_create_right_for_file($row['folders_id']) ? 1 : 0);
//		$json_d .= " ]},\n";
//		$json_d_count ++;
//    }
//}
//
//$json .= $json_d;


// Insert files data.

$sql = sprintf("SELECT * FROM %s %s %s %s;",$xoopsDB->prefix("fcabix_files"),$where,$sort,$limit);
$result = $xoopsDB->query($sql);
$rc = false;
$json_invisible_data = '';
while ($row = $xoopsDB->fetchArray($result)) {
	if ($rc) $json .= ",\n";
	$json .= "{";
	$json .= "id:'-f-".$row['files_id']."',"; // ex) <tr id="row-f-13">...</tr>

	// Visible data

	$json .= "cell:[";
	/* name       */ $json .= "'".addslashes( $row['files_name'] )."', ";
	/* created_at    $json .= "'".addslashes(date('Y/m/d H:i',  $row['files_createddate'] ) )."', ";*/
	/* created_by    $json .= "'".(XoopsUser::getUnameFromId($row['files_owner']))."', ";*/
	/* updated_at */ $json .= "'".addslashes(date('Y/m/d H:i',  $row['files_modificationdate'] ) )."', ";
	/* updated_by */ $json .= "'".(XoopsUser::getUnameFromId($row['files_usermod']))."', ";
	/* size       */ $json .= "'".addslashes($row['files_space'] < 1000 ? $row['files_space']." KB" : round($row['files_space']/1000, 1)." MB")."'";
	/* type          $json .= "'".addslashes(str_replace('.png', '', $row['files_type']))."'";*/
	$json .= "]";
	
	// Invisible data for client-side JS processing.
	
	$json .= ',data:{';
		$json .= "id:'".$row['files_id']."', ";
		$json .= "name:'".addslashes( $row['files_name'] )."', ";
		$json .= "created_at:'".addslashes(date('Y/m/d H:i',  $row['files_createddate'] ) )."', ";
		$json .= "created_by__uid:'".$row['files_owner']."', ";
		$json .= "created_by__uname:'".(XoopsUser::getUnameFromId($row['files_owner']))."', ";
		$json .= "updated_at:'".addslashes(date('Y/m/d H:i',  $row['files_modificationdate'] ) )."', ";
		$json .= "updated_by__uid:'".$row['files_usermod']."', ";
		$json .= "updated_by__uname:'".(XoopsUser::getUnameFromId($row['files_usermod']))."', ";
		$json .= "size:'".addslashes($row['files_space'] < 1000 ? $row['files_space']." KB" : round($row['files_space']/1000, 1)." MB")."', ";
		$json .= "type:'".addslashes(str_replace('.png', '', check_file_type($row['files_name'])))."', ";	// $row['files_type']
		$json .= "erasable:".(have_erase_right_to_file($row['files_foldersid']) ? 1 : 0).", ";
		$json .= "modifiable:".(have_mod_right_to_file($row['files_foldersid']) ? 1 : 0).", ";
		$json .= "files_foldersid:".$row['files_foldersid'];
	$json .= '}';
	
	$json .="}";
	$rc = true;
}
$json .= "\n],\n";

// Current folder's infos

$sql = "SELECT * FROM ".$xoopsDB->prefix("fcabix_folders")." WHERE folders_id = ".$query;
$result = $xoopsDB->query($sql);
$row = $xoopsDB->fetchArray($result);


$json .= "current_folder:{";
$json .= 	"id:'".$row['folders_id']."',";
$json .= 	"name: '".addslashes($row['folders_id'] == 1 ? constant($row['folders_name']) : $row['folders_name'])."',";
$json .= 	"created_at: '".addslashes(date('Y/m/d H:i', $row['folders_createddate']))."',";
$json .= 	"created_by__uid: '".$row['folders_owner']."',\n";
$json .= 	"created_by__uname: '".(XoopsUser::getUnameFromId($row['folders_owner']))."',";
$json .= 	"updated_at: '".addslashes(date('Y/m/d H:i', $row['folders_createddate']))."',";
$json .= 	"updated_by__uid: '".$row['folders_usermod']."',\n";
$json .= 	"updated_by__uname: '".(XoopsUser::getUnameFromId($row['folders_usermod']))."',";
$json .= 	"hidden: ".(is_not_hidden($row['folders_id']) ? 0 : 1).",";
$json .= 	"inherit: ".$row['folders_inheritrights'].",";
$json .= 	"erasable: ".(have_erase_right_to_folder($row['folders_id']) ? 1 : 0).",";
$json .= 	"modifiable: ".(have_mod_right_to_folder($row['folders_id']) ? 1 : 0).",";
$json .= 	"perm_settable: ".($is_mod_admin ? 1 : 0).",";
$json .= 	"folder_writable: ".(have_create_right_for_folder($row['folders_id']) ? 1 : 0).",";
$json .= 	"file_writable: ".(have_create_right_for_file($row['folders_id']) ? 1 : 0).",";
$json .= 	"parent: '".$row['folders_parent_id']."'";
$json .= "},\n";


// Path (Breadcrumbs nav) to current folder

$json .= "folder_path:[\n";
$json_p = '';
$parent_folder = $query;
while ($parent_folder != 0) {
    $sql = "SELECT folders_id, folders_name, folders_parent_id FROM ".$xoopsDB->prefix("fcabix_folders")." WHERE folders_id = ".$parent_folder;
    $result = $xoopsDB->query($sql);
    $row = $xoopsDB->fetchArray($result);
    $parent_folder = $row['folders_parent_id'];
    $json_p = "{ id: ".$row['folders_id'].", name: '".addslashes($row['folders_id'] == 1 ? constant($row['folders_name']) : $row['folders_name'])."'},".$json_p;
}
$json .= substr($json_p, 0, -1)."\n],\n";

// for Treeview (jQuery plugin)

$json_t = 'folder_tree: {id: 1, name: "'._MD_PARENTFOLDER.'"';

function get_branches($fid){
	global $xoopsDB, $json_t;
	$sql = "SELECT folders_id,folders_name FROM ".$xoopsDB->prefix("fcabix_folders")." WHERE folders_parent_id = ".$fid." ORDER BY folders_name";
	$result = $xoopsDB->query($sql);
	if($xoopsDB->getRowsNum($result) > 0){
		$json_t .= ', folders: [';
		while($row = $xoopsDB->fetchArray($result)){
			if(have_access_right_to_folder($row['folders_id'])){
				$json_t .= '{id: '.$row['folders_id'].', name: "'.$row['folders_name'].'"';
				get_branches($row['folders_id']);
				$json_t .= '},';
			}
		}
		$json_t = substr($json_t, 0, -1).']';
	}
}

get_branches(1);

$json_t .= "},\n";
$json .= $json_t;


// GroupPermForm

if($query || $query > 0){
	include_once 'class/grouppermform.php';
	$sql = "SELECT rights_id, rights_name FROM ".$xoopsDB->prefix("fcabix_rights")." WHERE rights_applied_to_folders = 1";
	$result = $xoopsDB->query($sql);
	while ($rights_array = $xoopsDB->fetchArray($result)) {
	    $item_list[$rights_array['rights_id']] = constant($rights_array['rights_name']);
	}
	$form = new XoopsGroupPermForm($xoopsModule->getVar('mid'), $query);
	foreach ($item_list as $item_id => $item_name) {
		$form->addItem($item_id, $item_name);
	}
	$gpf_data = $form->assign();
	$json .= "groupperm: {\n";
	$json .= "name: '".$gpf_data['name']."', action: '"./*$gpf_data['action']*/'json_perm.php'."', \n";
	$json .= "hiddens: [";
	foreach($gpf_data['hiddens'] as $i => $h){
		$json .= ($i > 0 ? ', ' : '')."{name: '".$h['name']."', value: '".$h['value']."'}";
	}
	$json .= "],\n";
	$json .= "groups: [\n";
	foreach($gpf_data['groups'] as $i => $g){
		$json .= ($i > 0 ? ",\n" : '')."{id: ".$g['id'].", name: '".$g['name']."', rights: [";
		foreach($g['rights'] as $j => $r){
			$json .= ($j > 0 ? ", " : '')."{checkbox: {name: '".$r['checkbox']['name']."', label: '".$r['checkbox']['label']."', checked: ".$r['checkbox']['checked'].", value: '".$r['checkbox']['value']."'}, ";
			$json .= "hiddens: [";
			foreach($r['hiddens'] as $k => $ih){
				$json .= ($k > 0 ? ", " : '')."{name: '".$ih['name']."', value: '".$ih['value']."'}";
			}
			$json .= "]}";
		}
		$json .= "]}";
	}
	$json .= "]\n";
	$json .= "},\n";
}

$json .= "upload_ticket: '".session_id()."',";
$json .= $xoopsGTicket->getTicketJson( md5(uniqid(rand())) );

$json .= "\n}";
echo $mb->internal2utf8($json);
?>