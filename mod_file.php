<?php
// $Id: mod_file.php,v 1.4 2005/03/14 20:05:43 jsaucier Exp $
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
$xoopsOption['template_main'] = 'fcabix_modfile.html';    
    

// Include Xoops header
require(XOOPS_ROOT_PATH.'/header.php');

$tpl_vars = array('content' => array(), 'langs' => array(), 'config' => array());


// Set the CSS for the module
$xoops_module_header = '
<link rel="stylesheet" type="text/css" media="screen,tv,print" href="style.css" />
<link rel="stylesheet" type="text/css" media="screen,tv,print" href="js/ui/themes/ui-lightness/jquery-ui-1.7.1.custom.css" />
';




// Select the current_file
if (!empty($_GET['curent_file'])) {
    $current_file = (int) $_GET['curent_file'];
}
else {
    $current_file = 0;
}


// Select the current_dir that we are in
if (!empty($_GET['curent_dir'])) {
    $current_dir = (int) $_GET['curent_dir'];
}
else {
    $current_dir = 0;
}



// Test if the file exist in the current dir, if we have the right to access it, if we can mod the file and if the folder is not hidden
if (if_file_is_in_dir($current_file, $current_dir) && have_access_right_to_folder($current_dir) && (is_not_hidden($current_dir) || $is_mod_admin)) {
	
    $sql = "SELECT * FROM ".$xoopsDB->prefix("fcabix_files")." WHERE files_id = ".$current_file;
    $result = $xoopsDB->query($sql);
    $myrow = $xoopsDB->fetchArray($result);

	$tpl_vars['content']['file_id'] = $myrow['files_id'];
	$tpl_vars['content']['file_name'] = $myrow['files_name'];
	$tpl_vars['content']['folder_id'] = $current_dir;
	
}
else {
	redirect_header('index.php', 1, _MD_FOLDER_ERROR_RIGHTS);
}

$tpl_vars['langs'] = array(
	'main_title' => _MD_FCABIX_MAIN_TITLE,
	'modfile_title' => _MD_MODFILE_TITLE,
	'modfile_input' => sprintf(_MD_MODFILE_INPUT, $myrow['files_name']),
	'modfile_rename' => _MD_MODFILE_RENAME
);
$xoopsTpl->assign('fcabix', $tpl_vars);

// Set the CSS for the module
$xoopsTpl->assign('xoops_module_header', $xoops_module_header);

// Include Xoops footer
include 'footer.php';

?>