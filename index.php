<?php
// $Id: index.php,v 1.5 2005/03/14 20:05:43 jsaucier Exp $
//  ------------------------------------------------------------------------ //
//                           Document Manager                                //
//            Copyright (c) 2004 Informatique Strategique IS                 //
//                  <http://www.infostrategique.com/>                        //
//  ------------------------------------------------------------------------ //
//  This program is free software; you can redistribute it and/or modify     //
//  it under the terms of the GNU General Public License as published by     //
//  the Free Software Foundation; either version 2 of the License, or        //
//  (at your option) any later version.                                      //
//                                                                           //
//  You may not change or alter any portion of this comment or credits       //
//  of supporting developers from this source code or any supporting         //
//  source code which is considered copyrighted (c) material of the          //
//  original comment or credit authors.                                      //
//                                                                           //
//  This program is distributed in the hope that it will be useful,          //
//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
//  GNU General Public License for more details.                             //
//                                                                           //
//  You should have received a copy of the GNU General Public License        //
//  along with this program; if not, write to the Free Software              //
//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
//  ------------------------------------------------------------------------ //
// Include the module header
include 'header.php';

// Set the template
$xoopsOption['template_main'] = 'fcabix_index.html';

// Include Xoops header
require(XOOPS_ROOT_PATH.'/header.php');

// Set the CSS for the module
$xoops_module_header = '<link rel="stylesheet" type="text/css" media="screen,tv,print" href="style.css" />';
// Build the navigation bar //

// Select the current_dir that we are in
if(!empty($_GET['curent_dir'])){
    $current_dir = (int) $_GET['curent_dir'];
}else{
    $current_dir = 1;
}
$tpl_vars = array('content' => array(), 'langs' => array(), 'config' => array());

// Configuration for Flexigrid window
$treeViewUnique = FALSE;
if (isset($xoopsModuleConfig['treeViewUnique'])) $treeViewUnique = $xoopsModuleConfig['treeViewUnique']==1 ? TRUE : FALSE;
$tpl_vars['config']['width'] = isset($xoopsModuleConfig['fcabixWidth']) ? $xoopsModuleConfig['fcabixWidth'] : ''; // int or strval(int) or ''
$tpl_vars['config']['flexigrid_useRp'] = 'true'; // true or false
$tpl_vars['config']['flexigrid_rp'] = 128; // int
$tpl_vars['config']['flexigrid_rpOptions'] = '16, 32, 64, 128, 256'; // comma separated
//$tpl_vars['config']['flexigrid_width'] = 500; // int or strval(int)
$tpl_vars['config']['flexigrid_height'] = 412; // int or strval(int)
$tpl_vars['config']['flexigrid_col_name_width'] = 200; // int or strval(int)
$tpl_vars['config']['flexigrid_col_updatedat_width'] = 115; // int or strval(int)
$tpl_vars['config']['flexigrid_col_updatedby_width'] = 70; // int or strval(int)
$tpl_vars['config']['flexigrid_col_size_width'] = 60; // int or strval(int)
$tpl_vars['config']['flexigrid_cols_order'] = array('name', 'updatedat', 'updatedby', 'size'); // string-array
$tpl_vars['config']['treeview_width'] = 25; // Set % by int or strval(int)  * min 1, max 89
$tpl_vars['config']['treeview_unique'] =  $treeViewUnique; // bool ( Auto Roll On/Off )
//$tpl_vars['config']['treeview_collapsed'] = TRUE; // bool
$tpl_vars['config']['upload_maxnum'] = isset($xoopsModuleConfig['max_filenum']) ? $xoopsModuleConfig['max_filenum'] : 128; // int
$tpl_vars['config']['upload_maxsize'] = isset($xoopsModuleConfig['max_filesize']) ? $xoopsModuleConfig['max_filesize'] : 1048576; // int (byte)
$tpl_vars['config']['upload_maxsize_withunit'] = $tpl_vars['config']['upload_maxsize'] >= 1000
                                                     ? $tpl_vars['config']['upload_maxsize'] >= 1000000
                                                         ? round(($tpl_vars['config']['upload_maxsize'] / 1000000), 1).'MB'
                                                         : round(($tpl_vars['config']['upload_maxsize'] / 1000), 1).'KB'
                                                     : $tpl_vars['config']['upload_maxsize'].'B';
$tpl_vars['config']['upload_ext'] = $xoopsModuleConfig['ext_filter']; // comma separated
$tpl_vars['config']['use_flash'] = isset($xoopsModuleConfig['use_flash']) ? $xoopsModuleConfig['use_flash'] : 1;
$tpl_vars['config']['dev_dualui'] = FALSE; // bool
$tpl_vars['config']['dev_smartydebug'] = FALSE; // bool

