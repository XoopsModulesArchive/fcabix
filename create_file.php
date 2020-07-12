<?php
// $Id: create_file.php,v 1.4 2005/03/14 20:05:43 jsaucier Exp $
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
$xoopsOption['template_main'] = 'fcabix_createfile.html';    
    

// Include Xoops header
require(XOOPS_ROOT_PATH.'/header.php');


$tpl_vars = array('content' => array(), 'langs' => array(), 'config' => array());


// Select the current_dir that we are in
if (!empty($_GET['curent_dir'])) {
    $current_dir = (int) $_GET['curent_dir'];
}
else {
    $current_dir = 0;
}



// Test if the folder exist, if we have the right to access it, if we can create a new file and if the folder is not hidden
if (if_folder_exist($current_dir) && have_access_right_to_folder($current_dir) && have_create_right_for_file($current_dir) && (is_not_hidden($current_dir) || $is_mod_admin)) {


    $sql = "SELECT * FROM ".$xoopsDB->prefix("fcabix_folders")." WHERE folders_id = ".$current_dir;
    $result = $xoopsDB->query($sql);
    $myrow = $xoopsDB->fetchArray($result);
    
    $tpl_vars['content']['folder'] = array(
        'id' => $myrow['folders_id'],
        'name' => $myrow['folders_id'] == 1 ? constant($myrow['folders_name']) : $myrow['folders_name'],
        'created_at' => formatTimestamp($myrow['folders_createddate'], 'm'),
        'created_by__uname' => XoopsUser::getUnameFromId($myrow['folders_owner']),
        'created_by__uid' => $myrow['folders_owner'],
        'updated_at' => formatTimestamp($myrow['folders_modificationdate'], 'm'),
        'updated_by__uname' => XoopsUser::getUnameFromId($myrow['folders_usermod']),
        'updated_by__uid' => $myrow['folders_usermod'],
        'hidden' => is_not_hidden($myrow['folders_id']) ? 0 : 1,
        'erasable' => have_erase_right_to_folder($myrow['folders_id']) ? 1 : 0,
        'modifiable' => have_mod_right_to_folder($myrow['folders_id']) ? 1 : 0,
        'writable_for_file' => have_create_right_for_file($myrow['folders_id']) ? 1 : 0,
    );


}else {
	redirect_header('index.php', 1, _MD_FOLDER_ERROR_RIGHTS);
}

$tpl_vars['langs'] = array(
	'main_title' => _MD_FCABIX_MAIN_TITLE,
	'createfile_title' => _MD_CREATEFILE_TITLE,
	'createfile_selectfile' => _MD_CREATEFILE_SELECTFILE,
	'createfile_upload' => _MD_CREATEFILE_UPLOAD,
    'dialog_upload_ext' => _MD_UIDIALOG_UPLOAD_EXT,
    'dialog_upload_maxnum' => _MD_UIDIALOG_UPLOAD_MAXNUM,
    'dialog_upload_maxsize' => _MD_UIDIALOG_UPLOAD_MAXSIZE
);
$tpl_vars['config']['upload_maxnum'] = 128; // int
$tpl_vars['config']['upload_maxsize'] = isset($xoopsModuleConfig['max_filesize']) ? $xoopsModuleConfig['max_filesize'] : 1048576; // int (byte)
$tpl_vars['config']['upload_maxsize_withunit'] = $tpl_vars['config']['upload_maxsize'] >= 1000
                                                     ? $tpl_vars['config']['upload_maxsize'] >= 1000000
                                                         ? round(($tpl_vars['config']['upload_maxsize'] / 1000000), 1).'MB'
                                                         : round(($tpl_vars['config']['upload_maxsize'] / 1000), 1).'KB'
                                                     : $tpl_vars['config']['upload_maxsize'].'B';
$tpl_vars['config']['upload_ext'] = $xoopsModuleConfig['ext_filter']; // comma separated

$xoopsTpl->assign('fcabix', $tpl_vars);

// Set the CSS for the module
$xoops_module_header = '
<link rel="stylesheet" type="text/css" media="screen,tv,print" href="style.css" />
<link rel="stylesheet" type="text/css" media="screen,tv,print" href="js/ui/themes/ui-lightness/jquery-ui-1.7.1.custom.css" />
';

$xoopsTpl->assign('xoops_module_header', $xoops_module_header);


// Include Xoops footer
include 'footer.php';

?>