// Test if we have the right to access this folder
/*
echo "<br />if_folder_exist:".if_folder_exist($current_dir);
echo "<br />have_access_right_to_folder:".have_access_right_to_folder($current_dir);
echo "<br />is_not_hidden:".is_not_hidden($current_dir);
echo "<br />is_mod_admin:".$is_mod_admin;
*/
if (if_folder_exist($current_dir) && have_access_right_to_folder($current_dir) && (is_not_hidden($current_dir) || $is_mod_admin)) {


    // Loop to find the current folders and all the parent folders
    
    $i = 0;
    
    $parent_dir = $current_dir;
    
    while ($parent_dir != 0) {
        $sql = "SELECT folders_id, folders_name, folders_hidden, folders_parent_id FROM ".$xoopsDB->prefix("fcabix_folders")." WHERE folders_id = ".$parent_dir;
        $result = $xoopsDB->query($sql);
        $list_folder[$i] = $xoopsDB->fetchArray($result);
        $parent_dir = $list_folder[$i]['folders_parent_id'];
        $i++;
    }
    
    $i--;
    
    
    // Build the navigation bar with the folder array
    
    $j = $i;
    
    $first = true;
    $tpl_vars['content']['path'] = array();
    
    while ($j >= 0) {
    
        // If it's the first folder, take the constant name
        if ($first == true) {
            $list_folder[$j]['folders_name'] = constant($list_folder[$j]['folders_name']);
            $first = false;
        }
        
        $tpl_vars['content']['path'][] = array(
            'id' => $list_folder[$j]['folders_id'],
            'name' => $list_folder[$j]['folders_name'],
            'hidden' => is_not_hidden($list_folder[$j]['folders_id']) ? 0 : 1,
        );
        
        $j--;
    }
    
/*	$folder_tree = '<ul class="filetree"><li><span class="folder" id="folder-1">'.$tpl_vars['content']['path'][0]['name'].'</span>';

	function get_branches($fid){
		global $xoopsDB, $folder_tree;
		$sql = "SELECT * FROM ".$xoopsDB->prefix("fcabix_folders")." WHERE folders_parent_id = ".$fid." ORDER BY folders_name";
		$result = $xoopsDB->query($sql);
		$folder_tree .= '<ul>';
		while($row = $xoopsDB->fetchArray($result)){
			$folder_tree .= '<li><span class="folder" id="folder-'.$row['folders_id'].'">'.$row['folders_name'].'</span>';
			get_branches($row['folders_id']);
			$folder_tree .= '</li>';
		}
		$folder_tree .= '</li></ul>';
	}
	
	get_branches(1);
	
	$folder_tree .= '</li></ul>';
    $tpl_vars['content']['folder_tree'] = str_replace('<ul></ul>', '', $folder_tree);
    */
    
    $tpl_vars['config']['current_folder'] = array(
        'id' => $list_folder[0]['folders_id'],
        'name' => $list_folder[0]['folders_name'],
        'folder_writable' => have_create_right_for_folder($list_folder[0]['folders_id']) ? 1 : 0,
        'file_writable' => have_create_right_for_file($list_folder[0]['folders_id']) ? 1 : 0,
        'folder_modifiable' => have_mod_right_to_folder($list_folder[0]['folders_id']) ? 1 : 0,
        'folder_erasable' => have_erase_right_to_folder($list_folder[0]['folders_id']) ? 1 : 0,
    );
    
    
    // Build the folders list //
    
    $sql = "SELECT * FROM ".$xoopsDB->prefix("fcabix_folders")." WHERE folders_parent_id = ".$list_folder[0]['folders_id']." ORDER BY folders_name";
    $result = $xoopsDB->query($sql);
    
       $tpl_vars['content']['folders'] = array();
       
    while ($myrow = $xoopsDB->fetchArray($result)) {
        if (have_access_right_to_folder($myrow['folders_id']) && (is_not_hidden($myrow['folders_id']) || $is_mod_admin)) {
            $tpl_vars['content']['folders'][] = array(
                'id' => $myrow['folders_id'],
                'name' => $myrow['folders_name'],
//                'created_at' => formatTimestamp($myrow['folders_createddate'], 'm'),
//                'created_by__uname' => XoopsUser::getUnameFromId($myrow['folders_owner']),
//                'created_by__uid' => $myrow['folders_owner'],
                'updated_at' => formatTimestamp($myrow['folders_modificationdate'], 'm'),
                'updated_by__uname' => XoopsUser::getUnameFromId($myrow['folders_usermod']),
                'updated_by__uid' => $myrow['folders_usermod'],
                'hidden' => is_not_hidden($myrow['folders_id']) ? 0 : 1,
                'erasable' => have_erase_right_to_folder($myrow['folders_id']) ? 1 : 0,
                'modifiable' => have_mod_right_to_folder($myrow['folders_id']) ? 1 : 0
            );
        }
    }
	
    // Build the documents list //
    
    $sql = "SELECT * FROM ".$xoopsDB->prefix("fcabix_files")." WHERE files_foldersid = ".$list_folder[0]['folders_id']." ORDER BY files_name";
    $result = $xoopsDB->query($sql);
    
    $tpl_vars['content']['files'] = array();

    while ($myrow = $xoopsDB->fetchArray($result)) {
        $tpl_vars['content']['files'][] = array(
            'id' => $myrow['files_id'],
            'name' => $myrow['files_name'],
//            'created_at' => formatTimestamp($myrow['files_createddate'],"m"),
//            'created_by__uname' => XoopsUser::getUnameFromId($myrow['files_owner']),
//            'created_by__uid' => $myrow['files_owner'],
            'updated_at' => formatTimestamp($myrow['files_modificationdate'],"m"),
            'updated_by__uname' => XoopsUser::getUnameFromId($myrow['files_usermod']),
            'updated_by__uid' => $myrow['files_usermod'],
            'type' => str_replace('.png', '', check_file_type($myrow['files_name'])),
//            'icon' => $myrow['files_type'],
            'size_by_kb' => $myrow['files_space'],
            'size_with_unit' => $myrow['files_space'] < 1000 ? round(intval($myrow['files_space']), 1).' KB' : round(intval($myrow['files_space'])/1000, 1).' MB',
            'erasable' => have_erase_right_to_file($myrow['files_foldersid']) ? 1 : 0,
            'modifiable' => have_mod_right_to_file($myrow['files_foldersid']) ? 1 : 0,
        );
    }
    $error = 0;
} else {
    // Display the error
    $tpl_vars['config']['error'] = _MD_FOLDER_ERROR_RIGHTS;
    $error = 1;
}
$tpl_vars['config']['perm_settable'] = $is_mod_admin && $error == 0 ? 1 : 0;
$tpl_vars['config']['module_url'] = $fcabix_url;

$tpl_vars['langs'] = array(
    'nav_bar_title' => _MD_NAVIGATION_TITLE,
    'act_bar_title' => _MD_ACTION_TITLE,
    'folder_title' => _MD_FOLDER_TITLE,
    'folder_name' => _MD_FOLDER_NAME,
    'folder_modify' => _MD_FOLDER_MOD2,
    'folder_delete' => _MD_FOLDER_DELETE,
    'file_title' => _MD_DOCUMENT_TITLE,
    'file_name' => _MD_FILE_NAME,
    'file_date' => _MD_FILE_DATE,
    'file_space' => _MD_FILE_SPACE,
    'file_summary' => _MD_FILE_SUMMARY,
    'file_delete' => _MD_FILE_DELETE,
    'folder_hidden' => _MD_FOLDER_HIDDEN,
    'admin_title' => _MD_ADMIN_TITLE,
    'edit_permission' => _MD_EDITRIGHTS,
    'main_title' => _MD_FCABIX_MAIN_TITLE,
    'file_size' => _MD_FILE_SPACE,
    'file_summary' => _MD_FILE_SUMMARY,
    'file_delete' => _MD_FILE_DELETE, 
    'create_folder'=> _MD_FOLDER_CREATE,
    'create_file' => _MD_FILE_CREATE,
    'modify_folder' => _MD_FOLDER_MOD,
    'delete_folder' => _MD_FOLDER_ERASE,
    
    'explorer_title' => _MD_FLEXIGRID_EXPLORER_TITLE,
    'root' => _MD_PARENTFOLDER,
    'name' => _MD_FLEXIGRID_COL_NAME,
    'created_at' => _MD_FLEXIGRID_COL_CREATEDAT,
    'created_by' => _MD_FLEXIGRID_COL_CREATEDBY,
    'updated_at' => _MD_FLEXIGRID_COL_UPDATEDAT,
    'updated_by' => _MD_FLEXIGRID_COL_UPDATEDBY,
    'size' => _MD_FLEXIGRID_COL_SIZE,
    'type' => _MD_FLEXIGRID_COL_TYPE,
    'displaying' => _MD_FLEXIGRID_DISPLAYING,
    'processing' => _MD_FLEXIGRID_PROCESSING,
    'noitems' => _MD_FLEXIGRID_NOITEMS,
    'newfolder' => _MD_FLEXIGRID_COMMAND_MAKEDIR,
    'connection_error' => _MD_FLEXIGRID_CONNECTIONERROR,
    'confirm_multidl' => _MD_FLEXIGRID_CONFIRM_MULTIDL,
    'confirm_delete' => _MD_FLEXIGRID_CONFIRM_DELETE,
    'command_delete' => _MD_FLEXIGRID_COMMAND_DELETE,
    'command_deletedir' => _MD_FLEXIGRID_COMMAND_DELETEDIR,
    'command_download' => _MD_FLEXIGRID_COMMAND_DOWNLOAD,
    'command_parent' => _MD_FLEXIGRID_COMMAND_PARENT,
    'command_upload' => _MD_FLEXIGRID_COMMAND_UPLOAD,
    'command_fileproperty' => _MD_FLEXIGRID_COMMAND_FILEPROPERTY,
    'command_dirproperty' => _MD_FLEXIGRID_COMMAND_DIRPROPERTY,
    'command_makedir' => _MD_FLEXIGRID_COMMAND_MAKEDIR,
    'dialog_control' => _MD_UIDIALOG_CONTROL,
    'dialog_inherit' => _MD_ATTRFOLDER_INHERIT_FOLDER,
    'dialog_hide' => _MD_ATTRFOLDER_HIDDEN_FOLDER,
    'dialog_delete' => _MD_UIDIALOG_DELETE,
    'dialog_groupperm' => _MD_UIDIALOG_GROUPPERM,
    'dialog_ok' => _MD_UIDIALOG_OK,
    'dialog_cancel' => _MD_UIDIALOG_CANCEL,
    'dialog_title_setperm' => _MD_ATTRFOLDER_TITLE,
    'dialog_title_about' => _MD_UIDIALOG_TITLE_ABOUT,
    'dialog_title_makedir' => _MD_UIDIALOG_TITLE_MAKEDIR,
    'dialog_title_upload' => _MD_UIDIALOG_TITLE_UPLOAD,
    'dialog_title_download' => _MD_UIDIALOG_TITLE_DOWNLOAD,
    'dialog_title_deletefile' => _MD_FLEXIGRID_COMMAND_DELETE,
    'dialog_title_deletedir' => _MD_FLEXIGRID_COMMAND_DELETEDIR,
    'dialog_title_fileproperty' => _MD_FLEXIGRID_COMMAND_FILEPROPERTY,
    'dialog_title_dirproperty' => _MD_FLEXIGRID_COMMAND_DIRPROPERTY,
    'dialog_upload' => _MD_UPLOADIFY_START,
    'dialog_upload_ext' => _MD_UIDIALOG_UPLOAD_EXT,
    'dialog_upload_maxnum' => _MD_UIDIALOG_UPLOAD_MAXNUM,
    'dialog_upload_maxsize' => _MD_UIDIALOG_UPLOAD_MAXSIZE,
    
    'nojsgrid_select' => _MD_NOJSGRID_SELET,
    'nojsgrid_rename' => _MD_NOJSGRID_RENAME,
    'nojsgrid_delete' => _MD_NOJSGRID_DELETE,
    'nojsgrid_setgroupperm' => _MD_NOJSGRID_SETGROUPPERM,
    'nojsgrid_downloadselected' => _MD_NOJSGRID_DOWNLOAD_SELETED
);
// Set jQuery and plugins to header
$xoops_module_header .='
<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript"><!--
	jQuery.noConflict();
//--></script>
<link rel="stylesheet" type="text/css" media="screen,tv,print" href="js/flexigrid/flexigrid.css" />
<link rel="stylesheet" type="text/css" media="screen,tv,print" href="js/uploadify/uploadify.css" />
<link rel="stylesheet" type="text/css" media="screen,tv,print" href="js/treeview/jquery.treeview.css" />
<link rel="stylesheet" type="text/css" media="screen,tv,print" href="js/ui/themes/ui-lightness/jquery-ui-1.7.1.custom.css" />
<script type="text/javascript" src="js/flexigrid/flexigrid.custom.min.js"></script>
<script type="text/javascript" src="js/uploadify/jquery.uploadify.js"></script>
<script type="text/javascript" src="js/treeview/jquery.treeview.custom.min.js" charset="utf-8"></script>
<script type="text/javascript" src="js/ui/jquery-ui-1.7.1.custom.min.js"></script>
<script type="text/javascript" src="js/ifixpng/jquery.ifixpng2.js"></script>
';

$xoopsTpl->assign('xoops_module_header', $xoops_module_header);
$xoopsTpl->assign('fcabix', $tpl_vars);
include 'footer.php';
?>